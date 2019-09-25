/*global define*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote'
    ],
    function (Component, quote) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Fooman_Surcharge/summary/surcharge_tax'
            },
            getPureValue: function () {
                var totals = quote.getTotals()();
                
                for (var i in totals.total_segments) {
                    if (totals.total_segments[i].code === 'fooman_surcharge_tax') {
                        return totals.total_segments[i].value;
                    }
                }
            },
            getValue: function () {
                return this.getFormattedPrice(this.getPureValue());
            }
        });
    }
);
