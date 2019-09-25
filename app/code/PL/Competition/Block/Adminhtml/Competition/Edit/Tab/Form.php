<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 12:18 PM
 */

namespace PL\Competition\Block\Adminhtml\Competition\Edit\Tab;


class Form extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface{

    protected $_helper;

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



    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('_current_competition');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        //$form->setHtmlIdPrefix('salenotice_');
        $form->setFieldNameSuffix('competition');



        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                ['name' => 'entity_id']
            );
        }
        $fieldset->addField(
            'title',
            'text',
            [
                'name'        => 'title',
                'label'    => __('Title'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'url_key',
            'text',
            [
                'name'      => 'url_key',
                'label'     => __('Url Key'),
                'class' => 'validate-identifier',
                'required'  => false
            ]
        );
        $fieldset->addField(
            'number_of_winners',
            'text',
            [
                'name'      => 'number_of_winners',
                'label'     => __('Number of Winners'),
                'required'  => true,
                'class' => 'required-entry validate-number validate-greater-than-zero'
            ]
        );
        $fieldset->addField(
            'date_from',
            'date',
            [
                'name'      => 'date_from',
                'label'     => __('From Date'),
                'title'     => __('From Date'),
                'required'  => true,
                'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT)
            ]
        );
        $fieldset->addField(
            'date_to',
            'date',
            [
                'name'      => 'date_to',
                'label'     => __('To Date'),
                'title'     => __('To Date'),
                'required'  => true,
                'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT)
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true)
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'short_description',
            'editor',
            [
                'name' => 'short_description',
                'label' => __('Short Description'),
                'title' => __('Short Description'),
                'wysiwyg' => true,
                'required' => false,
            ]
        );
        $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'wysiwyg' => true,
                'required' => false,
            ]
        );

        $fieldset->addField(
            'disclaimer',
            'editor',
            [
                'name' => 'disclaimer',
                'label' => __('Disclaimer'),
                'title' => __('Disclaimer'),
                'wysiwyg' => true,
                'required' => false,
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'options' => \PL\Competition\Model\Status::getAvailableStatuses(),
                'disabled' => false,
            ]
        );




        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Info');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }


}