<?php

namespace Infortis\Infortis\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ColorConfig extends Field
{
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Initialize color pickers
     *
     * @param AbstractElement $element
     * @return String
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $fieldSelectorAttr = \Infortis\Infortis\Block\Adminhtml\System\Config\Form\Field\Color::FIELD_SELECTOR_ATTRIBUTE;
        $fieldSelectorAttrString = "[{$fieldSelectorAttr}]";

        $html = '
            <script type="text/javascript">
                require(["jquery", "spectrum", "module"], function(jQuery, colorpicker, module) {
                    jQuery(function($) {

                        // Helper functions
                        // ----------------------------------------------

                        var processColor = function(elem, color) {

                            var returnValue = "";

                            // If input color is not tinycolor, validate the input
                            if (!(color instanceof tinycolor))
                            {
                                if (color === "")
                                {
                                    color = null;
                                }
                                else if (color === null)
                                {
                                    color = null;
                                }
                                else if (color === " ")
                                {
                                    color = null;
                                    returnValue = " ";
                                }
                                else
                                {
                                    color = tinycolor(color);
                                }
                            }

                            var $elem = $(elem);
                            $elem.removeClass("transparent"); // Always remove class which indicates transparency

                            // Apply background color to the field and change the text value (if needed)
                            if (color)
                            {
                                $elem.css("background-color", color.toRgbString());

                                var alpha = color.getAlpha();
                                if (alpha === 0)
                                {
                                    $elem.addClass("transparent").val("transparent");
                                }
                                else if (alpha < 1)
                                {
                                    $elem.val(color.toRgbString());
                                }
                            }
                            else
                            {
                                $elem.css("background-color", "transparent").val(returnValue);
                            }

                            // Adjust text color
                            $elem.css("color", getTextColor(color));

                        }; // end: processColor

                        var getTextColor = function(color) {

                            if (color)
                            {
                                if (color.getAlpha() < 0.5)
                                {
                                    return "black";
                                }
                                else
                                {
                                    var hex = color.toHexString(false);
                                    hex = String(hex);
                                    var gray2 = parseInt(hex.substr(1, 2), 16) * .3 + parseInt(hex.substr(3, 2), 16) * .59 + parseInt(hex.substr(5, 2), 16) * .11;
                                    return (gray2 < 126) ? "white" : "black";
                                }
                            }
                            else // No color passed
                            {
                                return "black";
                            }

                        }; // end: getTextColor

                        var initSpectrum = function($field) {

                            $field.spectrum({

                                allowEmpty: true
                                , disabled: false
                                , showAlpha: true
                                , cancelText: "Cancel"
                                , chooseText: "Apply"
                                , localStorageKey: "infortis.base.color"
                                , preferredFormat: "hex3"
                                , showInput: true
                                , showInitial: true
                                , showPalette: true
                                , showSelectionPalette: true
                                , maxPaletteSize: 20
                                , palette: [
                                    ["#000000", "#434343", "#666666", "#999999", "#b7b7b7", "#cccccc", "#d9d9d9", "#efefef", "#f3f3f3", "#ffffff"],
                                    ["#980000", "#ff0000", "#ff9900", "#ffff00", "#00ff00", "#00ffff", "#4a86e8", "#0000ff", "#9900ff", "#ff00ff"],
                                    ["#e6b8af", "#f4cccc", "#fce5cd", "#fff2cc", "#d9ead3", "#d9ead3", "#c9daf8", "#cfe2f3", "#d9d2e9", "#ead1dc"],
                                    ["#dd7e6b", "#ea9999", "#f9cb9c", "#ffe599", "#b6d7a8", "#a2c4c9", "#a4c2f4", "#9fc5e8", "#b4a7d6", "#d5a6bd"],
                                    ["#cc4125", "#e06666", "#f6b26b", "#ffd966", "#93c47d", "#76a5af", "#6d9eeb", "#6fa8dc", "#8e7cc3", "#c27ba0"],
                                    ["#a61c00", "#cc0000", "#e69138", "#f1c232", "#6aa84f", "#45818e", "#3c78d8", "#3d85c6", "#674ea7", "#a64d79"],
                                    ["#85200c", "#990000", "#b45f06", "#bf9000", "#38761d", "#134f5c", "#1155cc", "#0b5394", "#351c75", "#741b47"],
                                    ["#5b0f00", "#660000", "#783f04", "#7f6000", "#274e13", "#0c343d", "#1c4587", "#073763", "#20124d", "#4c1130"]
                                ]

                                , beforeShow: function(color) {
                                    if (this.disabled)
                                    {
                                        $(this).next(".sp-replacer").addClass("sp-disabled");
                                        return false;
                                    }
                                    else
                                    {
                                        $(this).next(".sp-replacer").removeClass("sp-disabled");
                                    }
                                }

                                , change: function(color) {
                                    $(this).data("refresh", false);
                                    processColor(this, color);
                                }

                            }); // end: spectrum

                        }; // end: initSpectrum

                        // Initialize fields
                        // ----------------------------------------------

                        var selector = "' . $fieldSelectorAttrString . '";
                        $(selector).each(function() {

                            var $field = $(this);

                            // Set default value of the flag
                            $field.data("refresh", true);

                            // Initialize color picker
                            if (this.disabled)
                            {
                                $field.prop("disabled", false);
                                initSpectrum($field);
                                $field.prop("disabled", true);
                                $field.next(".sp-replacer").addClass("sp-disabled");
                            }
                            else
                            {
                                initSpectrum($field);
                            }

                            // Decorate color field
                            processColor(this, $field.attr("value"));

                            // Refresh color field on value change
                            $field.on("change", function(e) {

                                var $target = $(e.target);

                                // Check if flag allows to refresh the field
                                var refreshValue = $target.data("refresh");
                                $target.data("refresh", true); // Restore default value
                                if (!refreshValue)
                                {
                                    return false;
                                }

                                // Refresh
                                var curValue = $target.val();
                                processColor(e.target, curValue);

                                // If space, instead firing spectrum("set"), manually change color picker
                                if (curValue === " ")
                                {
                                    $target.next(".sp-replacer").find(".sp-preview-inner").css("background-color", "transparent").addClass("sp-clear-display");
                                }
                                else if (curValue === "transparent")
                                {
                                    $target.next(".sp-replacer").find(".sp-preview-inner").css("background-color", "transparent").removeClass("sp-clear-display");
                                }
                                else
                                {
                                    $target.spectrum("set", curValue);
                                }
                            });

                        }); // end: each

                    }); // end: on doc ready
                });
            </script>
        ';
        
        return $html;
    }
}
