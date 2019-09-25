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
$installer->getConnection()->query(
    'DELETE FROM '.$installer->getTableName('mst_helpdesk_gateway').';'
);
$installer->getConnection()->query(
    'ALTER TABLE '.$installer->getTableName('mst_helpdesk_gateway').' AUTO_INCREMENT = 1;'
);

$currentStoreId = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();

/** @var \Mirasvit\Helpdesk\Model\Gateway $gateway */
$gateway = $objectManager->create('Mirasvit\Helpdesk\Model\Gateway');
$data = [
    'name' => 'Some Gateway',
    'email' => 'support2@mirasvit.com.ua',
    'login' => 'support2@mirasvit.com.ua',
    'password' => '6Vl5gxZmxpeE',
    'is_active' => '1',
    'host' => 'imap.gmail.com',
    'protocol' => 'imap',
    'encryption' => 'ssl',
    'port' => '993',
    'fetch_frequency' => '5',
    'fetch_max' => '10',
    'fetch_limit' => '1',
    'is_delete_emails' => '0',
    'store_id' => '1',
    'department_id' => '1'
];
$gateway->addData($data);
$gateway->save();
