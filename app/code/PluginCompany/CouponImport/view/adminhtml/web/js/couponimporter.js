/**
 * Created by:  Milan Simek
 * Company:     Plugin Company
 *
 * LICENSE: http://plugin.company/docs/magento-extensions/magento-extension-license-agreement
 *
 * YOU WILL ALSO FIND A PDF COPY OF THE LICENSE IN THE DOWNLOADED ZIP FILE
 *
 * FOR QUESTIONS AND SUPPORT
 * PLEASE DON'T HESITATE TO CONTACT US AT:
 *
 * SUPPORT@PLUGIN.COMPANY
 */
define([
    'jquery',
    "uiRegistry",
    'Magento_Ui/js/modal/alert',
    'PluginCompany_CouponImport/js/lib/jquery.iframe-transport',
    'prototype',
    'mage/mage'
], function (jQuery, registry, alert) {
    return {
        importUrl: '',
        gridJsObject: null,
        wrapperEmId: '',
        fileEmId: '',
        couponsTextAreaId: '',
        setWrapperEmId: function (wrapperId) {
            this.wrapperEmId = wrapperId;
            return this;
        },
        setImportUrl: function (importUrl) {
            this.importUrl = importUrl;
            return this;
        },
        setGridJsObject: function (gridJsObject) {
            this.gridJsObject = gridJsObject;
            return this;
        },
        setFileEmId: function (fileId) {
            this.fileEmId = fileId;
            return this;
        },
        setCouponTextAreaEmId: function (emId) {
            this.couponsTextAreaId = emId;
            return this;
        },
        import: function () {
            if (!this.isValidated()) {
                return this.showValidationError();
            }
            this.submitCouponData();
        },
        isValidated: function () {
            return this.executeValidation();
        },
        executeValidation: function () {
            $(this.wrapperEmId).removeClassName('ignore-validate');
            var valResult = this.validateInputs();
            $(this.wrapperEmId).addClassName('ignore-validate');
            return valResult;
        },
        validateInputs: function () {
            return $(this.wrapperEmId).select(
                'input',
                'select',
                'textarea',
                'file'
            ).collect(function (elm) {
                return jQuery.validator.validateElement(elm);
            }).all();
        },
        showValidationError: function () {
            alert({
                content: "Please add some coupons first"
            });
            return false;
        },
        submitCouponData: function () {
            this.clearMessages();
            var couponImporter = this;
            jQuery.ajax(this.importUrl, {
                files: jQuery(':file'),
                iframe: true,
                dataType: "json",
                data: {coupons: this.getCouponsFromTextArea()},
                processData: false,
                showLoader: true,
                context: $('#' + couponImporter.wrapperEmId),
                success: function (response) {
                    couponImporter.addMessages(response.messages);
                    couponImporter.refreshGrid();
                },
                error: function (response) {
                    couponImporter.addMessages(response.messages);
                    couponImporter.refreshGrid();
                }
            });
        },
        clearMessages: function () {
            if ($$('#' + this.wrapperEmId + ' .messages')) {
                $$('#'+ this.wrapperEmId + ' .messages')[0].update();
            }
        },
        getCouponsFromTextArea: function () {
            return jQuery('#' + this.couponsTextAreaId).val();
        },
        addMessages: function (messages) {
            if ($$('#' + this.wrapperEmId + ' .messages')) {
                $$('#'+ this.wrapperEmId + ' .messages')[0].update(messages);
            }
        },
        refreshGrid: function () {
            var grid = eval(this.gridJsObject);
            grid.reload();
        }
        
    };
});  