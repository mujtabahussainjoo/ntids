<?php

namespace Infortis\Base\Controller\Adminhtml\Cmsimport;

use Infortis\Base\Helper\Data as HelperData;
use Infortis\Base\Model\Import\Cms;

class Pages extends AbstractCmsimport
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var Cms
     */
    protected $_importCms;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,    
        HelperData $helperData, 
        Cms $importCms
    ) {
        $this->_helperData = $helperData;
        $this->_importCms = $importCms;
        parent::__construct($context);
    }

    public function execute()
    {
        $package = $this->getRequest()->getParam('package');
        
        $demoNumber = $this->_helperData->getCfg('install/demo_number');
        $overwrite = $this->_helperData->getCfg('install/overwrite_pages');
        $this->_importCms->importCmsItems('cms/page', 'page', $demoNumber, $overwrite, $package);
        
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/theme_settings/"));
    }
}
