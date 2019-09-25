/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            paymentData.extension_attributes = {
                po_newsletter_subscribe: Boolean(jQuery('.po_checkout_newsletter').first().prop('checked'))
            };
            return originalAction(paymentData, messageContainer);
        });
    };
});