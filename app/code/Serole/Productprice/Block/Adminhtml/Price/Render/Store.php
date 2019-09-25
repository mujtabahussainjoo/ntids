<?php

namespace Serole\Productprice\Block\Adminhtml\Price\Render;

class Store extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    protected $backendSession;

    protected $store;

    public function __construct(\Magento\Backend\Block\Context $context,
                                \Magento\Backend\Model\Session $backendSession,
                                \Magento\Store\Model\Store $store,
                                array $data = []) {
        $this->backendSession = $backendSession;
        $this->store = $store;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $storeIdentifySessionVal = $this->backendSession->getStoreidfilter();
        if($storeIdentifySessionVal){
            $storeDetails = $this->store->load($storeIdentifySessionVal);
            return $storeDetails->getName();
        }else{
            return "Admin";
        }
    }

}
