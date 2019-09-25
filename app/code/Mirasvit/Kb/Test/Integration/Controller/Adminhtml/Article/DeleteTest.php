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
 * Admin Article Delete Test
 */
namespace Mirasvit\Kb\Controller\Adminhtml\Article;

/**
 * Class DeleteTest.
 *
 * @magentoDataFixture Mirasvit/Kb/_files/articles.php
 */
/**
 * @magentoAppArea adminhtml
 */
class DeleteTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Kb::kb_article';
        $this->uri = 'backend/kbase/article/delete';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Kb\Controller\Adminhtml\Article\Delete::execute
     */
    public function testDeleteAction()
    {
        $this->getRequest()->setParam('id', 5);
        $this->dispatch('backend/kbase/article/delete');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertSessionMessages(
            $this->equalTo(['Article was successfully deleted']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/kbase/article/index/'));

        /** @var \Mirasvit\Kb\Model\Article $article */
        $article = $this->_objectManager->create('Mirasvit\Kb\Model\Article')->load(5);
        $this->assertEquals(0, $article->getId());
    }
}
