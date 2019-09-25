/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Initialization custom select component
 *
 * @method initialize()
 * @method onUpdate()
 * @method showHideFields()
 */
define([
    'jquery',
    'Magento_Ui/js/lib/view/utils/async',
    'Magento_Ui/js/form/element/select'
], function ($, async, select) {
    'use strict';

    return select.extend({

        /**
         * Initialize component.
         * @returns {Element}
         */
        initialize: function () {
            var self = this;

            this._super();
            async.async('fieldset, div', document.getElementById('container'), function () {
                self.showHideFields();
            });

            return this;
        },

        /**
         * Update action
         */
        onUpdate: function () {
            this.showHideFields();
        },

        /**
         * Show/Hide fields on rule edit page
         */
        showHideFields: function () {
            var self = this;

            $.each(this.rbsliderSwitcher, function() {
                if (this.values.indexOf(parseInt(self.value())) != -1) {
                    $.each(this.actions, function() {
                        if (this.action == 'hide') {
                            $(this.selector).hide()
                        }

                        if (this.action == 'show') {
                            $(this.selector).show()
                        }
                    });
                }
            });
        }
    });
});
