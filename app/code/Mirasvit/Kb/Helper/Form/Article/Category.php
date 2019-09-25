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


namespace Mirasvit\Kb\Helper\Form\Article;

use Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Mirasvit\Kb\Model\CategoryFactory;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Catalog\Model\Category as CategoryModel;

class Category extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    private $categoriesTrees = [];

    public function __construct(
        CategoryFactory $categoryFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\StoreFactory $storeFactory,
        LocatorInterface $locator,
        DbHelper $dbHelper,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->categoryFactory           = $categoryFactory;
        $this->storeManager              = $storeManager;
        $this->storeFactory              = $storeFactory;
        $this->locator                   = $locator;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->dbHelper                  = $dbHelper;
        $this->context                   = $context;

        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @param string                     $container
     * @param string                     $updateUrl
     * @param string                     $renderUrl
     * @return array
     */
    public function getCategoryField($article, $container, $updateUrl, $renderUrl)
    {
        return '
<div>
    <div data-role="spinner" data-component="'.$container.'.'.$container.'"
        class="admin__form-loading-mask">
        <div class="spinner">
            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
        </div>
    </div>
    <div data-bind="scope: \''.$container.'.'.$container.'\'" class="entry-edit form-inline">
        <!-- ko template: getTemplate() --><!-- /ko -->
    </div>
<script type="text/x-magento-init">
    {
        "*": '.json_encode($this->getCategoryJsLayout($article, $container, $updateUrl, $renderUrl)).'
    }
</script>';
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @param string                     $container
     * @param string                     $updateUrl
     * @param string                     $renderUrl
     * @return array
     */
    public function getCategoryJsLayout($article, $container, $updateUrl, $renderUrl)
    {
        return [
            "Magento_Ui/js/core/app" => [
                "types" => [
                    "dataSource" => [
                        "component" => "Magento_Ui/js/form/provider"
                    ],
                    "container" => [
                        "extends" => $container
                    ],
                    "select" => [
                        "extends" => $container
                    ],
                    "multiselect" => [
                        "extends" => $container
                    ],
                    "form.select" => [
                        "extends" => "select"
                    ],
                    "fieldset" => [
                        "component" => "Magento_Ui/js/form/components/fieldset",
                        "extends"   => $container,
                    ],
                    "html_content" => [
                        "component" => "Magento_Ui/js/form/components/html",
                        "extends"   => $container,
                    ],
                    "form.multiselect" => [
                        "extends"   => 'multiselect',
                    ],
                    $container => [
                        "component" => "Magento_Ui/js/form/form",
                        "provider"  => $container.".categories_data_source",
                        "deps"      => $container.".categories_data_source",
                        "namespace" => $container
                    ]
                ],
                "components" => [
                    $container => [
                        "children" => [
                            $container => [
                                "type"     => $container,
                                "name"     => $container,
                                "children" => [
                                    "create_category_modal" => $this->getCategoryModal($updateUrl, $renderUrl),
                                    'category-details' => [
                                        "children" => [
                                            "container_category_ids" => [
                                                "type"     => "container",
                                                "name"     => "container_category_ids",
                                                "children" => [
                                                    "category_ids"           => $this->getCatgoryField($article),
                                                    "create_category_button" => $this->getCategoryCreateButton()
                                                ],
                                                "dataScope" => "",
                                                "config"    => [
                                                    "component"     => "Magento_Ui/js/form/components/group",
                                                    "label"         => __("Categories"),
                                                    "breakLine"     => false,
                                                    "formElement"   => "container",
                                                    "componentType" => "container",
                                                    "scopeLabel"    => __("[GLOBAL]"),
                                                    "sortOrder"     => 0
                                                ]
                                            ]
                                        ],
                                        'config' => [
                                            'collapsible'   => false,
                                            'componentType' => 'fieldset',
                                            'label'         => '',
                                            'sortOrder'     => 0,
                                        ],
                                        'name'      => 'category-details',
                                        'dataScope' => 'data.category',
                                        'type'      => 'fieldset',
                                    ],
                                ]
                            ],
                            "categories_data_source" => [
                                "type"      => "dataSource",
                                "name"      => "categories_data_source",
                                "dataScope" => $container,
                                "config"    => [
                                    "data" => [
                                        "article" => $article->getData()
                                    ],
                                    "params" => [
                                        "namespace" => $container
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param string $updateUrl
     * @param string $renderUrl
     *
     * @return array
     */
    public function getCategoryModal($updateUrl, $renderUrl)
    {
        return [
            "type"     => "container",
            "name"     => "create_category_modal",
            "children" => [
                "create_category" => [
                    "type"      => "container",
                    "name"      => "create_category",
                    "dataScope" => "",
                    "config"    => [
                        "component"        => "Magento_Ui/js/form/components/insert-form",
                        "label"            => "",
                        "componentType"    => "container",
                        "update_url"       => $updateUrl,
                        "render_url"       => $renderUrl,
                        "autoRender"       => false,
                        "ns"               => "kb_new_category_form",
                        "externalProvider" => "kb_new_category_form.new_category_form_data_source",
                        "toolbarContainer" => '${ $.parentName }',
                        "formSubmitType"   => "ajax"
                    ]
                ]
            ],
            "config" => [
                "component"     => "Magento_Ui/js/modal/modal-component",
                "options"       => [
                    "type"  => "slide",
                    "title" => __("New Category")
                ],
                "isTemplate"    => false,
                "componentType" => "modal",
                "imports"       => [
                    "state" => "!index=create_category:responseStatus"
                ]
            ]
        ];
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @return array
     */
    public function getCatgoryField($article)
    {
        return [
            "type"      => "form.select",
            "name"      => "category_ids",
            "dataScope" => "category_ids",
            "config"    => [
                "code"             => "category_ids",
                "component"        => "Mirasvit_Kb/js/form/components/article-edit-group",
                "template"         => "ui/form/field",
                "dataType"         => "text",
                "formElement"      => "select",
                "componentType"    => "field",
                "label"            => "Categories",
                "source"           => 'category-details',
                "scopeLabel"       => '[GLOBAL]',
                "sortOrder"        => 90,
                "globalScope"      => true,
                "filterOptions"    => true,
                "chipsEnabled"     => true,
                "disableLabel"     => true,
                "levelsVisibility" => "1",
                "elementTmpl"      => "ui/grid/filters/elements/ui-select",
                "options"          => $this->getCategoriesTree(),
                "value"            => array_map('intval', $article->getCategoryIds()), // var type is important here
                'visible'          => 1,
                "listens"          => [
                    "index=create_category:responseData" => "setParsed",
                    "newOption"                          => "toggleOptionSelected"
                ],
                "config"           => [
                    "dataScope" => "category_ids",
                    "sortOrder" => 10
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getCategoryCreateButton()
    {
        return [
            "type"   => "container",
            "name"   => "create_category_button",
            "config" => [
                "component"         => "Magento_Ui/js/form/components/button",
                "title"             => __("New Category"),
                "formElement"       => "container",
                "additionalClasses" => "admin__field-small",
                "componentType"     => "container",
                "template"          => "ui/form/components/button/container",
                "actions"           => [
                    [
                        "targetName" => "kb_article_categories.kb_article_categories.create_category_modal",
                        "actionName" => "toggleModal"
                    ],
                    [
                        "targetName" => "kb_article_categories.kb_article_categories.create_category_modal.".
                            "create_category",
                        "actionName" => "render"
                    ],
                    [
                        "targetName" => "kb_article_categories.kb_article_categories.create_category_modal.".
                            "create_category",
                        "actionName" => "resetForm"
                    ]
                ],
                "additionalForGroup" => true,
                "provider"           => false,
                "source"             => "category-details",
                "displayArea"        => "insideGroup",
                "sortOrder"          => 20
            ]
        ];
    }

    /**
     * Retrieve categories tree
     *
     * @param string|null $filter
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getCategoriesTree($filter = null)
    {
        if (isset($this->categoriesTrees[$filter])) {
            return $this->categoriesTrees[$filter];
        }

        /* @var $matchingNamesCollection \Mirasvit\Kb\Model\ResourceModel\Category\Collection */
        $matchingNamesCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('path')
            ->addAttributeToFilter('category_id', ['neq' => CategoryModel::TREE_ROOT_ID])
        ;

        if ($filter !== null) {
            $matchingNamesCollection->addAttributeToFilter(
                'name',
                ['like' => $this->dbHelper->addLikeEscape($filter, ['position' => 'any'])]
            );
        }

        $shownCategoriesIds = [];

        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($matchingNamesCollection as $category) {
            foreach (explode('/', $category->getPath()) as $parentId) {
                $shownCategoriesIds[$parentId] = 1;
            }
        }

        /* @var $collection \Mirasvit\Kb\Model\ResourceModel\Category\Collection */
        $collection = $this->categoryCollectionFactory->create();

        $collection->addAttributeToFilter('category_id', ['in' => array_keys($shownCategoriesIds)])
            ->addAttributeToSelect('path')
            ->addAttributeToSelect(['category_id', 'name', 'is_active', 'parent_id', 'level'])
        ;

        $categoryById = [
            CategoryModel::TREE_ROOT_ID => [
                'value'    => CategoryModel::TREE_ROOT_ID,
                'level'    => 0,
                'optgroup' => null,
            ],
        ];

        foreach ($collection as $category) {
            $categoryId = (int)$category->getId();
            $parentCategoryId = (int)$category->getParentId();
            foreach ([$categoryId, $parentCategoryId] as $id) {
                if (!isset($categoryById[$id])) {
                    $categoryById[$id] = ['value' => $id, 'level' => 0];
                }
            }

            $categoryById[$categoryId]['is_active'] = $category->getIsActive();
            $categoryById[$categoryId]['label']     = $category->getName();
            $categoryById[$categoryId]['stores']    = $this->getStores($category);
            $categoryById[$categoryId]['level']     = $category->getLevel();

            if ($parentCategoryId) {
                if (
                    $categoryById[$parentCategoryId]['level'] == $categoryById[$categoryId]['level'] - 1 ||
                    $collection->getItemById($parentCategoryId)->getLevel() == $categoryById[$categoryId]['level'] - 1
                ) {
                    $categoryById[$parentCategoryId]['optgroup'][] = &$categoryById[$category->getId()];
                }
            }
        }

        $this->categoriesTrees[$filter] = $categoryById[CategoryModel::TREE_ROOT_ID]['optgroup'];

        return $this->categoriesTrees[$filter];
    }

    /**
     * @var array
     */
    private $categoryStores = [];

    /**
     * @param \Mirasvit\Kb\Model\Category $category
     * @return array
     */
    public function getStores($category)
    {
        $data = [];

        $rootId = $category->getParentRootCategory();
        if (!$rootId) {
            return $data;
        }

        if (!isset($this->categoryStores[$rootId])) {
            $rootCategory = $this->categoryFactory->create()->load($rootId);
            $this->categoryStores[$rootId] = (array)$rootCategory->getStoreIds();
        }

        if (in_array(0, $this->categoryStores[$rootId])) {
            $this->categoryStores[$rootId] = array_keys($this->storeManager->getStores(true));
        }

        foreach ($this->categoryStores[$rootId] as $id) {
            if ($id == 0) {
                $data[] = [
                    'is_active' => 1,
                    'label'     => __('All Store Views'),
                    'value'     => 0,
                ];
            } else {
                $store = $this->storeFactory->create()->load($id);
                $data[] = [
                    'is_active' => $store->isActive(),
                    'label'     => $store->getName(),
                    'value'     => (int)$store->getId(),
                ];
            }
        }

        return $data;
    }
}
