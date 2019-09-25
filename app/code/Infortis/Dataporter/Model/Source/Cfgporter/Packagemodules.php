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

namespace Infortis\Dataporter\Model\Source\Cfgporter;

use Infortis\Dataporter\Helper\Cfgporter\Data as CfgporterData;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Packagemodules
{
    /**
     * @var CfgporterData
     */
    protected $_cfgporterData;

    /**
     * @var ScopeConfigInterface
     */
    protected $_configScopeConfigInterface;

    public function __construct(CfgporterData $cfgporterData, 
        ScopeConfigInterface $configScopeConfigInterface)
    {
        $this->_cfgporterData = $cfgporterData;
        $this->_configScopeConfigInterface = $configScopeConfigInterface;

    }

	protected $_options;

	public function toOptionArray($package = NULL)
	{
		if (!$this->_options)
		{
			$this->_options = [];
			$this->_options[] = ['value' => '', 'label' => __('-- Please Select --')]; //First option is empty

			if (NULL !== $package)
			{
				$h = $this->_cfgporterData;
				$modules = $h->getPackageModules($package);
				if ($modules)
				{
					$moduleNames = $h->getModuleNames();
					foreach ($modules as $mod)
					{
						$this->_options[] = ['value' => $mod, 'label' => $moduleNames[$mod]];
					}
				}
			}
			else
			{
				//TODO: fix. If $package is NULL, this line throws an error:
				//Call to a member function children() on a non-object.
				$modulesFromConfig = (array) $this->_configScopeConfigInterface->getValue('modules', 'default')->children();
				$modules = array_keys($modulesFromConfig);
				sort($modules);
				foreach ($modules as $mod)
				{
					$this->_options[] = ['value' => $mod, 'label' => $mod];
				}
			}
		}
		return $this->_options;
	}
}
