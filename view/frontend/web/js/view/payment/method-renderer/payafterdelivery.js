define([
        'jquery',
        'Magento_Payment/js/view/payment/cc-form'
    ],
    function ($, Component) {
        'use strict';

        console.log('test2');
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