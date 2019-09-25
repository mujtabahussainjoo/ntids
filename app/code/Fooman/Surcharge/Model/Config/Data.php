<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Model\Config;

class Data extends \Magento\Framework\Config\Data
{
    public function __construct(
        \Fooman\Surcharge\Model\Config\Reader $reader,
        \Magento\Framework\Config\CacheInterface $cache,
        $cacheId = 'fooman_surcharge_config_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }
}
