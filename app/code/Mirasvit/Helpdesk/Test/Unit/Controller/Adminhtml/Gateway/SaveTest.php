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



namespace Mirasvit\Helpdesk\Test\Unit\Controller\Adminhtml\Gateway;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Helpdesk\Controller\Adminhtml\Gateway\Save
 * @SuppressWarnings(PHPMD)
 */
class SaveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Controller\Adminhtml\Gateway\Save|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayController;

    /**
     * @var \Mirasvit\Helpdesk\Model\GatewayFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Gateway|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayMock;

    /**
     * @var \Mirasvit\Core\Helper\Date|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mstcoreDateMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Fetch|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskFetchMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Checkenv|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskCheckenvMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendSessionMock;

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
        $this->gatewayFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\GatewayFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->gatewayMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Gateway',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->gatewayFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->gatewayMock));
        $this->mstcoreDateMock = $this->getMock(
            '\Mirasvit\Core\Helper\Date',
            [],
            [],
            '',
            false
        );
        $this->helpdeskFetchMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Fetch',
            [],
            [],
            '',
            false
        );
        $this->helpdeskCheckenvMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Checkenv',
            [],
            [],
            '',
            false
        );
        $this->localeDateMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Stdlib\DateTime\TimezoneInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->registryMock = $this->getMock(
            '\Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->backendSessionMock = $this->getMock(
            '\Magento\Backend\Model\Session',
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
        $this->gatewayController = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Controller\Adminhtml\Gateway\Save',
            [
                'gatewayFactory' => $this->gatewayFactoryMock,
                'mstcoreDate' => $this->mstcoreDateMock,
                'helpdeskFetch' => $this->helpdeskFetchMock,
                'helpdeskCheckenv' => $this->helpdeskCheckenvMock,
                'localeDate' => $this->localeDateMock,
                'registry' => $this->registryMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->gatewayController, $this->gatewayController);
    }
}
