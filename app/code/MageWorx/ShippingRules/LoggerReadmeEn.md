# MageWorx Shipping Rules Logger for Magento 2

## Logger usage

### Notes

1. The most appropriate way is to use the logger on the cart and checkout page, where the changing of the devivery costs happens quite often. On the regular pages, such as products grid or product page, the possible changes may not be visible.

2. The logger is initialized through *requireJS*, for this reason it may not be available in the first seconds of the page load.
The logger is loaded from:

	```
	pub/static/frontend/{theme_vendor}/{theme}/{locale}/MageWorx_ShippingRules/js/shippingRulesLogger.js
	```

3. To access the logger one should perform this command from the browser console

	```
	require('uiRegistry').get('shippingRulesLogger')
	```

### Entities
1. *Story* - array, iterations of calculations occur on the server side as soon as available, stores the *Info* 

2. *Info* - an object, an iteration of calculations on the server side, contains the shipping *Method* 

3. *Method* - an object, its key is the method code *carrier_method*, contains the list of all the rules that can be applied to this method (valid in terms of the store and the customer group). If the required rule is not found in the list, one should first check whether the rule in question is enabled for this store and the customer matches the appropriate customer group.

4. *Rule* - an object, its key is the rule id. Contains the rule info. Possible properties and the values:

	- **index** *(int)* - the rule application precedence. The first applied rule has 0.

	- **rule_id** *(string|int)* - the id of the corresponding rule, same as the key.

	- **sort_order** *(string|int)* - the rule priority order, provided in the rule configuration.

	- **valid** *(boolean)* - checks whether rule validation has successfully passed. If the value is *false*, the rule was not applied.

	- **processed** *(boolean)* - checks whether the rule could edit some shiiping method (disable, modify its cost, etc)

	- **disabled** *(boolean)* - checks whether the rule has disabled the method

	- **cost_overwrote** *(boolean)* - checks whether the rule has modified the method's cost

	- **invalid_shipping_method** *(boolean)* - specifies whether the current method is adjusted or not

	- **input_data** *(object)* - the method data on the input (before applying the rule, should be the same as the **output_data** of the previous rule)

		- **availability** *(boolean)* - checks whether the method is enabled (*false* for disabled)

		- **price** *(decimal)* - method cost

	- **output_data** *(object)* - the method data on the output (after applying the rule, should be the same as the **input_data** of the next rule)

		- **availability** *(boolean)* - checks whether the method is enabled (*false* for disabled)

		- **price** *(decimal)* - method cost

		- **detailed_actions** *(object)* - the list of the actions applied to this rule with the details.


### Methods

1. Get all the history:


		require('uiRegistry').get('shippingRulesLogger').getStory() 

		
2. Get the latest iteration of calculations:


		require('uiRegistry').get('shippingRulesLogger').getInfo()

		
3. Get the exact iteration of calculations (this example shows the first and the third iterations):


		require('uiRegistry').get('shippingRulesLogger').getInfo(0)
		require('uiRegistry').get('shippingRulesLogger').getInfo(2)

		
4. Forced history refresh  (request to the server):


		require('uiRegistry').get('shippingRulesLogger').refreshStory()

		
5. Check what was the first rule to disable the method (this example shows the methods **flatrate** and **length19testcarrier_firstclassdelivery**):
		

		require('uiRegistry').get('shippingRulesLogger').whenIsDisabled('flatrate_flatrate')
		require('uiRegistry').get('shippingRulesLogger').whenIsDisabled('length19testcarrier_firstclassdelivery')
 
		
	where ```flatrate``` and ```length19testcarrier``` are the carriers codes while ```flatrate``` и ```firstclassdelivery``` are the methods codes, so the structure is ('carriercode_methodcode'). 

6. Check what was the first rule to disable the method for some *Info* (info_id is the number):


		require('uiRegistry').get('shippingRulesLogger').whenIsDisabled('flatrate_flatrate', 1)
		require('uiRegistry').get('shippingRulesLogger').whenIsDisabled('length19testcarrier_firstclassdelivery', 5)

		
	where ```1``` and ```5``` are the info_ids. It can be specified by using the command  `getStory()`
		
7. Get the information for some method (by default it uses the last *Info*, if it is not specially provided in the end):


		require('uiRegistry').get('shippingRulesLogger').getMethodInfoByCode('length19testcarrier_firstclassdelivery')
		require('uiRegistry').get('shippingRulesLogger').getMethodInfoByCode('flatrate_flatrate', 1)

		
	second request checks the info_id 1.
		
8. Forced history refresh  (request to the server):


		require('uiRegistry').get('shippingRulesLogger').refreshStory()


9. Get the amount difference for some method that is modifyed by some rules:


		require('uiRegistry').get('shippingRulesLogger').showDiff('flatrate_flatrate', 21, 24)

		
	where ```21``` and ```24``` are the rule_ids. 

10. It is also possible to use the *Info* parameter (by default it uses the last *Info*, if it is not specially provided in the end):


		require('uiRegistry').get('shippingRulesLogger').showDiff('flatrate_flatrate', 21, 24, 4)

		
	where ```21``` and ```24``` are the rules_ids, ```4``` is the info_id