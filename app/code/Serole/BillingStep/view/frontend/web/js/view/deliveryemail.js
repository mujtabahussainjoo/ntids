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
        /**
         * check-login - is the name of the component's .html template
         */
        return Component.extend({
            defaults: {
                template: 'Serole_BillingStep/deliveryemail'
            },
            isVisible: ko.observable(quote.isVirtual()),
            isLogedIn: customer.isLoggedIn(),
            stepCode: 'delivery-email-step',
            stepTitle: 'Delivery Email',

            initialize: function () {
                var giftEmail = localStorage.getItem('giftEmail');
                if(giftEmail){
                    console.log(giftEmail);
                    setInterval(function() {
                        console.log(111);
                        $('[name="deliveryemail"]').val(giftEmail);
                    }, 1000);
                }
              this._super();
              if(quote.isVirtual()) {
                  stepNavigator.registerStep(
                      'delivery-email-step',
                      null,
                      $t('Delivery Email'),
                      this.isVisible, _.bind(this.navigate, this),
                      11
                  );

              }

                return this;
            },

            navigate: function () {

            },

            navigateToNextStep: function () {
                this.saveCustomFields();
                stepNavigator.next();
            },

            saveCustomFields: function () {
                var deliveryemail;
                var confirmDeliveryEmail
                deliveryemail = jQuery('input[name="deliveryemail"]').val();
                confirmDeliveryEmail = jQuery('[name="confirmdeliveryemail"]').val();
                //console.log(confirmDeliveryEmail);
                if(!deliveryemail){
                    deliveryemail = jQuery('#deliveryemail').val();
                }
                if(!confirmDeliveryEmail){
                    confirmDeliveryEmail = jQuery('#confirmdeliveryemail').val();
                }

                jQuery('.continue').click(function () {
                    jQuery('.email-error').remove();
                });

                if(deliveryemail == ''){
                    if(deliveryemail == ''){
                        jQuery('input[name="deliveryemail"]').css('border-color','#ed8380');
                        jQuery('input[name="deliveryemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">This is Required Filed</p>');
                    }
                    /*if(confirmDeliveryEmail == ''){
                        jQuery('[name="confirmdeliveryemail"]').css('border-color','#ed8380');
                        jQuery('[name="confirmdeliveryemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">This is Required Filed</p>');
                    }*/
                    throw new DOMException('Delivery email are not empty');
                }

                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if(regex.test(deliveryemail) == false){
                    if(regex.test(deliveryemail) == false){
                        //console.log("Email validation");
                        jQuery('input[name="deliveryemail"]').css('border-color','#ed8380');
                        jQuery('input[name="deliveryemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">Please Enter Valid Email</p>');
                    }
                    /*if(regex.test(confirmDeliveryEmail) == false){
                        //console.log("confirm Email validation");
                        jQuery('[name="confirmdeliveryemail"]').css('border-color','#ed8380');
                        jQuery('[name="confirmdeliveryemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">Please Enter Valid Email</p>');
                    }*/
                    throw new DOMException('Please Enter Valid Email');
                }

                /*if(deliveryemail != confirmDeliveryEmail){
                    jQuery('[name="confirmdeliveryemail"]').css('border-color','#ed8380');
                    jQuery('[name="confirmdeliveryemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">Emails are not match</p>');
                    throw new DOMException('Emails are not match');
                }*/


                var payload;
                var quoteId = quote.getQuoteId();
                var data = {
                    "deliveryemail": deliveryemail
                };

                var url = urlBuilder.createUrl('/carts/mine/set-order-deliveryemail', {});

                var payload = {
                    cartId: quoteId,
                    deliveryemail: data
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

                return result;
            }
        });
    }
);


//  fullScreenLoader.startLoader();

/*  return storage.post(
      resourceUrlManager.getUrlForSetShippingInformation(quote),
      JSON.stringify(payload)
  ).done(
      function (response) {
          console.log(response);
          quote.setTotals(response.totals);
          paymentService.setPaymentMethods(methodConverter(response.payment_methods));
          fullScreenLoader.stopLoader();
      }
  ).fail(
      function (response) {
          console.log("fails");
          errorProcessor.process(response);
          fullScreenLoader.stopLoader();
      }
  );*/