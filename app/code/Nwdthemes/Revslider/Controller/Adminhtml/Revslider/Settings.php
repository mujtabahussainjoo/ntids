<?php

namespace Nwdthemes\Revslider\Controller\Adminhtml\Revslider;

class Settings extends \Nwdthemes\Revslider\Controller\Adminhtml\Revslider {

    protected $_resultPageFactory;

    /**
     * Constructor
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderLoadBalancer $revSliderLoadBalancer
    ) {
        $this->_resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * Global Settings
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */

    public function execute() {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Nwdthemes_Revslider::settings');
        $resultPage->getConfig()->getTitle()->prepend(__('Global Settings'));
        $resultPage->addBreadcrumb(__('Nwdthemes'), __('Nwdthemes'));
        $resultPage->addBreadcrumb(__('Slider Revolution'), __('Slider Revolution'));
        $resultPage->addBreadcrumb(__('Global Settings'), __('Global Settings'));
        return $resultPage;
    }

}
