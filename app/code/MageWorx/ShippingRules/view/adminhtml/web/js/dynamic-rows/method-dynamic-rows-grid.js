/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
    'uiRegistry',
    'mageUtils',
    'mage/validation/url'
], function ($, dynamicRowsGrid, uiRegistry, utils, mageValidationUrl) {
    'use strict';

    return dynamicRowsGrid.extend({

        /**
         * Delete the method using redirect to the target page
         * @param id
         */
        deleteMethodX: function (id) {
            var regex = '/'+this.idPlaceholder+'/';
            var deleteUrl = this.deleteRecordUrl.replace(regex, '/'+id+'/');
            this.redirect(deleteUrl);
        },

        /**
         * Edit a method using redirect to the target page
         * @param id
         */
        editMethodX: function (id) {
            var regex = '/'+this.idPlaceholder+'/';
            var editUrl = this.editRecordUrl.replace(regex, '/'+id+'/');
            this.redirect(editUrl);
        },

        /**
         * Normal redirect to the target url
         * @param url
         */
        redirect: function (url) {
            mageValidationUrl.redirect(url);
        }
    });
});
