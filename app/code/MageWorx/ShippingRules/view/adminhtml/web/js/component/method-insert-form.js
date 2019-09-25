/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/components/insert',
    'uiRegistry',
    'mageUtils'
], function ($, InsertForm, uiRegistry, utils) {
    'use strict';

    /**
     * Get page actions element.
     *
     * @param {String} elem
     * @param {String} actionsClass
     * @returns {String}
     */
    function getPageActions(elem, actionsClass)
    {
        var el = document.createElement('div');

        el.innerHTML = elem;

        return el.getElementsByClassName(actionsClass)[0];
    }

    /**
     * Return element without page actions toolbar
     *
     * @param {String} elem
     * @param {String} actionsClass
     * @returns {String}
     */
    function removePageActions(elem, actionsClass)
    {
        var el = document.createElement('div'),
            actions;

        el.innerHTML = elem;
        actions = el.getElementsByClassName(actionsClass)[0];
        if (typeof actions != 'undefined') {
            el.removeChild(actions);
        }

        return el.innerHTML;
    }

    return InsertForm.extend({
        defaults: {
            externalFormName: '${ $.ns }.${ $.ns }',
            pageActionsClass: 'page-actions',
            actionsContainerClass: 'page-main-actions',
            exports: {
                prefix: '${ $.externalFormName }:selectorPrefix'
            },
            imports: {
                toolbarSection: '${ $.toolbarContainer }:toolbarSection',
                prefix: '${ $.toolbarContainer }:rootSelector',
                messagesClass: '${ $.externalFormName }:messagesClass'
            },
            settings: {
                ajax: {
                    ajaxSave: true,
                    exports: {
                        ajaxSave: '${ $.externalFormName }:ajaxSave'
                    },
                    imports: {
                        responseStatus: '${ $.externalFormName }:responseStatus',
                        responseData: '${ $.externalFormName }:responseData'
                    }
                }
            },
            modules: {
                externalForm: '${ $.externalFormName }'
            }
        },

        /** @inheritdoc */
        initObservable: function () {
            return this._super()
                .observe('responseStatus');
        },

        /** @inheritdoc */
        initConfig: function (config) {
            var defaults = this.constructor.defaults;
            utils.extend(defaults, defaults.settings[config.formSubmitType] || {});

            return this._super();
        },

        /** @inheritdoc*/
        destroyInserted: function () {
            if (this.isRendered && this.externalForm()) {
                this.externalForm().delegate('destroy');
                this.removeActions();
                this.responseStatus(undefined);
                this.responseData = {};
            }

            return this._super();
        },

        /** @inheritdoc */
        onRender: function (data) {
            var actions = getPageActions(data, this.pageActionsClass);

            if (!data.length) {
                return this;
            }
            if (typeof actions != 'undefined') {
                data = removePageActions(data, this.pageActionsClass);
                this.renderActions(actions);
            }
            this._super(data);
        },

        /**
         * Insert actions in toolbar.
         *
         * @param {String} actions
         */
        renderActions: function (actions) {
            var $container = $('<div/>');

            $container
                .addClass(this.actionsContainerClass)
                .append(actions);

            this.formHeader = $container;

            $(this.toolbarSection).append(this.formHeader);
        },

        /**
         * Remove actions toolbar.
         */
        removeActions: function () {
            $(this.formHeader).siblings('.' + this.messagesClass).remove();
            $(this.formHeader).remove();
            this.formHeader = $();
        },

        /**
         * Reset external form data.
         */
        resetForm: function () {
            if (this.externalSource()) {
                this.externalSource().trigger('data.reset');
                this.responseStatus(undefined);
            }
        },

        save: function () {
            this.modal = uiRegistry.get('index = mageworx_shippingrules_method_form');
            if (this.validate()) {
                this.submitForm();
                return true;
            } else {
                return false;
            }

        },

        submitForm : function () {
            var parent = this;
            var form = uiRegistry.get('index = mageworx_shippingrules_method_form');
            var submitUrl = uiRegistry.get('index = mageworx_shippingrules_method_form_data_source').submit_url;
            var currentCarrierId = form.get('imports').currentCarrierId;
            var source = form.get('source');
            var data = source['mageworx_shippingrules_method_form'].data;

            jQuery.ajax(submitUrl, {
                data: {'data':data,'currentProductId':currentCarrierId},
                complete: function (result) {
                    parent.closeModal();
                },
                error: function (e) {
                    console.log(e);
                }
            });
        },

        validate: function () {
            return true;
        }
    });
});
