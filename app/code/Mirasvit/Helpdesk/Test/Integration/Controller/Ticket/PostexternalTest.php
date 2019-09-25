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

class PostexternalTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Mirasvit/Helpdesk/_files/ticket.php
     * @covers  Mirasvit\Helpdesk\Controller\Ticket\Postexternal::execute
     */
    public function testPostNewMessageAction()
    {
        $params = [
            'id' => 'a975606afcef8e12f24d1b599f0e5544',
            'message' => 'New Message',
        ];
        $this->getRequest()->setParams($params);
        $this->dispatch('helpdesk/ticket/postexternal');

        $this->assertSessionMessages(
            $this->equalTo(['Your message was successfuly posted']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('helpdesk/ticket/'));
    }
}
