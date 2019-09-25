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

class Seo extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Mirasvit\Kb\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Kb\Model\CategoryFactory    $categoryFactory
     * @param \Magento\Framework\Data\FormFactory   $formFactory
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Mirasvit\Kb\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
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

        $fieldset = $form->addFieldset('seo_fieldset', ['legend' => __('Meta Information')]);
        if ($model->getId()) {
            $fieldset->addField('category_id', 'hidden', [
                'name' => 'category_id',
                'value' => $model->getId(),
            ]);
        }
        $fieldset->addField('meta_title', 'text', [
            'label' => __('Meta Title'),
            'name' => 'meta_title',
            'value' => $model->getMetaTitle(),

        ]);
        $fieldset->addField('meta_keywords', 'textarea', [
            'label' => __('Meta Keywords'),
            'name' => 'meta_keywords',
            'value' => $model->getMetaKeywords(),

        ]);
        $fieldset->addField('meta_description', 'textarea', [
            'label' => __('Meta Description'),
            'name' => 'meta_description',
            'value' => $model->getMetaDescription(),

        ]);

        $form->setFieldNameSuffix('seo');

        return parent::_prepareForm();
    }
}
