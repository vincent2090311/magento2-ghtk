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
            var insurance_amount = (window.checkoutConfig.gthk != undefined) ? window.checkoutConfig.gthk.insurance_amount : 0;
            return priceUtils.formatPrice(insurance_amount, quote.getPriceFormat());
        },
        getLabel: function () {
            return (window.checkoutConfig.gthk != undefined) ? window.checkoutConfig.gthk.insurance_message : '';
        },
        isDisplayed: function () {
            return window.checkoutConfig.gthk != undefined;
        }
    });
});
