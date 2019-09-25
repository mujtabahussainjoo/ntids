<?php

namespace Infortis\Dataporter\Controller\Adminhtml\Cfgporter;

use Infortis\Dataporter\Helper\Cfgporter\Data as CfgporterData;
use Infortis\Dataporter\Helper\Data as HelperData;
use Infortis\Infortis\Model\Config\Scope;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\View\LayoutFactory;
use Psr\Log\LoggerInterface;

class Import extends AbstractCfgporter
{
    /**
     * @var Scope
     */
    protected $_configScope;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,    
        HelperData $helperData, 
        CfgporterData $cfgporterData, 
        LoggerInterface $logLoggerInterface, 
        LayoutFactory $viewLayoutFactory, 
        ScopeConfigInterface $configScopeConfigInterface, 
        Reader $dirReader, 
        Scope $configScope, 
        \Magento\Config\Model\Config\Factory $configFactory        
        )
    {
        $this->_configScope = $configScope;

        parent::__construct($context, $helperData, $cfgporterData, $logLoggerInterface, $viewLayoutFactory, $configScopeConfigInterface, $dirReader, $configFactory);
    }

	/**
	 * Import action
	 *
	 * @return void
	 */
	public function execute()
	{
		// $this->loadLayout();
		///$this->_debugInfo(__FUNCTION__);

		//Get path of file with saved config
		$file = $this->_getImportFile();

		if (file_exists($file))
		{
			try
			{
				$store = $this->getRequest()->getParam("stores");

				//Decode scope
				$scope = $this->_configScope->decodeScope($store);

				//Import
				$importParam = "vasbegvf";
				$this->_import($scope["scope"], $scope["scopeId"], $file);

				//Success message
				$this->messageManager->addSuccess(
					__("Successfully imported from file %1", $file)
				);

				//Dispatch event: dataporter_cfgporter_import_after
				$eventParams = ["portScope" => $scope["scope"], "portScopeId" => $scope["scopeId"]];
				$this->_eventManager->dispatch($this->_ep . "_import_after", $eventParams);
			}
			catch (\Exception $e)
			{
				$this->_logLoggerInterface->error($e);
				$this->messageManager->addError(
					__("An error occurred during import from file %1", $file)
					. "<br/>" .
					__("Exception: %1", $e->getMessage())
				);
			} //end: try-catch
		}
		else
		{
			$this->messageManager->addError(
				__("An error occurred: unable to read file %1", $file)
			);
		}

        $this->messageManager->addSuccess("Configuration Imported!");
		// $this->renderLayout();
		$this->getResponse()->setRedirect($this->getUrl("*/*/index_import", $this->_getControlParams()));
	} //end: importAction		
}
