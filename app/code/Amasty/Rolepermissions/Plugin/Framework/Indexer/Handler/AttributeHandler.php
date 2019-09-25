<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\Framework\Indexer\Handler;

use Magento\Framework\App\ResourceConnection\SourceProviderInterface;

class AttributeHandler extends \Magento\Framework\Indexer\Handler\AttributeHandler
{
    /**
     * Fix for indexing of customer grid
     * @param \Magento\Framework\Indexer\Handler\AttributeHandler $subject
     * @param \Closure $closure
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $source
     * @param string $alias
     * @param array $fieldInfo
     */
    public function aroundPrepareSql($subject, $closure, $source, $alias, $fieldInfo)
    {
        if ($source instanceof \Magento\Customer\Model\Indexer\Source
            && !isset($fieldInfo['bind'])
        ) {
            $fieldInfo['bind'] = '';
            $source->addFieldToSelect($fieldInfo['origin'], $alias);
            return;
        }
        $closure($source, $alias, $fieldInfo);
    }
}
