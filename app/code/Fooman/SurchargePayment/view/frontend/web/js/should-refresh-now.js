/**
 * @author     Kristof Ringleff
 * @package    Fooman_SurchargePayment
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
define([
    'Magento_Customer/js/model/customer'
], function (
    customer
) {
    'use strict';
    return function (checkoutData) {
        return checkoutData.getValidatedEmailValue() !== '' || customer.isLoggedIn();
    };
});