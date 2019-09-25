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
 * Admin Category Delete Test.
 */
namespace Mirasvit\Kb\Controller\Adminhtml\Category;

/**
 * @magentoDataFixture Mirasvit/Kb/_files/categories.php
 *
 * @magentoAppArea adminhtml
 */
class DeleteTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /** @var  \Magento\TestFramework\ObjectManager */
    protected $objectManager;

    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Kb::kb_category';
        $this->uri = 'backend/kbase/category/delete';
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Kb\Controller\Adminhtml\Category\Delete::execute
     */
    public function testDeleteAction()
    {
        $this->getRequest()->setParam('id', 3);
        $this->dispatch('backend/kbase/category/delete');

        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());

        /** @var \Mirasvit\Kb\Model\Category $category */
        $category = $this->objectManager->create('Mirasvit\Kb\Model\Category');
        $category->load(1);
        $this->assertEquals(1, $category->getChildren()->count());
        $category = $this->objectManager->create('Mirasvit\Kb\Model\Category')->load(3);
        $this->assertEquals(0, $category->getId());
    }
}
