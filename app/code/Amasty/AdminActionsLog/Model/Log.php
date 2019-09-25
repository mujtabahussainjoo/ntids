<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Model;

use Magento\Framework\Model\AbstractModel;

class Log extends AbstractModel
{
    protected $_objectManager;
    protected $_authSession;
    protected $_helper;
    protected $_scopeConfig;
    protected $_registryManager;

    protected function _construct()
    {
        $this->_init('Amasty\AdminActionsLog\Model\ResourceModel\Log');
    }

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Context $context,
        \Amasty\AdminActionsLog\Helper\Data $helper
    )
    {
        parent::__construct($context, $coreRegistry);
        $this->_objectManager = $objectManager;
        $this->_authSession = $authSession;
        $this->_registryManager = isset($data['registry']) ? $data['registry'] : $coreRegistry;
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
    }

    public function prepareLogData($object)
    {
        $data['date_time'] = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
        if ($user = $this->_authSession->getUser()) {
            $data['username'] = $user->getUserName();
        }
        $data['category'] = $this->_registryManager->registry('amaudit_category');
        if ($data['category'] == 'amaudit/actionslog') {
            $data['category'] = str_replace('_', '/', $object->getEventPrefix());
        }
        $data['category_name'] = $this->_helper->getCategoryName($data['category']);
        $data['parametr_name'] = $this->_getParametrName($object);
        $action = $this->_registryManager->registry('amaudit_action');
        $data['type'] = $this->getSaveType($object, $action);

        $data['element_id'] = $this->_getElementId($object);
        $data['item'] = $this->_getItem($object);
        $data['store_id'] = 0;
        if ($object->getStoreId()) {
            $data['store_id'] = $object->getStoreId();
        } elseif ($object->getScopeId()) {
            $data['store_id'] = $object->getScopeId();

        }

        return $data;
    }

    public function getSaveType($object, $action)
    {
        if ($action == 'restore') {
            $type = 'Restore';
        } else {
            $type = 'Edit';

            if (!($object instanceof \Magento\Catalog\Model\Product\Option)) {
                if (
                    ($object->isObjectNew() && !($object instanceof \Magento\Framework\App\Config\Value))
                    || $object instanceof \Magento\Quote\Model\Quote
                    || $object instanceof \Magento\Sales\Model\Order\Invoice
                    || ((!$this->_helper->isOriginData($object)))
                ) {
                    $type = 'New';
                }
            }


            if ($object instanceof \Magento\Customer\Model\Backend\Customer) {
                $oldDataBeforeSave = $this->_registryManager->registry('amaudit_data_before');
                if (!empty($oldDataBeforeSave)) {
                    $type = 'Edit';
                }
            }

            if ($object->isDeleted()) {
                $type = 'Delete';
            }
        }

        return $type;
    }

    public function clearLog($fromObserver = true)
    {
        $logCollection = $this->getCollection();

        $where = [];

        if ($fromObserver) {
            $days = $this->_scopeConfig->getValue('amaudit/log/log_delete_logs_after_days');
            $where['date_time < NOW() - INTERVAL ? DAY'] = $days;
        }

        $logCollection->getConnection()->delete($logCollection->getMainTable(), $where);
    }

    protected function _getParametrName($object)
    {
        $parametrName = 'id';

        return $parametrName;
    }

    protected function _getElementId($object)
    {
        if ($object instanceof \Magento\Sales\Model\Order\Status\History) {
            $elementId = $object->getParentId();
        } elseif ($object instanceof \Magento\Quote\Model\Quote) {
            $elementId = $object->getReservedOrderId();
        } elseif (
            $object instanceof \Magento\Sales\Model\Order\Shipment
            || $object instanceof \Magento\Sales\Model\Order\Invoice
            || $object instanceof \Magento\Sales\Model\Order\Creditmemo
        ) {
            $elementId = $object->getOrderId();
        } else {
            $elementId = $object->getEntityId();
        }

        if (!$elementId) {
            $elementId = $object->getPageId();
        }

        if (!$elementId && $object instanceof \Magento\Downloadable\Model\Link) {
            $elementId = $object->getProductId();
        }

        if (!$elementId && $object instanceof \Magento\Store\Model\Website\Interceptor) {
            $elementId = $object->getWebsiteId();
        }

        if (!$elementId && $object instanceof \Magento\Catalog\Model\ResourceModel\Eav\Attribute\Interceptor) {
            $elementId = $object->getAttributeId();
        }

        if (!$elementId && $object instanceof \Magento\Customer\Model\Group\Interceptor) {
            $elementId = $object->getCustomerGroupId();
        }

        if (!$elementId && $object instanceof \Magento\CatalogRule\Model\Rule\Interceptor) {
            $elementId = $object->getRuleId();
        }

        if (!$elementId && $object instanceof \Magento\Catalog\Model\Product\Option) {
            $elementId = $object->getProductId();
        }

        return $elementId;
    }

    protected function _getItem($object)
    {
        $item = $object->getTitle();

        if ($object instanceof \Magento\Catalog\Model\Product\Option) {
            $product = $object->getProduct();
            if (!is_null($product)) {
                $item = $product->getName();
            }
        }

        if (!$item) {
            $item = $object->getName();
        }

        if (!$item) {
            $entity = $this->_registryManager->registry('amaudit_entity_before_delete');
            if (!is_null($entity)) {
                $item = $entity->getName();
                if (!$item) {
                    $item = $entity->getTitle();
                }
            }
        }

        if (!$item) {
            $item = $object->getParentId();
        }

        if (!$item && $object instanceof \Magento\Customer\Model\Group\Interceptor) {
            $item = $object->getCustomerGroupCode();
        }

        if (!$item && $object instanceof \Magento\Quote\Model\Quote) {
            $item = __('Order') . ' #' . $object->getReservedOrderId();
        }

        if (!$item && $object instanceof \Magento\Sales\Model\Order\Invoice) {
            $item = __('Invoice for Order') . ' #' . $object->getOrderId();
        }

        if (!$item && $object instanceof \Magento\Sales\Model\Order\Shipment) {
            $item = __('Shipment for Order') . ' #' . $object->getOrderId();
        }

        if (!$item && $object instanceof \Magento\Sales\Model\Order\Creditmemo) {
            $item = __('Credit Memo for Order') . ' #' .  $object->getOrderId();
        }

        if (!$item && $object instanceof \Magento\Sales\Model\Order) {
            $item = __('Order') . ' #' .  $object->getRealOrderId();
        }

        if (is_array($item) && $object instanceof \Magento\Tax\Model\Calculation\Rate) {
            $item = NULL;
        }

        if (!$item) {
            $item = $object->getCode();
        }

        if (!$item) {
            $item = $object->getTemplateCode();
        }

        return $item;
    }
}
