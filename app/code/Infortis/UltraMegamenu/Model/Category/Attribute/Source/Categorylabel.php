<?php

namespace Infortis\UltraMegamenu\Model\Category\Attribute\Source;

use Infortis\UltraMegamenu\Helper\Data as HelperData;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Categorylabel extends AbstractSource
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    public function __construct(HelperData $helperData)
    {
        $this->_helperData = $helperData;

    }

	protected $_options;
	
	/**
     * Get list of existing category labels
     */
	public function getAllOptions()
	{
		$h = $this->_helperData;
		
		if (!$this->_options)
		{	
			$this->_options[] =
					['value' => '', 'label' => " "];
					
			if ($tmp = trim($h->getCfg('category_labels/label1')))
			{
				$this->_options[] =
					['value' => 'label1', 'label' => $tmp];
			}
			if ($tmp = trim($h->getCfg('category_labels/label2')))
			{
				$this->_options[] =
					['value' => 'label2', 'label' => $tmp];
			}
        }
        return $this->_options;
    }
}
