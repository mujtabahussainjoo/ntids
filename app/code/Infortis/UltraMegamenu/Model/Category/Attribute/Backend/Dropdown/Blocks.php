<?php

namespace Infortis\UltraMegamenu\Model\Category\Attribute\Backend\Dropdown;

use Infortis\UltraMegamenu\Block\Category\Attribute\Helper\Dropdown\Blocks as DropdownBlocks;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\DataObject;

class Blocks 
	extends AbstractBackend
{
	/**
	 * Before save method
	 *
	 * @param DataObject $object
	 * @return AbstractBackend
	 */
	public function beforeSave($object)
	{
		$attributeCode = $this->getAttribute()->getAttributeCode();
		$attributeValue = $object->getData($attributeCode);
		$delimiter = DropdownBlocks::DELIMITER;
		$maxBlocks = DropdownBlocks::MAX_BLOCKS;

		if ($attributeValue)
		{
			//To make parsing simpler, replace value with empty string if all blocks are empty
			$exploded = explode($delimiter, $attributeValue);
			$allBlocksAreEmpty = TRUE;
			for ($i = 0; $i < $maxBlocks; $i++)
			{
				if (isset($exploded[$i]))
				{
					if (trim($exploded[$i]))
					{
						//Block is not empty after trimming
						$allBlocksAreEmpty = FALSE;
						break;
					}
				}
			}

			if ($allBlocksAreEmpty)
			{
				$object->setData($attributeCode, ''); //Set empty value
			}
		}

		return parent::beforeSave($object);
	}
}
