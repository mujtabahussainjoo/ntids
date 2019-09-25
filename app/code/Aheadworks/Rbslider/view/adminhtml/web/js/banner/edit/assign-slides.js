/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Assign slide to banner
 *
 * @method registerSlides(grid, element, checked)
 * @method slideRowClick(grid, event)
 * @method slideRowClick(grid, event)
 * @method positionChange(event)
 * @method slideRowInit(grid, row)
 */
define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedSlides = config.selectedSlides,
            slides = $H(selectedSlides),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;
        $('slide_position').value = Object.toJSON(slides);

        /**
         * Register Category Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerSlides(grid, element, checked) {
            if (checked) {
                if (element.positionElement) {
                    element.positionElement.disabled = false;
                    slides.set(element.value, element.positionElement.value);
                }
            } else {
                if (element.positionElement) {
                    element.positionElement.disabled = true;
                }
                slides.unset(element.value);
            }
            $('slide_position').value = Object.toJSON(slides);
            grid.reloadParams = {
                'selected_slides[]': slides.keys()
            };
        }

        /**
         * Click on slide row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function slideRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        /**
         * Change slide position
         *
         * @param {String} event
         */
        function positionChange(event) {
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                slides.set(element.checkboxElement.value, element.value);
                $('slide_position').value = Object.toJSON(slides);
            }
        }

        /**
         * Initialize slide row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function slideRowInit(grid, row) {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                position = $(row).getElementsByClassName('input-text')[0];

            if (checkbox && position) {
                checkbox.positionElement = position;
                position.checkboxElement = checkbox;
                position.disabled = !checkbox.checked;
                position.tabIndex = tabIndex++;
                Event.observe(position, 'keyup', positionChange);
            }
        }

        gridJsObject.rowClickCallback = slideRowClick;
        gridJsObject.initRowCallback = slideRowInit;
        gridJsObject.checkboxCheckCallback = registerSlides;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                slideRowInit(gridJsObject, row);
            });
        }
    };
});
