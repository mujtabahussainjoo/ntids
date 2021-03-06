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

class TicketTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /** @var \Magento\Customer\Api\AccountManagementInterface */
    private $accountManagement;

    /**
     * setUp.
     */
    protected function setUp()
    {
        parent::setUp();
        $logger = $this->getMock('Psr\Log\LoggerInterface', [], [], '', false);
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Model\Session',
            [$logger]
        );
        $this->accountManagement = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Api\AccountManagementInterface'
        );
        $customer = $this->accountManagement->authenticate('customer@example.com', 'password');
        $session->setCustomerDataAsLoggedIn($customer);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     * @magentoDataFixture Mirasvit/Helpdesk/_files/ticket.php
     * @covers Mirasvit\Helpdesk\Controller\Adminhtml\Ticket\Index::execute
     */
    public function testIndexAction()
    {
        $this->dispatch('helpdesk/ticket/index');

        $body = $this->getResponse()->getBody();
        $this->assertContains('My Tickets', $body);
        $this->assertContains('Create New Ticket', $body);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     * @magentoDataFixture Mirasvit/Helpdesk/_files/ticket.php
     * @covers Mirasvit\Helpdesk\Controller\Ticket\View::execute
     */
    public function testViewAction()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Mirasvit\Helpdesk\Model\Ticket $ticket */
        $ticket = $objectManager->create('Mirasvit\Helpdesk\Model\Ticket')->load(1);

        $data = [
            'id' => $ticket->getId(),
        ];
        $this->getRequest()->setParams($data);

        $this->dispatch('helpdesk/ticket/view');

        $body = $this->getResponse()->getBody();
        $this->assertContains('Some ticket', $body);
    }
}
