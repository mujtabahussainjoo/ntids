/*global define,alert*/
define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/select-billing-address'
    ],
    function (
        $,
        ko,
        quote,
        resourceUrlManager,
        storage,
        paymentService,
        methodConverter,
        errorProcessor,
        fullScreenLoader,
        selectBillingAddressAction
    ) {
        'use strict';

        return {
            saveShippingInformation: function () {
                var payload;

                if (!quote.billingAddress()) {
                    selectBillingAddressAction(quote.shippingAddress());
                }
                if($('[name="shippingAddress.deliveryemail"]').size() != 0) {
                    console.log("check this button");
                    var deliveryEmail = $('[name="deliveryemail"]').val();
                    var confirmDeliveryEmail = $('[name="confirmdeliveryemail"]').val();

                    $('.continue').click(function () {
                        $('.email-error').remove();
                    });

                    if ($('[name="deliveryemail"]').val() == '') {
                        if ($('[name="deliveryemail"]').val() == '') {
                            $('[name="deliveryemail"]').css('border-color', '#ed8380');
                            $('[name="deliveryemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">This is Required Filed</p>');
                        }
                        /* if($('[name="confirmdeliveryemail"]').val() == ''){
                             $('[name="confirmdeliveryemail"]').css('border-color','#ed8380');
                             $('[name="confirmdeliveryemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">This is Required Filed</p>');
                         }*/
                        throw new DOMException('Delivery email are not empty');
                    }

                    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                    if (regex.test(deliveryEmail) == false) {
                        if (regex.test(deliveryEmail) == false) {
                            $('[name="deliveryemail"]').css('border-color', '#ed8380');
                            $('[name="deliveryemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">Please Enter Valid Email</p>');
                        }
                        /* if(regex.test(confirmDeliveryEmail) == false){
                             $('[name="confirmdeliveryemail"]').css('border-color','#ed8380');
                             $('[name="confirmdeliveryemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">Please Enter Valid Email</p>');
                         }*/
                        throw new DOMException('Please Enter Valid Email');
                    }

                    /* if(deliveryEmail != confirmDeliveryEmail){
                         jQuery('[name="confirmdeliveryemail"]').css('border-color','#ed8380');
                         jQuery('[name="confirmdeliveryemail"]').after('<p class="email-error" style="color: #e02b27;font-size: 1.2rem;">Emails are not match</p>');
                         throw new DOMException('Emails are not match');
                     }*/

                }

                
                payload = {
                    addressInformation: {
                        shipping_address: quote.shippingAddress(),
                        billing_address: quote.billingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code,
                        extension_attributes:{
                            deliveryemail: $('[name="deliveryemail"]').val(),
                        }
                    }
                };

                fullScreenLoader.startLoader();

                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                        fullScreenLoader.stopLoader();
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                        fullScreenLoader.stopLoader();
                    }
                );
            }
        };
    }
);
