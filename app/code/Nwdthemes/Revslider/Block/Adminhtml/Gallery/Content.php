<?php

namespace Nwdthemes\Revslider\Block\Adminhtml\Gallery;

class Content extends \Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content {

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $jsonEncoder, $data);
    }

    public function getContentsUrl() {
        return $this->getUrl('nwdthemes_revslider/*/contents', ['type' => $this->getRequest()->getParam('type')]);
    }

    public function getNewfolderUrl() {
        return $this->getUrl('nwdthemes_revslider/*/newFolder');
    }

    protected function getDeletefolderUrl() {
        return $this->getUrl('nwdthemes_revslider/*/deleteFolder');
    }

    public function getDeleteFilesUrl() {
        return $this->getUrl('nwdthemes_revslider/*/deleteFiles');
    }

    public function getOnInsertUrl() {
        return $this->getUrl('nwdthemes_revslider/*/onInsert');
    }

}
