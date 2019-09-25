<?php
namespace Serole\Racparkpasses\Block\Adminhtml\Racparkpass\Renderer;
 
use Magento\Framework\DataObject;
 
class Itemoption extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
	protected $_serialize;
	
	public function __construct(\Magento\Framework\Serialize\Serializer\Json $serialize) {
       $this->_serialize = $serialize;
    }
   
    /**
     * get option
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
       $search = $this->getColumn()->getOptionLabel();
		
		$optionData = $row->getData($this->getColumn()->getIndex());
		if ($optionData){
			$options = $this->_serialize->unserialize($optionData);

			if (isset($options['options'])){
				
				foreach($options['options'] as $option){
				
					if (substr($option['label'], 0, strlen($search)) === $search){
						$optionValue = $option['value'];
						return $optionValue;
					}
				}
			}
		}
		return '';
    }
}
?>