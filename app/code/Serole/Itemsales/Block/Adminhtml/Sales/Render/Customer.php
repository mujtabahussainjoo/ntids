<?php

namespace Serole\Itemsales\Block\Adminhtml\Sales\Render;

class Customer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

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
        /*if($row->getStoreId()){
            $storeDetails = $this->store->load($row->getStoreId());
            return $storeDetails->getName();
        }else{
            return "Admin";
        }*/
        print_r($row->getData());
    }

}
