define([
    'jquery',
    'underscore',
    'Magento_Catalog/js/components/new-category',
    'uiRegistry'
], function ($, _, UiSelect, uiRegistry) {
    'use strict';

    var store = uiRegistry;

    return UiSelect.extend({
        onUpdate: function (currentValue) {
            var $categories = $('#categories');
            $categories.val(currentValue.join(','));

            var uiBlockName = "kb_article_store_views.kb_article_store_views.storeview-details." +
                "container_storeview_ids.storeview_ids";
            var storeOptionsTree = [];
            var values = [];
            var selectAll = true;

            var categoryObject = this;

            var storeView = store.get(uiBlockName);
            if (typeof storeView == 'undefined') {
                return;
            }

            $.each(currentValue, function(index, value) {
                var option = _.findWhere(categoryObject.cacheOptions.plain, {value: value});
                if (option) {
                    if (_.findWhere(option.stores, {value: 0})) {
                        selectAll = false;
                    }
                    $.each(option.stores, function(index, newOption) {
                        var storeOption = _.findWhere(storeOptionsTree, {value: newOption.value});
                        if (!storeOption) {
                            if (storeView.addLastElement(newOption)) {
                                // sort stores by ID
                                if (
                                    typeof storeOptionsTree[0] != 'undefined' &&
                                    newOption.value > storeOptionsTree[0].value
                                ) {
                                    storeOptionsTree.push(newOption);
                                } else {
                                    storeOptionsTree.unshift(newOption);
                                }
                                values.push(newOption.value);
                            }
                        }
                    });
                }
            });
            if (!selectAll) {
                values = [0];
            }
            storeView.cacheOptions.tree = [];
            storeView.checkOptionsList(storeOptionsTree);
            storeView.value(values);
            storeView.options(storeOptionsTree);
        },

        /**
         * Normalize option object.
         *
         * @param {Object} data - Option object.
         * @returns {Object}
         */
        parseData: function (data) {
            return {
                'is_active': data.category['is_active'],
                level:       data.category.level,
                value:       data.category['entity_id'],
                label:       data.category.name,
                stores:      data.category.stores,
                parent:      data.category.parent
            };
        }
    });
});
