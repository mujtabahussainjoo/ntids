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

class SurchargeConfig extends \Magento\Framework\DataObject
{
    /**
     * @var array
     */
    private $surchargeBasis;

    /**
     * @var array
     */
    private $groups;

    /**
     * @var array
     */
    private $countries;

    /**
     * @var array
     */
    private $regions;

    /**
     * @return array
     */
    public function getSurchargeBasis()
    {
        if (null === $this->surchargeBasis) {
            $surchargeBasis = $this->getData('based_on');
            if (empty($surchargeBasis)) {
                $this->surchargeBasis = [System\SurchargeBasis::BASED_ON_SUBTOTAL];
            } elseif (is_array($surchargeBasis)) {
                $this->surchargeBasis = $surchargeBasis;
            } else {
                $this->surchargeBasis = explode(',', $surchargeBasis);
            }
        }
        return $this->surchargeBasis;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        if (null === $this->groups) {
            $groups = $this->getData('groups');
            if (empty($groups)) {
                $this->groups = [];
            } elseif (is_array($groups)) {
                $this->groups = $groups;
            } else {
                $this->groups = explode(',', $groups);
            }
        }
        return $this->groups;
    }

    /**
     * @return array
     */
    public function getCountries()
    {
        if (null === $this->countries) {
            $countries = $this->getData('countries');
            if (empty($countries)) {
                $this->countries = [];
            } elseif (is_array($countries)) {
                $this->countries = $countries;
            } else {
                $this->countries = explode(',', $countries);
            }
        }
        return $this->countries;
    }

    /**
     * @return array
     */
    public function getRegions()
    {
        if (null === $this->regions) {
            $regions = $this->getData('regions');
            if (empty($regions)) {
                $this->regions = [];
            } elseif (is_array($regions)) {
                $this->regions = $regions;
            } else {
                $this->regions = explode(',', $regions);
            }
        }
        return $this->regions;
    }
}
