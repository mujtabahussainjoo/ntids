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
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Controller\Ticket;

class SatisfactionTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @covers  Mirasvit\Helpdesk\Controller\Satisfaction\Rate::execute
     * @magentoDataFixture Mirasvit/Helpdesk/_files/customer.php
     * @magentoDataFixture Mirasvit/Helpdesk/_files/ticket.php
     */
    public function testRateAction()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Mirasvit\Helpdesk\Model\Message $message */
        $message = $objectManager->create('Mirasvit\Helpdesk\Model\Message')->getCollection()->getLastItem();

        $this->getRequest()->setParams(
            [
                'rate' => 1,
                'uid' => $message->getUid(),
            ]
        );
        $this->dispatch('helpdesk/satisfaction/rate');
        $this->assertRedirect($this->stringContains('helpdesk/satisfaction/form/uid/'.$message->getUid().'/'));
    }

    /**
     * @covers  Mirasvit\Helpdesk\Controller\Satisfaction\Form::execute
     * @magentoDataFixture Mirasvit/Helpdesk/_files/customer.php
     * @magentoDataFixture Mirasvit/Helpdesk/_files/ticket.php
     */
    public function testFormAction()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Mirasvit\Helpdesk\Model\Message $message */
        $message = $objectManager->create('Mirasvit\Helpdesk\Model\Message')->getCollection()->getLastItem();

        $this->getRequest()->setParams(
            [
                'uid' => $message->getUid(),
            ]
        );
        $this->dispatch('helpdesk/satisfaction/form');
        $body = $this->getResponse()->getBody();
        $this->assertContains('Thank you', $body);
        $this->assertContains('Submit Message', $body);
    }

    /**
     * @covers  Mirasvit\Helpdesk\Controller\Satisfaction\Post::execute
     * @magentoDataFixture Mirasvit/Helpdesk/_files/customer.php
     * @magentoDataFixture Mirasvit/Helpdesk/_files/ticket.php
     */
    public function testPostAction()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Mirasvit\Helpdesk\Model\Message $message */
        $message = $objectManager->create('Mirasvit\Helpdesk\Model\Message')->getCollection()->getLastItem();

        $this->getRequest()->setParams(
            [
                'uid' => $message->getUid(),
                'comment' => 'some message',
            ]
        );
        $this->dispatch('helpdesk/satisfaction/post');
        $body = $this->getResponse()->getBody();
        $this->assertContains('Thank you', $body);
        $this->assertContains('Thank you for your feedback', $body);
    }
}
