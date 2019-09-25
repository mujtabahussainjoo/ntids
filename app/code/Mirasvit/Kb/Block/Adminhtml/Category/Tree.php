<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-kb
 * @version   1.0.49
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Kb\Block\Adminhtml\Category;

class Tree extends \Magento\Backend\Block\Template
{
    /**
     * @var bool
     */
    protected $withProductCount;

    /**
     * @var \Mirasvit\Kb\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\DB\Helper
     */
    protected $dbHelper;

    /**
     * @param \Mirasvit\Kb\Model\CategoryFactory    $categoryFactory
     * @param \Magento\Backend\Model\Auth\Session   $authSession
     * @param \Magento\Framework\Json\Helper\Data   $jsonEncoder
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Framework\DB\Helper          $dbHelper
     * @param array                                 $data
     */
    public function __construct(
        \Mirasvit\Kb\Model\CategoryFactory $categoryFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Json\Helper\Data $jsonEncoder,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\DB\Helper $dbHelper,
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->authSession = $authSession;
        $this->jsonEncoder = $jsonEncoder;
        $this->context = $context;
        $this->registry = $registry;
        $this->dbHelper = $dbHelper;
        parent::__construct($context, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->withProductCount = true;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $addUrl = $this->getUrl('*/*/add', ['_current' => true, 'id' => null, '_query' => false]);

        $this->addChild(
            'add_sub_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label'   => __('Add Subcategory'),
                'onclick' => "addNew('" . $addUrl . "', false)",
                'class'   => 'add',
                'id'      => 'add_subcategory_button',
                'style'   => $this->canAddSubCategory() ? '' : 'display: none;',
            ]
        );

        if ($this->canAddRootCategory()) {
            $this->addChild(
                'add_root_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label'   => __('Add Root Category'),
                    'onclick' => "addNew('" . $addUrl . "', true)",
                    'class'   => 'add',
                    'id'      => 'add_root_category_button',
                ]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * @return bool
     */
    public function canAddSubCategory()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canAddRootCategory()
    {
        return true;
    }

    /**
     * Retrieve category object.
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * @param bool $expanded
     * @return string
     */
    public function getLoadTreeUrl($expanded = null)
    {
        $params = ['_current' => true, 'id' => null, 'store' => null];
        if (($expanded === null && $this->authSession->getIsTreeWasExpanded())
            || $expanded == true
        ) {
            $params['expand_all'] = true;
        }

        return $this->getUrl('*/*/categoriesJson', $params);
    }

    /**
     * @return string
     */
    public function getNodesUrl()
    {
        return $this->getUrl('*/*/jsonTree');
    }

    /**
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/category/move');
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl('*/category/edit');
    }

    /**
     * @return array
     */
    public function getTree()
    {
        $rootArray = $this->_getNodeJson($this->getCategory());
        $tree = isset($rootArray['children']) ? $rootArray['children'] : [];

        return $tree;
    }

    /**
     * @param \Mirasvit\Kb\Model\Category|null $parenNodeCategory
     *
     * @return string
     */
    public function getTreeJson($parenNodeCategory = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parenNodeCategory));
        $json = $this->jsonEncoder->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : []);

        return $json;
    }

    /**
     * @param string $path
     * @param string $javascriptVarName
     * @return string
     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }
        $items = [];
        $ids = explode('/', $path);

        foreach ($ids as $id) {
            $item = $this->categoryFactory->create()->load($id);
            $items[] = $this->_getNodeJson($item);
        }

        return
            '<script type="text/javascript">'
            . $javascriptVarName . ' = ' . $this->jsonEncoder->jsonEncode($items) . ';'
            . '</script>';
    }

    /**
     * @param \Mirasvit\Kb\Model\Category $node
     *
     * @return array
     */
    protected function _getNodeJson($node)
    {
        $item = [
            'id'        => $node->getId(),
            'path'      => $node->getPath(),
            'cls'       => ($node->getIsActive() ? 'active' : 'no-active'),
            'text'      => $this->buildNodeName($node),
            'allowDrag' => true,
            'allowDrop' => true,
        ];

        if ($node->hasChildren()) {
            $item['children'] = [];
            foreach ($node->getAllChildren() as $child) {
                $item['children'][] = $this->_getNodeJson($child);
            }
            $item['expanded'] = true;
        }

        return $item;
    }

    /**
     * @param object $node
     * @return string
     */
    protected function buildNodeName($node)
    {
        $name = '';
        $name .= $node->getName();

        return $name;
    }

    /**
     * @param \Mirasvit\Kb\Model\Category|null $parentNodeCategory
     * @param int                              $recursionLevel
     *
     * @return object|array|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getRoot($parentNodeCategory = null, $recursionLevel = 3)
    {
        if ($parentNodeCategory !== null && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = $this->registry->registry('root');
        if ($root === null) {
            //@todo при чем тут это????
            $rootId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;
            $root = $this->categoryFactory->create()->load($rootId);

            if ($root && $rootId != \Magento\Catalog\Model\Category::TREE_ROOT_ID) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == \Magento\Catalog\Model\Category::TREE_ROOT_ID) {
                $root->setName(__('Root'));
            }

            $this->registry->register('root', $root);
        }

        return $root;
    }

    /**
     * @param \Mirasvit\Kb\Model\Category $parentNodeCategory
     *
     * @return object
     */
    public function getNode($parentNodeCategory)
    {
        $nodeId = $parentNodeCategory->getId();
        $node = $this->categoryFactory->create()->load($nodeId);

        return $node;
    }

    /**
     * @return bool
     */
    public function isClearEdit()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getAddRootButtonHtml()
    {
        return $this->getChildHtml('add_root_button');
    }

    /**
     * @return string
     */
    public function getAddSubButtonHtml()
    {
        return $this->getChildHtml('add_sub_button');
    }

    /**
     * @return string
     */
    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    /**
     * @return string
     */
    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    /**
     * @return string
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getId();
        }

        return \Magento\Catalog\Model\Category::TREE_ROOT_ID;
    }

    /**
     * Retrieve list of categories with name containing $namePart and their parents.
     *
     * @param string $namePart
     *
     * @return string
     */
    public function getSuggestedCategoriesJson($namePart)
    {
        /* @var $collection \Mirasvit\Kb\Model\ResourceModel\Category\Collection|\Mirasvit\Kb\Model\Category[] */
        $collection = $this->categoryFactory->create()->getCollection();

        $matchingNamesCollection = clone $collection;
        $escapedNamePart = $this->dbHelper->addLikeEscape(
            $namePart,
            ['position' => 'any']
        );
        $matchingNamesCollection->addAttributeToFilter(
            'name',
            ['like' => $escapedNamePart]
        )->addAttributeToSelect(
            'path'
        );

        $shownCategoriesIds = [];
        foreach ($matchingNamesCollection as $category) {
            foreach (explode('/', $category->getPath()) as $parentId) {
                $shownCategoriesIds[$parentId] = 1;
            }
        }

        $collection->addAttributeToFilter(
            'category_id',
            ['in' => array_keys($shownCategoriesIds)]
        )->addAttributeToSelect(
            ['category_id', 'name', 'is_active', 'parent_id']
        );

        $categoryById = [
            \Magento\Catalog\Model\Category::TREE_ROOT_ID => [
                'id'       => \Magento\Catalog\Model\Category::TREE_ROOT_ID,
                'children' => [],
            ],
        ];

        foreach ($collection as $category) {
            foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                if (!isset($categoryById[$categoryId])) {
                    $categoryById[$categoryId] = ['id' => $categoryId, 'children' => []];
                }
            }
            $categoryById[$category->getId()]['is_active'] = $category->getIsActive();
            $categoryById[$category->getId()]['label'] = $category->getName();
            $categoryById[$category->getParentId()]['children'][] = &$categoryById[$category->getId()];
        }

        return $this->jsonEncoder->jsonEncode($categoryById[\Magento\Catalog\Model\Category::TREE_ROOT_ID]['children']);
    }
}
