/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Mpqa
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

define([
    "jquery",
    'mage/mage'
], function ($,mage) {
    'use strict';
    $.widget('mage.supplier_options', {
        
        _create: function () {
            var self = this;
            var count=self.options.count;
            var seller_list=self.options.supplier_list;
            $("#add_new_supplier").click(function () {
                $("#product_options_container").append("<div class='option-box'><table cellspacing='0' cellpadding='0' class='option-header data-grid data-grid-draggable'><thead><tr><th class='opt-req'>Choose Supplier<span class='required'>*</span></th><th class='opt-req'>Minimal Quantity<span class='required'>*</span></th><th class='opt-req'>Lead Time(In Days) <span class='required'>*</span></th><th class='opt-req'>Priority <span class='required'>*</span></th><th class='opt-order'>Status</th><th class='a-right'><button  class='delete-option' type='button' title='Delete Option'><span><span><span>Delete Option</span></span></span></button></th></tr></thead><tbody><tr><td><select class='admin__control-select select-product-option-type required-option-select required-entry' data-form-part='product_form' name='supplierarr[supplier]["+count+"]'><option value=''>-- Please select --</option>"+seller_list+"</select></td><td><input data-form-part='product_form' class='admin__control-text required-entry validate-zero-or-greater input-text required-entry' name='supplierarr[minimal]["+count+"]'/></td><td><input data-form-part='product_form' class='admin__control-text required-entry validate-zero-or-greater input-text required-entry' name='supplierarr[lead_time]["+count+"]'/></td><td><input data-form-part='product_form' class=' admin__control-text required-entry validate-zero-or-greater input-text required-entry' name='supplierarr[sequence]["+count+"]'/></td><td><select data-form-part='product_form' class='admin__control-select select-supplier-status-type required-option-select' name='supplierarr[status]["+count+"]' value='"+count+"'><option value='0'>Disabled</option><option value='1'>Enabled</option></select></td></tr></tbody></table><div class='grid suplier_one_column'><table class='border data-grid data-grid-draggable'><thead><tr class='headings'><th>Quantity <span class='required'>*</span></th><th>Cost Price <span class='required'>*</span></th><th class='last'>&nbsp;</th></tr></thead><tbody class='price_qty_container'></tbody><tfoot><tr><td class='a-right' colspan='100'><button data-thiscount='1' data-count="+count+" class='add' type='button' title='Add New Row'><span><span><span>Add New Row</span></span></span></button></td></tr></tfoot></table></div></div>");
                count ++;
            });
            $("body").on('click','.add',function () {
                var this_this=$(this);
                var this_count=$(this_this).attr('data-count');
                var this_row_count = $(this_this).attr("data-thiscount");
                $(this_this).parents(".suplier_one_column").find(".price_qty_container").append("<tr><td><input data-form-part='product_form' type='text' name='supplier["+this_count+"][qty]["+this_row_count+"]' class='admin__control-text input-text required-entry validate-digits'></td><td><input data-form-part='product_form' type='text' name='supplier["+this_count+"][price]["+this_row_count+"]' class='admin__control-text input-text required-entry validate-zero-or-greater'></td><td class='last'><span title='Delete row'><button class='delete delete-select-row icon-btn' type='button' title='Delete Row'><span><span><span>Delete Row</span></span></span></button></span></td></tr>");
                this_row_count++;
                $(this_this).attr("data-thiscount",this_row_count);
            });

            $("body").on('click','.delete-option',function () {
                var this_this=$(this);
                var id = $(this_this).attr("data-todelete-supplier");
                if(typeof id != "undefined")
                    $(".supplier_options").append("<input data-form-part='product_form' type='hidden' name='todeletesupplier["+id+"]' value='"+id+"'/>");
                $(this_this).parents(".option-box").remove();
            });

            $("body").on('click','.delete-select-row',function () {
                var this_this=$(this);
                var id = $(this_this).attr("data-todelete");
                if(typeof id != "undefined")
                    $(".supplier_options").append("<input data-form-part='product_form' type='hidden' name='todelete["+id+"]' value='"+id+"'/>");
                $(this_this).parents("tr").remove();
            });
        },
    });
    return $.mage.supplier_options;
});