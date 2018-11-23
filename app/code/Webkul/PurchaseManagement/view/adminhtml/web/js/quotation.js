/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PurchaseManagement
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

define([
    "jquery",
    'mage/mage'
], function ($,mage) {
    'use strict';
    $.widget('mage.quotation', {
        
        _create: function () {
            var self = this;
            $(document).ready(function () {
                $('div.entry-edit.form-inline').removeClass('form-inline');
                
                $("#submit_comment").click(function () {
                    var objective='quotation';
                    var status_id = $("#order_status").val();
                    if(status_id>1){
                        objective='order';
                    }
                    if($("#main_comment").val()!=''){
                        $.ajax({
                            url:self.options.comment_url,
                            type:'get',
                            showLoader: true,
                            data:{
                                order_id    : $(this).attr("data-orderid"),
                                status_id   : status_id,
                                comment     : $("#main_comment").val(),
                                is_notified : $("#comment_notify").is(":checked"),
                                objective   : objective
                            },
                            success:function (data) {
                                location.reload();
                            }
                        });
                    }
                });
                $(".update_item").click(function () {
                    $.ajax({
                        url: self.options.updateitem,
                        type:'get',
                        showLoader: true,
                        data    :   {   item_id  : $(this).attr("data-itemid"),
                                        qty      : $(this).parents(".border").find(".item_qty").val(),
                                        price    : $(this).parents(".border").find(".item_price").val()
                                    },
                        success:function () {
                            location.reload();
                        }
                    });
                });
                $(".delete_item").click(function () {
                    $.ajax({
                        url: self.options.deleteitem,
                        type:'get',
                        showLoader: true,
                        data    :   { 
                                        item_id  : $(this).attr("data-itemid"),
                                        qty      : $(this).parents(".border").find(".item_qty").val(),
                                        price    : $(this).parents(".border").find(".item_price").val()
                                    },
                        success:function () {
                            location.reload();
                        }
                    });
                });
            });
        },
        
    });
    return $.mage.quotation;
});