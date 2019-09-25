<?php

namespace Nwdthemes\Revslider\Controller\Adminhtml\Revslider;

class Addonajax extends \Nwdthemes\Revslider\Controller\Adminhtml\Revslider {

    protected $_resultLayoutFactory;

    /**
     * Constructor
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        $this->_resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context);
    }

    /**
     * Ajax action
     */

    public function execute() {
        $resultLayout = $this->_resultLayoutFactory->create();
        return $resultLayout;
    }

}
