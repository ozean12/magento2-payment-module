define([
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';
        try {
            rendererList.push(
                {
                    type: 'magento_billiePaymentMethod',
                    component: 'Mgento_BilliePaymentMethod/js/view/payment/method-renderer/payafterdelivery'
                }
            );
        } catch ( e){

            console.log('test 1',e);
        }

        console.log('test2');
        /** Add view logic here if needed */
        return Component.extend({});
    });