define([
    'uiComponent',
    'jquery',
    'ko',
    'underscore',
    'moment',
], function (Component, $, ko,_) {
    'use strict';

    return Component.extend({
        defaults: {
            template: "Ibnab_OwlSlider/ibnabslidersshow",
            content: '',
        },

        /**
         * Initialize
         * @param {Array} options
         */
        initialize: function (options) {
            this._super(options);
        },

        getTemplate: function () {
          return this.template;
        },
        getContent: function () {
            var self = this;
            $.ajax({
                type: 'POST',
                data: {form_key: window.FORM_KEY,slider_id:this.sliderId},
                url: this.statisticUrl,
                async: false,
                showLoader: true,
                context: $('body')
            }).success(function (data) {
                self.content = data;

            });
            
            var data = [];
            data = $.parseJSON(JSON.stringify(this.content.result)); 
            //alert(data.toSource());
            return this.content;
        },

    });
});
