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

            console.log('test',e);
        }

        console.log('test1',rendererList.push);
        /** Add view logic here if needed */
        return Component.extend({});
    });