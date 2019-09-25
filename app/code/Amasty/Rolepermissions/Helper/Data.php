<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Attributes;

class Data extends AbstractHelper
{
    protected $_skipObjectRestriction = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Amasty\Base\Helper\Utils
     */
    protected $baseUtils;

    /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attrSetCollectionFactory */
    protected $attrSetCollectionFactory;

    /** @var \Magento\Catalog\Model\ProductFactory $productFactory */
    protected $productFactory;

    /** @var \Amasty\Rolepermissions\Model\RuleFactory $rule */
    protected $ruleFactory;

    protected $_restrictedAttributeIds = [];
    protected $_restrictedAttrSetIds = [];
    protected $_allowedSetIds = [];
    protected $_allowedAttCodes = [];

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $_category;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\Auth\Session\Proxy $authSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Amasty\Base\Helper\Utils $baseUtils
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attrSetCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Amasty\Rolepermissions\Model\RuleFactory $ruleFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Amasty\Rolepermissions\Model\ResourceModel\Rule $ruleResource
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\Auth\Session\Proxy $authSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\App\ResponseInterface $response,
        \Amasty\Base\Helper\Utils $baseUtils,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attrSetCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Amasty\Rolepermissions\Model\RuleFactory $ruleFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->_coreRegistry = $registry;
        $this->_authSession = $authSession;
        $this->messageManager = $messageManager;
        $this->_backendUrl = $backendUrl;
        $this->_response = $response;
        $this->baseUtils = $baseUtils;
        $this->collectionFactory = $collectionFactory;
        $this->attrSetCollectionFactory = $attrSetCollectionFactory;
        $this->productFactory = $productFactory;
        $this->ruleFactory = $ruleFactory;
        $this->categoryFactory = $categoryFactory;
        return parent::__construct($context);
    }

    /**
     * @param $ruleCategoryIds
     * @return mixed
     */
    public function getParentCategoriesIds($ruleCategoryIds)
    {
        foreach ($ruleCategoryIds as $categoryId) {
            $parentCategories = $this->getParentIds($categoryId);

            if ($parentCategories) {
                foreach ($parentCategories as $parentId) {
                    if (!in_array($parentId, $ruleCategoryIds)) {
                        array_push($ruleCategoryIds, $parentId);
                    }
                }
            }
        }

        return $ruleCategoryIds;
    }

    /**
     * Get all parent categories ids
     *
     * @return array
     */
    public function getParentIds($categoryId = false)
    {
        if ($this->_category) {
            return $this->_category->getParentIds();
        } else {
            return $this->getCategory($categoryId)->getParentIds();
        }
    }

    /**
     * Get category object
     * Using $_categoryFactory
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory($categoryId)
    {
        $this->_category = $this->categoryFactory->create();
        $this->_category->load($categoryId);
        return $this->_category;
    }

    /**
     * @return \Amasty\Rolepermissions\Model\Rule
     */
    public function currentRule()
    {
        if (($rule = $this->_coreRegistry->registry('current_amrolepermissions_rule')) == null) {

            $user = $this->_authSession->getUser();

            if (!$user) {
                return false;
            }

            $rule = $this->ruleFactory->create()->loadByRole($user->getRole()->getId());
            $this->_coreRegistry->register('current_amrolepermissions_rule', $rule, true);
        }

        return $rule;
    }

    public function redirectHome()
    {
        if (!$this->_authSession->getUser()) {
            return;
        }

        $this->messageManager->addError(__('Access Denied'));

        if ($this->_request->getActionName() == 'index') {
            $page = $this->_backendUrl->getStartupPageUrl();

            $url = $this->_backendUrl->getUrl($page);
        } else {
            $url = $this->_backendUrl->getUrl('*/*');
        }

        $this->_response
            ->setRedirect($url)
            ->sendResponse();

        $this->baseUtils->_exit(0);
    }

    public function restrictObjectByStores($data)
    {
        list($name, $value, $isWebsite) = $this->_getRelationField($data);

        if ($value) {
            $rule = $this->currentRule();

            if ($isWebsite) {
                $allowedIds = $rule->getPartiallyAccessibleWebsites();
            } else {
                $allowedIds = $rule->getScopeStoreviews();
            }

            if (!is_array($value)) {
                $value = explode(',', $value);
            }

            if (($value != [0]) && !array_intersect($value, $allowedIds)) {
                $this->redirectHome();
            }
        }

        return $this;
    }

    public function alterObjectStores($object)
    {
        list($name, $value, $isWebsite) = $this->_getRelationField($object->getData());
        if ($value) {
            if (!is_array($value)) {
                $value = explode(',', $value);
                $array = false;
            } else {
                $array = true;
            }

            if ($object->getId()) {
                list($origName, $origValue, $isWebsite) = $this->_getRelationField($object->getOrigData());

                if ($origName === null) {
                    $oldObject = clone $object;
                    $oldObject->load($object->getId());

                    list($origName, $origValue, $isWebsite) = $this->_getRelationField($oldObject->getOrigData());
                }

                if (!is_array($origValue)) {
                    $origValue = explode(',', $origValue);
                }
            } else {
                $origValue = [];
            }

            if ($value != $origValue) {
                $rule = $this->currentRule();

                if ($isWebsite) {
                    $allowedIds = $rule->getPartiallyAccessibleWebsites();
                } else {
                    $allowedIds = $rule->getScopeStoreviews();
                }

                $newValue = $this->combine($origValue, $value, $allowedIds);

                if (!$array) {
                    $newValue = implode(',', array_filter($newValue));
                }

                $object->setData($name, $newValue);
            }
        }

        return $this;
    }

    /**
     * @param array $old
     * @param array $new
     * @param array $allowed
     *
     * @return array
     */
    public function combine($old, $new, $allowed)
    {
        if (!is_array($old)) {
            $old = [];
        }

        $map = array_flip(array_unique(array_merge($new, $old)));

        if ($allowed) {
            foreach ($map as $id => $order) {
                if (in_array($id, $allowed)) {
                    if (!in_array($id, $new)) {
                        unset($map[$id]);
                    }
                } else {
                    if (!in_array($id, $old)) {
                        unset($map[$id]);
                    }
                }
            }
        }

        return array_keys($map);
    }

    protected function _getRelationField($data)
    {
        if (!$data) {
            return false;
        }

        $fieldNames = [
            'websites', 'website_id', 'website_ids',
            'stores', 'store_id', 'store_ids',
        ];

        foreach ($fieldNames as $name) {
            if (isset($data[$name])) {
                if (substr($name, 0, 7) == 'website') {
                    $isWebsite = true;
                } else {
                    $isWebsite = false;
                }

                return [$name, $data[$name], $isWebsite];
            }
        }
    }

    public function canSkipObjectRestriction()
    {
        if ($this->_skipObjectRestriction === null) {
            $this->_skipObjectRestriction = false;

            $action = $this->_request->getActionName();

            if (in_array($action, ['edit', 'view', 'index', 'render'])) {
                $controller = $this->_request->getControllerName();

                $rule = $this->_coreRegistry->registry('current_amrolepermissions_rule');

                if (
                    (!$rule->getLimitOrders() && ($controller == 'order' || ($this->_request->getParam('namespace') == 'sales_order_grid')))
                    ||
                    (!$rule->getLimitInvoices() && ($controller == 'order_invoice' || $controller == 'order_transactions'))
                    ||
                    (!$rule->getLimitShipments() && $controller == 'order_shipment')
                    ||
                    (!$rule->getLimitMemos() && $controller == 'order_creditmemo')
                ) {
                    $this->_skipObjectRestriction = true;
                }
            }
        }

        return $this->_skipObjectRestriction;
    }

    public function getAllowedAttributeCodes()
    {
        if (empty($this->_allowedAttCodes)) {
            /** @var \Amasty\Rolepermissions\Model\ResourceModel\Rule $rule */
            $rule = $this->currentRule();

            if (is_object($rule)) {
                if (Attributes::MODE_SELECTED == $rule->getAttributeAccessMode()) {
                    $allowedAttributeIds = $rule->getAttributes();
                    $collectionFactory = $this->collectionFactory->create();
                    $collectionFactory->addFieldToFilter('main_table.attribute_id', ['in' => $allowedAttributeIds]);
                    $this->_allowedAttCodes = $collectionFactory->getColumnValues('attribute_code');
                } else {
                    $this->_allowedAttCodes = true;
                }
            }
        }

        return $this->_allowedAttCodes;
    }

    public function getRestrictedAttributeIds()
    {
        if (empty($this->_restrictedAttributeIds)) {
            /** @var \Amasty\Rolepermissions\Model\ResourceModel\Rule $rule */
            $rule = $this->currentRule();

            if (is_object($rule) && $allowedAttributeIds = $rule->getAttributes()) {
                $collectionFactory = $this->collectionFactory->create();
                $this->_coreRegistry->register('its_amrolepermissions', 1, true);//without this line we get only allowed attribute ids instead of all
                $allAttributeIds = $collectionFactory->addVisibleFilter()->getColumnValues('attribute_id');
                $this->_coreRegistry->unregister('its_amrolepermissions');
                $this->_restrictedAttributeIds = array_diff($allAttributeIds, $allowedAttributeIds);
            }
        }

        return $this->_restrictedAttributeIds;
    }

    public function getRestrictedSetIds()
    {
        if (empty($this->_restrictedAttrSetIds)) {
            $restrictedAttributeIds = $this->getRestrictedAttributeIds();

            if ($restrictedAttributeIds) {
                /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $collection */
                $collection = $this->attrSetCollectionFactory->create();
                $connection = $collection->getConnection();
                $select = $connection->select()->distinct(
                    true
                )->from(
                    $collection->getTable('eav_entity_attribute'),
                    'attribute_set_id'
                )->where(
                    'attribute_id IN(?)',
                    $restrictedAttributeIds
                );
                $this->_restrictedAttrSetIds = $connection->fetchCol($select);
            }
        }

        return $this->_restrictedAttrSetIds;
    }

    public function getAllowedSetIds()
    {
        if (empty($this->_allowedSetIds)) {
            $restrictedSetIds = $this->getRestrictedSetIds();
            /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $collection */
            $collection = $this->attrSetCollectionFactory->create();
            if (!empty($restrictedSetIds)) {
                $collection
                    ->distinct(true)
                    ->addFieldToFilter('entity_type_id', ['eq' => $this->productFactory->create()->getResource()->getTypeId()])
                    ->addFieldToFilter('attribute_set_id', ['nin' => $restrictedSetIds]);
            }
            $this->_allowedSetIds = $collection->getConnection()->fetchCol($collection->getSelect());
        }

        return $this->_allowedSetIds;
    }

    public function restrictAttributeSets()
    {
        $restrict = true;

        $collectionSize = $this->attrSetCollectionFactory->create()->addFieldToFilter(
            'entity_type_id',
            ['eq' => $this->productFactory->create()->getResource()->getTypeId()]
        )->getSize();
        if ($collectionSize > 0) {
            $restrict = false;
        }

        return $restrict;
    }
}
