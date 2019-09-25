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
use Mirasvit\ReportApi\Api\Processor\RequestSortOrderInterface;

class RequestSortOrder extends AbstractSimpleObject implements RequestSortOrderInterface
{
    const COLUMN    = 'column';
    const DIRECTION = 'direction';

    public function setColumn($column)
    {
        return $this->setData(self::COLUMN, $column);
    }

    public function getColumn()
    {
        return $this->_get(self::COLUMN);
    }

    public function setDirection($direction)
    {
        return $this->setData(self::DIRECTION, $direction);
    }

    public function getDirection()
    {
        return $this->_get(self::DIRECTION);
    }

    public function __toString()
    {
        return \Zend_Json::encode($this->__toArray());
    }
}
