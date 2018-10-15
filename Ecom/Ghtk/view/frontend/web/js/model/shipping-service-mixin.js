define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (shippingService) {
        var shippingRates = wrapper.wrap(shippingService.setShippingRates, function (_super, ratesData) {
            _super(ratesData);
            var insurance = false;
            $.each(ratesData, function( index, value ) {
                if (value.carrier_code == 'ghtk') {
                    insurance = true;
                }
            });

            window.checkoutConfig.ghtk.show = insurance;
            if (insurance == false) {
                $('.ghtk-checkout-insurance').hide();
                $('[name="order-insurance"]').prop("checked", false);
            } else {
                $('.ghtk-checkout-insurance').show();
            }
        });

        shippingService.setShippingRates = shippingRates;

        return shippingService;
    }
});