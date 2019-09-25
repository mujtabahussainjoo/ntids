<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use MageWorx\ShippingRules\Model\Carrier\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ShippingRules\Api\Data\CarrierInterface;
use MageWorx\ShippingRules\Api\ImportExportEntity;
use MageWorx\ShippingRules\Model\ResourceModel\Carrier as CarrierResource;
use Zend\Stdlib\DateTime;
use Magento\Framework\App\State;

/**
 * Class Carrier
 *
 * @method CarrierResource _getResource()
 * @method CarrierResource getResource()
 *
 */
class Carrier extends AbstractModel implements CarrierInterface, ImportExportEntity
{
    const CURRENT_CARRIER = 'current_carrier';

    const CARRIER_TABLE_NAME = 'mageworx_shippingrules_carrier';
    const METHOD_TABLE_NAME = 'mageworx_shippingrules_methods';
    const RATE_TABLE_NAME = 'mageworx_shippingrules_rates';
    const CARRIER_LABELS_TABLE_NAME = 'mageworx_shippingrules_carrier_label';
    const METHOD_LABELS_TABLE_NAME = 'mageworx_shippingrules_methods_label';
    const RATE_LABELS_TABLE_NAME = 'mageworx_shippingrules_rates_label';
    const METHOD_STORE_SPECIFIC_EDT_MESSAGE_TABLE_NAME = 'mageworx_shippingrules_method_edt_store_specific_message';

    const DEFAULT_MODEL = 'MageWorx\ShippingRules\Model\Carrier\Artificial';
    const DEFAULT_TYPE = 'I';
    const DEFAULT_ERROR_MESSAGE =
        'This shipping method is not available. To use this shipping method, please contact us.';

    /**
     * Columns which will be ignored during import/export process
     * @see \MageWorx\ShippingRules\Model\Carrier\AbstractModel::getIgnoredColumnsForImportExport()
     */
    const IMPORT_EXPORT_IGNORE_COLUMNS = [
        'created_at',
        'updated_at',
        'carrier_id',
        'model',
        'sallowspecific',
        'type',
        'specificerrmsg'
    ];

    /**
     * @var \MageWorx\ShippingRules\Model\ResourceModel\Method\Collection
     */
    protected $methodsCollection;

    /**
     * @var array
     */
    private $methodsByStoreId = [];

    /**
     * Carrier constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $storeManager,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MageWorx\ShippingRules\Model\ResourceModel\Carrier');
        $this->setIdFieldName('carrier_id');
    }

    /**
     * @return ResourceModel\Method\Collection
     */
    public function getMethodsCollection()
    {
        return $this->methodsCollection;
    }

    /**
     * @param ResourceModel\Method\Collection $methods
     * @return $this
     */
    public function setMethodsCollection(\MageWorx\ShippingRules\Model\ResourceModel\Method\Collection $methods)
    {
        $this->methodsCollection = $methods;
        return $this;
    }

    /**
     * Validate model data
     *
     * @param DataObject $dataObject
     * @return array|bool
     */
    public function validateData(DataObject $dataObject)
    {
        $errors = [];

        if (!$dataObject->getCarrierCode()) {
            $errors[] = __('Carrier code is required');
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

    /**
     * Set if not yet and retrieve carrier store labels
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }

        return $this->_getData('store_labels');
    }

    /**
     * Initialize carrier model data from array.
     * Set store labels if applicable.
     *
     * @param array $data
     * @return $this
     */
    public function loadPost(array $data)
    {
        if (isset($data['store_labels'])) {
            $this->setStoreLabels($data['store_labels']);
        }

        return $this;
    }

    /**
     * Prepare data before saving
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        /**
         * Prepare store Ids if applicable and if they were set as string in comma separated format.
         * Backwards compatibility.
         */
        if ($this->hasStoreIds()) {
            $storeIds = $this->getStoreIds();
            if (!empty($storeIds)) {
                $this->setStoreIds($storeIds);
            }
        }

        if (!$this->getData('model')) {
            $this->setModel(static::DEFAULT_MODEL);
        }

        parent::beforeSave();

        return $this;
    }

    /**
     * Get rule associated store Ids
     *
     * @return array
     */
    public function getStoreIds()
    {
        if (!$this->hasStoreIds()) {
            $storeIds = $this->_getResource()->getStoreIds($this->getId());
            $this->setData('store_ids', (array)$storeIds);
        }

        return $this->getData('store_ids');
    }

    /**
     * Retrieve corresponding model name\path
     *
     * @return string
     */
    public function getModel()
    {
        return $this->getData('model') ? $this->getData('model') : static::DEFAULT_MODEL;
    }

    /**
     * Check is carrier active
     *
     * @return int|bool
     */
    public function getActive()
    {
        return $this->getData('active');
    }

    /**
     * sallowspecific
     *
     * @return int
     */
    public function getSallowspecific()
    {
        return $this->getData('sallowspecific');
    }

    /**
     * Carrier type
     *
     * @return string
     */
    public function getType()
    {
        return $this->getData('type');
    }

    /**
     * Carrier error message
     *
     * @return string
     */
    public function getSpecificerrmsg()
    {
        return $this->getData('specificerrmsg');
    }

    /**
     * Default carrier price
     *
     * @return float (12,2)
     */
    public function getPrice()
    {
        return $this->getData('price');
    }

    /**
     * Get created at date
     *
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    /**
     * Get last updated date
     *
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    /**
     * Retrieve carrier name
     *
     * If name is no declared, then default_name is used
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * Retrieve carrier title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Retrieve carrier code
     *
     * @return string
     */
    public function getCarrierCode()
    {
        return $this->getData('carrier_code');
    }

    /**
     * Retrieve carrier ID
     *
     * @return int
     */
    public function getCarrierId()
    {
        return $this->getData('carrier_id');
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getMethods($storeId = null)
    {
        if ($storeId === null) {
            $storeId = 0;
        }

        if (empty($this->methodsByStoreId[$storeId])) {
            $methodsCollection = $this->getMethodsCollection();
            $methodsCollection->clear();
            $methodsCollection->addCarrierFilter($this->getId());
            if ($storeId !== 0) {
                $methodsCollection->addStoreFilter($storeId);
            }

            $this->methodsByStoreId[$storeId] = $methodsCollection->getItems();
        }

        $this->setMethods($this->methodsByStoreId[$storeId]);

        return $this->methodsByStoreId[$storeId];
    }

    /**
     * @param $storeLabels
     * @return $this
     */
    public function setStoreLabels($storeLabels = [])
    {
        return $this->setData('store_labels', $storeLabels);
    }

    /**
     * @param $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds = [])
    {
        return $this->setData('store_ids', $storeIds);
    }

    /**
     * Retrieve corresponding model name\path
     *
     * @param string $model
     * @return $this
     */
    public function setModel($model)
    {
        return $this->setData('model', $model);
    }

    /**
     * Set is carrier active
     *
     * @param int $active
     * @return $this
     */
    public function setActive($active)
    {
        return $this->setData('active', $active);
    }

    /**
     * sallowspecific
     *
     * @param int $sallowSpecific
     * @return $this
     */
    public function setSallowspecific($sallowSpecific)
    {
        return $this->setData('sallowspecific', $sallowSpecific);
    }

    /**
     * Carrier type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData('type', $type);
    }

    /**
     * Carrier error message
     *
     * @param string $msg
     * @return $this
     */
    public function setSpecificerrmsg($msg)
    {
        return $this->setData('specificerrmsg', $msg);
    }

    /**
     * Default carrier price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price)
    {
        return $this->setData('price', $price);
    }

    /**
     * Get created at date
     *
     * @param string|int|DateTime $date
     * @return $this
     */
    public function setCreatedAt($date)
    {
        return $this->setData('created_at', $date);
    }

    /**
     * Get last updated date
     *
     * @param string|int|DateTime $date
     * @return $this
     */
    public function setUpdatedAt($date)
    {
        return $this->setData('updated_at', $date);
    }

    /**
     * Set carrier name
     *
     * If name is no declared, then default_name is used
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    /**
     * Set carrier title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData('title', $title);
    }

    /**
     * Set carrier code
     *
     * @param string $code
     * @return $this
     */
    public function setCarrierCode($code)
    {
        return $this->setData('carrier_code', $code);
    }

    /**
     * Set carrier ID
     *
     * @param int $id
     * @return $this
     */
    public function setCarrierId($id)
    {
        return $this->setData('carrier_id', $id);
    }
}
