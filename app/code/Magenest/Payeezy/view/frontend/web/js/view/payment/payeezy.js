/**
 * Copyright © 2017 Magenest. All rights reserved.
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        rendererList.push(
            {
                type: 'payeezy',
                component: 'Magenest_Payeezy/js/view/payment/method-renderer/direct'
            }
        );

        /**
         * Add view logic here if needed
         */

        return Component.extend({});
    }
);
