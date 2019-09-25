var config = {
    map: {
        '*': {
            "Magento_Checkout/js/view/payment": "Serole_BillingStep/js/view/payment",
			"Magento_Checkout/js/view/minicart": "Serole_BillingStep/js/view/minicart",
            "Magento_CheckoutAgreements/js/model/agreement-validator":'Serole_BillingStep/js/model/payment/agreement-validator',
            'Magento_Checkout/js/model/shipping-save-processor/default': 'Serole_BillingStep/js/model/shipping-save-processor/default',
            'Magento_Checkout/js/model/place-order': 'Serole_BillingStep/js/model/place-order',
            'Magento_Checkout/js/model/full-screen-loader': 'Serole_BillingStep/js/model/full-screen-loader',
            'Magento_CheckoutAgreements/template/checkout/checkout-agreements.html': 'Serole_BillingStep/template/checkout/checkout-agreements.html',			
			'Magento_Checkout/js/view/shipping': 'Serole_BillingStep/js/view/shipping'
        }
    }
};
