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
 * Admin Article Mass Delete Test
 */
namespace Mirasvit\Kb\Controller\Adminhtml\Article;

/**
 * @magentoDataFixture Mirasvit/Kb/_files/articles.php
 */
/**
 * @magentoAppArea adminhtml
 */
class MassDeleteTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Kb::kb_article';
        $this->uri = 'backend/kbase/article/massdelete';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Kb\Controller\Adminhtml\Article\MassDelete::execute
     */
    public function testMassDeleteAction()
    {
        $this->getRequest()->setParam('article_id', [5, 4]);
        $this->dispatch('backend/kbase/article/massdelete');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());

        $this->assertSessionMessages(
            $this->equalTo(['Total of 2 record(s) were successfully deleted']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/kbase/article/index/'));

        /** @var \Mirasvit\Kb\Model\Article $article */
        $article = $this->_objectManager->create('Mirasvit\Kb\Model\Article')->load(5);
        $this->assertEquals(0, $article->getId());
        $article = $this->_objectManager->create('Mirasvit\Kb\Model\Article')->load(4);
        $this->assertEquals(0, $article->getId());
    }
}
