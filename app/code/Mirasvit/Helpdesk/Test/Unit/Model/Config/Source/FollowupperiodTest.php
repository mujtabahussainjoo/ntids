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


namespace Mirasvit\Helpdesk\Test\Unit\Model\Config\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * @covers \Mirasvit\Helpdesk\Model\Config\Source\Followupperiod
 * @SuppressWarnings(PHPMD)
 */
class FollowupperiodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config\Source\Followupperiod|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $followupperiodModel;


    /**
     * setup tests
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->followupperiodModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Config\Source\Followupperiod',
            [
            ]
        );
    }

    /**
     * dummy test
     */
    public function testDummy()
    {
        $this->assertEquals($this->followupperiodModel, $this->followupperiodModel);
    }
}
