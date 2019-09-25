/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'underscore',
    'uiRegistry',
    'mageUtils',
    'Magento_Ui/js/form/element/multiselect'
], function (_, registry, utils, Multiselect) {
    'use strict';

    return Multiselect.extend({
        defaults: {
            size: 5,
            elementTmpl: 'ui/form/element/multiselect',
            listens: {
                value: 'setDifferedFromDefault setPrepareToSendData'
            },
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        },

        /**
         * Filters 'initialOptions' property by 'field' and 'value' passed,
         * calls 'setOptions' passing the result to it
         *
         * @param {*} values
         * @param {String} field
         */
        filter: function (values, field) {
            var countries = registry.get(this.parentName + '.' + 'country_id'),
                countryOptions = countries.indexedOptions,
                options,
                source,
                result,
                value;

            if (!values || _.isEmpty(values)) {
                // In case country_id is empty all region ids should be visible:
                this.setVisible(true);
                source = this.initialOptions;
                this.clear();
                this.setOptions(source);
                value = this.value().length ? this.value() : this.initialValue;
                this.value(value);

                return;
            }

            // Filter countries
            options = values.map(
                function (element) {
                    return !_.isEmpty(countryOptions[element]) ? countryOptions[element] : null;
                }
            ).filter(function (element) {
                return element;
            });

            // If options should be visible for this countries:
            if (!_.isEmpty(options)) {
                source = this.initialOptions;

                field = field || this.filterBy.field;

                result = source.filter(
                    function (item) {
                        return values.indexOf(item[field]) !== -1;
                    }
                );

                // Collect all available region ids by selected countries
                var availableValuesIndexes = [];
                result.forEach(function (country) {
                    if (_.isEmpty(country.value) || !_.isArray(country.value)) {
                        return;
                    }
                    country.value.forEach(function (element) {
                        if (element.value) {
                            availableValuesIndexes.push(element.value);
                        }
                    });
                });

                value = this.value().length ? this.value() : [];
                // Filter value, remove values which no longer available for selected countries
                var filteredValue = value.filter(
                    function (item) {
                        return availableValuesIndexes.indexOf(item) !== -1;
                    }
                );

                // Update options and values
                this.clear();
                this.setOptions(result);
                this.value(filteredValue);

                var isRegionVisible = options.some(
                    function (element, index, array) {
                        return element['is_region_visible'];
                    }
                );

                if (isRegionVisible) {
                    this.setVisible(true);
                    if (this.customEntry) {
                        this.toggleInput(true);
                    }
                } else {
                    // hide select and corresponding text input field if region must not be shown for selected country
                    this.setVisible(false);
                    this.value([]);
                    if (this.customEntry) {
                        this.toggleInput(true);
                    }
                }
            }
        }
    });
});
