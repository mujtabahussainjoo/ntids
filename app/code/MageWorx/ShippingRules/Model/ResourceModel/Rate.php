<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\ShippingRules\Api\MethodRepositoryInterface;
use MageWorx\ShippingRules\Model\Carrier as CarrierModel;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\StringUtils;
use MageWorx\ShippingRules\Helper\Data as Helper;
use Magento\Store\Model\StoreManagerInterface;

class Rate extends AbstractResourceModel
{
    /**
     * Store associated with method entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'store' => [
            'associations_table' => CarrierModel::RATE_TABLE_NAME . '_store',
            'ref_id_field' => 'entity_id',
            'entity_id_field' => 'store_id',
        ]
    ];

    /**
     * @var array
     */
    protected $priceFields = [
        'price_from',
        'price_to',
        'price',
        'price_per_product',
        'price_per_item',
        'price_per_weight'
    ];

    /**
     * @var MethodRepositoryInterface
     */
    protected $methodRepository;

    /**
     * List of a fields which store a value as comma separated string
     * converts to array after load
     * @see \MageWorx\ShippingRules\Model\ResourceModel\Rate::serializeFields()
     * @see \MageWorx\ShippingRules\Model\ResourceModel\Rate::unserializeFields()
     *
     * @var array
     */
    private $commaSeparatedFields = [
        'country_id',
        'region_id'
    ];

    /**
     * Rate constructor.
     * @param Context $context
     * @param StringUtils $string
     * @param Helper $helper
     * @param StoreManagerInterface $storeManager
     * @param MethodRepositoryInterface $methodRepository
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        StringUtils $string,
        Helper $helper,
        StoreManagerInterface $storeManager,
        MethodRepositoryInterface $methodRepository,
        $connectionName = null
    ) {
        parent::__construct($context, $string, $helper, $storeManager, $connectionName);
        $this->methodRepository = $methodRepository;
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CarrierModel::RATE_TABLE_NAME, 'rate_id');
    }

    /**
     * @param AbstractModel|\MageWorx\ShippingRules\Api\Data\RateInterface $object
     * @return $this|AbstractResourceModel
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        // Link method
        if (!$object->getMethodCode() && $object->getMethodId()) {
            $correspondingMethod = $this->methodRepository->getById($object->getMethodId());
            $object->setMethodCode($correspondingMethod->getCode());
        } elseif ($object->getMethodCode() && !$object->getMethodId()) {
            $correspondingMethod = $this->methodRepository->getByCode($object->getMethodCode());
            $object->setMethodId($correspondingMethod->getId());
        }

        parent::_beforeSave($object);
        $this->validateModel($object);

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method\Rate $object */
        parent::_afterLoad($object);

        $storeId = $this->storeManager->getStore()->getId();
        $label = $object->getStoreLabel($storeId);
        if ($label) {
            $object->setTitle($label);
        }

        return $this;
    }

    /**
     * Un-serialize serializable object fields
     *
     * @param AbstractModel $object
     * @return AbstractModel
     */
    public function unserializeFields(AbstractModel $object)
    {
        parent::unserializeFields($object);
        foreach ($this->commaSeparatedFields as $field) {
            if (is_array($object->getData($field))) {
                continue;
            } elseif ($object->getData($field)) {
                $object->setData($field, explode(',', $object->getData($field)));
            } else {
                $object->setData($field, []);
            }
        }
        // Workaround for the old values (not an array) @ver 2.1.1+
        $countryId = $object->getData('country_id');
        if (!is_array($countryId)) {
            if ($countryId) {
                $object->setData('country_id', [$countryId]);
            } else {
                $object->setData('country_id', []);
            }
        }
        $regionId = $object->getData('region_id');
        if (!is_array($regionId)) {
            if ($regionId) {
                $object->setData('region_id', [$regionId]);
            } else {
                $object->setData('region_id', []);
            }
        }

        return $object;
    }

    /**
     * Serialize serializable fields of the object
     *
     * @param AbstractModel $object
     * @return AbstractModel
     */
    public function serializeFields(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::serializeFields($object);
        $this->makeCommaSeparatedFields($object);

        return $object;
    }

    /**
     * Serialize serializable fields of the object
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return void
     */
    protected function _serializeFields(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_serializeFields($object);
        $this->makeCommaSeparatedFields($object);
    }

    /**
     * Make comma separated fields
     *
     * @param AbstractModel $object
     */
    protected function makeCommaSeparatedFields(\Magento\Framework\Model\AbstractModel $object)
    {
        foreach ($this->commaSeparatedFields as $field) {
            if (is_array($object->getData($field))) {
                $object->setData($field, implode(',', $object->getData($field)));
            }
        }
    }

    /**
     * Validate model required fields
     *
     * @param AbstractModel $object
     * @throws LocalizedException
     */
    public function validateModel(AbstractModel $object)
    {
        /** @var Method $object */
        if (!$object->getRateCode()) {
            throw new LocalizedException(__('Rate Code is required'));
        }

        if (!$object->getMethodCode()) {
            throw new LocalizedException(__('Corresponding Method Code is required'));
        }
    }

    /**
     * Save rate's associated store labels.
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    protected function _afterSave(AbstractModel $object)
    {
        return parent::_afterSave($object);
    }

    /**
     * Get store labels table
     *
     * @return string
     */
    protected function getStoreLabelsTable()
    {
        return $this->getTable(CarrierModel::RATE_LABELS_TABLE_NAME);
    }

    /**
     * Get reference id column name from the labels table
     *
     * @return string
     */
    protected function getStoreLabelsTableRefId()
    {
        return 'rate_id';
    }
}
