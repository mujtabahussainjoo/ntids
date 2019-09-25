<?php

namespace Infortis\Base\Controller\Adminhtml\Cmsimport;

use Infortis\Base\Helper\Data as HelperData;
use Infortis\Base\Model\Import\Cms;

class Exppages extends AbstractCmsimport
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

        $storeId = null;
        $paramStore = $this->getRequest()->getParam('s');
        if ($paramStore !== null)
        {
            $storeId = $paramStore;
        }

        $withDefaultStore = true;
        $paramWithdefaultstore = $this->getRequest()->getParam('withdefaultstore');
        if ($paramWithdefaultstore !== null)
        {
            $withDefaultStore = $paramWithdefaultstore;
        }

        $this->_importCms->exportCmsItems('cms/page', 'page', $storeId, $withDefaultStore, $package);

        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/theme_settings/"));
    }
}
