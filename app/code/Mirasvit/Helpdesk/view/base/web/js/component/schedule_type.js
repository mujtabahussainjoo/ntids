define([
    'Magento_Ui/js/form/element/select',
    'uiRegistry',
    'jquery'
], function (Select, uiRegistry, $) {
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
            this._super();

            this.showTimeBlock();
        },

        initialize: function () {
            this._super();

            this.showTimeBlock();

            return this;
        },

        showTimeBlock: function () {
            var self = this;
            var scheduleTime = uiRegistry.get('index = working_time');
            $(scheduleTime.elems()).each(function () {
                if (self.value() == self.showTime) {
                    this.visible(true);
                } else {
                    this.visible(false);
                }
            });
        }
    });
});
