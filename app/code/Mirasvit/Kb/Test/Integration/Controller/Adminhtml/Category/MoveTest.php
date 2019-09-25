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
 * Admin Move Category Test.
 */
namespace Mirasvit\Kb\Controller\Adminhtml\Category;

/**
 * @magentoDataFixture Mirasvit/Kb/_files/categories.php
 *
 * @magentoAppArea adminhtml
 */
class MoveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /** @var  \Magento\TestFramework\ObjectManager */
    protected $objectManager;

    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Kb::kb_category';
        $this->uri = 'backend/kbase/category/move';
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Kb\Controller\Adminhtml\Category\Move::execute
     */
    public function testMoveAction()
    {
        $pid = 5;
        $aid = 3;
        $this->getRequest()->setParam('pid', $pid);
        $this->getRequest()->setParam('aid', $aid);
        $this->dispatch('backend/kbase/category/move');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $category = $this->objectManager->create('Mirasvit\Kb\Model\Category')->load($pid);
        $this->assertContains($aid, $category->getParentIds());
    }
}
