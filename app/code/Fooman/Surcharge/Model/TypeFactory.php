<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Model;

class TypeFactory
{

    private $config;

    private $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Fooman\Surcharge\Model\Config $config
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
    }

    public function get($type)
    {
        $info = $this->config->getType($type);

        if (!empty($info['instance']) && class_exists($info['instance'])) {
            return $this->objectManager->create($info['instance']);
        }

        return null;
    }
}
