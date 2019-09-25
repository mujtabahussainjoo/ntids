<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\GeoIP\Controller\Adminhtml\Database;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * GeoIP UPDATE controller
 */
class Update extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
    
        parent::__construct($context);
        $this->resultJsonFactory = $jsonFactory;
    }

    /**
     * Update country/city database
     *
     * @return string
     */
    public function execute()
    {
        /** @var \MageWorx\GeoIP\Helper\Database $helperDatabase */
        $helperDatabase =  $this->_objectManager->get('MageWorx\GeoIP\Helper\Database');
        /** @var \MageWorx\GeoIP\Model\Geoip $geoip */
        $geoip = $this->_objectManager->get('MageWorx\GeoIP\Model\Geoip');
        /** @var \Magento\Config\Model\ResourceModel\Config $config */
        $config = $this->_objectManager->get('Magento\Config\Model\ResourceModel\Config');
        
        $createBackupFlag = $this->getRequest()->getParam('backup');
        
        try {
            $geoip->downloadFile($helperDatabase->getDbUpdateSource(), $helperDatabase->getTempUpdateFile(), $createBackupFlag);

            $time = time();
            $config->saveConfig(
                \MageWorx\GeoIP\Helper\Database::XML_GEOIP_UPDATE_DB,
                $time,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                0
            );

            $returnData['last_update'] = date('F d, Y / h:i', $time);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $returnData['last_update'] = $e->getRawMessage();
        }

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        /** You may introduce your own constants for this custom REST API */
        $result->setData($returnData);

        return $result;
    }
}
