define([
    'Magento_Ui/js/form/element/select',
    'jquery'
], function (Select, $) {
    'use strict';

    return Select.extend({
        defaults: {
            previousType: '',
            parentContainer: '',
            selections: '',
            targetIndex: '',
            typeMap: {}
        },

        /**
         * @inheritdoc
         */
        onUpdate: function () {
            $('.color #example').attr('class', this.value());

            this._super();
        },
        initialize: function () {
            this._super();

            this.additionalInfo = this.additionalInfo.replace('%%init_class%%', this.initialValue)

            return this;
        }
    });
});
