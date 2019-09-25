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



namespace Mirasvit\Helpdesk\Test\Unit\Console\Command;

use Symfony\Component\Console\Tester\CommandTester;

class HelpdeskCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Mirasvit\Helpdesk\Console\Command\HelpdeskCommand::execute
     */
    public function testExecute()
    {
        $objectManagerFactory = $this->getMock('Magento\Framework\App\ObjectManagerFactory', [], [], '', false);
        $objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface', [], [], '', false);
        $state = $this->getMock('Magento\Framework\App\State', [], [], '', false);
        $cron = $this->getMock('Mirasvit\Helpdesk\Model\Cron', [], [], '', false);
        $objectManager->expects($this->once())->method('create')->willReturn($cron);
        $objectManager->expects($this->once())->method('get')->willReturn($state);
        $cron->expects($this->once())->method('shellCronRun');
        $objectManagerFactory->expects($this->once())->method('create')->willReturn($objectManager);
        $commandTester = new CommandTester(
            new \Mirasvit\Helpdesk\Console\Command\HelpdeskCommand($objectManagerFactory)
        );
        $commandTester->execute([]);
        $expectedMsg = 'Ran helpdesk jobs.' . PHP_EOL;
        $this->assertEquals($expectedMsg, $commandTester->getDisplay());
    }
}
