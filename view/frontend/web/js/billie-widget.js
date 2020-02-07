define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/checkout-data'
], function ($, quote, selectShippingAddressAction, checkoutData) {
    'use strict';
    var bcwSrc = 'https://static-paella-sandbox.billie.io/checkout/billie-checkout.js';
    (function(w,d,s,o,f,js,fjs){
        w['BillieCheckoutWidget']=o;w[o]=w[o]||function(){(w[o].q=w[o].q||[]).push(arguments)};
        w.billieSrc=f;js=d.createElement(s);fjs=d.getElementsByTagName(s)[0];js.id=o;
        js.src=f;js.charset='utf-8';js.async=1;fjs.parentNode.insertBefore(js,fjs);bcw('init');
    }(window,document,'script','bcw', bcwSrc));

    function payWithBillie() {
        // prepare / get all the data and assign it to billie_order_data, please refer to 3a section
        BillieCheckoutWidget.mount({
            billie_config_data: billie_config_data,
            billie_order_data: billie_order_data
        })
            .then(function success(ao) {
                // code to execute when approved order
                console.log('Approved order', ao);
            })
            .catch(function failure(err) {
                // code to execute when there is an error or when order is rejected
                console.log('Error occurred', err);
            });
    }
    const test_order = checkoutData
    const billie_config_data = {
        'session_id': '868a9206-a70d-4aaa-8da8-62e3322a17f2',
        'merchant_name': 'test-user-1'
    }
    const billie_order_data = {
        "amount": { "net": 100, "gross": 100, "tax": 0 },
        "comment": "string",
        "duration": 30,
        "delivery_address": {
            "house_number": "string",
            "street": "string",
            "city": "string",
            "postal_code": "10000",
            "country": "DE",
            "addition": "string"
        },
        "debtor_company": {
            "name": "string",
            "established_customer": false,
            "address_house_number": "string",
            "address_street": "string",
            "address_city": "string",
            "address_postal_code": "10000",
            "address_country": "DE",
            "address_addition": "string"
        },
        "debtor_person": {
            "salutation": "m",
            "first_name": "string",
            "last_name": "string",
            "phone_number": "030 31199251",
            "email": "james.smith@example.com"
        },
        "line_items": [
            {
                "external_id": "string",
                "title": "string",
                "description": "string",
                "quantity": 1,
                "category": "string",
                "brand": "string",
                "gtin": "string",
                "mpn": "string",
                "amount": { "net": 100, "gross": 100, "tax": 0 },
            }
        ]
    };

});