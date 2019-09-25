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
 * Model Category Test.
 */
namespace Mirasvit\Kb\Model;

/**
 * @magentoDataFixture Mirasvit/Kb/_files/categories.php
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\TestFramework\ObjectManager */
    protected $objectManager;

    /**
     * setUp.
     */
    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @covers Mirasvit\Kb\Model\ResourceModel\Category::_beforeSave
     */
    public function testBeforeSave1()
    {
        $category = $this->objectManager->create('Mirasvit\Kb\Model\Category');

        $category->load(1);
        $this->assertEquals([
            'category_id'      => '1',
            'name'             => 'Root Category',
            'url_key'          => 'root-category',
            'meta_title'       => '',
            'meta_keywords'    => null,
            'meta_description' => null,
            'is_active'        => '1',
            'sort_order'       => '0',
            'parent_id'        => null,
            'path'             => '1',
            'level'            => '0',
            'position'         => '1',
            'children_count'   => '11',
            'display_mode'     => '',
        ], $category->getData());
    }

    /**
     * @covers Mirasvit\Kb\Model\ResourceModel\Category::_beforeSave
     */
    public function testBeforeSave2()
    {
        $category = $this->objectManager->create('Mirasvit\Kb\Model\Category');
        $category->load(2);
        $this->assertEquals([
            'category_id'      => '2',
            'name'             => 'Knowledge base',
            'url_key'          => 'knowledge-base',
            'meta_title'       => '',
            'meta_keywords'    => null,
            'meta_description' => null,
            'is_active'        => '1',
            'sort_order'       => '0',
            'parent_id'        => '1',
            'path'             => '1/2',
            'level'            => '1',
            'position'         => '1',
            'children_count'   => '10',
            'store_ids'        => [1],
            'display_mode'     => '',
        ], $category->getData());
    }

    /**
     * @covers Mirasvit\Kb\Model\ResourceModel\Category::_beforeSave
     */
    public function testBeforeSave3()
    {
        $category = $this->objectManager->create('Mirasvit\Kb\Model\Category');
        $category->load(4);
        $this->assertEquals([
            'category_id'      => '4',
            'name'             => 'Category 1.1',
            'url_key'          => 'category-1-1',
            'meta_title'       => '',
            'meta_keywords'    => null,
            'meta_description' => null,
            'is_active'        => '1',
            'sort_order'       => '0',
            'parent_id'        => '3',
            'path'             => '1/2/3/4',
            'level'            => '3',
            'position'         => '1',
            'children_count'   => '1',
            'display_mode'     => '',
        ], $category->getData());
    }

    /**
     * @covers Mirasvit\Kb\Model\ResourceModel\Category::_beforeSave
     */
    public function testBeforeSave4()
    {
        $category = $this->objectManager->create('Mirasvit\Kb\Model\Category');
        $category
            ->setName('Category Custom')
            ->setParentId(2)
            ->setIsActive(true)
            ->save();
        $category->load($category->getId());

        $this->assertEquals([
            'category_id'      => '13',
            'name'             => 'Category Custom',
            'url_key'          => 'category-custom',
            'meta_title'       => '',
            'meta_keywords'    => null,
            'meta_description' => null,
            'is_active'        => '1',
            'sort_order'       => '0',
            'parent_id'        => '2',
            'path'             => '1/2/13',
            'level'            => '2',
            'position'         => '2',
            'children_count'   => '0',
            'display_mode'     => '',
        ], $category->getData());

        //////
        $category = $this->objectManager->create('Mirasvit\Kb\Model\Category');
        $category
            ->setName('Category Custom 2')
            ->setParentId(13)
            ->setIsActive(true)
            ->save();
        $category->load($category->getId());
        $this->assertEquals([
            'category_id'      => '14',
            'name'             => 'Category Custom 2',
            'url_key'          => 'category-custom-2',
            'meta_title'       => '',
            'meta_keywords'    => null,
            'meta_description' => null,
            'is_active'        => '1',
            'sort_order'       => '0',
            'parent_id'        => '13',
            'path'             => '1/2/13/14',
            'level'            => '3',
            'position'         => '1',
            'children_count'   => '0',
            'display_mode'     => '',
        ], $category->getData());
    }

    /**
     * @covers Mirasvit\Kb\Model\ResourceModel\Category::getAllChildren
     * @covers Mirasvit\Kb\Model\Category::getAllChildren
     */
    public function testGetAllChildren()
    {
        /** @var \Mirasvit\Kb\Model\Category $category */
        $category = $this->objectManager->create('Mirasvit\Kb\Model\Category');
        $category->load(2);
        $this->assertEquals(8, $category->getAllChildren()->count());
    }

    /**
     * @covers Mirasvit\Kb\Model\ResourceModel\Category::getChildren
     * @covers Mirasvit\Kb\Model\Category::getChildren
     */
    public function testGetActiveChildren()
    {
        /** @var \Mirasvit\Kb\Model\Category $category */
        $category = $this->objectManager->create('Mirasvit\Kb\Model\Category');
        $category->load(2);
        $this->assertEquals(7, $category->getChildren()->count());
    }
}
