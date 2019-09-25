<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\GeoIP\Block\Adminhtml\System\Config;

/**
 * GeoIP Update system block to display custom field in module settings
 */
class Update extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \MageWorx\GeoIP\Helper\Database
     */
    protected $helperDatabase;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \MageWorx\GeoIP\Helper\Database $helperDatabase
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \MageWorx\GeoIP\Helper\Database $helperDatabase,
        array $data = []
    ) {
        $this->helperDatabase = $helperDatabase;
        parent::__construct($context, $data);
    }
    
    /**
     * Set template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('MageWorx_GeoIP::update-database.phtml');
    }
    
    /**
     * Adds update button to config field
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
        
    /**
     * Return update button html
     *
     * @param string $sku
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'database_update',
                'label' => __('Update Database'),
                'onclick' => 'javascript:startUpdate(); return false;',
            ]
        );
        
        return $button->toHtml();
    }
    
    public function getFormUrl()
    {
        return $this->_urlBuilder->getUrl('geoip/database/update/') . '?isAjax=1';
    }

    public function getLastUpdateTime()
    {
        return $this->helperDatabase->getLastUpdateTime();
    }
}
