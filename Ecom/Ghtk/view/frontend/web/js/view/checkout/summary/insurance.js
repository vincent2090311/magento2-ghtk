define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals'
], function (Component, quote, totals) {
    "use strict";

    return Component.extend({
        defaults: {
            template: 'Ecom_Ghtk/checkout/summary/insurance'
        },
        totals: quote.getTotals(),
        isDisplayed: function() {
            return this.getPureValue() != 0;
        },
        getPureValue: function() {
            let price = 0;
            if (this.totals() && totals.getSegment('insurance')) {
                price = totals.getSegment('insurance').value;
            }
            return price;
        },
        getValue: function() {
            let price = this.getPureValue();
            return this.getFormattedPrice(price);
        }
    });
});