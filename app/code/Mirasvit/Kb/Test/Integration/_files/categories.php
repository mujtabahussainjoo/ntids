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
    'DELETE FROM ' . $installer->getTableName('mst_kb_category_store') . ' WHERE as_category_id > 1;'
);
$installer->getConnection()->query(
    'INSERT INTO ' . $installer->getTableName('mst_kb_category_store') . ' (as_category_id, as_store_id) VALUE (2, 1);'
);
$installer->getConnection()->query(
    'DELETE FROM ' . $installer->getTableName('mst_kb_category') . ' WHERE category_id > 2;'
);
$installer->getConnection()->query(
    'UPDATE ' . $installer->getTableName('mst_kb_category') . ' SET `children_count`= 0 WHERE category_id = 2;'
);
$installer->getConnection()->query(
    'UPDATE ' . $installer->getTableName('mst_kb_category') . ' SET `children_count`= 1 WHERE category_id = 1;'
);
$installer->getConnection()->query('ALTER TABLE ' .
    $installer->getTableName('mst_kb_category') . ' AUTO_INCREMENT = 3;');

$category = $objectManager->create('Mirasvit\Kb\Model\Category');
$category->isObjectNew(true);
$category
    //->setId(3)
    ->setName('Category 1')
    ->setParentId(2)
    ->setPath('1/2/3')
    ->setLevel(2)
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

$category = $objectManager->create('Mirasvit\Kb\Model\Category');
$category->isObjectNew(true);
$category
    //->setId(4)
    ->setName('Category 1.1')
    ->setParentId(3)
    ->setPath('1/2/3/4')
    ->setLevel(3)
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

$category = $objectManager->create('Mirasvit\Kb\Model\Category');
$category->isObjectNew(true);
$category
    //->setId(5)
    ->setName('Category 1.1.1')
    ->setParentId(4)
    ->setPath('1/2/3/4/5')
    ->setLevel(4)
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

$category = $objectManager->create('Mirasvit\Kb\Model\Category');
$category->isObjectNew(true);
$category//->setId(6)
->setName('Category 2')
    ->setParentId(2)
    ->setPath('1/2/6')
    ->setLevel(2)
    ->setIsActive(true)
    ->setPosition(2)
    ->save();

$category = $objectManager->create('Mirasvit\Kb\Model\Category');
$category->isObjectNew(true);
$category
    //->setId(7)
    ->setName('Movable')
    ->setParentId(2)
    ->setPath('1/2/7')
    ->setLevel(2)
    ->setIsActive(true)
    ->setPosition(3)
    ->save();

$category = $objectManager->create('Mirasvit\Kb\Model\Category');
$category->isObjectNew(true);
$category
    //->setId(8)
    ->setName('Inactive')
    ->setParentId(2)
    ->setPath('1/2/8')
    ->setIsActive(false)
    ->setPosition(4)
    ->save();

$category = $objectManager->create('Mirasvit\Kb\Model\Category');
$category->isObjectNew(true);
$category
    //->setId(9)
    ->setName('Movable Position 1')
    ->setParentId(2)
    ->setPath('1/2/9')
    ->setLevel(2)
    ->setIsActive(true)
    ->setPosition(5)
    ->save();

$category = $objectManager->create('Mirasvit\Kb\Model\Category');
$category->isObjectNew(true);
$category
    //->setId(10)
    ->setName('Movable Position 2')
    ->setParentId(2)
    ->setPath('1/2/10')
    ->setLevel(2)
    ->setIsActive(true)
    ->setPosition(6)
    ->save();

$category = $objectManager->create('Mirasvit\Kb\Model\Category');
$category->isObjectNew(true);
$category
    //->setId(11)
    ->setName('Movable Position 3')
    ->setParentId(2)
    ->setPath('1/2/11')
    ->setLevel(2)
    ->setIsActive(true)
    ->setPosition(7)
    ->save();

$category = $objectManager->create('Mirasvit\Kb\Model\Category');
$category->isObjectNew(true);
$category
    //->setId(12)
    ->setName('Category 12')
    ->setParentId(2)
    ->setPath('1/2/12')
    ->setLevel(2)
    ->setIsActive(true)
    ->setPosition(8)
    ->save();
