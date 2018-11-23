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
    'mage/mage',
    "mage/template"
], function ($,mage,template) {
    'use strict';
    $.widget('mage.createquotation', {

        _create: function () {
            var self = this;
           // var qid=self.options.question_id;
           var source=self.options.source;
            $("#purchasemanagement_quotation_supplier").before("<div class='entry-edit'>"+
                "<div>"+
                "<strong><label for='po_source_doc' class='wk_pm_padding_right'>Source Document: </label></strong>"+
                "<input id='po_source_doc' type='text' name='source' class=' input-text' value='"+source+"'/>"+
                "</div>"+
                "<div class='entry-edit-head'>"+
                    "<span class='fieldset-legend quotation_legend'>Please Select a Supplier</span>"+
                    "<div id='add_selected_product_button' style='display:none;'>"+
                        "<button  class='scalable add add_selected_product_button' type='button' title='Add Selected Product(s) to Quotation'>"+
                            "<span><span><span>Add Selected Product(s) to Quotation</span></span></span>"+
                        "</button>"+
                    "</div>"+
                    "<div id='add_product_button' style='display:none;'>"+
                        "<button  class='scalable add' id='product_button' type='button'><span><span><span>Add Products</span></span></span></button>"+
                    "</div>"+
                "</div>"+
                "<div class='fieldset'>"+
                    "<div id='quotation_items' style='display:none;'>"+
                        "<div class='entry-edit'>"+
                            "<div class='entry-edit-head'>"+
                                "<h4 class='icon-head head-cart'>Items Ordered</h4>"+
                                "<div class='form-buttons'></div>"+
                            "</div>"+
                            "<div>"+
                                "<div id='order-items_grid' class='grid'>"+
                                    "<table cellspacing='0' id='quotation_items_list' class='data order-tables data-grid'>"+
                                        "<thead>"+
                                            "<tr class='headings'>"+
                                                "<th class='no-link data-grid-th'>Product</th>"+
                                                "<th class='no-link data-grid-th'>Cost Price</th>"+
                                                "<th class='no-link data-grid-th'>Qty</th>"+
                                                "<th class='no-link data-grid-th'>Subtotal</th>"+
                                                "<th class='no-link last data-grid-th'>Action</th>"+
                                            "</tr>"+
                                        "</thead>"+
                                        "<tbody class='even'>"+
                                        "</tbody>"+
                                   "</table>"+
                                "</div>"+
                            "</div>"+
                        "</div>"+
                    "</div>"+
                    "<div class='quotation_fieldset'></div>"+
                "</div>"+
            "</div>");
            $(".quotation_fieldset").append($("#purchasemanagement_quotation_supplier"));

            $("body").on("click","#purchasemanagement_quotation_supplier_table tbody tr",function (event) {
                // event.preventDefault();
                supplier_id = parseInt($(this).find("td.col-id").html());
                if(supplier_id){
                    $("#edit_form").append("<input type='hidden' value='"+supplier_id+"' name='supplier_id'/>");
                    $(".quotation_fieldset").html("").parent().parent().find("#add_product_button").show();
                    // $("#add_product_button").css('display','flex');
                    // $("#add_product_button").css('justify-content','flex-end');
                    $(".quotation_legend").text("Add Products");
                    $.ajax({
                        url: self.options.supplier_url,
                        type:'json',
                        method:'get',
                        showLoader: true,
                        data    :   {supplier_id:supplier_id},
                        success:function (content) {
                            $.each(content, function () {
                                var templateData = template('#supplier-details-container');
                                var questions = templateData({
                                                data: {
                                                    name:this['name'],
                                                    email:this['email'],
                                                    company:this['company'],
                                                    street:this['street'],
                                                    city:this['city'],
                                                    country:this['country'],
                                                    state:this['state'],
                                                    zip:this['zip'],
                                                    phone:this['phone']
                                                }
                                            });
                                $('.fieldset').after(questions);
                            });
                            $('.admin__scope-old').append($('.supplier_details_container'));
                            $('.supplier_details_container').show();
                        }
                    });
                }
            });

            var supplier_id = self.options.supplier_id;
            if (supplier_id){
                $("#purchasemanagement_quotation_supplier_table tbody tr").each(function () {
                    var thisSupplierId = parseInt($(this).find("td.col-id").html())
                        if(thisSupplierId == supplier_id){
                        $(this).trigger("click");
                        return;
                    }
                });
            }

            $("#product_button").on("click",function () {
                $.ajax({
                    url     :   self.options.product_grid,
                    type    :   "get",
                    showLoader: true,
                    dataType:   "html",

                    success :   function (content) {
                        $(".quotation_fieldset").html(content);
                        $("#loading-mask").hide();
                        $(".quotation_fieldset").parent().parent().find("#add_product_button").hide().prev().show();
                        // $(".to_cancel").show();
                        // $(".to_save").show();
                    }
                });
            });
            $(".add_selected_product_button").on("click",function () {
                //iterate product grid and add or update product to final list
                var is_selected_any = false;
                var total_price = 0;
                $(".quotation_fieldset").find(".checkbox").each(function () {
                    if($(this).is(":checked")){console.log(this);
                        is_selected_any = true;
                        var this_this = $(this);
                        var product_grid_tr = this_this.parents("tr"); console.log(product_grid_tr);
                        var pro_name = product_grid_tr.find("td").eq(1).text();
                        var pro_sku = product_grid_tr.find("td").eq(2).text();
                        var pro_option = product_grid_tr.find("dl");
                        var pro_option_buffer = "";
                        // console.log(pro_option);
                        var pro_qty = parseInt(product_grid_tr.find(".qty").val());
                        var pro_price = '0.0';
                        var cost_price = product_grid_tr.find("td").eq(3).text().trim(); console.log(cost_price);
                        if(cost_price != ""){
                            if(cost_price.split(self.options.currency_symbol)[1] == ""){
                                pro_price = cost_price.split(self.options.currency_symbol)[0].replace(/[^0-9.]/g, "");
                            }else{
                                pro_price = cost_price.split(self.options.currency_symbol)[1].replace(/[^0-9.]/g, "");
                            }
//after currency symbol cmnt below line
                            pro_price=cost_price;
                        }
                        console.log(pro_price);
                        var pro_id = this_this.val();
                        if(typeof $("#quotation_items tbody tr").attr("data-id") != "undefined"){
                            var is_this_pro_already_exist_in_final_list = 0;
                            $("#quotation_items tbody").find("tr").each(function () {
                                var final_list_tr = $(this);
                                if(final_list_tr.attr("data-id") == pro_id){
                                    pro_option_buffer = final_list_tr.find("dl");
                                    is_this_pro_already_exist_in_final_list = 1;
                                    var this_item_qty = parseInt(final_list_tr.find(".item-qty").val());
                                    this_item_qty += pro_qty;
                                    var this_row_html = "<td>"+
                                        "<h5 class='title'>"+pro_name+"</h5>"+
                                        "<div><strong>SKU:</strong>"+pro_sku+"</div>"+
                                        "<input type='hidden' class='purchase_pro_id' name='purchase[product][]' value='"+pro_id+"'/>"+
                                    "</td>"+
                                    "<td class='wk_price'>"+
                                        "<span class='wk_price'>"+pro_price+"</span>"+
                                    "</td>"+
                                    "<td>"+
                                        "<input maxlength='5' value='"+this_item_qty+"' class='input-text required-entry _required validate-digits item-qty' name='purchase[qty][]'>"+
                                    "</td>"+
                                    "<td class='wk_price'>"+
                                        "<span class='wk_price1'>"+(this_item_qty*pro_price)+"</span>"+
                                    "</td>"+
                                    "<td class='last'>"+
                                        "<button class='delete_item scalable delete' type='button' title='Delete'><span><span><span>Delete</span></span></span></button>"+
                                    "</td>";
                                    final_list_tr.html(this_row_html).append(pro_option_buffer);
                                    console.log("first wala");
                                    return;
                                }
                            });
                            if(is_this_pro_already_exist_in_final_list == 0){
                                $("#quotation_items tbody").append("<tr id='item_pro"+pro_id+"' data-id='"+pro_id+"'><td>"+
                                    "<h5 class='title'>"+pro_name+"</h5>"+
                                    "<div><strong>SKU:</strong>"+pro_sku+"</div>"+
                                    "<input type='hidden' class='purchase_pro_id' name='purchase[product][]' value='"+pro_id+"'/>"+
                                "</td>"+
                                "<td class='wk_price'>"+
                                    "<span class='wk_price'>"+pro_price+"</span>"+
                                "</td>"+
                                "<td>"+
                                    "<input maxlength='5' value='"+pro_qty+"' class='input-text item-qty required-entry _required validate-digits' name='purchase[qty][]'>"+
                                "</td>"+
                                "<td class='wk_price'>"+
                                    "<span class='wk_price'>"+pro_price+"</span>"+
                                "</td>"+
                                "<td class='last'>"+
                                        "<button class='delete_item scalable delete' type='button' title='Delete'><span><span><span>Delete</span></span></span></button>"+
                                "</td></tr>");//.append(pro_option);
                            $("#quotation_items tbody").find("#item_pro"+pro_id).append(pro_option);
                            console.log("second wala");
                            }
                        }
                        else{
                            $("#quotation_items tbody").append("<tr id='item_pro"+pro_id+"' data-id='"+pro_id+"'><td>"+
                                        "<h5 class='title'>"+pro_name+"</h5>"+
                                        "<div><strong>SKU:</strong>"+pro_sku+"</div>"+
                                        "<input type='hidden' class='purchase_pro_id' name='purchase[product][]' value='"+pro_id+"'/>"+
                                    "</td>"+
                                    "<td class='wk_price'>"+
                                        "<span class='wk_price'>"+pro_price+"</span>"+
                                    "</td>"+
                                    "<td>"+
                                        "<input maxlength='5' value='"+pro_qty+"' class='input-text item-qty required-entry _required validate-digits' name='purchase[qty][]'>"+
                                    "</td>"+
                                    "<td class='wk_price'>"+
                                        "<span class='wk_price'>"+(pro_price*pro_qty)+"</span>"+
                                    "</td>"+
                                    "<td class='last'>"+
                                        "<button class='delete_item scalable delete' type='button' title='Delete'><span><span><span>Delete</span></span></span></button>"+
                                    "</td></tr>");
                            $("#quotation_items tbody").find("#item_pro"+pro_id).append(pro_option);
                            console.log("else wala");
                        }
                        $("#quotation_items").show();
                    }
                });
                if(is_selected_any == false)
                    alert("Please select any product");
                else{
                    $(".to_cancel").show();
                    $(".to_save").show();
                }
            });

            $("#save").on("click",function (event) {
                console.log('prevent');
                event.preventDefault();
                console.log(event);
            });
            $("#submit_comment").click(function () {
                var objective='quotation';
                status_id = $("#order_status").val();
                if(status_id>1){
                    objective='order';
                }
                if($("#main_comment").val()!=''){
                    $.ajax({
                        url:self.options.save_url,
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
            $('body').on('click','.delete_item',function () {
                $(this).parents("tr").remove();
            });

            $("body").on('click',".admin__control-checkbox.checkbox",function () {
                var qty_input = $(this).parents("._clickable").find(".qty");
                var is_checked = $(this).is(":checked");
                var is_disabled = $(this).parents(".pointer").find(".f-right").attr("disabled");
                if(is_checked && qty_input.val() == "")
                    qty_input.val(1);
            });
        },

    });
    return $.mage.createquotation;
});
