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



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Gateway;

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
        $this->resource = 'Mirasvit_Helpdesk::helpdesk_gateway';
        $this->uri = 'backend/helpdesk/gateway/save';
        parent::setUp();
    }

    /**
     * @magentoDataFixture Mirasvit/Helpdesk/_files/gateway.php
     *
     * @covers  Mirasvit\Helpdesk\Controller\Adminhtml\Gateway\Save::execute
     */
    public function testSaveAction()
    {
        $data = [
            'id' => 1,
            'name' => 'Edit Gateway',
            'email' => 'support2@mirasvit.com.ua',
            'login' => 'support2@mirasvit.com.ua',
            'password' => '6Vl5gxZmxpeE',
            'is_active' => '1',
            'host' => 'imap.gmail.com',
            'protocol' => 'imap',
            'encryption' => 'ssl',
            'port' => '993',
            'fetch_frequency' => '5',
            'fetch_max' => '10',
            'fetch_limit' => '1',
            'is_delete_emails' => '0',
            'store_id' => '1',
            'department_id' => '1'
        ];
        $this->getRequest()->setParams($data);
        $this->dispatch('backend/helpdesk/gateway/save');

        $this->assertSessionMessages(
            $this->equalTo(['Gateway was successfully saved. Connection is established.']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('helpdesk/gateway/index/'));

        /** @var \Mirasvit\Helpdesk\Model\Gateway $gateway */
        $gateway = $this->_objectManager->create('Mirasvit\Helpdesk\Model\Gateway')->load(1);
        $this->assertEquals($data['name'], $gateway->getName());
    }

    /**
     * @covers  Mirasvit\Helpdesk\Controller\Adminhtml\Gateway\Save::execute
     */
    public function testSaveNewAction()
    {
        $data = [
            'name' => 'New Gateway',
            'email' => 'support2@mirasvit.com.ua',
            'login' => 'support2@mirasvit.com.ua',
            'password' => '6Vl5gxZmxpeE',
            'is_active' => '1',
            'host' => 'imap.gmail.com',
            'protocol' => 'imap',
            'encryption' => 'ssl',
            'port' => '993',
            'fetch_frequency' => '5',
            'fetch_max' => '10',
            'fetch_limit' => '1',
            'is_delete_emails' => '0',
            'store_id' => '1',
            'department_id' => '1'
        ];
        $this->getRequest()->setParams($data);
        $this->dispatch('backend/helpdesk/gateway/save');

        $this->assertSessionMessages(
            $this->equalTo(['Gateway was successfully saved. Connection is established.']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('helpdesk/gateway/index/'));

        /** @var \Mirasvit\Helpdesk\Model\Gateway $gateway */
        $gateway = $this->_objectManager->create('Mirasvit\Helpdesk\Model\Gateway')->getCollection()->getLastItem();
        $this->assertEquals($data['name'], $gateway->getName());

        /** @var \Magento\Framework\App\ResourceConnection $installer */
        $installer = $this->_objectManager->create('Magento\Framework\App\ResourceConnection');
        //remove created gateway
        $installer->getConnection()->query(
            'DELETE FROM '.$installer->getTableName('mst_helpdesk_gateway').';'
        );
    }
}
