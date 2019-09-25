/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Magento_Ui/js/grid/controls/columns',
    'jquery',
    'jquery/ui'
], function (uiColumns, $) {
    'use strict';

    return uiColumns.extend({
        defaults: {
            template: 'MageWorx_ShippingRules/grid/controls',
        },

        cancel: function () {
            $('#column-controls-button').trigger('click');
            return this;
        }
    });
});
