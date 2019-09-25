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
 * Admin Suggestion Categories Test.
 */
namespace Mirasvit\Kb\Controller\Adminhtml\Category;

/**
 * @magentoDataFixture Mirasvit/Kb/_files/categories.php
 *
 * @magentoAppArea adminhtml
 */
class SuggestCategoriesTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Kb::kb_category';
        $this->uri = 'backend/kbase/category/suggestCategories?isAjax=true';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Kb\Controller\Adminhtml\Category\SuggestCategories::execute
     */
    public function testSuggestCategoriesAction()
    {
        $data = ['label_part' => 'Category 12'];
        $this->getRequest()->setPostValue($data);
        $this->dispatch('backend/kbase/category/suggestCategories?isAjax=true');
        $body = $this->getResponse()->getBody();
        $expected = [
            [
                'id' => '2',
                'is_active' => '1',
                'label' => 'Knowledge base',
                'children' => [
                    [
                        'id' => '12',
                        'is_active' => '1',
                        'label' => 'Category 12',
                        'children' => [],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, json_decode($body, true));
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
    }
}
