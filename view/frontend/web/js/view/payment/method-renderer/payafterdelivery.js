// define([
//         'jquery',
//         'Magento_Payment/js/view/payment/cc-form'
//     ],
//     function ($, Component) {
//         'use strict';
//
//         console.log('test2');
//         return Component.extend({
//             defaults: {
//                 template: 'Magento_BilliePaymentMethod/payment/payafterdelivery'
//             },
//
//             context: function() {
//                 return this;
//             },
//
//             getCode: function() {
//                 return 'magento_billiePaymentMethod';
//             },
//
//             isActive: function() {
//                 return true;
//             }
//         });
//     }
// );
define([
    'Magento_Checkout/js/view/payment/default'
], function (Component) {
    'use strict';

    console.log('test2payafter');
    return Component.extend({
        defaults: {
            template: 'Magento_BilliePaymentMethod/payment/payafterdelivery'
        },

        /**
         * Returns send check to info.
         *
         * @return {*}
         */
        getMailingAddress: function () {
            return window.checkoutConfig.payment.checkmo.mailingAddress;
        },

        /**
         * Returns payable to info.
         *
         * @return {*}
         */
        getPayableTo: function () {
            return window.checkoutConfig.payment.checkmo.payableTo;
        }
    });
});