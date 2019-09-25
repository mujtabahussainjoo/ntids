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


namespace Mirasvit\Helpdesk\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * @covers \Mirasvit\Helpdesk\Model\Search
 * @SuppressWarnings(PHPMD)
 */
class SearchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Search|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchModel;

    /**
     * @var \Mirasvit\Core\Api\TextHelperInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreStringMock;

    /**
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dbResourceMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;


    /**
     * setup tests
     */
    public function setUp()
    {
        $this->coreStringMock = $this->getMock(
            '\Mirasvit\Core\Api\TextHelperInterface',
            [],
            [],
            '',
            false
        );
        $this->dbResourceMock = $this->getMock(
            '\Magento\Framework\App\ResourceConnection',
            [],
            [],
            '',
            false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->searchModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Search',
            [
                'coreString' => $this->coreStringMock,
                'dbResource' => $this->dbResourceMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test
     */
    public function testDummy()
    {
        $this->assertEquals($this->searchModel, $this->searchModel);
    }
}
