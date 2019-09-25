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

class PostmessageTest extends \Magento\TestFramework\TestCase\AbstractController
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
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @covers  Mirasvit\Helpdesk\Controller\Ticket\Postmessage::execute
     */
    public function testPostNewTicketAction()
    {
        $params = [
            'subject' => 'New Ticket',
            'message' => 'New Message',
            'priority_id' => 1,
            'department_id' => 1,
        ];
        $this->getRequest()->setParams($params);
        $this->dispatch('helpdesk/ticket/postmessage');

        $this->assertSessionMessages(
            $this->equalTo(['Your ticket was successfuly posted']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('helpdesk/ticket/'));
    }

    /**
     * @magentoDataFixture Mirasvit/Helpdesk/_files/ticket.php
     * @magentoDataFixture Mirasvit/Helpdesk/_files/customer.php
     * @covers  Mirasvit\Helpdesk\Controller\Ticket\Postmessage::execute
     */
    public function testPostNewMessageAction()
    {
        $params = [
            'id' => 1,
            'message' => 'New Message',
        ];
        $this->getRequest()->setParams($params);
        $this->dispatch('helpdesk/ticket/postmessage');

        $this->assertSessionMessages(
            $this->equalTo(['Your message was successfuly posted']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('helpdesk/ticket/'));
    }
}
