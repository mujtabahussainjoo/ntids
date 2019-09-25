<?php

namespace Infortis\Infortis\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\UrlInterface;
//use Infortis\Base\Helper\Design as HelperDesign;

class Tex extends Field
{
    const TEX_PREVIEW_SUFFIX = '-tex-preview';

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Theme design helper
     *
     * @var HelperDesign
     */
    //protected $helperDesign;

    public function __construct(
        Context $context, 
        array $data = []
    ) {
        $this->_urlBuilder = $context->getUrlBuilder();
        //$this->$helperDesign = $helperDesign;

        parent::__construct($context, $data);
    }

    /**
     * Add pattern preview
     *
     * @param AbstractElement $element
     * @return String
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        //$elementOriginalData = $element->getOriginalData();

        $texPath = 'images/tex/';
        $initialTexName = '0.png';
        $html = $element->getElementHtml(); //Default HTML
        
        // $initialTexUrl = $this->$helperDesign->getPatternUrl(
        //     $initialTexName,
        //     ['area' => 'frontend', 'theme' => 'Infortis/base']
        // );

        $initialTexUrl = $this->_assetRepo->getUrlWithParams(
            $texPath . $element->getEscapedValue() . '.png',
            ['area' => 'frontend', 'theme' => 'Infortis/base']
        );
        //$initialTexUrl = $this->_urlBuilder->getBaseUrl() . $texPath;
        $texBaseUrl = str_ireplace($element->getEscapedValue() . '.png', '', $initialTexUrl);
        
        //Recreate ID of the background color picker which is related with this pattern field.
        //From the pattern selector field ID get the identifier suffix which begins with '_tex'
        $fieldIdSuffix = strstr($element->getHtmlId(), '_tex');
        
        //Replace the suffix with a new suffix for the background color picker in the current options group
        $bgcolorPickerId = str_replace($fieldIdSuffix, '_bg_color', $element->getHtmlId());
        
        //Create ID of the pattern preview box
        $previewId = $element->getHtmlId() . self::TEX_PREVIEW_SUFFIX;

        $html .= '<br/>
        <div id="'. $previewId .'" style="height:160px; margin:10px 0; background-color:transparent; border: 1px solid #f5f5f5;" title="'. $element->getEscapedValue() .'"></div>
        <script type="text/javascript">
            require(["jquery"], function(jQuery) {
                jQuery(function($) {
                    var tex     = $("#'. $element->getHtmlId()  .'");
                    var bgc     = $("#'. $bgcolorPickerId       .'");
                    var preview = $("#'. $previewId             .'");
                    
                    preview
                        .css("background-color", bgc.attr("value") )
                        .css("background-image", "url('. $initialTexUrl .')" );

                    // preview.css({
                    //     "background-color": bgc.attr("value"),
                    //     "background-image": "url('. $initialTexUrl .')"
                    // });
                    
                    tex.change(function() {
                        preview.css({
                            "background-color": bgc.css("background-color"),
                            "background-image": "url('. $texBaseUrl .'" + tex.val() + ".png)"
                        });
                    });
                });
            });
        </script>
        ';
        
        return $html;
    }
}
