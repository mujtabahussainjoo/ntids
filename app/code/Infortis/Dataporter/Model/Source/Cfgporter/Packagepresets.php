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

class Packagepresets
{
    /**
     * @var CfgporterData
     */
    protected $_cfgporterData;

    public function __construct(CfgporterData $cfgporterData)
    {
        $this->_cfgporterData = $cfgporterData;

    }

	protected $_options;

	public function toOptionArray($package = NULL)
	{
		if (!$this->_options)
		{
			$this->_options = [];
			$this->_options[] = ['value' => '', 'label' => __('-- Please Select --')]; //First option is empty

			$dir = $this->_cfgporterData->getPresetDir($package);
			if (is_dir($dir))
			{
				$files = scandir($dir);
				foreach ($files as $file)
				{
					if (!is_dir($dir . $file))
					{
						$path = pathinfo($file);
						$this->_options[] = ['value' => $path['filename'], 'label' => $path['filename']];
					}
				}
			}

			//Last option
			$this->_options[] = ['value' => 'upload_custom_file', 'label' => __('Upload custom file...')];
		}
		return $this->_options;
	}
}
