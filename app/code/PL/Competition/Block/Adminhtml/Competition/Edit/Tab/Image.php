<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 3:54 PM
 */
namespace PL\Competition\Block\Adminhtml\Competition\Edit\Tab;

class Image extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected function _prepareForm()
    {


        $model = $this->_coreRegistry->registry('_current_competition');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        //$form->setHtmlIdPrefix('salenotice_');
        $form->setFieldNameSuffix('competition');

        $fieldset = $form->addFieldset(
            'images_fieldset',
            ['legend' => __('Competition Images')]
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
            'logo',
            'image',
            [
                'name'        => 'logo',
                'label'    => __('Logo'),
                'required'     => false
            ]
        );
        $fieldset->addField(
            'image',
            'image',
            [
                'name'        => 'image',
                'label'    => __('Base Image'),
                'required'     => false
            ]
        );
        $fieldset->addField(
            'thumb_1',
            'image',
            [
                'name'        => 'thumb_1',
                'label'    => __('Thumbnail 1'),
                'required'     => false
            ]
        );
        $fieldset->addField(
            'thumb_2',
            'image',
            [
                'name'        => 'thumb_2',
                'label'    => __('Thumbnail 2'),
                'required'     => false
            ]
        );
        $fieldset->addField(
            'thumb_3',
            'image',
            [
                'name'        => 'thumb_3',
                'label'    => __('Thumbnail 3'),
                'required'     => false
            ]
        );
        $fieldset->addField(
            'thumb_4',
            'image',
            [
                'name'        => 'thumb_4',
                'label'    => __('Thumbnail 4'),
                'required'     => false
            ]
        );
        $fieldset->addField(
            'thumb_5',
            'image',
            [
                'name'        => 'thumb_5',
                'label'    => __('Thumbnail 5'),
                'required'     => false
            ]
        );

        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        parent::_prepareForm();

    }


    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Competition Images');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Competition Images');
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