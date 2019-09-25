<?php
namespace Infortis\UltraMegamenu\Plugin\Magento\Catalog\Model\Category;

use Magento\Eav\Model\Config;

class DataProvider
{
    //function beforeMETHOD($subject, $arg1, $arg2){}
    //function aroundMETHOD($subject, $procede, $arg1, $arg2){return $proceed($arg1, $arg2);}
    //function afterMETHOD($subject, $result){return $result;}
    protected $eavConfig;
    public function __construct(
        Config $eavConfig)
    {
        $this->eavConfig = $eavConfig;
    }
    
    public function afterPrepareMeta($subject, $meta)
    {           
        $meta = array_replace_recursive($meta, $this->prepareFieldsMeta(
            $this->getFieldsMap(),
            $subject->getAttributesMeta($this->eavConfig->getEntityType('catalog_category'))
        ));

        return $meta;            
    }  
    
    public function getFieldsMap()
    {
        return [
            'menu' => [
                'umm_dd_type',
                'umm_dd_width',
                'umm_dd_proportions',
                'umm_dd_columns',
                'umm_dd_block_top',
                'umm_dd_block_left',
                'umm_dd_block_right',
                'umm_dd_block_bottom',
                'umm_cat_label',
                'umm_cat_target',
            ],
        ];
    }
    
    private function prepareFieldsMeta($fieldsMap, $fieldsMeta)
    {
        $result = [];
        foreach ($fieldsMap as $fieldSet => $fields) {
            foreach ($fields as $field) {
                if (isset($fieldsMeta[$field])) {
                    $result[$fieldSet]['children'][$field]['arguments']['data']['config'] = $fieldsMeta[$field];
                }
            }
        }
        return $result;
    }
}
