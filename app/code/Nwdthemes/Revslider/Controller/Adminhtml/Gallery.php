<?php

namespace Nwdthemes\Revslider\Controller\Adminhtml;

abstract class Gallery extends \Magento\Cms\Controller\Adminhtml\Wysiwyg\Images {

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Nwdthemes_Revslider::overview';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context, $coreRegistry);
    }

    public function getStorage() {
        if (!$this->_coreRegistry->registry('nwdthemes_revslider_gallery_storage')) {
            $storage = $this->_objectManager->create('\Nwdthemes\Revslider\Model\Gallery\Storage');
            $this->_coreRegistry->register('nwdthemes_revslider_gallery_storage', $storage);
        }
        return $this->_coreRegistry->registry('nwdthemes_revslider_gallery_storage');
    }

}
