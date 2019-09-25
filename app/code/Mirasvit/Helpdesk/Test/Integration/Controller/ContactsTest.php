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

class ContactsTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * covers layout contact_index_index
     *
     * @magentoConfigFixture current_store helpdesk/contact_form/is_active_attachment 1
     * @magentoConfigFixture current_store helpdesk/contact_form/is_allow_priority 1
     *
     * @magentoConfigFixture current_store helpdesk/contact_form/is_active_kb 1
     * @magentoConfigFixture current_store helpdesk/contact_form/is_allow_department 1
     * @magentoDataFixture Mirasvit/Helpdesk/_files/schedule_always.php
     */
    public function testIndexAction()
    {
        ini_set('xdebug.max_nesting_level', 1000); //to fix this

        $this->dispatch('contact/index/index');

        $body = $this->getResponse()->getBody();

        $this->assertContains('Contact Information', $body);
        $this->assertContains('Subject', $body);
        $this->assertContains('Comment', $body);
        $this->assertContains('Attach files', $body);
        $this->assertContains('Department', $body);
        $this->assertContains('Priority', $body);
        $this->assertContains('Test. We are working:', $body);
        $this->assertNotContains('Test. We are closed for something', $body);
    }

    /**
     * covers layout contact_index_index
     *
     * @magentoDataFixture Mirasvit/Helpdesk/_files/schedule_custom.php
     */
    public function testCustomSchedule()
    {
        ini_set('xdebug.max_nesting_level', 1000); //to fix this

        $this->dispatch('contact/index/index');

        $body = $this->getResponse()->getBody();

        $this->assertContains('Test. We are closed', $body);
    }
}
