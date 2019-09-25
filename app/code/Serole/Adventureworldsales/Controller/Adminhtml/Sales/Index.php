<?php

namespace Serole\Adventureworldsales\Controller\Adminhtml\Sales;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPagee;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        //echo 'Jeee'; exit;
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Serole_Adventureworldsales::adventureworldsales');
        $resultPage->addBreadcrumb(__('Serole'), __('Serole'));
        $resultPage->addBreadcrumb(__('Manage item'), __('Adventure World Sales'));
        $resultPage->getConfig()->getTitle()->prepend(__('Adventure World Sales'));
        return $resultPage;
    }
}
?>