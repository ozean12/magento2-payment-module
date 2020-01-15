define([
        'jquery',
        'Magento_Payment/js/view/payment/cc-form'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magento_BilliePaymentMethod/payment/payafterdelivery'
            },

            context: function() {
                return this;
            },

            getCode: function() {
                return 'magento_billiePaymentMethod';
            },

            isActive: function() {
                return true;
            }
        });
    }
);