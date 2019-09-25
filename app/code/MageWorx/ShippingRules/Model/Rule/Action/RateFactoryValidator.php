<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Rule\Action;

use MageWorx\ShippingRules\Model\Rule;

class RateFactoryValidator
{

    /**
     * Do some important validation before trying to create a new object
     *
     * @param array $map
     * @param string $type
     * @param string $realType
     * @return $this
     */
    public function validate(array $map, $type, $realType)
    {
        $matrix = Rule::getCalculationMatrix();
        if (!in_array($type, $matrix)) {
            throw new \InvalidArgumentException($type . ' is unavailable type');
        }

        if (!isset($map[$realType])) {
            throw new \InvalidArgumentException($realType . ' is unknown type');
        }

        return $this;
    }
}
