define([
    'underscore',
    'ko',
    'uiComponent',
    'Magento_Ui/js/lib/collapsible',
    'jquery'
], function (_, ko, Component, Collapsible, $) {
    'use strict';

    return Collapsible.extend({
        current: ko.observable(0),
        defaults: {
            closeOnOuter: false,
            stores: [
                {"id": "public", "label":$.mage.__("Public Reply"), note: $.mage.__("Your message will be emailed to customer")},
                {"id": "internal", "label":$.mage.__("Internal Note"), note: $.mage.__("Your message will be emailed to your college. Customer will not see it.")},
                {"id": "public_third", "label":$.mage.__("Message to Third Party"), note: $.mage.__("Your message will be emailed to the third party. Customer will see it in the ticket history.")},
                {"id": "internal_third", "label":$.mage.__("Internal Message to Third Party"), note: $.mage.__("Your message will be emailed to the third party. Customer will not see it.")}
            ],
            template: 'Mirasvit_Helpdesk/reply-switcher',
            listens: {}
        },

        initialize: function () {
            this._super();
            _.bindAll(this, 'onChangeStore');
            this.current(this.stores[0]);
            return this;
        },

        onChangeStore: function (store) {
            this.set('current', store);
            this.close();
            $('body').trigger('helpdesk-switch-reply-type', store.id);
        },
        isThirdParty: function () {
            return this.current().id == 'public_third' || this.current().id == 'internal_third';
        }
    });
});
