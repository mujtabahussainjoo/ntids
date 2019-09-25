<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Regular License.
 * You may not use any part of the code in whole or part in any other software
 * or product or website.
 *
 * @author      Infortis
 * @copyright   Copyright (c) 2014 Infortis
 * @license     Regular License http://themeforest.net/licenses/regular 
 */

namespace Infortis\Dataporter\Block\System\Config\Form\Field;

use Magento\Backend\Block\AbstractBlock;
use Magento\Backend\Block\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Store\Model\Store;

class Configimpex 
    extends AbstractBlock implements RendererInterface
{
    /**
     * @var UrlInterface
     */
    protected $_frameworkUrlInterface;

    /**
     * @var LayoutFactory
     */
    protected $_viewLayoutFactory;

    public function __construct(
        Context $context,
        LayoutFactory $viewLayoutFactory,
        array $data = []
    ) {
        $this->_frameworkUrlInterface = $context->getUrlBuilder();
        $this->_viewLayoutFactory = $viewLayoutFactory;

        parent::__construct($context, $data);
    }

    /**
    * The Magento 1 version of this extension used non-standard
    * nodes in system.xml to pass data to the render method. This
    * is not possible in Magento 2 since every XML file must 
    * conform to a defined schema
    */
    // protected function fixElementData($elementData)
    // {
    //     $full_path = $elementData['path'] . '/' . $elementData['id'];
    //     switch($full_path)
    //     {
    //         case 'theme_settings/install/heading_buttons':
    //             $elementData['package']     = 'Infortis_Base';
    //             $elementData['sublabel']    = 'Click to go to the import/export page';
    //             break;
    //         default:
    //             throw new \Exception("non-standard nodes in system.xml: $full_path");
    //     }
    //     return $elementData;
    // }
    
    /**
     * Render element html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $imageDirectoryBaseUrl = $this->_frameworkUrlInterface->getBaseUrl() . 'infortis/system/css/images';
        $elementData = $element->getOriginalData();
        //$elementData = $this->fixElementData($elementData);

        if(!array_key_exists('package', $elementData))
        {
            $elementData['package']     = 'Infortis_Base';
            $elementData['sublabel']    = 'Click to go to the import/export page';
        }

        $url1 = $this->getUrl('adminhtml/cfgporter/index_import/action_type/import/package/' . $elementData['package']);
        $url2 = $this->getUrl('adminhtml/cfgporter/index_export/action_type/export/package/' . $elementData['package']);
        
        //Start base HTML
        $html = '';
        $html .= sprintf('<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5"><h4 id="%s">%s</h4></td></tr>',
            $element->getHtmlId(), $element->getHtmlId(), $element->getLabel()
        );

        //Open row
        $html .= sprintf('<tr class="" id="row_%s_content">',
            $element->getHtmlId()
        );

        //Add label cell
        $html .= sprintf('<td class="label"><label>%s</label></td>',
            $elementData['sublabel']
        );

        //Open main cell
        $html .= '<td class="value">';

        //Buttons
        $html .= $this->_viewLayoutFactory->create()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setType('button')
            ->setClass('go-to-page')
            ->setLabel('Import')
            ->setOnClick("setLocation('{$url1}')")
            ->toHtml();
        $html .= '&nbsp;';
        $html .= $this->_viewLayoutFactory->create()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setType('button')
            ->setClass('go-to-page')
            ->setLabel('Export')
            ->setOnClick("setLocation('{$url2}')")
            ->toHtml();

        //Close all wrappers: cell and row
        $html .= '</td>';
        $html .= '</tr>';

        $background_url = $this->_assetRepo
            ->createAsset('Infortis_Dataporter::images/btn-go-to-page-icon.png')
            ->getUrl();
            
        //Add CSS
        $html .=
'<style>
button.go-to-page span {
    background-repeat: no-repeat;
    background-position: 100% 50%;
    background-image: url(' . $background_url . ');
    padding-right: 26px;
}
</style>';

        return $html;
    }
}
