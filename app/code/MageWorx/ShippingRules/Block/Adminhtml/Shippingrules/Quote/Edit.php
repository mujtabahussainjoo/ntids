<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Quote;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    const URL_PATH_DUPLICATE = 'mageworx_shippingrules/shippingrules_quote/duplicate';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_shippingrules_quote';
        $this->_blockGroup = 'MageWorx_ShippingRules';

        parent::_construct();

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );

        if ($this->getRequest()->getParam('id')) {
            $duplicateUrl = $this->_urlBuilder->getUrl(
                static::URL_PATH_DUPLICATE,
                [
                    'id' => $this->getRequest()->getParam('id'),
                ]
            );
            $this->buttonList->add(
                'duplicate',
                [
                    'class' => 'save',
                    'label' => __('Duplicate'),
                    'onclick' => 'setLocation("' . $duplicateUrl . '")'
                ],
                12
            );
        }
    }

    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $rule = $this->coreRegistry->registry('current_promo_quote_rule');
        if ($rule->getRuleId()) {
            return __("Edit Rule '%1'", $this->escapeHtml($rule->getName()));
        } else {
            return __('New Rule');
        }
    }
}
