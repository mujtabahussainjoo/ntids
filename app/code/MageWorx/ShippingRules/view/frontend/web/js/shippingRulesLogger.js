/**
 * Created by siarhey on 8/5/17.
 */
define([
    'jquery',
    'underscore',
    'Magento_Customer/js/customer-data',
    'uiComponent',
    'mage/translate',
    'uiRegistry',
    'jquery/ui'
], function ($, _, customerData, Component, __, uiRegistry) {
    'use strict';

    return Component.extend({
        defaults: {
            name: 'shippingRulesLogger',
            index: 'shipping_rules_logger',
            story: {},
            submitUrl: ''
        },

        initialize: function () {
            this._super();
            this.refreshStory();
            uiRegistry.set('shippingRulesLogger', this);

            return this;
        },

        /**
         * Make request for a new data
         *
         * @returns {exports.refreshStory}
         */
        refreshStory: function () {
            var self = this;
            $.ajax(this.submitUrl, {
                type: 'POST',
                async: false,
                data: {},
                success: function (data) {
                    if (typeof data.success != 'undefined' && data.success) {
                        self._updateStory(data);
                    } else {
                        console.log(__('Seems there was some kind of backend error during request.'));
                        console.log(__('We can\'t get a right data on backend :('));
                        console.log(__('Please, check whats going wrong in the PHP.'));
                        console.log(
                            __(
                                'I\'ll recommend you to start debugging from the' +
                                'MageWorx\\ShippingRules\\Logger\\Index.php controller.'
                            )
                        );
                    }
                },
                error: function (e) {
                    console.log(__('Seems there was some kind of backend error during request.'));
                    console.log(__('Here is it:'));
                    console.log(e);
                }
            });

            return this;
        },

        /**
         * Get last info array
         *
         * @returns {*}
         */
        getInfo: function (id) {
            if (!this.story || !this.story.length) {
                console.log(__('The Story is empty :('));
                return null;
            }

            if (typeof id === 'undefined' || id === null || id === '') {
                console.log(__('We are using last info, because the info id is not specified.'));
                return this.story[this.story.length - 1];
            }

            if (typeof this.story[id] == 'undefined') {
                console.log(__('We can\'t find an info with ID ') + id);
                return null;
            }

            return this.story[id]
        },

        /**
         * Get all story
         *
         * @returns {exports.defaults.story|{}|*}
         */
        getStory: function () {
            return this.story;
        },

        /**
         * Update story with new data
         *
         * @param data
         * @returns {exports._updateStory}
         * @private
         */
        _updateStory: function (data) {
            if (typeof data.story === 'undefined') {
                console.log(__('Something went wrong. Please, check an output data:'));
                console.log(data);
                return this;
            }

            this.story = data.story;
            console.log(__('Story was successfully updated.'));

            return this;
        },

        /**
         *
         * @param methodCode {string}
         * @param infoId {int|null}
         * @returns {*}
         */
        whenIsDisabled: function (methodCode, infoId) {
            var rules = this.getMethodInfoByCode(methodCode, infoId);
            if (!rules) {
                console.log(__('There is no rules in the specified methods data.'));
            }
            var sortedRules = this.sortRulesByIndex(rules);

            for (var index in sortedRules) {
                if (!sortedRules.hasOwnProperty(index)) {
                    continue;
                }
                var sortedRule = sortedRules[index];
                if (typeof sortedRule.disabled == 'undefined' || !sortedRule.disabled) {
                    continue;
                }

                console.log(__('First Time Disabled in rule with id ') + sortedRule.rule_id);
                console.log(__('Here is that rule:'));
                return sortedRule;
            }

            console.log(__('Method is not disabled by any rule.'));
            return null;
        },

        showDiff: function (methodCode, rule1, rule2, infoId) {
            var rules = this.getMethodInfoByCode(methodCode, infoId);
            if (!rules) {
                console.log(__('There is no rules for the method ' + methodCode + ' in the info with id ' + infoId));
                return null;
            }

            var ruleOneInstance,
                ruleTwoInstance;

            if (typeof rule1 === 'Object') {
                ruleOneInstance = rule1;
            } else {
                if (!rules.hasOwnProperty(rule1)) {
                    console.log(__('There is no rule with id ' + rule1 + ' in the specified info.'));
                    return null;
                }
                ruleOneInstance = rules[rule1];
            }

            if (typeof rule2 === 'Object') {
                ruleTwoInstance = rule2;
            } else {
                if (!rules.hasOwnProperty(rule2)) {
                    console.log(__('There is no rule with id  ' + rule2 + '  in the specified info.'));
                    return null;
                }
                ruleTwoInstance = rules[rule2];
            }

            if (typeof ruleOneInstance === 'undefined' && typeof ruleTwoInstance === 'undefined') {
                console.log(__('Both rules are undefined.'));
                console.log(__('You should specify correct rules.'));
                console.log(__('Use rule instances or corresponding rule ID\'s.'));

                return null;
            } else if (typeof ruleOneInstance === 'undefined') {
                console.log(__('Rule one is undefined.'));
                console.log(__('You should specify correct rules.'));
                console.log(__('Use rule instances or corresponding rule ID\'s.'));

                return null;
            } else if (typeof ruleTwoInstance === 'undefined') {
                console.log(__('Rule two is undefined.'));
                console.log(__('You should specify correct rules.'));
                console.log(__('Use rule instances or corresponding rule ID\'s.'));

                return null;
            }

            return this._diff(ruleOneInstance, ruleTwoInstance);
        },

        _diff: function (r1, r2) {
            // Here we are saving first and second rules by its index (sort them).
            // Next we will show the difference between the first applied rule and the second applied rule in format:
            // Second.param - First.param = result
            var first,
                second;
            if (r1.index < r2.index) {
                first = r1;
                second = r2;
                console.log(__('Rule #1 applied first'));
            } else {
                first = r2;
                second = r1;
                console.log(__('Rule #2 applied first'));
            }

            // Show rules in console for manual review
            console.log(__('Here is the rule applied first:'));
            console.log(first);
            console.log(__('Here is the rule that applies after:'));
            console.log(second);

            // Check valid rules
            if (first.valid === false) {
                console.log(__('The First rule is invalid and does not change anything.'));
                console.log(__('Please, specify a valid rules to see a difference.'));
                return null;
            }

            if (second.valid === false) {
                console.log(__('The Second rule is invalid and does not change anything.'));
                console.log(__('Please, specify a valid rules to see a difference.'));
                return null;
            }

            var secondRuleInputPrice = this._getRuleInputPrice(second),
                firstRuleOutputPrice = this._getRuleOutputPrice(first),
                secondRuleOutputPrice = this._getRuleOutputPrice(second);

            var priceDiff;
            if (firstRuleOutputPrice == secondRuleInputPrice) {
                console.log(__('From the first to second rule price has not been changed.'));
                howPriceWasChangedInSecondRule();
            } else if (firstRuleOutputPrice > secondRuleInputPrice) {
                priceDiff = firstRuleOutputPrice - secondRuleInputPrice;
                console.log(__('From the first to second rule ') + priceDiff + __(' was added to the method price.'));
                howPriceWasChangedInSecondRule();
            } else if (firstRuleOutputPrice < secondRuleInputPrice) {
                priceDiff = secondRuleInputPrice - firstRuleOutputPrice;
                console.log(__('From the first to second rule the price was decreased to ') + priceDiff);
                howPriceWasChangedInSecondRule();
            }

            var firstRuleInputMethodAvailability = this._getRuleInputAvailability(first),
                firstRuleOutputMethodAvailability = this._getRuleOutputAvailability(first),
                secondRuleInputMethodAvailability = this._getRuleInputAvailability(second),
                secondRuleOutputMethodAvailability = this._getRuleOutputAvailability(second);

            if (!firstRuleInputMethodAvailability) {
                console.log(__('Method was disabled before the first rule processing.'));
            } else if (firstRuleInputMethodAvailability && !firstRuleOutputMethodAvailability) {
                console.log(__('Method was disabled during first rule processing.'));
            } else if (firstRuleOutputMethodAvailability && !secondRuleInputMethodAvailability) {
                console.log(__('Method was disabled somewhere between first and second rules.'));
            } else if (secondRuleInputMethodAvailability && !secondRuleOutputMethodAvailability) {
                console.log(__('Method was disabled during second rule processing.'));
            } else {
                console.log(__('Method is not disabling by this rules and by rules between.'));
            }

            function howPriceWasChangedInSecondRule()
            {
                if (secondRuleInputPrice === secondRuleOutputPrice) {
                    console.log(__('Second rule does not change method price.'));
                } else if (secondRuleInputPrice < secondRuleOutputPrice) {
                    var secondRuleAdds = secondRuleOutputPrice - secondRuleInputPrice;
                    console.log(__('Second rule adds ') + secondRuleAdds + __(' to the method price.'));
                } else if (secondRuleInputPrice > secondRuleOutputPrice) {
                    var secondRuleDecreasePrice = secondRuleInputPrice - secondRuleOutputPrice;
                    console.log(__('Second rule decreases method price by ') + secondRuleDecreasePrice);
                }
            }
        },

        _getRuleInputAvailability: function (rule) {
            return rule.input_data.availability;
        },

        _getRuleOutputAvailability: function (rule) {
            return rule.output_data.availability;
        },

        _getRuleInputPrice: function (rule) {
            return rule.input_data.price;
        },

        _getRuleOutputPrice: function (rule) {
            return rule.output_data.price;
        },

        /**
         * Return method info by specified method code & info id
         * If info id is not specified returns data for the last info
         *
         * @param methodCode
         * @param infoId
         * @returns {*}
         */
        getMethodInfoByCode: function (methodCode, infoId) {
            var info = this.getInfo(infoId);

            if (!info) {
                return null;
            }

            if (!info.hasOwnProperty(methodCode)) {
                return null;
            }
            var methodData = info[methodCode];

            if (!methodData) {
                return null;
            }

            return methodData.rules;
        },

        /**
         * Sort rules by index (in order they was applied)
         *
         * @param rules
         * @returns {Array}
         */
        sortRulesByIndex: function (rules) {
            var sortedRules = [];
            for (var i in rules) {
                if (!rules.hasOwnProperty(i)) {
                    continue;
                }
                var rule = rules[i];
                sortedRules[rule.index] = rule;
            }

            return sortedRules;
        }
    });


});