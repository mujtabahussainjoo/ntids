/**
 * @author     Kristof Ringleff
 * @package    Fooman_SurchargePayment
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
define([
    'jquery'
], function (
    jQuery
) {
    'use strict';

    return function (orderCreateOrig) {
        AdminOrder.prototype.switchPaymentMethod = function (method) {
            jQuery('#edit_form')
                .off('submitOrder')
                .on('submitOrder', function () {
                    jQuery(this).trigger('realOrder');
                });
            jQuery('#edit_form').trigger('changePaymentMethod', [method]);
            this.setPaymentMethod(method);
            var data = {};
            data['order[payment_method]'] = method;
            //EDIT changed to also reload totals block
            this.loadArea(['card_validation', 'totals'], true, data);
            //END EDIT
        };
        return orderCreateOrig;
    };
});



