define([
    "jquery",
    'underscore',
    'uiCollection',
    'Magento_Ui/js/lib/spinner',
    "domReady!",
    "form"
], function ($, _,  Collection, loader) {
    'use strict';

    $(document).ready(function() { //prevent double click
        var buttons = 0;
        var otherGrid = $('[data-bind="scope: \'ticket_other_grid.ticket_other_grid\'"]');

        otherGrid.hide();

        if ($('.helpdesk-ticket-edit [data-save-target]').length) {
            var el = $('.helpdesk-ticket-edit [data-save-target]')[0];
            $($(el).data('save-target')).on('beforeSubmit', function(e) {
                if (buttons) {
                    e.preventDefault();
                }
                buttons = 1;
            });
            $($(el).data('save-target')).on('invalid-form.validate', function() {
                buttons = 0;
            });
        }
        if ($('.helpdesk-ticket-add [data-save-target]').length) {
            var el = $('.helpdesk-ticket-add [data-save-target]')[0];
            $($(el).data('save-target')).on('beforeSubmit', function(e) {
                if (buttons) {
                    e.preventDefault();
                }
                buttons = 1;
            });
            $($(el).data('save-target')).on('invalid-form.validate', function() {
                buttons = 0;
            });
        }

        $('.tab-item-link').click(function() {
            if ($(this).hasClass('.other-grid-tab')) {
                otherGrid.hide();
            } else {
                otherGrid.show();
            }
        });
    });

    return Collection.extend({
        initialize: function () {
            this._super()
                .hideLoader();
            return this;
        },
        hideLoader: function () {
            loader.hide();


            return this;
        },
        /**
         * Shows loader.
         */
        showLoader: function () {
            loader.show();
        }
    });
});