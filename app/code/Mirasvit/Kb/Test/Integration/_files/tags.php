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
    'DELETE FROM '.$installer->getTableName('mst_kb_tag').';'
);

$installer->getConnection()->query('ALTER TABLE '.$installer->getTableName('mst_kb_tag').' AUTO_INCREMENT = 1;');

$article = $objectManager->create('Mirasvit\Kb\Model\Tag');
$article->isObjectNew(true);
$article
    ->setName('tag 1')
    ->setUrlKey('tag1')
    ->save();

$article = $objectManager->create('Mirasvit\Kb\Model\Tag');
$article->isObjectNew(true);
$article
    ->setName('tag 2')
    ->setUrlKey('tag2')
    ->save();

$article = $objectManager->create('Mirasvit\Kb\Model\Tag');
$article->isObjectNew(true);
$article
    ->setName('tag 3')
    ->setUrlKey('tag3')
    ->save();
