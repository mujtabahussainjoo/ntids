<?php

namespace Infortis\Brands\Model\System\Config\Source;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Brands
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_configScopeConfigInterface;

    /**
     * @var Config
     */
    protected $_modelConfig;

    public function __construct(ScopeConfigInterface $configScopeConfigInterface, 
        Config $modelConfig)
    {
        $this->_configScopeConfigInterface = $configScopeConfigInterface;
        $this->_modelConfig = $modelConfig;

    }

	protected $_options;

	public function toOptionArray()
	{
		if (!$this->_options)
		{
			$attributeCode = $this->_configScopeConfigInterface->getValue('brands/general/attr_id');
			$attributeModel = $this->_modelConfig
				->getAttribute('catalog_product', $attributeCode);
				
			/* Important:
			getAllOptions ([bool $withEmpty = true], [bool $defaultValues = false])
				- bool $withEmpty: Add empty option to array
				- bool $defaultValues: Return default values
			*/
			$this->_options = [];
			foreach ($attributeModel->getSource()->getAllOptions(false, true) as $o)
			{
				$this->_options[] =
					['value' => $o['label'], 'label' => $o['label']];
			}
		}
		return $this->_options;
	}
}