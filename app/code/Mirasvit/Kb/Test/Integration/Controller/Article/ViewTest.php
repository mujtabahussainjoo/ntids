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
 * Article View Test.
 */
namespace Mirasvit\Kb\Controller\Article;

/**
 * @magentoDataFixture Mirasvit/Kb/_files/articles.php
 */
class ViewTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Kb::kb_article';
        $this->uri = 'kbase/article/view';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Kb\Controller\Article\View::execute
     */
    public function testViewAction()
    {
        $this->getRequest()->setParam('id', '3');
        $this->dispatch('kbase/article/view');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertContains('<span class="base" data-ui-id="page-title-wrapper" >Article 1</span>', $body);
    }
}
