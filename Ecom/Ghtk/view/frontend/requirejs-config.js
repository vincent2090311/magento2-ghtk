var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Ecom_Ghtk/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/model/shipping-service': {
                'Ecom_Ghtk/js/model/shipping-service-mixin': true
            }
        }
    }
};