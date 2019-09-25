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
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Block\Adminhtml\User\Edit\Tab;

class Helpdesk extends \Magento\Backend\Block\Widget\Form implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    public function __construct(
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->config        = $config;
        $this->formFactory   = $formFactory;
        $this->registry      = $registry;

        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabLabel()
    {
        return __('Help Desk');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getAfter()
    {
        return 'roles_section';
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->_initForm();

        return parent::_beforeToHtml();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     */
    protected function _initForm()
    {
        $form = $this->formFactory->create();
        $model = $this->registry->registry('permissions_user');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Help Desk Settings')]);

        if ($this->config->getGeneralIsWysiwyg()) {
            $fieldset->addField('signature', 'editor', [
                'label'   => __('Signature for Emails'),
                'name'    => 'signature',
                'value'   => $model->getSignature(),
                'config'  => $this->wysiwygConfig->getConfig(),
                'wysiwyg' => true,
                'style'   => 'height:20em',
            ]);
        } else {
            $fieldset->addField('signature', 'textarea', [
                'name'  => 'signature',
                'label' => __('Signature for Emails'),
                'id'    => 'signature',
                'value' => $model->getSignature(),
            ]);
        }

        $this->setForm($form);
    }
}
