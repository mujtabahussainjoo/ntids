<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\StringUtils;
use MageWorx\ShippingRules\Model\Carrier as CarrierModel;
use MageWorx\ShippingRules\Api\CarrierRepositoryInterface;
use MageWorx\ShippingRules\Model\ResourceModel\Rate\CollectionFactory as RatesCollectionFactory;
use MageWorx\ShippingRules\Helper\Data as Helper;
use Magento\Store\Model\StoreResolver;
use Magento\Store\Model\StoreManagerInterface;

class Method extends AbstractResourceModel
{
    /**
     * Store associated with method entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'store' => [
            'associations_table' => CarrierModel::METHOD_TABLE_NAME . '_store',
            'ref_id_field' => 'entity_id',
            'entity_id_field' => 'store_id',
        ]
    ];

    /**
     * @var RatesCollectionFactory
     */
    protected $rateCollectionFactory;

    /**
     * @var array
     */
    protected $priceFields = [
        'max_price_threshold',
        'min_price_threshold',
        'price',
        'cost'
    ];

    /**
     * @var StoreResolver
     */
    protected $storeResolver;

    /**
     * @var CarrierRepositoryInterface
     */
    protected $carrierRepository;

    /**
     * @param Context $context
     * @param StringUtils $string
     * @param \MageWorx\ShippingRules\Helper\Data $helper
     * @param StoreManagerInterface $storeManager
     * @param Rate\CollectionFactory $rateCollectionFactory
     * @param StoreResolver $storeResolver
     * @param CarrierRepositoryInterface $carrierRepository
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        StringUtils $string,
        Helper $helper,
        StoreManagerInterface $storeManager,
        RatesCollectionFactory $rateCollectionFactory,
        StoreResolver $storeResolver,
        CarrierRepositoryInterface $carrierRepository,
        $connectionName = null
    ) {
        $this->rateCollectionFactory = $rateCollectionFactory;
        $this->storeResolver = $storeResolver;
        parent::__construct($context, $string, $helper, $storeManager, $connectionName);
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CarrierModel::METHOD_TABLE_NAME, 'entity_id');
    }

    /**
     * Add customer group ids and store ids to rule data after load
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method $object */
        parent::_afterLoad($object);
        $this->addRates($object);
        $storeId = $this->storeManager->getStore()->getId();
        $label = $object->getStoreLabel($storeId);
        if ($label) {
            $object->setTitle($label);
        }
        $edtMessage = $object->getEdtStoreSpecificMessage($storeId);
        if ($edtMessage) {
            $object->setEstimatedDeliveryTimeMessage($edtMessage);
        }

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method $object */
        if (!$object->getMaxPriceThreshold()) {
            $object->setMaxPriceThreshold(null);
        }
        if (!$object->getMinPriceThreshold()) {
            $object->setMinPriceThreshold(null);
        }

        // Link carrier
        if (!$object->getCarrierCode() && $object->getCarrierId()) {
            $correspondingCarrier = $this->carrierRepository->getById($object->getCarrierId());
            $object->setCarrierCode($correspondingCarrier->getCarrierCode());
        } elseif ($object->getCarrierCode() && !$object->getCarrierId()) {
            $correspondingCarrier = $this->carrierRepository->getByCode($object->getCarrierCode());
            $object->setCarrierId($correspondingCarrier->getId());
        }

        parent::_beforeSave($object);

        $this->validateModel($object);

        return $this;
    }

    /**
     * Validate model required fields.
     * @important Throws an Exception if model invalid.
     *
     * @param AbstractModel $object
     * @return void
     * @throws LocalizedException
     */
    public function validateModel(AbstractModel $object)
    {
        /** @var Method $object */
        if (!$object->getCode()) {
            throw new LocalizedException(__('Method Code is required'));
        }

        if (!$object->getCarrierCode()) {
            throw new LocalizedException(__('Corresponding Carrier Code is required'));
        }
    }

    /**
     * Save method's associated store labels.
     * Save method's associated store specific EDT messages
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method $object */
        if ($object->hasEdtStoreSpecificMessages()) {
            $this->saveEdtStoreSpecificMessages($object->getId(), $object->getEdtStoreSpecificMessages());
        }

        return parent::_afterSave($object);
    }

    /**
     * Get store labels table
     *
     * @return string
     */
    protected function getStoreLabelsTable()
    {
        return $this->getTable(CarrierModel::METHOD_LABELS_TABLE_NAME);
    }

    /**
     * Get reference id column name from the labels table
     *
     * @return string
     */
    protected function getStoreLabelsTableRefId()
    {
        return 'method_id';
    }

    /**
     * Save method EDT store specific messages for the different store views
     *
     * @param int $methodId
     * @param array $messages
     * @throws \Exception
     * @return $this
     */
    public function saveEdtStoreSpecificMessages($methodId, $messages)
    {
        $deleteByStoreIds = [];
        $table = $this->getTable(CarrierModel::METHOD_STORE_SPECIFIC_EDT_MESSAGE_TABLE_NAME);
        $connection = $this->getConnection();

        $data = [];
        foreach ($messages as $storeId => $message) {
            if ($message != '') {
                $data[] = ['method_id' => $methodId, 'store_id' => $storeId, 'message' => $message];
            } else {
                $deleteByStoreIds[] = $storeId;
            }
        }

        $connection->beginTransaction();
        try {
            if (!empty($data)) {
                $connection->insertOnDuplicate($table, $data, ['message']);
            }

            if (!empty($deleteByStoreIds)) {
                $connection->delete($table, ['method_id=?' => $methodId, 'store_id IN (?)' => $deleteByStoreIds]);
            }
        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Get all existing method store specific EDT messages
     *
     * @param int $methodId
     * @return array
     */
    public function getEdtStoreSpecificMessages($methodId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable(CarrierModel::METHOD_STORE_SPECIFIC_EDT_MESSAGE_TABLE_NAME),
            ['store_id', 'message']
        )->where(
            'method_id = :method_id'
        );
        return $this->getConnection()->fetchPairs($select, [':method_id' => $methodId]);
    }

    /**
     * Get method's EDT message by specific store id
     *
     * @param int $methodId
     * @param int $storeId
     * @return string
     */
    public function getEdtStoreSpecificMessage($methodId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable(CarrierModel::METHOD_STORE_SPECIFIC_EDT_MESSAGE_TABLE_NAME),
            'message'
        )->where(
            'method_id = :method_id'
        )->where(
            'store_id IN(0, :store_id)'
        )->order(
            'store_id DESC'
        );
        return $this->getConnection()->fetchOne($select, [':method_id' => $methodId, ':store_id' => $storeId]);
    }

    /**
     * Adds corresponding shipping rates to the method
     * @param AbstractModel $object
     */
    public function addRates(AbstractModel $object)
    {
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method $object */
        if (empty($object->getRates())) {
            /** @var \MageWorx\ShippingRules\Model\ResourceModel\Rate\Collection $ratesCollection */
            $ratesCollection = $this->getRatesCollection($object);
            $object->setRates($ratesCollection->getItems());
        }
    }

    /**
     * @param AbstractModel $object
     * @return Rate\Collection
     */
    public function getRatesCollection(AbstractModel $object)
    {
        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Rate\Collection $rates */
        $rates = $this->rateCollectionFactory->create();
        $rates->addFieldToFilter('method_id', $object->getId());
        $storeId = $this->storeResolver->getCurrentStoreId();
        $rates->addStoreFilter($storeId);
        $rates->addOrder('priority');
        $object->setRatesCollection($rates);

        return $rates;
    }
}
