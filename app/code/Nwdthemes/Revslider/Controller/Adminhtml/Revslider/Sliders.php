<?php

namespace Nwdthemes\Revslider\Controller\Adminhtml\Revslider;

class Sliders extends \Nwdthemes\Revslider\Controller\Adminhtml\Revslider {

    protected $_resultPageFactory;

    /**
     * Constructor
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Nwdthemes\Revslider\Helper\Framework $frameworkHelper
    ) {
        $this->_resultPageFactory = $resultPageFactory;

        parent::__construct($context);

        $frameworkHelper->add_action('plugins_loaded', array( '\Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderPluginUpdate', 'do_update_checks' ));
    }

    /**
     * Sliders Overview
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */

    public function execute() {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Nwdthemes_Revslider::overview');
        $resultPage->getConfig()->getTitle()->prepend(__('Slider Overview'));
        $resultPage->addBreadcrumb(__('Nwdthemes'), __('Nwdthemes'));
        $resultPage->addBreadcrumb(__('Slider Revolution'), __('Slider Revolution'));
        $resultPage->addBreadcrumb(__('Slider Overview'), __('Slider Overview'));
        return $resultPage;
    }

}
