define([
    'uiComponent',
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/core/app',
    'uiLayout',
    'prototype',
], function (Component, $, ko, _, bootstrap, layout,prototype) {
    window.IbnabSliders = Class.create();
    IbnabSliders.prototype = {
        staticSelector: '[id="preview-modal"]',
        createStaticSelector: '[id="create-modal"]',
        previewModal: null,
        createModal: null,
        OnclickShowPreview: {
            name: 'OnclickShowPreview',
            component: 'Ibnab_OwlSlider/js/ibnabslidersshow',
            enabled: true,
            statisticUrl: '',
            sliderId: '',
        },
        OnclickShowCreate: {
            name: 'OnclickShowCreate',
            component: 'Ibnab_OwlSlider/js/ibnabsliderscreateshow',
            enabled: true,
            statisticUrl: '',
            sliderId: '',
        },
        initialize: function () {
            this.previewModal = $(this.staticSelector).modal({
                title: $.mage.__('Preview of slider'),
                type: 'slide',
                buttons: [
                    {
                        text: $.mage.__('Done'),
                        /**
                         * Close modal
                         * @event
                         */
                        click: function () {
                            this.closeModal();
                        }
                    }
                ]
            });
            this.createModal = $(this.createStaticSelector).modal({
                title: $.mage.__('Create Banner'),
                type: 'slide',
                buttons: [
                    {
                        text: $.mage.__('Done'),
                        /**
                         * Close modal
                         * @event
                         */
                        click: function () {
                            this.closeModal();
                        }
                    }
                ]
            });
            //this.OnclickShowListIp.statisticUrl = this.statisticUrl;
        },
        initPreview: function () {

            layout([this.OnclickShowPreview]);
            return this;
        },
        initBanner: function () {

            layout([this.OnclickShowCreate]);
            return this;
        },
        /**
         * Open
         */
        open: function (url,id) {
            this.previewModal.trigger('openModal');
            this.OnclickShowPreview.statisticUrl = url;
            this.OnclickShowPreview.sliderId = id;
            this.initPreview();
            //this._initContentDashboard();
        },
        /**
         * Close
         */
        close: function () {
            this.previewModal.trigger('closeModal');

        },
        /**
         * Open
         */
        openCreate: function (url,id) {
            this.createModal.trigger('openModal');
            this.OnclickShowCreate.statisticUrl = url;
             this.OnclickShowCreate.sliderId = id;
            this.initBanner();
            //this._initContentDashboard();
        },
        /**
         * Close
         */
        close: function () {
            this.createModal.trigger('closeModal');

        }
    };
    ibnabSlider = new IbnabSliders();
});
