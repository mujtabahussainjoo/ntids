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

use Magento\Catalog\Model\Category as CategoryModel;

class Storeview extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    private $storeTrees = [];

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->storeManager = $storeManager;
        $this->context      = $context;

        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @param string                     $container
     * @return array
     */
    public function getField($article, $container)
    {
        return '
<div>
    <div data-role="spinner" data-component="'.$container.'.'.$container.'"
        class="admin__form-loading-mask">
        <div class="spinner">
            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
        </div>
    </div>
    <div data-bind="scope: \''.$container.'.'.$container.'\'" class="entry-edit form-inline '.$container.'">
        <!-- ko template: getTemplate() --><!-- /ko -->
    </div>
<script type="text/x-magento-init">
    {
        ".'.$container.'": '.json_encode($this->getJsLayout($article, $container)).'
    }
</script>
</div>';
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @param string                     $container
     * @return array
     */
    public function getJsLayout($article, $container)
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
                        "provider"  => $container.".storeview_data_source",
                        "deps"      => $container.".storeview_data_source",
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
                                    'storeview-details' => [
                                        "children" => [
                                            "container_storeview_ids" => [
                                                "type"     => "container",
                                                "name"     => "container_storeview_ids",
                                                "children" => [
                                                    "storeview_ids" => $this->getStoreviewField($article),
                                                ],
                                                "dataScope" => "",
                                                "config"    => [
                                                    "component"     => "Magento_Ui/js/form/components/group",
                                                    "label"         => __("Store View"),
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
                                        'name'      => 'storeview-details',
                                        'dataScope' => 'data.storeview',
                                        'type'      => 'fieldset',
                                    ],
                                ]
                            ],
                            "storeview_data_source" => [
                                "type"      => "dataSource",
                                "name"      => "storeview_data_source",
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
     * @param \Mirasvit\Kb\Model\Article $article
     * @return array
     */
    public function getStoreviewField($article)
    {
        return [
            "type"      => "form.select",
            "name"      => "storeview_ids",
            "dataScope" => "storeview_ids",
            "config"    => [
                "code"             => "storeview_ids",
                "component"        => "Mirasvit_Kb/js/form/components/article-edit-storeview",
                "template"         => "ui/form/field",
                "dataType"         => "text",
                "formElement"      => "select",
                "componentType"    => "field",
                "label"            => "Store View",
                "source"           => 'storeview-details',
                "scopeLabel"       => '[GLOBAL]',
                "sortOrder"        => 90,
                "globalScope"      => true,
                "filterOptions"    => true,
                "chipsEnabled"     => true,
                "disableLabel"     => true,
                "levelsVisibility" => "1",
                "elementTmpl"      => "ui/grid/filters/elements/ui-select",
                "options"          => $this->getTree($article->getStoreIds()),
                "value"            => array_map('intval', $article->getStoreIds()), // var type is important here
                'visible'          => 1,
                "listens"          => [],
                "config"           => [
                    "dataScope" => "storeview_ids",
                    "sortOrder" => 10
                ]
            ]
        ];
    }

    /**
     * Retrieve store tree
     *
     * @param array $storeIds
     * @return array
     */
    protected function getTree($storeIds)
    {
        $filter = implode(',', $storeIds);
        if (isset($this->storeTrees[$filter])) {
            return $this->storeTrees[$filter];
        }

        $data = [];
        $stores = $this->storeManager->getStores(true);
        foreach ($stores as $store) {
            if (in_array($store->getId(), $storeIds)) {
                $data[$store->getId()] = $this->getStoreOptions($store);
            } elseif ($filter == 0) {
                $data[$store->getId()] = $this->getStoreOptions($store);
            }
        }

        $this->storeTrees[$filter] = $data;

        return $this->storeTrees[$filter];
    }

    /**
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return array
     */
    public function getStoreOptions($store)
    {
        if ($store->getId() == 0) {
            $option = [
                'is_active' => 1,
                'label'     => __('All Store Views'),
                'value'     => 0,
            ];
        } else {
            $option = [
                'value'     => (int)$store->getId(),
                'is_active' => $store->isActive(),
                'label'     => $store->getName()
            ];
        }

        return $option;
    }
}
