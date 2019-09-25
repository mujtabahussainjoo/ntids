/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Customer/js/customer-data',
    'uiComponent',
    'ko',
    'Magento_Ui/js/modal/modal',
    'mage/template',
    'mage/translate',
    'text!MageWorx_ShippingRules/template/modal/location.html',
    'text!MageWorx_ShippingRules/template/modal/extendedZonesSelector.html',
    'text!MageWorx_ShippingRules/template/modal/fields.html',
    'jquery/ui',
    'slick'
], function ($, _, customerData, Component, ko, modal, template, translate, locationTpl, ezSelectorTpl, fieldsTpl) {
    'use strict';

    var locationComponent;

    /**
     * Select country and region modal window
     */
    $.widget('mage.shippingZoneSelectorModal', modal, {
        options: {
            debug: false,
            locationTpl: locationTpl,
            countryCodeDataRole: 'country_code',
            regionCodeDataRole: 'region_code',
            regionDataRole: 'region',
            buttons: [
                {
                    text: $.mage.__('Save Changes'),
                    class: 'save',
                    attr: {},

                    /**
                     * Default action on button click
                     */
                    click: function (event) {
                        this.saveChanges();
                    }
                }
            ]
        },

        /**
         * Creates modal widget.
         */
        _create: function () {
            this._super();
            this.bindPromises();
        },

        /**
         * Bind promises to get data from customerData response
         */
        bindPromises: function () {
            var self = this;
            new Promise(function (resolve, reject) {
                var timer = setInterval(function () {
                    var container = $('.shipping-zone-selector').find('[name="country_code"]');
                    if (container.length > 0) {
                        clearInterval(timer);
                        resolve(container);
                    }
                }, 500);
            }).then(
                function (result) {
                    self.bindRegionSyncUpdater(result)
                },
                function (error) {
                    self.log(error);
                }
            );
        },

        /**
         * Show only corresponding regions when country select value is changed
         * @param $result jQuery
         */
        bindRegionSyncUpdater: function ($result) {
            var self = this;
            $result.on('change', function (e) {
                var t = $(e.target);
                self.updateSelectRegion(t.val());
            }).trigger('change');
        },

        /**
         * Show only corresponding regions in the select based on country selected
         * @param country_code
         */
        updateSelectRegion: function (country_code) {
            var listItems = '<option value="">' + translate('Please select') + '</option>',
                regionList = this.filterRegionListByCountryCode(country_code),
                $shippingZoneSelector = $('.shipping-zone-selector'),
                $regionSelect = $shippingZoneSelector.find('[name="region_code"]'),
                $regionTextInput = $shippingZoneSelector.find('[name="region"]');
            for (var i = 0; i < regionList.length; i++) {
                var currentRegionName = regionList[i].name ? regionList[i].name : regionList[i].default_name;
                listItems += "<option value='" + regionList[i].code + "'>" + currentRegionName + "</option>";
            }
            $regionSelect.html(listItems);
            if (regionList.length < 1 || typeof regionList.length == 'undefined') {
                $regionSelect.prop('disabled', true).closest('.field-wrapper').hide();
                $regionTextInput.prop('disabled', false).closest('.field-wrapper').show();
            } else {
                $regionSelect.prop('disabled', false).closest('.field-wrapper').show();
                $regionTextInput.prop('disabled', true).closest('.field-wrapper').hide();
                $regionSelect.val(customerData.get('location')().region_code);
            }
        },

        /**
         * Return regions list by country code
         * @param country_code
         * @returns {{}}
         */
        filterRegionListByCountryCode: function (country_code) {
            var listItems = {};
            if (typeof this.options.regionJsonList != 'undefined' &&
                typeof this.options.regionJsonList[country_code] != 'undefined' &&
                this.options.regionJsonList[country_code].length > 0) {
                listItems = this.options.regionJsonList[country_code];
            }

            return listItems;
        },

        /**
         * Submit selected data: country and region
         *
         * @returns {mage.shippingZoneSelectorModal}
         */
        saveChanges: function () {
            var self = this,
                data = {
                    country_code: this.getSelectedCountryCode(),
                    region_code: this.getSelectedRegionCode(),
                    region: this.getSelectedRegion()
                };

            $.ajax(this.options.save_url, {
                data: data,
                dataType: 'json',
                method: 'POST',
                showLoader: true,
                context: '.shipping-zone-selector',
                success: function (responseData) {
                    new Promise(function (resolve, reject) {
                        resolve(customerData.reload(['location']));
                    }).then(
                        function (result) {
                            customerData.reload(['checkout-data']);
                        },
                        function (error) {
                            self.log(error);
                        }
                    );

                    self.closeModal();
                },
                error: function (e) {
                    self.log(e);
                }
            });

            return this;
        },

        /**
         * Returns selected country code
         * @returns {*}
         */
        getSelectedCountryCode: function () {
            var value = null,
                $elem = this._getElem('[data-role="' + this.options.countryCodeDataRole + '"]');

            if (typeof $elem != 'undefined') {
                value = $elem.val();
            }

            return value;
        },

        /**
         * Returns selected region code
         * @returns {*}
         */
        getSelectedRegionCode: function () {
            var value = null,
                $elem = this._getElem('[data-role="' + this.options.regionCodeDataRole + '"]');

            if (typeof $elem != 'undefined' && !$elem.prop('disabled')) {
                value = $elem.val();
            }

            return value;
        },

        /**
         * Returns selected region name (when there is no region id)
         * @returns {*}
         */
        getSelectedRegion: function () {
            var value = null,
                $elem = this._getElem('[data-role="' + this.options.regionDataRole + '"]');

            if (typeof $elem != 'undefined' && !$elem.prop('disabled')) {
                value = $elem.val();
            }

            return value;
        },

        /**
         * Returns main content html-element of the modal window
         * @returns $
         */
        getContentElement: function (column) {
            var $mainContainer = this._getElem(this.options.modalContent);
            if (column == 'left') {
                return $mainContainer.find('.sz-left-content');
            } else if (column == 'right') {
                return $mainContainer.find('.sz-right-content');
            }

            return $mainContainer;
        },

        /**
         * Add html content to the main html-container inside modal window
         * @param html
         * @returns {mage.shippingZoneSelectorModal}
         */
        changeContent: function (html, column) {
            this.getContentElement(column).html(html);

            return this;
        },

        /**
         * Append html content to the main html-container inside modal window
         * @param html
         * @returns {mage.shippingZoneSelectorModal}
         */
        addContent: function (html, column) {
            this.getContentElement(column).append(html);

            return this;
        },

        /**
         * Prepend html content to the main html-container inside modal window
         * @param html
         * @returns {mage.shippingZoneSelectorModal}
         */
        addContentBeforeAll: function (html, column) {
            var $contentEl = this.getContentElement(column);
            if (typeof selector != 'undefined') {
                $contentEl = $contentEl.find(selector);
            }
            $contentEl.prepend(html);

            return this;
        },

        /**
         * Log anything when debug is true
         *
         * @param data
         */
        log: function (data) {
            if (this.options.debug === true) {
                console.log(data);
            }
        }
    });

    /**
     * Modal container. Visual preview for the customer: current country
     */
    return Component.extend({

        debug: true,

        initialize: function () {
            var self = this;
            this._super();
            this.cData = customerData.get('location');
            var cacheStorage = JSON.parse(localStorage.getItem('mage-cache-storage'));
            if (typeof cacheStorage["location"] == 'undefined') {
                new Promise(function (resolve, reject) {
                    resolve(customerData.reload(['location']));
                }).then(
                    function (result) {
                        customerData.reload(['checkout-data']);
                    },
                    function (error) {
                        self.log(error);
                    }
                );
            }

            new Promise(function (resolve, reject) {
                var timer = setInterval(function () {
                    var loadedCustomerLocationData = customerData.get('location');
                    // Check customer data is loaded (not empty)
                    if (!_.isEmpty(customerData.get('location')())) {
                        clearInterval(timer);
                        resolve(loadedCustomerLocationData);
                    }
                }, 500);
            }).then(
                function (result) {
                    self.initializePromised(result);
                },
                function (error) {
                    self.initializePromised({});
                }
            );
        },

        /**
         * Update visual data when customerData is loaded:
         * country
         * @param loadedData
         */
        initializePromised: function (loadedData) {
            this.initModal();
            locationComponent = this;
        },

        /**
         * Create modal
         */
        initModal: function () {
            /** @important Next methods should be called one by one! Do not change their sort order! */
            // Create modal (render template)
            this.modal = $('<div/>').html(this.data.html).shippingZoneSelectorModal(this._getDataForTemplate());
            // Add country and region select template to the modal container
            this._addCountryRegionSelectHtml(this._getDataForTemplate());
            // Add zones template to the modal container
            this._processExtendedZones();
        },

        _processExtendedZones: function () {
            var self = this;
            if (this.data.extended_zones) {
                this.modal.shippingZoneSelectorModal('addContentBeforeAll', this._prepareExtendedZonesSelect(), 'left');
                var $szContainer = $('#sz-main-container');
                $szContainer.slick({
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 760,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 540,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });

                $('.mageworx-extended-zones').find('.zone').each(function (i, zone) {
                    var $zone = $(zone);
                    $zone.on('click', function (event) {
                        var target = event.target,
                            $target = $(target);
                        if (!$target.hasClass('zone')) {
                            $target = $target.closest('.zone');
                        }
                        $szContainer.find('.zone').removeClass('selected');
                        $target.toggleClass('selected');
                        if ($target.hasClass('selected')) {
                            $('#sz-fields').show();
                            var zoneId = self.parseZoneId($target);
                            self.filterFormDataByZoneId(zoneId);
                        } else {
                            $('#sz-fields').hide();
                        }
                    });
                });
            }
        },

        /**
         * Adds country and region select (html) to the modal template
         * @private
         */
        _addCountryRegionSelectHtml: function (data) {
            var mainFieldsetSelector = '#sz-select-address-container',
                $fieldset = $(mainFieldsetSelector),
                fieldsetTemplateProcessed = _.template(fieldsTpl)({"data": data});
            $fieldset.html(fieldsetTemplateProcessed);
            var $countrySelect = $('.shipping-zone-selector').find('[name="country_code"]');
            if ($countrySelect.length) {
                this.modal.shippingZoneSelectorModal('bindRegionSyncUpdater', $countrySelect);
            }
        },

        /**
         * Returns data for the underscore templates
         * @returns {*}
         */
        _getDataForTemplate: function (overwriteData) {
            if (typeof overwriteData === 'undefined' || !overwriteData) {
                overwriteData = {};
            }

            return _.extend(
                /**
                 * Customer Data
                 */
                this.cData(),
                /**
                 * Other important data from this class
                 */
                {
                    modalClass: 'shipping-zone-selector',
                    title: $.mage.__('Shipping Zone'),
                    type: 'location',
                    country_list: this.data.country_list,
                    save_url: this.data.save_url,
                    extended_zones: this.data.extended_zones,
                    countryCodeDataRole: 'country_code',
                    regionCodeDataRole: 'region_code',
                    regionDataRole: 'region',
                    label: {
                        country: $.mage.__('Country'),
                        region: $.mage.__('Region'),
                        region_code: $.mage.__('State/Province'),
                    },
                    displayAddressOnly: this.data.display_address_only
                },
                /**
                 * We can overwrite data using this argument,
                 * because last argument overwrite all previous when keys are identical
                 * @important data matched by keys will be overwriten but not cleared if the keys is not match
                 */
                overwriteData
            );
        },

        /**
         * Render new country & region select tempalte with filtered data
         * @param zoneId
         */
        filterFormDataByZoneId: function (zoneId) {
            var zoneData = this.data.extended_zones.filter(function(e){
                return e.id && e.id == zoneId;
            })[0];

            if (typeof zoneData == 'undefined') {
                this._addCountryRegionSelectHtml(this._getDataForTemplate());
            }

            var countries = zoneData.countries;
            if (!countries) {
                this._addCountryRegionSelectHtml(this._getDataForTemplate());
            }

            var countryList = $.extend(true, {}, this.data.country_list),
                newCountryList = {};
            _.map(countryList, function (value, key) {
                if (countries.indexOf(value['value']) !== -1 || value['value'] === '') {
                    newCountryList[key] = countryList[key];
                }
            });

            var data = this._getDataForTemplate();
            data.country_list = newCountryList;
            this._addCountryRegionSelectHtml(data);
        },

        /**
         * Get zone id data attribute from the element
         * @param $zone
         * @returns {Number|number}
         */
        parseZoneId: function ($zone) {
            return parseInt($zone.data('zone_id'));
        },

        /**
         * Prepare and render Pop-up Zones template
         * @private
         */
        _prepareExtendedZonesSelect: function () {
            if (this.data.display_address_only) {
                return '';
            }

            return _.template(ezSelectorTpl)({"zones": this.data.extended_zones});
        },

        /**
         * Check is empty object
         * @param obj
         * @returns {boolean}
         */
        isEmpty: function (obj) {
            if (obj == null) {
                return true;
            }
            if (obj.length > 0) {
                return false;
            }
            if (obj.length === 0) {
                return true;
            }
            if (typeof obj !== "object") {
                return true;
            }
            for (var key in obj) {
                if (hasOwnProperty.call(obj, key)) {
                    return false;
                }
            }

            return true;
        },

        /**
         * Observable
         */
        zoneLabel: ko.computed(function () {
            var countryCode = customerData.get('location')().country_code,
                country = customerData.get('location')().country,
                region = customerData.get('location')().region,
                $wrapper = $("#select-shipping-zone"),
                $icon = $wrapper.find('i.sz-icon');

            $wrapper.parent('.shipping-zone-location-container').css('width', 'auto');

            if (!country) {
                return translate('Please, select your shipping region.');
            }

            if (!region) {
                region = '<span>' + translate('Select Region') + '</span>';
            }

            if (!$wrapper.find('.sz-inner').length) {
                $wrapper.wrapInner('<span class="sz-inner"></span>');
            }
            if (countryCode) {
                if ($icon.length) {
                    $icon.addClass(countryCode.toLowerCase());
                } else {
                    $wrapper.prepend('<i class="sz-icon flag ' + countryCode.toLowerCase() + '"><i>');
                }
            }

            return country + " / " + region;
        }),

        /**
         * Wrapper for the modal window object toggleModal
         */
        toggleModal: function () {
            if (typeof this.modal != 'undefined' && typeof this.modal.shippingZoneSelectorModal != 'undefined') {
                this.modal.shippingZoneSelectorModal('toggleModal');
            }
        },

        /**
         * Log anithing if debug is enabled in current object
         * @param data
         */
        log: function (data) {
            if (this.debug === true) {
                console.log(data);
            }
        }
    });
});
