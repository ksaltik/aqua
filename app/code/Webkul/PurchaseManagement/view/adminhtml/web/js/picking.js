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
    "mage/mage",
    "Magento_Ui/js/modal/modal"
], function ($,mage,modal) {
    'use strict';
    $.widget('mage.createquotation', {
        
        _create: function () {
            var self = this;
           // var qid=self.options.question_id;
            var options = {
                type: 'popup',responsive: true,innerScroll: true,title: 'Partial Shipment',
                buttons: [{
                        text: 'Cancel',
                        class:'',
                        click: function () {
                            this.closeModal();
                        } //handler on button click
                    },{
                        text: 'Receive Now',
                        class: 'wk-partialshipment-submit',
                        click: function () {
                            // console.log("generate Shipment");
                            if($('#wk-partialshipment').validate()) {
                                $("#wk-partialshipment").submit();
                            }
                        } //handler on button click
                    }
                ]
            };
            var popup = modal(options, $('#popup_background'));
            
            $('#partial').on('click',function () {
                $('#popup_background').modal('openModal');
            
            });
            $(document).ready(function () {
                $("#submit_comment").click(function () {
                    var objective='picking';
                    if($("#main_comment").val()!=''){
                        $.ajax({
                            url:self.options.comment_url,
                            type:'get',
                            showLoader: true,
                            data:{
                                order_id    : $(this).attr("data-pickingid"),
                                status_id   : $("#picking_status").val(),
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
            });
        },
        
    });
    return $.mage.createquotation;
});