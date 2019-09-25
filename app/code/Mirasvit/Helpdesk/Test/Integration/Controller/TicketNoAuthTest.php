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

class TicketNoAuthTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @covers Mirasvit\Helpdesk\Controller\Ticket\Index::execute
     */
    public function testIndexAction()
    {
        $this->markTestSkipped();
        $this->dispatch('helpdesk/ticket/index');

        $this->assertRedirect($this->stringContains('customer/account/login'));
    }

    /**
     * @covers Mirasvit\Helpdesk\Controller\Ticket\View::execute
     */
    public function testViewAction()
    {
        $this->markTestSkipped();
        $this->dispatch('helpdesk/ticket/view');

        $this->assertRedirect($this->stringContains('customer/account/login'));
    }

    /**
     * @covers Mirasvit\Helpdesk\Controller\Ticket\Postmessage::execute
     */
    public function testPostAction()
    {
        $this->markTestSkipped();
        $this->dispatch('helpdesk/ticket/postmessage');

        $this->assertRedirect($this->stringContains('customer/account/login'));
    }
}
