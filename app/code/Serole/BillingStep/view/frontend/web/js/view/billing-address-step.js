define(
    [
        'ko',
        'jquery',
        'mage/url',
        'Magento_Ui/js/form/form',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/quote',
        'mage/translate',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/url-builder'
    ],
    function (
        ko,
        $,
        urlFormatter,
        Component,
        _,
        stepNavigator,
        quote,
        $t,
        customer,
        urlBuilder
    ) {
        'use strict';
 
        return Component.extend({
            defaults: {
                template: 'Serole_BillingStep/view/billing-step'
            },
 
            //add here your logic to display step,
            isVisible: ko.observable(quote.isVirtual()),
            isVirtual: quote.isVirtual(),
 
            /**
            *
            * @returns {*}
            */
            initialize: function () {
                this._super();
				
                //if (!quote.isVirtual()) { //update condition if you need to enable for virtual products
                    // register your step
                    stepNavigator.registerStep(
                        'custom-billing-step',
                        null,
                        $t('Billing Address'),
                        this.isVisible, _.bind(this.navigate, this),
                        14
                    );
               // }
 
            },
 
            /**
            * The navigate() method is responsible for navigation between checkout step
            * during checkout. You can add custom logic, for example some conditions
            * for switching to your custom step
            */
            navigate: function () {
 
            },
 
            /**
            * @returns void
            */
            navigateToNextStep: function () {
                this.saveCustomFields();
                stepNavigator.next();
            },

            saveCustomFields: function () {
                var billingemail;
                var confirmBillingemail;
                var sameAsDelivery='';
                billingemail = jQuery('input[name="billingemail"]').val();
                jQuery( ".action-update" ).trigger( "click" );
                confirmBillingemail = jQuery('input[name="confirmbillingemail"]').val();
				
				jQuery(document).ready(function($){
					//$('#billing-address-same-as-shipping-shared').click(function(){
						if($('#billing-address-same-as-shipping-shared').prop("checked") == true){
							sameAsDelivery=1;
							//alert("Checkbox is checked."+sameAsDelivery);
						}
						else if($('#billing-address-same-as-shipping-shared').prop("checked") == false){
							sameAsDelivery=0;
							//alert("Checkbox is unchecked."+ sameAsDelivery);
						}
					//});
				});				
				
                if(!billingemail){
                    billingemail = jQuery('#billingemail').val();
                }
                if(!confirmBillingemail){
                    confirmBillingemail = jQuery('#confirmbillingemail').val();
                }
                jQuery('.continue').click(function () {
                    jQuery('.email-error').remove();
                });
                if(billingemail == ''){
                    if(billingemail == ''){
                        jQuery('[name="billingemail"]').css('border-color','#ed8380');
                        //jQuery('#billingemail').css('border-color','#ed8380');
                        jQuery('[name="billingemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">This is Required Filed</p>');
                        //jQuery('#billingemail').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">This is Required Filed</p>');
                    }
                    /*if(confirmBillingemail == ''){
                        jQuery('[name="confirmbillingemail"]').css('border-color','#ed8380');
                        //jQuery('#confirmbillingemail').css('border-color','#ed8380');
                        jQuery('[name="confirmbillingemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">This is Required Filed</p>');
                        //jQuery('#confirmbillingemail').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">This is Required Filed</p>');
                    }*/
                    throw new DOMException('Billing email are not empty');
                }
				//console.log(jQuery(".field-error").length);
				

				
				
				var errorCount = parseInt(jQuery(".billing-address-form .field-error").length);
				if(errorCount > 0 && sameAsDelivery!=1)
				{
					//alert(errorCount);
					throw new DOMException('Some Billing Address Fileds are missing.');
				}

                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if(regex.test(billingemail) == false){
                    if(regex.test(billingemail) == false){
                        $('[name="billingemail"]').css('border-color','#ed8380');
                        $('[name="billingemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">Please Enter Valid Email</p>');
                    }
                    /*if(regex.test(confirmBillingemail) == false){
                        $('[name="confirmbillingemail"]').css('border-color','#ed8380');
                        $('[name="confirmbillingemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">Please Enter Valid Email</p>');
                    }*/
                    throw new DOMException('Please Enter Valid Email');
                }

                /*if(billingemail != confirmBillingemail){
                    jQuery('[name="confirmbillingemail"]').css('border-color','#ed8380');
                    jQuery('[name="confirmbillingemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">Emails are not match</p>');
                    throw new DOMException('Emails are not match');
                }*/

                var payload;
                var quoteId = quote.getQuoteId();
                var data = {
                    "billingemail": billingemail
                };

                var url = urlBuilder.createUrl('/carts/mine/set-order-billingemail', {});

                var payload = {
                    cartId: quoteId,
                    billingemail: data
                };
                var result = true;
                $.ajax({
                    url: urlFormatter.build(url),
                    data: JSON.stringify(payload),
                    global: false,
                    contentType: 'application/json',
                    type: 'PUT',
                    async: true
                }).done(
                    function (response) {
                        //cartCache.set('custom-form', formData);
                        result = true;
                    }
                ).fail(
                    function (response) {
                        result = false;
                        errorProcessor.process(response);
                    }
                );

            }
        });
    }
);
