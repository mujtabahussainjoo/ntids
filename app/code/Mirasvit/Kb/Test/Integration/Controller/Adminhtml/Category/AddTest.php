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
 * Admin Category Add Test
 */
namespace Mirasvit\Kb\Controller\Adminhtml\Category;

/**
 * @magentoAppArea adminhtml
 */
class AddTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Kb::kb_category';
        $this->uri = 'backend/kbase/category/add';
        parent::setUp();
    }

    /**
     * @magentoDataFixture Mirasvit/Kb/_files/categories.php
     *
     * @covers  Mirasvit\Kb\Controller\Adminhtml\Category\Add::execute
     */
    public function testAddAction()
    {
        $this->dispatch('backend/kbase/category/add');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertContains('<h1 class="page-title">Categories</h1>', $body);
    }
}
