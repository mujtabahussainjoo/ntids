/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/form',
    'uiRegistry',
    'mageUtils',
    'mage/validation/url'
], function ($, form, uiRegistry, utils, mageValidationUrl) {
    'use strict';

    return form.extend({

        addMethod: function (method, params) {
            mageValidationUrl.redirect(params.redirectUrl);
        }
    });
});
