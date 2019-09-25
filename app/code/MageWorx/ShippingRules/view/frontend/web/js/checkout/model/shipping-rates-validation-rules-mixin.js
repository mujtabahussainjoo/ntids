/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'mage/utils/wrapper'
],function ($, Wrapper) {
    "use strict";

    var additionalFields = [
        'region',
        'region_id',
        'street',
        'city',
        'country_id',
        'postcode'
    ];

    return function (origRules) {
        origRules.getObservableFields = Wrapper.wrap(
            origRules.getObservableFields,
            function (originalAction) {
                var fields = originalAction();

                additionalFields.forEach(function (field) {
                    if (fields.indexOf(field) === -1) {
                        fields.push(field);
                    }
                });

                return fields;
            }
        );

        return origRules;
    };
});
