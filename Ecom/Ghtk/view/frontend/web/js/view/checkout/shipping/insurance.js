define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils'
], function (ko, Component, quote, priceUtils) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Ecom_Ghtk/checkout/shipping/insurance'
        },
        getFormattedPrice: function () {
            var insurance_amount = (window.checkoutConfig.ghtk != undefined) ? window.checkoutConfig.ghtk.insurance_amount : 0;
            return priceUtils.formatPrice(insurance_amount, quote.getPriceFormat());
        },
        getLabel: function () {
            return (window.checkoutConfig.ghtk != undefined) ? window.checkoutConfig.ghtk.insurance_message : '';
        },
        isDisplayed: function () {
            return (window.checkoutConfig.ghtk != undefined && window.checkoutConfig.ghtk.show == true);
        }
    });
});
