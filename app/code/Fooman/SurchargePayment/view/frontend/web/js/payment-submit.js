/**
 * @author     Kristof Ringleff
 * @package    Fooman_SurchargePayment
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/set-payment-information',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/get-totals',
    'jquery',
    'Fooman_SurchargePayment/js/should-refresh-now',
    'uiRegistry'
], function (
    Component,
    quote,
    setPaymentInformation,
    defaultPayment,
    checkoutData,
    getTotals,
    $,
    shouldRefreshNow,
    registry
) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();
        },
        executeDelayedRefreshIfNeeded: function () {
            var paymentMethodSubmit = registry.get('fooman_delayed_refresh_needed');
            if (paymentMethodSubmit !== false && shouldRefreshNow(checkoutData)) {
                registry.set('fooman_delayed_refresh_needed', false);
                $.when(setPaymentInformation(defaultPayment.messageContainer, paymentMethodSubmit))
                    .done(function () {
                        getTotals([], false);
                    });
            }
        }
    });
});
