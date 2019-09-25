<?php
namespace Folio3\MaintenanceMode\Block\Adminhtml\System\Config;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Date extends Field
{
    protected $_coreRegistry;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $value = $element->getValue();

        if ($value == "Today") {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
            $date = $objDate->gmtDate();

            $element->setValue(date('m/d/Y h:i:s', strtotime($date)));
        }

        //get configuration element
        $html = $element->getElementHtml();
        //check datepicker set or not
        if (!$this->_coreRegistry->registry('datepicker_loaded')) {
            $this->_coreRegistry->registry('datepicker_loaded', 1);
        }

        //add icon on datepicker
        $html .= '<button type="button" style="display:none;" class="ui-datepicker-trigger '
            . 'v-middle"><span>Select Date</span></button>';
        // add datePicker with element by jQuery
        $html .= '<script type="text/javascript">
            require([
                "jquery", 
                "jquery/ui", 
                "jquery/jquery-ui-timepicker-addon"
                ], function (jq) {
               jq(window).load(function() {
                    jq("#' . $element->getHtmlId() . '").datetimepicker({ 
                        dateFormat: "mm/dd/yy",
                        timeFormat: "HH:mm:ss"
                    });
                    jq(".ui-datepicker-trigger").removeAttr("style");
                    jq(".ui-datepicker-trigger").click(function(){
                        jq("#' . $element->getHtmlId() . '").focus();
                    });
                });
            });
            </script>';
        // return datepicker element
        return $html;
    }
}