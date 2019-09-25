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
use Mirasvit\ReportApi\Api\Processor\ResponseColumnInterface;

class ResponseColumn extends AbstractSimpleObject implements ResponseColumnInterface
{
    const NAME  = 'name';
    const LABEL = 'label';
    const TYPE  = 'type';

    public function toArray()
    {
        return [
            self::NAME  => $this->getName(),
            self::LABEL => $this->getLabel(),
            self::TYPE  => $this->getType(),
        ];
    }

    public function getName()
    {
        return $this->_get(self::NAME);
    }

    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    public function getType()
    {
        return $this->_get(self::TYPE);
    }
}
