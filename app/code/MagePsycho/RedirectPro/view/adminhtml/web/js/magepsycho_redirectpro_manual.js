define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ], function($) {
        'use strict';

        return function (optionsConfig) {
            var magepsychoRedirectProManual = $('<div/>').html(optionsConfig.html).modal({
                modalClass: 'manual-popup',
                title: $.mage.__('Notes on Redirection Url'),
                buttons: [{
                    text: 'OK',
                    click: function () {
                        this.closeModal();
                    }
                }]
            });
            $('#manual-popup').on(
                'click', function() {
                    magepsychoRedirectProManual.modal('openModal');
                }
            );
        };
    }
);