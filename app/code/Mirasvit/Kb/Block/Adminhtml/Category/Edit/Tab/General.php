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

class General extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Mirasvit\Kb\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

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
     * @param \Mirasvit\Kb\Model\CategoryFactory $categoryFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Mirasvit\Kb\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->systemStore = $systemStore;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = $this->formFactory->create();

        /** @var \Mirasvit\Kb\Model\Category $category */
        if ($this->registry->registry('current_category')) {
            $category = $this->registry->registry('current_category');
        } else {
            $category = $this->categoryFactory->create();
        }

        $fieldset = $form->addFieldset('edit_fieldset', [
            'legend' => __('General Information'),
            'class'  => 'field-category_ids',
        ]);
        if (!$category->getId()) {
            // path
            if ($this->getRequest()->getParam('parent')) {
                $fieldset->addField(
                    'path',
                    'hidden',
                    ['name' => 'path', 'value' => $this->getRequest()->getParam('parent')]
                );
            } else {
                $fieldset->addField('path', 'hidden', ['name' => 'path', 'value' => 1]);
            }
        } else {
            $fieldset->addField('id', 'hidden', ['name' => 'id', 'value' => $category->getId()]);
            $fieldset->addField(
                'path',
                'hidden',
                ['name' => 'path', 'value' => $category->getPath()]
            );
        }

        $fieldset->addField('name', 'text', [
            'label'    => __('Title'),
            'required' => true,
            'name'     => 'name',
            'value'    => $category->getName(false),
        ]);

        if ($category->getId() != 1) {
            if (
                !$category->isRootCategory() &&
                !(!$category->getId() && (int)$this->getRequest()->getParam('parent') < 2)
            ) {
                $fieldset->addField('url_key', 'text', [
                    'label' => __('URL Key'),
                    'name' => 'url_key',
                    'value' => $category->getUrlKey(),
                ]);
            }

            $fieldset->addField('is_active', 'select', [
                'label'  => __('Is Active'),
                'name'   => 'is_active',
                'value'  => $category->getId() ? $category->getIsActive() : true,
                'values' => [1 => __('Yes'), 0 => __('No')],
            ]);
        }

        if ((!$category->getId() && $this->getRequest()->getParam('parent') < 2) || $category->isRootCategory()) {
            if (!$this->context->getStoreManager()->isSingleStoreMode()) {
                $fieldset->addField('store_id', 'multiselect', [
                    'name'     => 'store_ids[]',
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $this->systemStore->getStoreValuesForForm(false, true),
                    'value'    => $category->getStoreIds(),
                ]);
            } else {
                $fieldset->addField('store_id', 'hidden', [
                    'name'  => 'store_ids',
                    'value' => $this->context->getStoreManager()->getStore()->getId(),
                ]);
                $category->setStoreId($this->context->getStoreManager()->getStore()->getId());
            }
        }

        $form->setFieldNameSuffix('general');
        $this->setForm($form);
    }
}
