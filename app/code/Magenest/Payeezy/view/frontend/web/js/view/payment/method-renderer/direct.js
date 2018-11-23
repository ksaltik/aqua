define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function ($, ccFormComponent, additionalValidators) {
        'use strict';

        return ccFormComponent.extend({
            defaults: {
                template: 'Magenest_Payeezy/payment/payeezy-direct-form'
            },
            placeOrderHandler: null,
            validateHandler: null,

            /**
             * @param {Function} handler
             */
            setPlaceOrderHandler: function (handler) {
                this.placeOrderHandler = handler;
            },

            /**
             * @param {Function} handler
             */
            setValidateHandler: function (handler) {
                this.validateHandler = handler;
            },

            /**
             * @returns {Object}
             */
            context: function () {
                return this;
            },

            /**
             * @returns {Boolean}
             */
            isShowLegend: function () {
                return true;
            },

            /**
             * @returns {String}
             */
            getCode: function () {
                return 'payeezy';
            },

            /**
             * @returns {Boolean}
             */
            isActive: function () {
                return true;
            },

            validate: function() {
                return true;
            }
        });
    }
);
