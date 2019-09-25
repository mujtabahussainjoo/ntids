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
class EditTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Helpdesk::helpdesk_status';
        $this->uri = 'backend/helpdesk/status/edit';
        parent::setUp();
    }

    /**
     * @magentoDataFixture Mirasvit/Helpdesk/_files/status.php
     *
     * @covers  Mirasvit\Helpdesk\Controller\Adminhtml\Status\Edit::execute
     */
    public function testEditAction()
    {
        $this->getRequest()->setParam('id', 5);
        $this->dispatch('backend/helpdesk/status/edit');
        $body = $this->getResponse()->getBody();
        $this->assertContains('<h1 class="page-title">Edit Status \'Custom Status\'</h1>', $body);
        ;
    }
}
