define([
    'ko',
    "jquery",
    'uiComponent'
], function (ko, $, Component) {
    'use strict';
    
    return Component.extend({
        initialize: function () {
            this._super();
            
            this.helpdesk = ko.observable({amount: 0});
            
            var self = this;
            $.ajax({
                url:      this.requestUrl,
                dataType: 'json',
                method:   'GET'
            }).done(function (data) {
                if (typeof data.amount != 'undefined') {
                    self.helpdesk(data);
                }
            });
        }
    });
});
