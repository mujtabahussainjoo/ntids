<?php

namespace Infortis\Dataporter\Controller\Adminhtml\Cfgporter;

use Infortis\Dataporter\Helper\Cfgporter\Data as CfgporterData;
use Infortis\Dataporter\Helper\Data as HelperData;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\View\LayoutFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultFactory;
class Export extends AbstractCfgporter
{

    public function __construct(
        \Magento\Backend\App\Action\Context $context,    
        HelperData $helperData, 
        CfgporterData $cfgporterData, 
        LoggerInterface $logLoggerInterface, 
        LayoutFactory $viewLayoutFactory, 
        ScopeConfigInterface $configScopeConfigInterface, 
        Reader $dirReader)
    {


        parent::__construct($context, $helperData, $cfgporterData, $logLoggerInterface, $viewLayoutFactory, $configScopeConfigInterface, $dirReader);
    }

	/**
	 * Export action
	 *
	 * @return void
	 */
	public function execute()
	{

		//Get list of modules to export
		$modules = $this->_getModulesToExport();
		if (!empty($modules))
		{
			try
			{
				//Get file to save config presets
				$file = $this->_getExportFile();
				$this->_createExportDir($file);

				//Important: for export the scope should be provided as store id - single value, not an array
				$store = $this->getRequest()->getParam("stores");
				if (is_array($store))
				{
					throw new \Exception("Website/Store ID retrieved as array. Expected string.");
				}

				$this->_export($modules, "default", $store, $file);

				$this->messageManager->addSuccess(
					__("Successfully exported to file %1", $file)
				);
			}
			catch (\Exception $e)
			{
			    $this->messageManager->addError(
					__("An error occurred during export to file %1", $file)
					. "<br/>" .
					__("Exception: %1", $e->getMessage())			    
			    );
			    
				$this->_logLoggerInterface->error($e);
			} //end: try-catch
		}
		else
		{
			$this->_session->addError(
				__("An error occurred: no source module selected for export.")
			);
		}		

		return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
		->setPath("adminhtml/cfgporter/index_export", $this->_getControlParams());
	}
}
