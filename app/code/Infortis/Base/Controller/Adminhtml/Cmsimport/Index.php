<?php

namespace Infortis\Base\Controller\Adminhtml\Cmsimport;

class Index extends AbstractCmsimport
{
    public function execute()
    {
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/theme_settings/"));
    }
}
