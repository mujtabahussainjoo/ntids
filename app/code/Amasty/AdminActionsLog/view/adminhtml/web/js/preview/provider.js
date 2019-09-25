define([
    "jquery",
    "Magento_Ui/js/modal/modal",
    'Magento_Ui/js/grid/provider'
], function (JQuery, modal, Provider) {
    'use strict';

    return Provider.extend({
        initialize: function(){
            this._super();
            Window.keepMultiModalWindow = true;
            this.overlayShowEffectOptions = null;
            this.overlayHideEffectOptions = null;
            this.modal = null;

            return this;
        },
        open : function(name, elementId, action) {
            var editorUrl = action.callback.params[0];
            if (!elementId) {
                elementId = action.callback.params[1];
            }
            if (editorUrl && elementId) {
                jQuery.ajax({
                    url: editorUrl,
                    data: {
                        element_id: elementId,
                        store_id: '<?php /* @escapeNotVerified */ echo $block->getStoreId() ?>'
                    },
                    showLoader: true,
                    dataType: 'html',
                    success: function(data, textStatus, transport) {
                        this.openDialogWindow(data, elementId);
                    }.bind(this)
                });
            }
        },
        openDialogWindow : function(data, elementId) {
            if (this.modal) {
                this.modal.html(jQuery(data).html());
            } else {
                this.modal = jQuery(data).modal({
                    title: '<?php /* @escapeNotVerified */ echo __(\'Action Log Details\'); ?>',
                    modalClass: 'magento',
                    type: 'slide',
                    firedElementId: elementId
                });
                this.modal.html(jQuery(data).html());
            }
            this.modal.modal('openModal');
        }

    });
});
