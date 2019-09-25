<?php

namespace Infortis\Dataporter\Controller\Adminhtml\Cfgporter\Index;
use Infortis\Dataporter\Controller\Adminhtml\Cfgporter\AbstractCfgporter;
use Infortis\Dataporter\Helper\Cfgporter\Data as CfgporterData;
use Infortis\Dataporter\Helper\Data as HelperData;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\View\LayoutFactory;
use Psr\Log\LoggerInterface;
class Export extends AbstractCfgporter
{

    protected $resultPageFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,            
        HelperData $helperData, 
        CfgporterData $cfgporterData, 
        LoggerInterface $logLoggerInterface, 
        LayoutFactory $viewLayoutFactory, 
        ScopeConfigInterface $configScopeConfigInterface, 
        Reader $dirReader,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory        
        )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $helperData, $cfgporterData, $logLoggerInterface, $viewLayoutFactory, $configScopeConfigInterface, $dirReader);
    }

	/**
	 * View form action
	 *
	 * @return void
	 */
	public function execute()
	{
		//Check requested action type
		$actionType = $this->getRequest()->getParam("action_type");
		if ($actionType === "export")
		{
			$block = $this->_viewLayoutFactory->create()->createBlock('Infortis\Dataporter\Block\Adminhtml\Cfgporter\Export\Edit');
		}
		elseif ($actionType === "import")
		{
			$block = $this->_viewLayoutFactory->create()->createBlock('Infortis\Dataporter\Block\Adminhtml\Cfgporter\Import\Edit');
		}
		elseif ($actionType === NULL)
		{
			$this->getResponse()->setRedirect($this->getUrl("adminhtml/dashboard"));
			return;
		}

        return $this->resultPageFactory->create();
        return $this->context->create();
// 		$this->loadLayout();
// 		$this->_setActiveMenu('infortis');
// 		$this->_addBreadcrumb($this->_helper->__("Config Import and Export"), $this->_helper->__("Config Import and Export"));
// 
// 		///$this->_debugInfo(__FUNCTION__);
// 		$this->_addContent($block);
// 
// 		$this->renderLayout();
	}
}
