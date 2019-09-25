define([
    'Magento_Ui/js/form/element/abstract'
], function (Element) {
    'use strict';

    return Element.extend({
        initSwitcher: function () {
            this._super();
            if (this.value()) {
                this.disable(true);
            }
        }
    });
});
