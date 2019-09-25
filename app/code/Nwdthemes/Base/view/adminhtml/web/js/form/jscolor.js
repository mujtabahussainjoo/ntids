/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
        'jquery',
        'Magento_Ui/js/form/element/abstract',
        'jscolor'
    ],
    function($, Abstract) {
    return Abstract.extend({
        defaults: {
        },

        /**
         * Initializes component, invokes initialize method of Abstract class.
         *
         *  @returns {Object} Chainable.
         */
        initialize: function () {
            this._super();
            return this;
        },

        /**
         * Handler function which is supposed to be invoked when
         * input element has been rendered.
         *
         * @param {HTMLInputElement} input
         */
        onElementRender: function (input) {
            $(input).width(100);
            new jscolor(input, {hash: true, uppercase: true});
        }

    });
});
