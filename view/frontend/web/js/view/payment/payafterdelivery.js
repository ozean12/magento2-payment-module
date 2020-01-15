define([
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push(
            {
                type: 'magento_billiePaymentMethod',
                component: 'Magento_BilliePaymentMethod/js/view/payment/method-renderer/payafterdelivery'
            }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    });