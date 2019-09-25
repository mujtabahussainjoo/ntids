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



namespace Mirasvit\Helpdesk\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * @covers \Mirasvit\Helpdesk\Helper\Notification
 * @SuppressWarnings(PHPMD)
 */
class NotificationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Notification|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $notificationHelper;

    /**
     * @var \Magento\User\Model\UserFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userFactoryMock;

    /**
     * @var \Magento\User\Model\User|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userMock;

    /**
     * @var \Magento\Email\Model\TemplateFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailTemplateFactoryMock;

    /**
     * @var \Magento\Email\Model\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailTemplateMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Core\Model\Translate|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $translateMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Ruleevent|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskRuleeventMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Email|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskEmailMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Framework\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetRepoMock;

    /**
     * @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Magento\Framework\View\DesignInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $designMock;

    /**
     * @var \Magento\Backend\Model\Auth|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->userFactoryMock = $this->getMock(
            '\Magento\User\Model\UserFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->userMock = $this->getMock(
            '\Magento\User\Model\User',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->userFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->userMock));
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
        $this->configMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->translateMock = $this->getMock(
            '\Mirasvit\Core\Model\Translate',
            [],
            [],
            '',
            false
        );
        $this->helpdeskRuleeventMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Ruleevent',
            [],
            [],
            '',
            false
        );
        $this->helpdeskEmailMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Email',
            [],
            [],
            '',
            false
        );
        $this->storeManagerMock = $this->getMockForAbstractClass(
            '\Magento\Store\Model\StoreManagerInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->assetRepoMock = $this->getMock(
            '\Magento\Framework\View\Asset\Repository',
            [],
            [],
            '',
            false
        );
        $this->filesystemMock = $this->getMock(
            '\Magento\Framework\Filesystem',
            [],
            [],
            '',
            false
        );
        $this->designMock = $this->getMockForAbstractClass(
            '\Magento\Framework\View\DesignInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->authMock = $this->getMock(
            '\Magento\Backend\Model\Auth',
            [],
            [],
            '',
            false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->notificationHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Notification',
            [
                'userFactory' => $this->userFactoryMock,
                'emailTemplateFactory' => $this->emailTemplateFactoryMock,
                'config' => $this->configMock,
                'translate' => $this->translateMock,
                'helpdeskRuleevent' => $this->helpdeskRuleeventMock,
                'helpdeskEmail' => $this->helpdeskEmailMock,
                'context' => $this->contextMock,
                'storeManager' => $this->storeManagerMock,
                'assetRepo' => $this->assetRepoMock,
                'filesystem' => $this->filesystemMock,
                'design' => $this->designMock,
                'auth' => $this->authMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->notificationHelper, $this->notificationHelper);
    }
}
