<?php
namespace Serole\SageAttributesDisable\Ui\DataProvider\Product\Form\Modifier;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

class Attributes extends AbstractModifier
{
    private $arrayManager;

    public function __construct(ArrayManager $arrayManager)
    {
        $this->arrayManager = $arrayManager;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $attributes = array('vendor_email_address','sage_synced_date','supplier_code','is_stock_item','isserializeditem','backtoback','enable_vendor_email','sku','price','weight','product_has_weight','tax_class_id','quantity_and_stock_status'); // Your attribute code goes here
        foreach ($attributes as $attribute) {
            //if($attribute!=''){
        	   $path = $this->arrayManager->findPath($attribute, $meta, null, 'children');
        	   $meta = $this->arrayManager->set("{$path}/arguments/data/config/disabled",$meta,true);
             //}
        }
        return $meta;
    }
}