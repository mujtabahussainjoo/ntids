<?php

namespace Nwdthemes\Revslider\Setup;

use \Magento\Framework\Setup\InstallDataInterface;
use \Magento\Framework\Setup\ModuleDataSetupInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Nwdthemes\Revslider\Model\CssFactory;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderPluginUpdate;

class InstallData implements InstallDataInterface {

    protected $_cssFactory;

    public function __construct(
        CssFactory $cssFactory
    ) {
        $this->_cssFactory = $cssFactory;
    }
        
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $cssModel = $this->_cssFactory->create();
        foreach (RevSliderPluginUpdate::get_v5_styles() as $css) {
            $cssModel->setData($css)->save();
        }
    }

}
