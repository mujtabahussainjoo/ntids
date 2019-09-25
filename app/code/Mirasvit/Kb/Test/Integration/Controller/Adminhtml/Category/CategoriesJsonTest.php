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
 * Admin Category CategoryJson Test.
 */
namespace Mirasvit\Kb\Controller\Adminhtml\Category;

/**
 * @magentoDataFixture Mirasvit/Kb/_files/categories.php
 *
 * @magentoAppArea adminhtml
 */
class CategoriesJsonTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Kb::kb_category';
        $this->uri = 'backend/kbase/category/categoriesjson';
        parent::setUp();
    }
    /**
     * @covers  Mirasvit\Kb\Controller\Adminhtml\Category\CategoriesJson::execute
     */
    public function testCategoriesJsonAction()
    {
        $this->getRequest()->setParam('id', 3);
        $this->dispatch('backend/kbase/category/categoriesJson');
        $body = $this->getResponse()->getBody();
        $expected = [
              [
                  'id' => '4',
                  'path' => '1/2/3/4',
                  'cls' => 'active',
                  'text' => 'Category 1.1',
                  'allowDrag' => true,
                  'allowDrop' => true,
                  'children' => [
                              [
                                  'id' => '5',
                                  'path' => '1/2/3/4/5',
                                  'cls' => 'active',
                                  'text' => 'Category 1.1.1',
                                  'allowDrag' => true,
                                  'allowDrop' => true,
                              ],
                      ],
                  'expanded' => true,
              ],
        ];
        $this->assertEquals($expected, json_decode($body, true));
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
    }
}
