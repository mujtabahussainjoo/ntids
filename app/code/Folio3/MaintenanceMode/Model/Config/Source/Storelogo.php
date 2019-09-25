<?php

namespace Folio3\MaintenanceMode\Model\Config\Source;

class Storelogo implements \Magento\Framework\Option\ArrayInterface {

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
            $storeLogo = $this->getStoreLogo();

            if ($storeLogo) {
                $this->_options[] = array('value' => 'store', 'label' => $storeLogo);
            }
            $this->_options[] = array('value' => 'custom', 'label' => 'Custom');

            return $this->_options;
        }
    }

    protected function getStoreLogo() {
        return $this->_scopeConfig->getValue(
                'design/header/logo_src', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
