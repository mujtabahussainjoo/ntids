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



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Status;

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
        $this->resource = 'Mirasvit_Helpdesk::helpdesk_status';
        $this->uri = 'backend/helpdesk/status/massdelete';
        parent::setUp();
    }

    /**
     * @magentoDataFixture Mirasvit/Helpdesk/_files/status.php
     *
     * @covers  Mirasvit\Helpdesk\Controller\Adminhtml\Status\MassDelete::execute
     */
    public function testMassDeleteAction()
    {
        $this->getRequest()->setParam('status_id', [5]);
        $this->dispatch('backend/helpdesk/status/massdelete');

        $this->assertSessionMessages(
            $this->equalTo(['Total of 1 record(s) were successfully deleted']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('helpdesk/status/index/'));

        /** @var \Mirasvit\Helpdesk\Model\Status $status */
        $status = $this->_objectManager->create('Mirasvit\Helpdesk\Model\Status')->load(5);
        $this->assertEquals(0, $status->getId());
    }
}
