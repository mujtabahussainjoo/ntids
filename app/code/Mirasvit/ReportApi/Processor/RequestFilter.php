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
 * @package   mirasvit/module-report-api
 * @version   1.0.16
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Processor;

use Magento\Framework\Api\AbstractSimpleObject;
use Mirasvit\ReportApi\Api\Processor\RequestFilterInterface;

class RequestFilter extends AbstractSimpleObject implements RequestFilterInterface
{
    const COLUMN         = 'column';
    const VALUE          = 'value';
    const CONDITION_TYPE = 'condition_type';
    const GROUP          = 'group';

    public function setColumn($column)
    {
        return $this->setData(self::COLUMN, $column);
    }

    public function getColumn()
    {
        return $this->_get(self::COLUMN);
    }

    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    public function getValue()
    {
        return $this->_get(self::VALUE);
    }

    public function setConditionType($type)
    {
        return $this->setData(self::CONDITION_TYPE, $type);
    }

    public function getConditionType()
    {
        return $this->_get(self::CONDITION_TYPE);
    }

    public function setGroup($group)
    {
        return $this->setData(self::GROUP, $group);
    }

    public function getGroup()
    {
        return $this->_get(self::GROUP);
    }

    public function __toString()
    {
        return \Zend_Json::encode($this->__toArray());
    }
}
