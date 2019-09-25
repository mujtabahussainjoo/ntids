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
 * @covers \Mirasvit\Helpdesk\Helper\Report
 * @SuppressWarnings(PHPMD)
 */
class ReportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Report|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reportHelper;

    /**
     * @var \Mirasvit\Helpdesk\Model\DepartmentFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Department|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskDataMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->departmentFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\DepartmentFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->departmentMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Department',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->departmentFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->departmentMock));
        $this->configMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->dateMock = $this->getMock(
            '\Magento\Framework\Stdlib\DateTime\DateTime',
            [],
            [],
            '',
            false
        );
        $this->helpdeskDataMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Data',
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
        $this->localeDateMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Stdlib\DateTime\TimezoneInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->reportHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Report',
            [
                'departmentFactory' => $this->departmentFactoryMock,
                'config' => $this->configMock,
                'date' => $this->dateMock,
                'helpdeskData' => $this->helpdeskDataMock,
                'context' => $this->contextMock,
                'storeManager' => $this->storeManagerMock,
                'localeDate' => $this->localeDateMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->reportHelper, $this->reportHelper);
    }
}
