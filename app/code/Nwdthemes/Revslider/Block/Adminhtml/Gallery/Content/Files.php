<?php

namespace Nwdthemes\Revslider\Block\Adminhtml\Gallery\Content;

class Files extends \Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content\Files {

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Nwdthemes\Revslider\Model\Gallery\Storage $imageStorage,
        \Nwdthemes\Revslider\Helper\Gallery\Images $imageHelper,
        array $data = []
    ) {
        parent::__construct($context, $imageStorage, $imageHelper, $data);
    }

}
