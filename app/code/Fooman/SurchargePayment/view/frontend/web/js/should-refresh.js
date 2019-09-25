/**
 * @author     Kristof Ringleff
 * @package    Fooman_SurchargePayment
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define(function () {
    'use strict';
    var counter = 1;
    return function (checkoutData, newValue) {
        return counter++ === 1 || checkoutData.getSelectedPaymentMethod() != newValue.method;
    };
});