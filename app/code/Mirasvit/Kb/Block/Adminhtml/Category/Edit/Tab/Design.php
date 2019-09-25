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



namespace Mirasvit\Kb\Block\Adminhtml\Category\Edit\Tab;

use Magento\Backend\Block\Widget\Form;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Mirasvit\Kb\Model\CategoryFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Context;

class Design extends Form
{
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var WysiwygConfig
     */
    private $wysiwygConfig;

    public function __construct(
        CategoryFactory $categoryFactory,
        FormFactory $formFactory,
        WysiwygConfig $wysiwygConfig,
        Registry $registry,
        Context $context
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->wysiwygConfig = $wysiwygConfig;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);

        if ($this->registry->registry('current_category')) {
            $model = $this->registry->registry('current_category');
        } else {
            $model = $this->categoryFactory->create();
        }

        $content = $form->addFieldset('content_fieldset', ['legend' => __('Content')]);
        $content->addField('description', 'editor', [
            'label'   => __('Content'),
            'name'    => 'description',
            'value'   => $model->getDescription(),
            'wysiwyg' => true,
            'config'  => $this->wysiwygConfig->getConfig(),
            'style'   => 'height:35em',
        ]);

        $design = $form->addFieldset('design_fieldset', ['legend' => __('Design')]);

        $design->addField('display_mode', 'select', [
            'label'  => __('Display Mode'),
            'name'   => 'display_mode',
            'value'  => $model->getDisplayMode(),
            'values' => [
                'default'  => __('Default'),
                'extended' => __('Extended'),
            ],
        ]);

        $design->addField('custom_layout_update', 'textarea', [
            'label' => __('Layout Update XML'),
            'name'  => 'custom_layout_update',
            'value' => $model->getCustomLayoutUpdate(),
        ]);

        $form->setFieldNameSuffix('design');

        return parent::_prepareForm();
    }
}
