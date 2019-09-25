<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Block\Adminhtml\Role\Tab;

class Categories extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const MODE_ALL = 0;
    const MODE_SELECTED = 1;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('Amasty_Rolepermissions::form/categories.phtml');
    }

    /**
     * Get tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Advanced: Categories');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        /** @var \Amasty\Rolepermissions\Model\Rule $model */
        $model = $this->_coreRegistry->registry('amrolepermissions_current_rule');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('amrolepermissions_categories_fieldset', ['legend' => __('Category Access')]);

        $mode = $fieldset->addField('category_access_mode', 'select', [
            'label'  => __('Allow Access To'),
            'id'     => 'amrolepermissions[category_access_mode]',
            'name'   => 'amrolepermissions[category_access_mode]',
            'values' => [
                __('All Categories'),
                __('Selected Categories')
            ]
        ]);

        $tree = $fieldset->addField(
            'categories',
            'hidden',
            [
                'id'   => 'amrolepermissions[categories]',
                'name' => 'amrolepermissions[categories]',
            ]
        );

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )
            ->addFieldMap($mode->getHtmlId(), $mode->getName())
            ->addFieldMap($tree->getHtmlId(), $tree->getName())
            ->addFieldDependence(
                $tree->getName(),
                $mode->getName(),
                1
            )
        );

        $form->addValues($model->getData());
        if (is_array($model->getCategories())) {
            $tree->setValue(implode(',', $model->getCategories()));
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTreeBlock()
    {
        /** @var \Amasty\Rolepermissions\Model\Rule $model */
        $model = $this->_coreRegistry->registry('amrolepermissions_current_rule');

        $categories = $model->getCategories() ?: [];

        $block = $this->getLayout()->createBlock(
            'Magento\Catalog\Block\Adminhtml\Category\Checkboxes\Tree',
            'amrolepermissions_widget_chooser_category_ids',
            ['data' => ['js_form_object' => 'amrolepermissions_js_object']]
        )->setCategoryIds($categories);

        return $block;
    }
}
