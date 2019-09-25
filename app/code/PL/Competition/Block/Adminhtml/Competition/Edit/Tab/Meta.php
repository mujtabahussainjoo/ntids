<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 12:34 PM
 */

namespace PL\Competition\Block\Adminhtml\Competition\Edit\Tab;

class Meta extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface{


    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('_current_competition');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        //$form->setHtmlIdPrefix('salenotice_');
        $form->setFieldNameSuffix('competition');

        $fieldset = $form->addFieldset(
            'meta_fieldset',
            ['legend' => __('Meta Information')]
        );
/*
        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'entity_id']
            );
        }
*/
        $fieldset->addField(
            'meta_title',
            'text',
            [
                'name'        => 'meta_title',
                'label'    => __('Meta Title'),
                'required'     => false
            ]
        );
        $fieldset->addField(
            'meta_keywords',
            'textarea',
            [
                'name'        => 'meta_keywords',
                'label'    => __('Meta Keywords'),
                'required'     => false
            ]
        );

        $fieldset->addField(
            'meta_description',
            'textarea',
            [
                'name'      => 'meta_description',
                'label'     => __('Meta Description'),
                'required'  => false
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
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Meta Data');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Meta Data');
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