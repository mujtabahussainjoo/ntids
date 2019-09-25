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



class Mirasvit_Helpdesk_Helper_TagTest extends \PHPUnit\Framework\TestCase
{
    /** @var  \Mirasvit\Helpdesk\Helper\Tag */
    protected $helper;

    /**
     * setUp.
     */
    public function setUp()
    {
        $this->helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Mirasvit\Helpdesk\Helper\Tag');
    }

    /**
     * @magentoDataFixture Mirasvit/Helpdesk/_files/ticket.php
     */
    public function test()
    {
        $ticket = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Mirasvit\Helpdesk\Model\Ticket');

        $ticket->load(1);

        $this->helper->addTags($ticket, 'aaa, bbb');
        $this->helper->addTags($ticket, 'ccc');
        $this->assertEquals('aaa, bbb, ccc', $this->helper->getTagsAsString($ticket));
        $this->helper->removeTags($ticket, 'aaa, ccc');
        $this->assertEquals('bbb', $this->helper->getTagsAsString($ticket));
    }
}
