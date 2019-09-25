<?php

namespace Infortis\UltraMegamenu\Model\Category\Attribute\Backend\Grid;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\DataObject;
use Magento\Framework\Message\ManagerInterface;

class Columns extends AbstractBackend
{
    /**
     * @var ManagerInterface
     */
    protected $messageManagerInterface;

    /**
     * Construct
     *
     * @param ManagerInterface
     */
    public function __construct(
        ManagerInterface $messageManagerInterface
    ) {
        $this->messageManagerInterface = $messageManagerInterface;
    }

    /**
     * Before save method
     *
     * @param DataObject $object
     * @return AbstractBackend
     */
    public function beforeSave($object)
    {
        $delimiter = ';';
        $maxColumns = 3;
        $maxUnitValue = 12;
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeValue = $object->getData($attributeCode);

        if ($attributeValue)
        {
            $outputNumbers = [];
            $sum = 0;
            $exploded = explode($delimiter, $attributeValue);

            for ($i = 0; $i < $maxColumns; $i++)
            {
                if (isset($exploded[$i]))
                {
                    $number = intval($exploded[$i]);
                    $outputNumbers[$i] = $number;
                    $sum += $number;
                }
            }

            if ($sum === 0)
            {
                // If sum of all units equals 0, don't save
                $object->unsetData($attributeCode);
            }
            else
            {
                // Format the final value
                $finalOutput = '';
                for ($i = 0; $i < $maxColumns; $i++)
                {
                    if (isset($outputNumbers[$i]))
                    {
                        $finalOutput .= $outputNumbers[$i] . $delimiter;
                    }
                    else
                    {
                        $finalOutput .= '0' . $delimiter;
                    }
                }

                // Save formatted value
                $object->setData($attributeCode, $finalOutput);
            }

            // Show message
            if ($sum !== $maxUnitValue)
            {
                if ($sum < $maxUnitValue)
                {
                    $sumMessage = __('<br/>Your sum is smaller than %1.', $maxUnitValue);
                }
                else
                {
                    $sumMessage = __('<br/>Your sum is larger than %1.', $maxUnitValue);
                }

                $this->messageManagerInterface->addError(
                    __('"Drop-down Content Proportions" field: sum of the numbers entered for all %1 sections has to be equal to %2.', $maxColumns, $maxUnitValue)
                    . $sumMessage
                );
            }
        }

        return parent::beforeSave($object);
    }
}
