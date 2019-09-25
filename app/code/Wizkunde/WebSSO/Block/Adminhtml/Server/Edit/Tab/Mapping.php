<?php

namespace Wizkunde\WebSSO\Block\Adminhtml\Server\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

class Mapping extends Generic
{
    protected $_template = 'Wizkunde_WebSSO::mapping.phtml';

    /**
     * Retrieve template object
     *
     * @return \Magento\Newsletter\Model\Template
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('_sso_server');
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form_mapping', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setFieldContainerIdPrefix('mapping_');
        $form->addFieldNameSuffix('mapping');

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
