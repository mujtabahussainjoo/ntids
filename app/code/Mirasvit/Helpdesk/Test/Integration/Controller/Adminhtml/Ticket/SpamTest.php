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



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Ticket;

use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * @magentoAppArea adminhtml
 */
class SpamTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Helpdesk::helpdesk_ticket';
        $this->uri = 'backend/helpdesk/ticket/spam';
        parent::setUp();
    }

    /**
     * @magentoDataFixture Mirasvit/Helpdesk/_files/ticket.php
     * @magentoDataFixture Mirasvit/Helpdesk/_files/customer.php
     * @covers  Mirasvit\Helpdesk\Controller\Adminhtml\Ticket\Spam::execute
     */
    public function testSpamAction()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Mirasvit\Helpdesk\Model\Ticket $ticket */
        $this->getRequest()->setParams(['id' => 1]);

        $this->dispatch('backend/helpdesk/ticket/spam');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());

        $this->assertSessionMessages(
            $this->contains('Ticket was moved to the Spam folder'),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertSessionMessages($this->isEmpty(), \Magento\Framework\Message\MessageInterface::TYPE_ERROR);

        $this->assertTrue($this->getResponse()->isRedirect());

        $ticket = $objectManager->create('Mirasvit\Helpdesk\Model\Ticket')->load(1);
        $this->assertEquals(Config::FOLDER_SPAM, $ticket->getFolder());
    }
}
