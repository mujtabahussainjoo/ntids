define([
    'underscore',
    'ko',
    'uiComponent',
    'jquery'
], function (_, ko, Component, $) {
    'use strict';

    return Component.extend({
        currentTemplateId: ko.observable(0),
        defaults: {
            template: 'Mirasvit_Helpdesk/quick-response'
        },

        initialize: function () {
            this._super();
            this.templates = this._templates;
            this.templates.unshift({"id":"0", "name": "Insert Quick Response"});
            this._bind();
            return this;
        },
        _bind: function () {

        },
        onChangeResponse: function () {
            var body = "";
            var self = this;
            if (self.currentTemplateId() == 0) {
                return;
            }
            $(this.templates).each(function (key, template ) {
                if (template.id == self.currentTemplateId()) {
                    body = template.body;
                    return false;
                }
            });
            $('body').trigger('helpdesk-insert-quick-response', body);
            this.currentTemplateId(0);
        },
        hasTemplates: function () {
            return this.templates.length > 1;
        }
    });
});
