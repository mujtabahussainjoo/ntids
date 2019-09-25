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



require getcwd().'/testsuite/Magento/Store/_files/core_fixturestore.php';

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\App\ResourceConnection $installer */
$installer = $objectManager->create('Magento\Framework\App\ResourceConnection');
$installer->getConnection()->delete('mst_helpdesk_ticket');
$installer->getConnection()->query(
    'ALTER TABLE '.$installer->getTableName('mst_helpdesk_ticket').' AUTO_INCREMENT = 1;'
);

$currentStore = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();

/** @var \Mirasvit\Helpdesk\Model\Department $department */
$department = $objectManager->create('Mirasvit\Helpdesk\Model\Department');
$department->setName('Sales')
    ->setSenderEmail('sales@example.com')
    ->setIsActive(1)
    ->save();

/* @var $ticket \Mirasvit\Helpdesk\Model\Ticket */
$ticket = $objectManager->create('Mirasvit\Helpdesk\Model\Ticket');
$ticket
    ->setStoreId($currentStore)
    ->setSubject('Some ticket')
    ->setCode('XQS-244-30031')
    ->setCustomerEmail('customer@example.com')
    ->setCustomerId(1)
    ->setDepartmentId($department->getId())
    ->setExternalId('a975606afcef8e12f24d1b599f0e5544')
    ->setCreatedAt('2015-07-03 00:00:00')
    ->setUpdatedAt('2015-07-05 00:00:00')
    ->setInTest(true)
    ->save();

/** @var \Mirasvit\Helpdesk\Model\Message $message */
$message = $objectManager->create('Mirasvit\Helpdesk\Model\Message');
$message->setTicketId($ticket->getId())
    ->setCustomerId(1)
    ->setBody('Some message')
    ->save();
