<?php

namespace Wizkunde\WebSSO\Controller\Adminhtml;

use Magento\Framework\Registry;

abstract class Log extends \Magento\Backend\App\AbstractAction
{
    private $resultPageFactory;
    private $coreRegistry = null;

    protected $logFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Wizkunde\WebSSO\Model\LogFactory $logFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $pageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->logFactory = $logFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function getResultPageFactory()
    {
        return $this->resultPageFactory;
    }

    /**
     * @return Registry|null
     */
    public function getCoreRegistry()
    {
        return $this->coreRegistry;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Wizkunde_WebSSO::server');
    }
}
