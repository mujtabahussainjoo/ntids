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
 * Admin Article Save Test
 */
namespace Mirasvit\Kb\Controller\Adminhtml\Article;

/**
 * @magentoAppArea adminhtml
 */
class SaveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Kb::kb_article';
        $this->uri = 'backend/kbase/article/save';
        parent::setUp();
    }

    /**
     * @magentoDataFixture Mirasvit/Kb/_files/articles.php
     *
     * @covers  Mirasvit\Kb\Controller\Adminhtml\Article\Save::execute
     */
    public function testSaveAction()
    {
        $data = [
            'id' => 5,
            'name' => 'Edited Article',
            'text' => 'some test text',
            'votes_sum' => 6,
            'votes_num' => 1,
            'tags' => '',
        ];
        $this->getRequest()->setParams($data);
        $this->dispatch('backend/kbase/article/save');

        $this->assertSessionMessages(
            $this->equalTo(['Article was successfully saved']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/kbase/article/index/'));

        /** @var \Mirasvit\Kb\Model\Article $article */
        $article = $this->_objectManager->create('Mirasvit\Kb\Model\Article')->load(5);
        $this->assertEquals($data['name'], $article->getName());
        $this->assertEquals($data['text'], $article->getText());
        $this->assertEquals($data['votes_sum'], $article->getVotesSum());
        $this->assertEquals($data['votes_num'], $article->getVotesNum());
    }

    /**
     * @magentoDataFixture Mirasvit/Kb/_files/articles.php
     *
     * @covers  Mirasvit\Kb\Controller\Adminhtml\Article\Save::execute
     */
    public function testSaveNewAction()
    {
        $data = [
            'name' => 'Edited Article',
            'text' => 'some test text',
            'votes_sum' => 10,
            'votes_num' => 2,
            'tags' => '',
        ];
        $this->getRequest()->setParams($data);
        $this->dispatch('backend/kbase/article/save');

        $this->assertSessionMessages(
            $this->equalTo(['Article was successfully saved']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/kbase/article/index/'));

        /** @var \Mirasvit\Kb\Model\Article $article */
        $article = $this->_objectManager->create('Mirasvit\Kb\Model\Article')->getCollection()->getLastItem();
        $this->assertEquals($data['name'], $article->getName());
        $this->assertEquals($data['text'], $article->getText());
        $this->assertEquals($data['votes_sum'], $article->getVotesSum());
        $this->assertEquals($data['votes_num'], $article->getVotesNum());
    }
}
