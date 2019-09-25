<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Regular License.
 * You may not use any part of the code in whole or part in any other software
 * or product or website.
 *
 * @author		Infortis
 * @copyright	Copyright (c) 2014 Infortis
 * @license		Regular License http://themeforest.net/licenses/regular 
 */

namespace Infortis\Dataporter\Controller\Adminhtml\Cfgporter;

use SimpleXMLElement;
abstract class AbstractCfgporter extends \Magento\Backend\App\Action
{
    protected $modelConfigFactory;

    /**
     * @var \Infortis\Dataporter\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Infortis\Dataporter\Helper\Cfgporter\Data
     */
    protected $_cfgporterData;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logLoggerInterface;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $_viewLayoutFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_configScopeConfigInterface;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $_dirReader;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Infortis\Dataporter\Helper\Data $helperData, 
        \Infortis\Dataporter\Helper\Cfgporter\Data $cfgporterData, 
        \Psr\Log\LoggerInterface $logLoggerInterface, 
        \Magento\Framework\View\LayoutFactory $viewLayoutFactory, 
        \Magento\Framework\App\Config\ScopeConfigInterface $configScopeConfigInterface, 
        \Magento\Framework\Module\Dir\Reader $dirReader,
        \Magento\Config\Model\Config\Factory $modelConfigFactory=null)
    {        
        parent::__construct($context);
        $this->_helperData = $helperData;
        $this->_cfgporterData = $cfgporterData;
        $this->_logLoggerInterface = $logLoggerInterface;
        $this->_viewLayoutFactory = $viewLayoutFactory;
        $this->_configScopeConfigInterface = $configScopeConfigInterface;
        $this->_dirReader = $dirReader;
        $this->modelConfigFactory = $modelConfigFactory;
        $this->_construct();
    }

	protected $_helper;
	protected $_hc; /*OBFFROM: $_helperCfgporter */
	protected $_ep; /*OBFFROM: $_eventPrefix */

	/*OBF: protected $_helper; protected $_hc; protected $_ep; */

	/**
	 * Resource initialization
	 */
	protected function _construct()
	{
		$this->_helper 			= $this->_helperData;
		$this->_hc 				= $this->_cfgporterData;
		$this->_ep				= "dataporter_cfgporter";
	}

	protected function _isAllowed()
	{
		$result = $this->_authorization->isAllowed('Infortis_Dataporter::config');	
		return $result;
	}

	/**
	 * Get file to save config presets
	 *
	 * @return string
	 */
	protected function _getExportFile()
	{
		return $this->_hc->getPresetFilepath(
			$this->getRequest()->getParam("preset_name"),
			$this->getRequest()->getParam("package")
		);
	}

	/**
	 * Check if directory of the file path exists and is writable. If not, create writable directory.
	 *
	 * @param string
	 * @return bool
	 */
	protected function _createExportDir($filepath)
	{
		$mode = 0777;
		$dir = dirname($filepath);

		if (is_dir($dir))
		{
			//If directory not writable, change mode
			if (!is_writable($dir))
			{
				chmod($dir, $mode);
			}
		}
		else //Directory doesn't exist
		{
			//Create directory
			if (!mkdir($dir, $mode, true))
			{
				//Unable to create directory.
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * Get modules associated with package
	 *
	 * @return array
	 */
	protected function _getModulesToExport()
	{
		//Get (from the form) list of modules to export
		$retrievedModules = $this->getRequest()->getParam("modules");

		//Get rid of empty names
		$modules = [];
		if (!empty($retrievedModules))
		{
			foreach ($retrievedModules as $mod)
			{
				if (!empty($mod))
				{
					$modules[] = $mod;
				}
			}
		}
		return $modules;
	}

	/**
	 * Get file path with saved configuration.
	 * File can be uploaded via the form or retrieved from predefined folder of the package (main module in the package)
	 *
	 * @return string
	 */
	protected function _getImportFile()
	{
		//Desitnation directory for files uploaded via form
		$tmpFileBaseDir = $this->_helper->getTmpFileBaseDir();
		$uploadedFileName = '';
		$resultFile = '';

		if (!empty($_FILES["data_import_file"]["name"])) //File was selected in the form
		{
			///$this->_logLoggerInterface->debug('_getImportFile() -- data: ' . print_r($this->getRequest()->getPost(), 1), null, 'dataporter.txt'); ///
			///$this->_logLoggerInterface->debug('_getImportFile() -- _FILES: ' . print_r($_FILES, 1), null, 'dataporter.txt'); ///
			if (file_exists($_FILES["data_import_file"]["tmp_name"])) //File successfully uploaded to temporary directory
			{
				try
				{
					$uploader = new \Magento\Framework\File\Uploader("data_import_file");
					$uploader->setAllowedExtensions(['xml']);
					$uploader->setAllowCreateFolders(true);
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					
					$uploader->save($tmpFileBaseDir, $_FILES["data_import_file"]["name"]);
					$resultFile = $tmpFileBaseDir . $_FILES["data_import_file"]["name"];
				}
				catch(\Exception $e)
				{
					$this->_session->addError(
						__("An error occurred during upload of file %1", $_FILES["data_import_file"]["name"])
						. "<br/>" .
						__("Exception: %1", $e->getMessage())
					);
				} //end: try-catch
			}
		}
		else //File NOT selected in the form
		{
			$resultFile = $this->_hc->getPresetFilepath(
				$this->getRequest()->getParam("preset_name"),
				$this->getRequest()->getParam("package")
			);
		}

		if (!empty($resultFile))
		{
			return $resultFile;
		}
		else
		{
			return '';
		}
	}



	////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Helper methods
	////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Get control parameters
	 *
	 * @return array
	 */
	protected function _getControlParams()
	{
		$params = [];
		$params["action_type"] = $this->getRequest()->getParam("action_type");
		$params["package"] = $this->getRequest()->getParam("package");
		return $params;
	}

	/**
	 * Print and log debug info
	 *
	 * @return void
	 */
	protected function _debugInfo($here)
	{
		$data = print_r($this->getRequest()->getParams(), 1);
		$this->_logLoggerInterface->debug("Here: " . $here . " :\n" . $data, null, "dataporter.log");

		$output  = "<pre>" . $here . " :\n";
		$output .= $data;
		$output .= "</pre>";
		$block = $this->_viewLayoutFactory->create()->createBlock("core/text", "debug-data-print")->setText($output);
		$this->_addContent($block);
	}



	////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Import/Export data
	////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Export configuration (by node specified in $path param) from config.xml file of modules
	 * and save it in a file
	 *
	 * @param array
	 * @param string $path Node of configuration XML which contains settings to export
	 * @param string
	 * @param string
	 * @param bool
	 * @param bool
	 * @return void
	 */
	protected function _export($modules, $path, $store, $filepath, $flag_ifOptionNullReplaceWithDefaultConfig=TRUE, $flag_ifDefaultConfigNullOmit = FALSE)
	{
		///$this->_logLoggerInterface->debug('_export: ' . implode(':', $modules) .' + '. $path .' + '. $store .' + '. $filepath); ///

		//Get root node
		$rootNode = $this->_getModulesConfigDefaultValues($modules, $path);
		$nodesToRemove = [];

		foreach ($rootNode->children() as $section)
		{
			///$this->_logLoggerInterface->debug($section->getName());
			foreach ($section->children() as $group)
			{
				///$this->_logLoggerInterface->debug('-- ' . $group->getName());
				foreach ($group->children() as $option)
				{
					//IMPORTANT: omit this node if it has deeper levels
					if ($option->hasChildren())
					{
						///$this->_logLoggerInterface->debug('---- ---- omit, this node has children: ' . $option->getName() . ' ==> ...'); ///
						continue; //Omit this node
					}

					//$section->getName() . '/' . $group->getName() . '/' . $option->getName()
					$optionPath = $section->getName() . '/' . $group->getName() . '/' . $option->getName();
					$valueFromConfig = $this->_configScopeConfigInterface->getValue(
						$optionPath,
						\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
						$store
					);
					///$this->_logLoggerInterface->debug('---- ' . $option->getName() . ' ===>' . $valueFromConfig);

					//Check additional constraints ONLY if export is for store view and not for the Default Config
					//and if option value is NULL (after retrieving from db it's an empty string)
					if ($store > 0 && '' === $valueFromConfig)
					{
						if ($flag_ifOptionNullReplaceWithDefaultConfig)
						{
							//Get option value from Default Config
							$valueFromConfig_DEFAULT = $this->_configScopeConfigInterface->getValue(
								$optionPath,
								0
							);

							//If Default Config option value is not NULL, export it instead of option value
							$valueFromConfig = $valueFromConfig_DEFAULT;
							///$this->_logLoggerInterface->debug('     NULL, replace with Default=' . $valueFromConfig_DEFAULT); ///

							if ($flag_ifDefaultConfigNullOmit)
							{
								if ('' === $valueFromConfig_DEFAULT)
								{
									//If Default Config option value is NULL too, omit this node and don't export.
									//So the node has to be removed from the main XML object.
									///$this->_logLoggerInterface->debug('     value from Default also NULL. Remove this path: '.$optionPath); ///
									$nodesToRemove[] = $optionPath;
									continue;
								}
							}
						}
					}

					//Add exported value to the XML
					$group->{$option->getName()} = $valueFromConfig;

				} //end: foreach
			} //end: foreach
		} //end: foreach

		//Remove nodes selected to be removed
		foreach ($nodesToRemove as $nodePath)
		{
			$node = $rootNode->xpath($nodePath);
			unset($node[0][0]);
		}

		//Save
		$niceXml = $rootNode->asNiceXml();
		if (!file_put_contents($filepath, $niceXml))
		{
			throw new \Exception("Unable to write file.");
		}
	}

	/**
	 * Get and collect configuration (by node specified in $path param) of all specified modules
	 *
	 * @param array
	 * @param string
	 * @return \Magento\Framework\Simplexml\Element
	 */
	protected function _getModulesConfigDefaultValues($modules, $path)
	{
		$rootXml = simplexml_load_string("<defaul></defaul>", "Magento\Framework\Simplexml\Element");
		//$rootXml = new \Magento\Framework\Simplexml\Element("<defaul></defaul>");
		foreach ($modules as $module)
		{
			//Get config nodes matching the path.
			//Nodes are inside the main node which is named the same as path.
			$node = $this->_getModuleConfig($module, $path);
						
			if ($node && ($node instanceof SimpleXMLElement))
			{
				//Get children of the main node and append them to the container node
				foreach ($node->children() as $child)
				{
					$rootXml->appendChild($child);
				}
			}
		}

		return $rootXml;
	}

	/**
	 * Get configuration of selected module
	 *
	 * @param array
	 * @param string
	 * @return \Magento\Framework\Simplexml\Element
	 */
	protected function _getModuleConfig($module, $path)
	{	
		$configFile = $this->_dirReader->getModuleDir("etc", $module) . 
		    DIRECTORY_SEPARATOR . "config.xml";

		//If file (and module) exists
		if (file_exists($configFile))
		{
			$content = file_get_contents($configFile);

			//If file content successfully retrieved
			if ($content !== FALSE)
			{
				$xml = simplexml_load_string($content, "Magento\Framework\Simplexml\Element");

				//Get selected node
				$node = $xml->descend($path);
				return $node;
			}
		}

		return NULL;
	}

    protected function getConfigDataTemplateArray($scope, $scopeId)
    {
        $configDataTemplate = [
            'website'   => null,
            'store'     => null
        ];
        if($scope == 'stores')
        {
            $configDataTemplate['store'] = $scopeId;
        }            
        else if($scope == 'websites')
        {
            $configDataTemplate['website'] = $scopeId;        
        }
        return $configDataTemplate;
    }
    
	////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Import configuration from a file and save all config values in database for specified scope and store
	 *
	 * @param string
	 * @param int
	 * @param string
	 * @return void
	 */
	protected function _import($scope, $scopeId, $filepath)
	{
		//Get root node
		$rootNode = $this->_getXmlFromFile($filepath);
		if (!$rootNode)
		{
			throw new \Exception("Unable to read XML data from file - empty file or invalid format.");
		}
        
        $configDataTemplate = $this->getConfigDataTemplateArray($scope, $scopeId);

		///$this->_logLoggerInterface->debug('-- import scope: ' . $scope .' + '. $scopeId);
		foreach ($rootNode->children() as $section)
		{
		    $configData = $configDataTemplate;
		    $configData['section'] = $section->getName();
		    $configData['groups']   = [];

			///$this->_logLoggerInterface->debug($section->getName());
			foreach ($section->children() as $group)
			{
			    $configData['groups'][$group->getName()] = [
			        'fields'=>[]];
			        
                $configData['groups'][$group->getName()]['fields'];			        
				///$this->_logLoggerInterface->debug('-- ' . $group->getName());
				foreach ($group->children() as $option)
				{
					///$this->_logLoggerInterface->debug('-- import to: ' . $section->getName() . '/' . $group->getName() . '/' . $option->getName() . ' <==' . $option);

					//IMPORTANT: omit this node if it has deeper levels
					if ($option->hasChildren())
					{
						///$this->_logLoggerInterface->debug('---- omit, this node has children: ' . $option->getName() . ' ==> ...'); ///
						continue;
					}

					//If option value is NULL (after retrieving it's an empty string), then import NULL
					$optionValue = (string) $option;
					if ('' === $optionValue)
					{
						$optionValue = NULL;
					}
					$configData['groups'][$group->getName()]['fields'][$option->getName()] =
					    ['value'=>$optionValue];
				}
			}
			
            $configModel = $this->modelConfigFactory->create(['data' => $configData]);
            $configModel->save();			
		} //end: foreach
	}

	/**
	 * Get XML data from a file and load it as XML object
	 *
	 * @param string
	 * @return \Magento\Framework\Simplexml\Element
	 */
	protected function _getXmlFromFile($file)
	{
		$content = file_get_contents($file);
		return simplexml_load_string($content, "Magento\Framework\Simplexml\Element");
	}
}
