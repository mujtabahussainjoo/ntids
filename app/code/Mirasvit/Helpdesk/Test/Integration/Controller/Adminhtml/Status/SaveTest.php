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
class SaveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Helpdesk::helpdesk_status';
        $this->uri = 'backend/helpdesk/status/save';
        parent::setUp();
    }

    /**
     * @magentoDataFixture Mirasvit/Helpdesk/_files/status.php
     *
     * @covers  Mirasvit\Helpdesk\Controller\Adminhtml\Status\Save::execute
     */
    public function testSaveAction()
    {
        $data = [
            'id' => 5,
            'name' => 'Edit Status',
            'color' => 'Red',
            'sort_order' => '77',
            'store_ids' => [0]
        ];
        $this->getRequest()->setParams($data);
        $this->dispatch('backend/helpdesk/status/save');

        $this->assertSessionMessages(
            $this->equalTo(['Status was successfully saved']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('helpdesk/status/index/'));

        /** @var \Mirasvit\Helpdesk\Model\Status $status */
        $status = $this->_objectManager->create('Mirasvit\Helpdesk\Model\Status')->load(5);
        $this->assertEquals($data['name'], $status->getName());
        $this->assertEquals($data['color'], $status->getColor());
        $this->assertEquals($data['sort_order'], $status->getSortOrder());
        $this->assertEquals($data['store_ids'], $status->getStoreIds());

    }

    /**
     * @covers  Mirasvit\Helpdesk\Controller\Adminhtml\Status\Save::execute
     */
    public function testSaveNewAction()
    {
        $data = [
            'name' => 'New Status',
            'color' => 'Red',
            'sort_order' => '77',
            'store_ids' => [0]
        ];
        $this->getRequest()->setParams($data);
        $this->dispatch('backend/helpdesk/status/save');

        $this->assertSessionMessages(
            $this->equalTo(['Status was successfully saved']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('helpdesk/status/index/'));

        /** @var \Mirasvit\Helpdesk\Model\Status $status */
        $status = $this->_objectManager->create('Mirasvit\Helpdesk\Model\Status')->getCollection()->getLastItem();
        $this->assertEquals($data['name'], $status->getName());
    }
}
