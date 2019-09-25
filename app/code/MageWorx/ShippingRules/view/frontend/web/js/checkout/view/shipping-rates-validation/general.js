/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    '../../model/shipping-rates-validator/general',
    '../../model/shipping-rates-validation-rules/general'
], function (
    Component,
    defaultShippingRatesValidator,
    defaultShippingRatesValidationRules,
    generalShippingRatesValidator,
    generalShippingRatesValidationRules
) {
    'use strict';

    defaultShippingRatesValidator.registerValidator(null, generalShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules(null, generalShippingRatesValidationRules);

    return Component;
});
