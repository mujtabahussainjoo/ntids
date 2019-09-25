<?php

namespace Serole\MemberList\Block\Adminhtml;

class Memberlist extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    protected $_template = 'memberlist/memberlist.phtml';

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Widget\Context $context,array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {

		
        $addButtonProps = [
            'id' => 'add_new',
            'label' => __('Add New'),
            'class' => 'add primary',
            'button_class' => '',
            'class_name' => 'Magento\Backend\Block\Widget\Button',
            'onclick' => "setLocation('" . $this->_getCreateUrl() . "')",
        ];
		
        $this->buttonList->add('add_new', $addButtonProps);
		
		$addUploadButtonProps = [
            'id' => 'upload_csv',
            'label' => __('Upload CSV'),
            'class' => 'add primary',
            'button_class' => '',
            'class_name' => 'Magento\Backend\Block\Widget\Button',
			'onclick' => "setLocation('" . $this->_getUploadUrl() . "')",
        ];
        $this->buttonList->add('upload_csv', $addUploadButtonProps);
		

        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Serole\MemberList\Block\Adminhtml\Memberlist\Grid', 'serole.memberlist.grid')
        );
        return parent::_prepareLayout();
    }

    /**
     *
     *
     * @return array
     */
    protected function _getAddButtonOptions()
    {

        $splitButtonOptions[] = [
            'label' => __('Add New'),
            'onclick' => "setLocation('" . $this->_getCreateUrl() . "')"
        ];

        return $splitButtonOptions;
    }
	
	/**
     *
     *
     * @return array
     */
    protected function _getUploadButtonOptions()
    {

        $splitButtonOptions[] = [
            'label' => __('Upload'),
            'onclick' => "setLocation('" . $this->_getUploadUrl() . "')"
        ];

        return $splitButtonOptions;
    }
	
	 /**
     *
     *
     * @param string $type
     * @return string
     */
    protected function _getUploadUrl()
    {
        return $this->getUrl(
            'memberlist/index'
        );
    }

    /**
     *
     *
     * @param string $type
     * @return string
     */
    protected function _getCreateUrl()
    {
        return $this->getUrl(
            'memberlist/*/new'
        );
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

}