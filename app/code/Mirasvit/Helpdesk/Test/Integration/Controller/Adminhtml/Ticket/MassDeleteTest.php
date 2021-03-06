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

/**
 * @magentoAppArea adminhtml
 */
class MassDeleteTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Helpdesk::helpdesk_ticket';
        $this->uri = 'backend/helpdesk/ticket/massdelete';
        parent::setUp();
    }

    /**
     * @magentoDataFixture Mirasvit/Helpdesk/_files/ticket.php
     *
     * @covers  Mirasvit\Helpdesk\Controller\Adminhtml\Ticket\MassDelete::execute
     */
    public function testMassDeleteAction()
    {
        $this->getRequest()
            ->setPostValue('selected', [1])
            ->setPostValue('namespace', 'helpdesk_ticket_listing')
            ->setPostValue('filters', ['placeholder' => true])
        ;
        $this->dispatch('backend/helpdesk/ticket/massDelete');
        $this->assertSessionMessages(
            $this->equalTo(['A total of 1 record(s) have been deleted.']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
    }
}
