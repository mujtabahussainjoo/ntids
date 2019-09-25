/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Ui/js/form/element/date'
], function (Element) {
    'use strict';

    return Element.extend({
        /**
         * @inheritdoc
         */
        prepareDateTimeFormats: function () {
            this._super();
            if (this.options.showsTime && this.timezoneFormat) {
                this.validationParams.dateFormat = this.timezoneFormat;
            }
        }
    });
});
