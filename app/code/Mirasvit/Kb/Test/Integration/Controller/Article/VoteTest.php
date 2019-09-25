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
 * Article Vote Test.
 */

namespace Mirasvit\Kb\Controller\Article;

/**
 * @magentoDataFixture Mirasvit/Kb/_files/articles.php
 */
class VoteTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Kb::kb_article';
        $this->uri = 'kbase/article/vote';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Kb\Controller\Article\Vote::execute
     */
    public function testVoteAction()
    {
        $this->getRequest()->setParam('id', '3');
        $this->getRequest()->setParam('vote', '5');
        $this->dispatch('kbase/article/vote');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());

        /** @var \Mirasvit\Kb\Model\Article $article */
        $article = $this->_objectManager->create('Mirasvit\Kb\Model\Article')->load(3);
        $this->assertEquals(5, $article->getVotesSum());
        $this->assertEquals(1, $article->getVotesNum());
    }
}
