<?php

namespace MagePsycho\GroupSwitcherPro\Logger;

use Monolog\Logger;
use Magento\Framework\Logger\Handler\Base;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Handler extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/magepsycho_groupswitcherpro.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::INFO;
}