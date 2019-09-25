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
 * Admin Article Edit test.
 */
namespace Mirasvit\Kb\Controller\Adminhtml\Article;

/**
 * @magentoDataFixture Mirasvit/Kb/_files/articles.php
 *
 * @magentoAppArea adminhtml
 */
class EditTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Kb::kb_article';
        $this->uri = 'backend/kbase/article/edit';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Kb\Controller\Adminhtml\Article\Edit::execute
     */
    public function testEditAction()
    {
        $this->getRequest()->setParam('id', 4);
        $this->dispatch('backend/kbase/article/edit');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertContains('Article 2', $body);
        $this->assertContains('<h1 class="page-title">Edit Article \'Article 2\'</h1>', $body);
    }
}
