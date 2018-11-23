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
    "mage/calendar"
], function ($,mage,calendar) {
    'use strict';
    $.widget('mage.createquotation', {
        
        _create: function () {
            var self = this;
            $.extend(true, $, {
                calendarConfig: { 
                    // dateFormat: "mm/dd/yy",
                    showsTime: true
                }
            });
            $("#schedule_date").datetimepicker();
              // $("#schedule_date").calendar();
            $("document").ready(function () {
                $("#update_schedule_date").click(function () {
                    $.ajax({
                        url:'<?php echo $this->getUrl("purchasemanagement/move/updateScheduleDate"); ?>',
                        type:"post",
                        showLoader: true,
                        data:{id:$(this).attr("data-moveid"),
                            updateddate:$('#schedule_date').val()
                        },
                        success:function () {
                            location.reload();
                        }
                    });
                });
                $("#submit_comment").click(function () {
                    var objective='move';
                    if($("#main_comment").val()!=''){
                        $.ajax({
                            url:self.options.comment_url,
                            type:'get',
                            showLoader: true,
                            data:{
                                order_id    : $(this).attr("data-moveid"),
                                status_id   : $("#move_status").val(),
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