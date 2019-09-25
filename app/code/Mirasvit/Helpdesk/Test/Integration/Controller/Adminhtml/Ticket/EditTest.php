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



namespace Mirasvit\Helpdesk\Controller\Admihtml\Ticket;

/**
 * @magentoAppArea adminhtml
 */
class EditTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Helpdesk::helpdesk_ticket';
        $this->uri = 'backend/helpdesk/ticket/edit';
        parent::setUp();
    }

    /**
     * @covers Mirasvit\Helpdesk\Controller\Adminhtml\Ticket\Edit::execute
     */
    public function test404()
    {
        $data = [
            'ticket_id' => 1444,
        ];
        $this->getRequest()->setPostValue($data);

        $this->dispatch('backend/helpdesk/ticket/edit');

        $this->assertSessionMessages(
            $this->contains('The ticket does not exist.'),
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @magentoDataFixture Mirasvit/Helpdesk/_files/ticket.php
     * @magentoDataFixture Mirasvit/Helpdesk/_files/customer.php
     * @covers Mirasvit\Helpdesk\Controller\Adminhtml\Ticket\Edit::execute
     */
    public function testSuccess()
    {
        $data = [
            'id' => 1,
        ];
        $this->getRequest()->setParams($data);

        $this->dispatch('backend/helpdesk/ticket/edit');
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertSessionMessages($this->isEmpty(), \Magento\Framework\Message\MessageInterface::TYPE_ERROR);
        $this->assertContains(
            '<h1 class="page-title">[#XQS-244-30031] Some ticket</h1>',
            $this->getResponse()->getBody()
        );
    }
}
