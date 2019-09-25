<?php

// @codingStandardsIgnoreFile

namespace Wizkunde\WebSSO\Block\Adminhtml\Log;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Wizkunde_WebSSO';
        $this->_controller = 'Adminhtml_Log';

        $this->removeButton("save");
        $this->removeButton("delete");
        $this->removeButton("reset");

        parent::_construct();
    }

    /**
     * Retrieve server object
     *
     * @return \Wizkunde\WebSSO\Model\Log
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('_sso_log');
    }

    /**
     * Return header text for form
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Audit Log');
    }
}