/*jshint browser:true jquery:true*/
/*global alert*/
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Potato_CheckoutNewsletter/js/action/place-order-mixin': true
            },
            'Afterpay_Payment/js/view/checkout/action/place-order': {
                'Potato_CheckoutNewsletter/js/action/place-order-mixin': true
            },
            'Amazon_Payment/js/action/place-order': {
                'Potato_CheckoutNewsletter/js/action/place-order-mixin': true
            }
        }
    }
};