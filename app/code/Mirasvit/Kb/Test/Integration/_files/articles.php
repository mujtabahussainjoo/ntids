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
 * @package   mirasvit/module-kb
 * @version   1.0.49
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



/**
 * After installation system has two categories: root one with ID:1 and Default category with ID:2.
 */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\App\ResourceConnection $installer */
$installer = $objectManager->create('Magento\Framework\App\ResourceConnection');

$installer->getConnection()->query(
    'DELETE FROM '.$installer->getTableName('mst_kb_article').';'
);

/* @todo move it to categories.php */
$installer->getConnection()->query(
    'DELETE FROM '.$installer->getTableName('mst_kb_category_store').' WHERE as_category_id > 1;'
);
$installer->getConnection()->query(
    'INSERT INTO '.$installer->getTableName('mst_kb_category_store').' (as_category_id, as_store_id) VALUE (2, 1);'
);

$installer->getConnection()->query('ALTER TABLE '.$installer->getTableName('mst_kb_article').' AUTO_INCREMENT = 3;');

$article = $objectManager->create('Mirasvit\Kb\Model\Article');
$article->isObjectNew(true);
$article
    ->setName('Article 1')
    ->setStoreIds(['1'])
    ->setCategoryIds(['2'])
    ->setUserId(1)
    ->setIsActive(true)
    ->save();

$article = $objectManager->create('Mirasvit\Kb\Model\Article');
$article->isObjectNew(true);
$article
    ->setName('Article 2')
    ->setStoreIds(['1'])
    ->setCategoryIds(['2'])
    ->setUserId(1)
    ->setIsActive(true)
    ->save();

$article = $objectManager->create('Mirasvit\Kb\Model\Article');
$article->isObjectNew(true);
$article
    ->setName('Article 3')
    ->setStoreIds(['1'])
    ->setUserId('1')
    ->setIsActive(true)
    ->save();

$article = $objectManager->create('Mirasvit\Kb\Model\Article');
$article->isObjectNew(true);
$article
    ->setName('Article 6')
    ->setStoreIds(['1'])
    ->setUserId('1')
    ->setIsActive(true)
    ->save();
