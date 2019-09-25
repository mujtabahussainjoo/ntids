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



namespace Mirasvit\Helpdesk\Controller\Form;

class PostTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @covers  Mirasvit\Helpdesk\Controller\Form\Post::execute
     */
    public function testPostmessageAction()
    {
        $subject = 'Subject'.rand();
        $this->getRequest()->setPostValue([
            'subject' => $subject,
            'name' => 'John Doe',
            'comment' => 'Comment',
            'mail' => 'john@example.com',
            'telephone' => '11111',
            'email' => '',
            'hideit' => '',
        ]);
        $this->dispatch('helpdesk/form/post');

        $this->assertSessionMessages(
            $this->contains(
                'Your inquiry was submitted and will be responded to as soon as possible. '.
                'Thank you for contacting us.'
            ),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect();

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Mirasvit\Helpdesk\Model\Ticket $ticket */
        $ticket = $objectManager->create('Mirasvit\Helpdesk\Model\Ticket')->getCollection()->getLastItem();
        $this->assertEquals($subject, $ticket->getSubject());
    }
}
