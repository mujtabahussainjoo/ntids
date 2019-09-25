<?php

namespace Infortis\Base\Controller\Adminhtml\Cmsimport;

abstract class AbstractCmsimport extends \Magento\Backend\App\Action
{
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        return parent::__construct($context);
    }
    
    protected function _isAllowed()
    {
        return $this
            ->_authorization
            ->isAllowed('Infortis_Base::import_export_page_block');
    }   
}
