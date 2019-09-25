<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\App\ResourceConnection $installer */
$installer = $objectManager->create('Magento\Framework\App\ResourceConnection');
$installer->getConnection()->delete('mst_helpdesk_schedule');
$installer->getConnection()->query(
    'ALTER TABLE '.$installer->getTableName('mst_helpdesk_schedule').' AUTO_INCREMENT = 1;'
);

/* @var $ticket \Mirasvit\Helpdesk\Model\Schedule */
$ticket = $objectManager->create('Mirasvit\Helpdesk\Model\Schedule');
$ticket
    ->setName('Common schedule')
    ->setIsActive(1)
    ->setActiveFrom((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT))
    ->setActiveTo((new \DateTime())->add((new \DateInterval('P2Y')))
        ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT))
    ->setTimezone('UTC')
    ->setType(\Mirasvit\Helpdesk\Model\Config::SCHEDULE_TYPE_ALWAYS)
    ->setOpenMessage('Test. We are working:')
    ->setClosedMessage('Test. We are closed')
    ->save();

$ticket = $objectManager->create('Mirasvit\Helpdesk\Model\Schedule');
$ticket
    ->setName('Closed schedule')
    ->setIsActive(1)
    ->setActiveFrom((new \DateTime())->add((new \DateInterval('P2D')))
        ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT))
    ->setActiveTo((new \DateTime())->add((new \DateInterval('P3D')))
        ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT))
    ->setTimezone('UTC')
    ->setType(\Mirasvit\Helpdesk\Model\Config::SCHEDULE_TYPE_ALWAYS)
    ->setOpenMessage('')
    ->setClosedMessage('Test. We are closed for something')
    ->save();
