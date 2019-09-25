define([
    'jquery',
    'Magento_Catalog/js/components/new-category'
    //'Mirasvit_Kb/js/form/components/article-edit-group'
], function ($, UiSelect) {
    'use strict';

    return UiSelect.extend({
        onUpdate: function (currentValue) {
            var $categories = $('#store_ids');
            $categories.val(currentValue.join(','));
        },
    });
});
