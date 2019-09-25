<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Rule\Action;

use Magento\Framework\ObjectManagerInterface as ObjectManager;

class RateFactory
{

    /**
     * Object manager
     *
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Instance name to create
     *
     * @var array
     */
    protected $map;

    /** @var string */
    private $type;

    /** @var string */
    private $realType;

    /** @var string */
    private $calculationMethod;

    /** @var string */
    private $applyMethod;


    /**
     * @param ObjectManager $objectManager
     * @param array $map
     */
    public function __construct(ObjectManager $objectManager, array $map = [])
    {
        $this->map = $map;
        $this->objectManager = $objectManager;
    }

    /**
     * Create calculator
     *
     * @param string $type
     * @param array $arguments
     * @return Rate\RateInterface
     */
    public function create($type, array $arguments = [])
    {
        $this->type = $type;

        $this->prepareType();
        $validator = $this->objectManager->create('\MageWorx\ShippingRules\Model\Rule\Action\RateFactoryValidator');
        $validator->validate($this->map, $this->type, $this->realType);

        $class = $this->getClass();

        $arguments = array_merge($arguments, [
            'amountType' => $this->type,
            'applyMethod' => $this->applyMethod,
            'calculationMethod' => $this->calculationMethod
        ]);

        /** @var \MageWorx\ShippingRules\Model\Rule\Action\Rate\AbstractRate $calculator */
        $calculator = $this->objectManager->create($class, $arguments);

        return $calculator;
    }

    /**
     * Prepare input data
     *
     * @return $this
     */
    private function prepareType()
    {
        $preparedType = explode('_', $this->type);

        $realType = array_pop($preparedType);
        $applyMethod = array_pop($preparedType);
        $calculationMethod = array_pop($preparedType);

        $this->realType = $realType;
        $this->applyMethod = $applyMethod;
        $this->calculationMethod = $calculationMethod;

        return $this;
    }

    /**
     * Get class from the array of available classes using real type
     *
     * @return mixed
     */
    private function getClass()
    {
        return $this->map[$this->realType];
    }
}
