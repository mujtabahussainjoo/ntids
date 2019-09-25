<?php

namespace Folio3\MaintenanceMode\Model\Config\Source;

class Store implements \Magento\Framework\Option\ArrayInterface {

    protected $_options;

    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->_scopeConfig = $config;
    }

    public function toOptionArray() {
        if (!$this->_options) {
            $storeName = $this->getStoreName();

            if ($storeName) {
                $this->_options[] = array('value' => 'store', 'label' => $storeName);
            }
            $this->_options[] = array('value' => 'custom', 'label' => 'Custom');

            return $this->_options;
        }
    }

    protected function getStoreName() {
        return $this->_scopeConfig->getValue(
                'general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
