/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpRmaSystem
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define([
    "jquery",
    "Magento_Ui/js/modal/alert",
    "jquery/ui",
], function ($, alert) {
    'use strict';
    $.widget('mprma.rma', {
        options: {},
        _create: function () {
            var self = this;
            var totalPrice = self.options.totalPrice;
            var totalPriceWithCurrency = self.options.totalPriceWithCurrency;
            var errorMsg = self.options.errorMsg;
            var warningLable = self.options.warningLable;
            $(document).ready(function () {
                $(".wk-refund-amount").html(totalPriceWithCurrency);
                $(".wk-refundable-amount").html(totalPriceWithCurrency);
                $(".wk-refund-block").removeClass("wk-display-none");
                $("#payment_type").change(function (e) {
                    var val = $(this).val();
                    if (val == 1) {
                        $(".wk-partial-amount").hide();
                        $("#partial_amount").removeClass("required-entry");
                    } else {
                        $(".wk-partial-amount").show();
                        $("#partial_amount").addClass("required-entry");
                    }
                });
                $(".wk-refund").click(function (e) {
                    if ($('#wk_rma_refund_form').valid()) {
                        var price = $("#partial_amount").val();
                        if (price > totalPrice) {
                            alert({
                                title: warningLable,
                                content: "<div class='wk-mprma-warning-content'>"+errorMsg+"</div>",
                                actions: {
                                    always: function (){}
                                }
                            });
                            return false;
                        }
                    }
                });
            });
        }
    });
    return $.mprma.rma;
});
