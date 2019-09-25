<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */

namespace Amasty\AdminActionsLog\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_objectManager;
    protected $_authSession;
    protected $_sessionManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Framework\App\Helper\Context $context
    )
    {
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->_authSession = $authSession;
        $this->_sessionManager = $sessionManager;
    }

    public function getCategoryName($category)
    {
        $categoryName = $category;

        $categoriesNames = [
            'cms/page' => __('CMS Manage Pages'),
            'admin/system_config' => __('System Configuration'),
            'catalog/product' => __('Product'),
            'customer/index' => __('Customer'),
            'customer' => __('Customer'),
            'admin/system_store' => __('Store'),
            'sales/order' => __('Order'),
            'catalog/product_attribute' => __('Product Attribute'),
            'customer/group' => __('Customer Group'),
            'sales/order_create' => __('New Order'),
            'admin/user' => __('User'),
            'sales/order_invoice' => __('Invoice'),
            'admin/order_shipment' => __('Shipment'),
            'sales/order_creditmemo' => __('Credit Memo'),
            'newsletter/template' => __('Newsletter Template'),
            'admin/email_template' => __('Email Template'),
            'tax/rule' => __('Tax Rule'),
            'catalog_rule/promo_catalog' => __('Catalog Price Rule'),
            'sales_rule/promo_quote' => __('Cart Price Rule'),
            'catalog/category' => __('Product Category'),
            'search/term' => __('Search Term'),
            'admin/url_rewrite' => __('Url Rewrite'),
            'review/product' => __('Product Review'),
            'checkout/agreement' => __('Terms and Conditions'),
            'sales/order_status' => __('Order Status'),
            'tax/rate' => __('Tax Rate'),
            'admin/system_surrencysymbol' => __('Currency Symbol'),
            'catalog/product_set' => __('Attribute Set'),
            'review/rating' => __('Rating'),
            'admin/integration' => __('Integration'),
            'admin/user_role' => __('User Role'),
            'indexer/indexer' => __('Index Management'),
        ];

        if (isset($categoriesNames[$category])) {
            $categoryName = $categoriesNames[$category];
        }

        return $categoryName;
    }

    public function isOriginData($object)
    {
        $isOrigData = false;

        if ($object->getOrigData()) {
            foreach ($object->getOrigData() as $data) {
                if (!empty($data)) {
                    $isOrigData = true;
                    break;
                }
            }
        }

        return $isOrigData;
    }

    public function needToSave($object)
    {
        $needToSave = true;

        $notForSaveClasses = [
            'Amasty\AdminActionsLog\Model\Log',
            'Amasty\AdminActionsLog\Model\LogDetails',
            'Magento\Downloadable\Model\Link',
            'Amasty\AdminActionsLog\Model\LoginAttempts',
            'Amasty\AdminActionsLog\Model\ActiveSessions',
            'Amasty\AdminActionsLog\Model\VisitHistoryDetails',
            'Amasty\AdminActionsLog\Model\VisitHistory',
            'Magento\Ui\Model\Bookmark',
            'Magento\Quote\Model\Quote\Address',
            'Magento\Store\Model\Store',
            'Magento\Quote\Model\Quote\Item',
            'Magento\Quote\Model\Quote\Payment',
            'Magento\Sales\Model\Order\Item',
            'Magento\Sales\Model\Order\Address',
            'Magento\Quote\Model\Quote\Item\Option',
            'Magento\Quote\Model\Quote\Address\Rate',
            'Magento\Sales\Model\Order\Payment',
            'Magento\Tax\Model\Sales\Order\Tax',
            'Magento\Sales\Model\Order\Tax\Item',
            'Magento\CatalogInventory\Model\Adminhtml\Stock\Item',
            'Magento\SalesRule\Model\Coupon',
            'Magento\Catalog\Model\Product\Option\Value',
            'Magento\Theme\Model\Theme',
            'Magento\Security\Model\AdminSessionInfo',
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute'
        ];

        foreach ($notForSaveClasses as $class) {
            if (is_a($object, $class)) {
                return false;
            }
        }

        if (!($this->_authSession->getUser())
            || !$this->isUserInLog($this->_authSession->getUser()->getId())
            || $this->isNoCompletedOrder($object)
        ) {
            $needToSave = false;
        }

        return $needToSave;
    }

    public function isNoCompletedOrder($object)
    {
        $isNoCompletedOrder = false;

        if ($object instanceof \Magento\Quote\Model\Quote) {
            if (!$object->getReservedOrderId()) {
                $isNoCompletedOrder = true;
            }
        } elseif ($this->isOrderCreate()) {
            $isNoCompletedOrder = true;
        }

        return $isNoCompletedOrder;
    }

    public function isOrderCreate()
    {
        $isOrderCreate = false;

        $backTrace = debug_backtrace();
        foreach ($backTrace as $step) {
            if (
                isset($step['object'])
                && ($step['object'] instanceof \Magento\Sales\Model\AdminOrder\Create)
                && ($step['function'] == 'createOrder'))
            {
                $isOrderCreate = true;
                break;
            }
        }
        $backTrace = NULL;

        return $isOrderCreate;
    }

    public function isCompletedOrder($object, $logModel)
    {
        $isCompletedOrder = false;

        if ($object instanceof \Magento\Quote\Model\Quote
            && $object->getReservedOrderId()
        ) {
            $isCompletedOrder = true;
            /**
             * @var \Amasty\AdminActionsLog\Model\LogDetails $logDetails
             */
            $logDetails = $this->_objectManager->get('Amasty\AdminActionsLog\Model\LogDetails');
            $logDetails->deleteUnnecessaryOrderData($logModel);
        }

        return $isCompletedOrder;
    }

    public function needOldData($object)
    {
        $needOldData = false;

        $neededObjects = [
            'Magento\Catalog\Model\Product\Interceptor',
        ];

        if (in_array(get_class($object), $neededObjects)
            || !$this->isOriginData($object)) {
            $needOldData = true;
        }

        return $needOldData;
    }

    public function isUserInLog($userId)
    {
        if (!$this->scopeConfig->getValue('amaudit/log/log_all_admins')) {
            $massId = $this->scopeConfig->getValue('amaudit/log/log_admin_users');
            $massId = explode(',', $massId);
            if (in_array($userId, $massId)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function canUseGeolocation()
    {
        $canUse = false;

        if ($this->_moduleManager->isEnabled('Amasty_Geoip')) {
            /**
             * @var \Amasty\Geoip\Helper\Data $geoIpHelper
             */
            $geoIpHelper = $this->_objectManager->get('\Amasty\Geoip\Helper\Data');
            if ($geoIpHelper->isDone()) {
                $canUse = true;
            }
        }

        return $canUse;
    }

    public function autoClear()
    {
        $this->_objectManager->get('Amasty\AdminActionsLog\Model\Log')->clearLog();
        $this->_objectManager->get('Amasty\AdminActionsLog\Model\VisitHistory')->clearLog();
        $this->_objectManager->get('Amasty\AdminActionsLog\Model\LoginAttempts')->clearLog();
    }

    public function getSessionId()
    {
        return $this->_sessionManager->getSessionId();
    }

    public function setSessionId($id = null)
    {
        $this->_sessionManager->setSessionId($id);
    }

    public function sessionDestroy($options = null)
    {
        $this->_sessionManager->destroy($options);
    }

    /**
     * @param \Magento\Catalog\Model\Product\Interceptor $product
     * @return array
     */
    public function _prepareProductData($product)
    {
        $data = $product->getData();

        $newAssociatedProductIds = $product->getAssociatedProductIds();

        if (isset($newAssociatedProductIds) && !empty($newAssociatedProductIds)) {
            $oldAssociatedProducts = $product->getTypeInstance()->getUsedProducts($product);
            $data['associated_product_ids'] = array_keys($oldAssociatedProducts);
        }

        return $data;
    }
}
