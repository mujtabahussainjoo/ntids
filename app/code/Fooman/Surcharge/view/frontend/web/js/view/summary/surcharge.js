/*global define*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote'
    ],
    function (Component, quote) {
        "use strict";
        var foomanSurchargeConfig = window.checkoutConfig.foomanSurchargeConfig;
        return Component.extend({
            defaults: {
                foomanSurchargeConfig: foomanSurchargeConfig,
                template: 'Fooman_Surcharge/summary/surcharge'
            },
            getPureValue: function () {
                var totals = quote.getTotals()();

                for (var i in totals.total_segments) {
                    if (totals.total_segments[i].code === 'fooman_surcharge') {
                        return totals.total_segments[i].value;
                    }
                }
            },
            getValue: function () {
                return this.getFormattedPrice(this.getPureValue());
            },
            getAsCurrency: function (value) {
                return this.getFormattedPrice(value);
            },
            isDisplayedTaxInclusive: function () {
                return foomanSurchargeConfig.isDisplayedTaxInclusive;
            },
            isDisplayedTaxExclusive: function () {
                return foomanSurchargeConfig.isDisplayedTaxExclusive;
            },
            isDisplayedBoth: function () {
                return foomanSurchargeConfig.isDisplayedBoth;
            },
            isDisplayed: function (value) {
                return (value != 0 && value != null) || foomanSurchargeConfig.isZeroDisplayed;
            },
            getSurcharges: function () {
                var totals = quote.getTotals()();

                for (var i in totals.total_segments) {
                    if (totals.total_segments[i].code === 'fooman_surcharge') {
                        if (totals.total_segments[i].extension_attributes) {
                            return totals.total_segments[i].extension_attributes.fooman_surcharge_details.items;
                        }
                    }
                }
                return [];
            }
        });
    }
);
