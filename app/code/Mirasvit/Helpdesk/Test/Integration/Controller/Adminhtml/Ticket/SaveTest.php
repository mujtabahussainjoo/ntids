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
class SaveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Helpdesk::helpdesk_ticket';
        $this->uri = 'backend/helpdesk/ticket/save';
        parent::setUp();
    }

    /**
     * @covers Mirasvit\Helpdesk\Controller\Adminhtml\Ticket\Save::execute
     */
    public function testExecute()
    {
        $data = [
            'customer_email' => 'jack@example.com',
            'reply' => 'some message',
            'reply_type' => 'public',
            'tags' => [],
            'store_id' => 1,
            'owner' => '1_0',
        ];
        $request = $this->getRequest();
        $request->setPostValue($data);

        $this->dispatch('backend/helpdesk/ticket/save');

        $this->assertSessionMessages(
            $this->contains('Message was successfully sent'),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
    }
}
