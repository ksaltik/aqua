/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpFedexShipping
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
/*global define*/
define(
    [
        'jquery',
        'mageUtils',
        './shipping-rates-validation-rules',
        'mage/translate'
    ],
    function ($, utils, validationRules, $t) {
        'use strict';
        var checkoutConfig = window.checkoutConfig;

        return {
            validationErrors: [],
            validate: function (address) {
                var rules = validationRules.getRules(),
                    self = this;

                $.each(rules, function (field, rule) {
                    if (rule.required && utils.isEmpty(address[field])) {
                        var message = $t('Field ') + field + $t(' is required.');
                        self.validationErrors.push(message);
                    }
                });

                if (!Boolean(this.validationErrors.length)) {
                    if (address.country_id == checkoutConfig.originCountryCode) {
                        return !utils.isEmpty(address.postcode);
                    }
                    return true;
                }
                return false;
            }
        };
    }
);
