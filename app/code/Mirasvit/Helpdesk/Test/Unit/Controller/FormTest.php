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



namespace Mirasvit\Helpdesk\Test\Unit\Controller;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * @covers \Mirasvit\Helpdesk\Controller\Form
 * @SuppressWarnings(PHPMD)
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Controller\Form|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formController;

    /**
     * @var \Magento\Email\Model\TemplateFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailTemplateFactoryMock;

    /**
     * @var \Magento\Email\Model\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailTemplateMock;

    /**
     * @var \Mirasvit\Core\Model\Translate|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $translateMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Field|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskFieldMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Process|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskProcessMock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Framework\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\View\Result\PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirectMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->emailTemplateFactoryMock = $this->getMock(
            '\Magento\Email\Model\TemplateFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->emailTemplateMock = $this->getMock(
            '\Magento\Email\Model\Template',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->emailTemplateFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->emailTemplateMock));
        $this->translateMock = $this->getMock(
            '\Mirasvit\Core\Model\Translate',
            [],
            [],
            '',
            false
        );
        $this->configMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->helpdeskFieldMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Field',
            [],
            [],
            '',
            false
        );
        $this->helpdeskProcessMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Process',
            [],
            [],
            '',
            false
        );
        $this->scopeConfigMock = $this->getMockForAbstractClass(
            '\Magento\Framework\App\Config\ScopeConfigInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->customerSessionMock = $this->getMock(
            '\Magento\Customer\Model\Session',
            [],
            [],
            '',
            false
        );
        $this->requestMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\RequestInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->resultFactoryMock = $this->getMock(
            'Magento\Framework\Controller\ResultFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resultPageMock = $this->getMock('Magento\Backend\Model\View\Result\Page', [], [], '', false);
        $this->resultFactoryMock->expects($this->any())
           ->method('create')
           ->willReturn($this->resultPageMock);

        $this->redirectMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\Response\RedirectInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->messageManagerMock = $this->getMockForAbstractClass(
            'Magento\Framework\Message\ManagerInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->getMock('\Magento\Backend\App\Action\Context', [], [], '', false);
        $this->contextMock->expects($this->any())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())->method('getObjectManager')->willReturn($this->objectManager);
        $this->contextMock->expects($this->any())->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->any())->method('getRedirect')->willReturn($this->redirectMock);
        $this->contextMock->expects($this->any())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->formController = $this->getMockForAbstractClass(
            '\Mirasvit\Helpdesk\Controller\Form',
            [
                'emailTemplateFactory' => $this->emailTemplateFactoryMock,
                'translate' => $this->translateMock,
                'config' => $this->configMock,
                'helpdeskField' => $this->helpdeskFieldMock,
                'helpdeskProcess' => $this->helpdeskProcessMock,
                'scopeConfig' => $this->scopeConfigMock,
                'customerSession' => $this->customerSessionMock,
                'context' => $this->contextMock,
            ],
            '',
            false,
            true,
            true,
            []
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->formController, $this->formController);
    }
}
