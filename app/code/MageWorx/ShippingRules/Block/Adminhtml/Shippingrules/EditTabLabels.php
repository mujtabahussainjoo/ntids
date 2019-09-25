<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

class EditTabLabels extends Generic implements
    TabInterface
{
    const DEFAULT_TAB_LABEL = 'Labels';
    const DEFAULT_TAB_TITLE = 'Labels';

    /**
     * @var string
     */
    protected $dataFormPart;

    /**
     * @var string
     */
    protected $_nameInLayout = 'store_view_labels';

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $data
        );
    }

    /**
     * Is need to show this tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        if (!$this->_storeManager->isSingleStoreMode()) {
            return true;
        }

        return false;
    }

    /**
     * Is tab hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get current tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        $label = static::DEFAULT_TAB_LABEL;

        return __($label);
    }

    /**
     * Get default tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        $title = static::DEFAULT_TAB_TITLE;

        return __($title);
    }

    /**
     * Get default tab class
     *
     * @return null
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * Get tab url
     *
     * @return null
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * Is tab loaded via ajax
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Create store specific fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @param array $labels
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function _createStoreSpecificFieldset($form, $labels)
    {
        $fieldset = $form->addFieldset(
            'store_labels_fieldset',
            [
                'legend' => __('Store View Specific Labels'),
                'class' => 'store-scope',
            ]
        );

        /** @var \MageWorx\ShippingRules\Block\Adminhtml\Store\Switcher\Form\Renderer\Fieldset $renderer */
        $renderer = $this->getLayout()
            ->createBlock('MageWorx\ShippingRules\Block\Adminhtml\Store\Switcher\Form\Renderer\Fieldset');

        $fieldset->setRenderer($renderer);
        $websites = $this->_storeManager->getWebsites();
        foreach ($websites as $website) {
            $fieldId = "w_{$website->getId()}_label";
            $fieldset->addField(
                $fieldId,
                'note',
                [
                    'label' => $website->getName(),
                    'fieldset_html_class' => 'website',
                    'class' => 'website',
                    'css_class' => 'website',
                ]
            );

            $groups = $website->getGroups();
            foreach ($groups as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }

                $groupFieldId = "sg_{$group->getId()}_label";
                $fieldset->addField(
                    $groupFieldId,
                    'note',
                    [
                        'label' => $group->getName(),
                        'fieldset_html_class' => 'store-group',
                        'class' => 'store-group',
                        'css_class' => 'store-group',
                    ]
                );

                foreach ($stores as $store) {
                    $id = $store->getId();
                    $storeFieldId = "s_{$id}";
                    $storeFieldName = 'store_labels[' . $id . ']';
                    $storeName = $store->getName();

                    if (isset($labels[$id])) {
                        $storeValue = $labels[$id];
                    } else {
                        $storeValue = '';
                    }

                    $fieldset->addField(
                        $storeFieldId,
                        'text',
                        [
                            'name' => $storeFieldName,
                            'title' => $storeName,
                            'label' => $storeName,
                            'required' => false,
                            'value' => $storeValue,
                            'fieldset_html_class' => 'store',
                            'data-form-part' => $this->dataFormPart,
                            'class' => 'store',
                            'css_class' => 'store',
                        ]
                    );
                }
            }
        }

        return $fieldset;
    }
}
