/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/boolean'
], function (boolean) {
    'use strict';

    return boolean.extend({

        /**
         * Converts provided value to boolean.
         *
         * @returns {Boolean}
         */
        normalizeData: function (value) {
            if (value == '1') {
                return this.activeLabel;
            }
            return this.inactiveLabel;
        }
    });
});
