<?php
/**
 * @deprecated
 * TODO: remove.
 * Form textarea element
 */

namespace Infortis\UltraMegamenu\Block\Category\Attribute\Helper\Dropdown;

use Magento\Backend\Helper\Data as HelperData;
use Magento\Catalog\Block\Adminhtml\Helper\Form\Wysiwyg;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\Store;

class Blocks extends Wysiwyg
{
    /**
     * @var Registry
     */
    protected $_frameworkRegistry;

    /**
     * @var UrlInterface
     */
    protected $_frameworkUrlInterface;

    /**
     * @var Layout
     */
    protected $_modelLayout;

    protected $_helperData;
            
    public function __construct(
        Factory $factoryElement, 
        CollectionFactory $factoryCollection, 
        Escaper $escaper, 
        Config $wysiwygConfig, 
        LayoutInterface $layout, 
        Manager $moduleManager, 
        HelperData $backendData,  
        Registry $frameworkRegistry, 
        UrlInterface $frameworkUrlInterface,        
        array $data = []
        )
    {
        $this->_frameworkRegistry = $frameworkRegistry;
        $this->_frameworkUrlInterface = $frameworkUrlInterface;
        $this->_modelLayout = $layout;
        $this->_helperData = $backendData;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $wysiwygConfig, $layout, $moduleManager, $backendData, $data);
    }

	const DELIMITER = '#@#';
	const MAX_BLOCKS = 4;

	protected $_secondaryAttributes = ['title', 'rows', 'cols', 'readonly', 'disabled', 'tabindex'];

	protected $_labels = ['Top Block', 'Left Block', 'Right Block', 'Bottom Block'];

	public function getElementHtml()
	{
		$html = '';
		$id = $this->getHtmlId();
		$wrapperId = $id . '_units';
		$attributeValue = $this->getEscapedValue();

		//Prepare unit values
		$exploded = explode(self::DELIMITER, $attributeValue);
		$units = [];
		for ($i = 0; $i < self::MAX_BLOCKS; $i++)
		{
			if (isset($exploded[$i]))
			{
				$units[] = $exploded[$i];
			}
			else
			{
				$units[] = '';
			}
		}

		//Main field
		$this->addClass('textarea');
		$html .= '<textarea id="' . $id . '" name="'.$this->getName().'" '.$this->serialize($this->getHtmlAttributes()).' ';
		$html .= 'style="display:none;" ';
		$html .= '>';
		$html .= $attributeValue;
		$html .= "</textarea>";

		//Unit fields
		$html .= '<div id="' . $wrapperId . '" class="">';

			for ($i = 0; $i < self::MAX_BLOCKS; $i++)
			{
				$curFieldId = $id . '_' . ($i+1);
				$html .= '<label for="' . $curFieldId . '">' . $this->_labels[$i] . '</label>';
				$html .= '<textarea id="' . $curFieldId . '" '.$this->serialize($this->_secondaryAttributes).' ';
				$html .= 'class="textarea" ';
				$html .= 'style="height:8em;" ';
				$html .= '>';
				$html .= $units[$i];
				$html .= '</textarea>';
				$html .= $this->getAfterElementHtml($curFieldId);
				$html .= '<br/><br/>';
			}

		$html .= '</div>';

		//Scripts
		if (!$this->_frameworkRegistry->registry('infortis_admin_jquery'))
		{
            // $jqueryUrl = $this->_frameworkUrlInterface->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_JS) . 'infortis/jquery/jquery-for-admin.min.js';
            // $html .= '<script type="text/javascript" src="' . $jqueryUrl . '"></script>';
            // $html .= '<script type="text/javascript">jQuery.noConflict();</script>';
			$this->_frameworkRegistry->register('infortis_admin_jquery', 1);
		}
		$html .= '
		<script type="text/javascript">
		//<![CDATA[
			jQuery(function($) {
				var mainId				= \'#' . $id . '\';
				var fieldsWrapperId		= \'#' . $wrapperId . '\';
				var delimiter			= \'' . self::DELIMITER . '\';

				var onChange = function(e) {
					var target = $(e.target);
					var output = "";

					target.addClass("modified");

					//Compile
					$(fieldsWrapperId + " textarea").each(function() {
						output += $.trim($(this).val()) + delimiter;
					});
					$(mainId).val(output);
				}

				$(fieldsWrapperId).on("focus change", "textarea", function(e) {
					onChange(e);
				});
				
			}); //end: on document ready
		//]]>
		</script>
		';

		return $html;
	}

	/**
	 * Additional html: put it at the end of each element html
	 *
	 * @return string
	 */
// 	public function getAfterElementHtml($fieldId=null)
// 	{
// 		//$html = parent::getAfterElementHtml();
// 		$html = '';
// 		if ($this->getIsWysiwygEnabled()) {
// 			$disabled = ($this->getDisabled() || $this->getReadonly());
// 			$html .= $this->_modelLayout
// 				->createBlock('Magento\Backend\Block\Widget\Button', '', 
// 					[
// 						'label'		=> __('WYSIWYG Editor'),
// 						'type'		=> 'button',
// 						'disabled'	=> $disabled,
// 						'class'		=> ($disabled) ? 'disabled btn-wysiwyg' : 'btn-wysiwyg',
// 						'onclick'	=> 'catalogWysiwygEditor.open(\''.$this->_helperData->getUrl('*/*/wysiwyg').'\', \'' . $fieldId . '\')'
// 					]
// 				)->toHtml();
// 		}
// 		return $html;
// 	}
}