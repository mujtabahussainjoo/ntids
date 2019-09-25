<?php

namespace Serole\OvernightUpload\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;

/**
 * Custom Attribute Renderer
 *
 * @author      Webkul Core Team <support@webkul.com>
 */
class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var OptionFactory
     */

    protected $optionFactory;


    protected $partnerCodes;

    /**
     * @param OptionFactory $optionFactory
     */
    public function __construct(\Serole\OvernightUpload\Model\Grid $partnerCodes,
                                OptionFactory $optionFactory)
    {
        $this->optionFactory = $optionFactory;
        $this->partnerCodes = $partnerCodes;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        /* your Attribute options list*/
        $partnercollection = $this->partnerCodes->getCollection();
		$this->_options[] = ['label' => "Select",'value' => ''];
        foreach ($partnercollection->getData() as $item){
           $this->_options[] = ['label' => $item['company_name'],'value' => $item['entity_id']];
        }
        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        return [
            $attributeCode => [
                'unsigned' => false,
                'default' => null,
                'extra' => null,
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Custom Attribute Options  ' . $attributeCode . ' column',
            ],
        ];
    }
}