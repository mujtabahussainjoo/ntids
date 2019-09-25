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
 * Admin Category Save Test.
 */
namespace Mirasvit\Kb\Controller\Adminhtml\Category;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @magentoAppArea adminhtml
 */
class SaveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Framework\App\ResourceConnection $installer */
        $installer = $objectManager->create('Magento\Framework\App\ResourceConnection');
        $installer->getConnection()->query(
            'ALTER TABLE ' . $installer->getTableName('mst_kb_category') . ' AUTO_INCREMENT = 3;'
        );

        $this->resource = 'Mirasvit_Kb::kb_category';
        $this->uri = 'backend/kbase/category/save';
        parent::setUp();
    }//end setUp()


    /**
     * TearDown.
     * For some reason, categories are not deleted automatically.
     */
    public function tearDown()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Framework\App\ResourceConnection $installer */
        $installer = $objectManager->create('Magento\Framework\App\ResourceConnection');
        $installer->getConnection()->query(
            'DELETE FROM ' . $installer->getTableName('mst_kb_category')
            . ' WHERE category_id <> 1 AND category_id <> 2;'
        );
        parent::tearDown();
    }

    /**
     * @covers  Mirasvit\Kb\Controller\Adminhtml\Category\Save::execute
     */
    public function testSaveAction()
    {
        $data =
            [
                'parent'  => 2,
                'id'      => '',
                'general' => [
                    'path'      => '1/2',
                    'name'      => 'Category X',
                    'url_key'   => 'somekey',
                    'is_active' => 1,
                    'store_ids' => [0 => 1],
                ],
                'seo'     => [],
            ];
            $this->getRequest()->setPostValue($data);
            $this->dispatch('backend/kbase/category/save');
            $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
            $this->assertTrue($this->getResponse()->isRedirect());

            $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
            /** @var \Mirasvit\Kb\Model\Category $category */
            $category = $objectManager->create('Mirasvit\Kb\Model\Category')->getCollection()->getLastItem();

            $this->assertEquals(
            [
                'category_id'      => '3',
                'name'             => 'Category X',
                'url_key'          => 'somekey',
                'meta_title'       => '',
                'meta_keywords'    => null,
                'meta_description' => null,
                'is_active'        => '1',
                'sort_order'       => '0',
                'parent_id'        => '2',
                'path'             => '1/2/3',
                'level'            => '2',
                'position'         => '1',
                'children_count'   => '0',
                'display_mode'     => ''

            ],
            $category->getData()
            );
    }
}
