(function (w, d, s, o, f, js, fjs) {
    w['BillieCheckoutWidget'] = o;
    w[o] = w[o] || function () {
        (w[o].q = w[o].q || []).push(arguments)
    };
    w.billieSrc = f;
    js = d.createElement(s);
    fjs = d.getElementsByTagName(s)[0];
    js.id = o;
    js.src = f;
    js.charset = 'utf-8';
    js.async = 1;
    fjs.parentNode.insertBefore(js, fjs);
    bcw('init');
}(window, document, 'script', 'bcw', window.checkoutConfig.billie_payment.config.widget_url));
