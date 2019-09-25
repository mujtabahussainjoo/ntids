<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 11:21 AM
 */
namespace PL\Competition\Controller\Adminhtml\Competition;

class Index extends \PL\Competition\Controller\Adminhtml\Competition
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {


        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('PL_Competition::competition');
        $resultPage->getConfig()->getTitle()->prepend(__('Competition'));

        return $resultPage;
    }


}