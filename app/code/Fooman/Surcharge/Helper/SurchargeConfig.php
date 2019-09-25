<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Helper;

class SurchargeConfig
{

    /**
     * @var \Fooman\Surcharge\Model\SurchargeConfigFactory
     */
    private $surchargeConfigFactory;

    /**
     * @param \Fooman\Surcharge\Model\SurchargeConfigFactory $surchargeConfigFactory
     */
    public function __construct(
        \Fooman\Surcharge\Model\SurchargeConfigFactory $surchargeConfigFactory
    ) {
        $this->surchargeConfigFactory = $surchargeConfigFactory;
    }

    /**
     * @param  \Fooman\Surcharge\Api\SurchargeInterface $surcharge
     * @return \Fooman\Surcharge\Model\SurchargeConfig
     */
    public function getConfig(\Fooman\Surcharge\Api\SurchargeInterface $surcharge)
    {
        $data = json_decode($surcharge->getDataRule(), true);
        return $this->surchargeConfigFactory
            ->create(['data' => $data]);
    }
}
