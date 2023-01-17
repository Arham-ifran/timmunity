// On product Change from get more vouchers form
$('body').on('change', 'select[name="product_id[]"]', function() {
    variation_count = $(this).find(':selected').data('variation-count');
    product_selection = $(this).find(':selected');
    val = $(this).find(':selected').val();
    // product_selection = $(this);
    // val = $(this).val();
    // val = $(this).val();
    url = url.replace(":id", $(this).val());
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            country_id : $('#getVoucherModal [name=country_id]').val(),
            reseller_id: $('select[name=reseller_id]').val()
        },
        success: function(data) {
            if (data['success'] == 'true') {
                // console.log(data)
                variation_div = product_selection.parents('.product_row').find('.variation_selection');
                variation_select = product_selection.parents('.product_row').find('.variation_selection select');

                product_price_div = product_selection.parents('.product_row').find('.product_price');
                product_price_label = product_selection.parents('.product_row').find('.product_price .product_price_label strong');
                if (variation_count > 0) {
                    variations = data.data.variations;
                    variation_select.find('option').remove().end()
                    variation_select.append('<option value="">' + variation_select_default_text + '</option>');
                    $.each(variations, function(index, value) {
                        variation_select.append('<option value="' + value.hashedid + '">' + data.data.product_name + ' ' + value.variation_name + '</option>')
                    })
                    variation_select.attr('required', 'required');
                    variation_div.show();
                } else {
                    variation_select.removeAttr('required', 'required');
                    variation_div.hide();
                }
                product_price = data.data.product_price;
                if (parseFloat(data.data.end_product_price) != 0 && parseFloat(data.data.product_price) < parseFloat(data.data.end_product_price)) {
                    product_price += ' - ' + data.data.end_product_price;
                }
                product_price_div.show();
                product_price_label.html(currency + product_price + ' ' + currency_code);
            }
            url = url.replace(val, ":id");
        },
        complete: function(data) {
            url = url.replace(val, ":id");
        }
    });
    html = '';
    length = $('option:selected', this).data('secondary-projects').length;
    if (typeof($('option:selected', this).data('secondary-projects')) == 'string') {
        html = $('option:selected', this).data('secondary-projects');
    } else {
        $('option:selected', this).data('secondary-projects').forEach(function(val, ind) {
            html += val;
            if (ind < length - 1) {
                html += ', ';
            }
        });
    }
    secondary_project_div = product_selection.parents('.product_row').find('.secondary_projects_div');
    secondary_project_data = product_selection.parents('.product_row').find('.secondary_projects_div .data');

    if (length > 0) {
        secondary_project_div.show();
        secondary_project_data.html(html);
    } else {
        secondary_project_div.hide();
    }
});

// On Variation Change
$('body').on('change', 'select[name="variation_id[]"]', function() {
    // url = "{{ route('get-product-variation-detail', ':id') }}";
    val = $(this).val();
    if(val != '' ){
        detail_url = detail_url.replace(":id", $(this).val());
        variation_selection = $(this);
        $.ajax({
            url: detail_url,
            type: 'GET',
            data: {
                country_id : $('#getVoucherModal [name=country_id]').val(),
                reseller_id: $('[name=reseller_id]').val()
            },
            success: function(data) {
                product_price_div = variation_selection.parents('.product_row').find('.product_price');
                product_price_label = variation_selection.parents('.product_row').find('.product_price .product_price_label strong');
                if (data['success'] == 'true') {
                    // console.log(data)
                    product_price = data.data;
                    product_price_div.show();
                    product_price_label.html(currency + product_price + ' ' + currency_code);
                }
                detail_url = detail_url.replace(val, ":id");
            },
            complete: function(data) {
                detail_url = detail_url.replace(val, ":id");
            }
        })
    }else{
        $('select[name="product_id[]"]').trigger('change');
    }
});
// On changing the country
$('body').on('change', '[name=country_id]', function() {
    vat_percentage = 0;
    vat_label = '';
    selected_option = $('option:selected', this);
    if (selected_option.data('is_default_vat') == 1) {
        vat_percentage = selected_option.data('default_vat');
        if (selected_option.data('vat_label')) {
            vat_label = selected_option.data('vat_label');
        } else {
            vat_label = 'VAT';
        }
    } else {
        vat_percentage = selected_option.data('vat_vercentage');
        if (selected_option.data('vat_label')) {
            vat_label = selected_option.data('vat_label');
        } else {
            vat_label = 'VAT';
        }
    }
    $("[name=vat_percentage]").val(vat_percentage)
    $(".vat_percentage").html(vat_percentage)
    $(".vat-label-d").html(vat_label)
    if($('select[name="product_id[]"]').val() != ''){
        $('select[name="product_id[]"]').trigger('change');
    }
    if($('select[name="variation_id[]"]').val() != ''){
        $('select[name="variation_id[]"]').trigger('change');
    }
})

// On clicking add new product
$('body').on('click', '#add_new_product_btn', function() {
    html = ' <div class="row product_row mb-2 mt-2">';
    html += '<div class="col-xs-12">';
    html += '<div class="form-group">';
    html += '<label for="product_id" class="control-label">' + product_label + '<span style="color:red">*</span> </label>';
    html += '<span class="remove-product"> <i class="fa fa-trash"></i></span>';
    html += '<select required name="product_id[]" id="" class="form-control">';
    html += '<option value="">' + select_product_label + '</option>';
    $.each(products, function(index, product) {
        html += '<option value="' + product.hashed_id + '" data-secondary-projects="' + product.secondary_projects + '" data-variation-count="' + product.variations_count + '">' + product.product_name + '</option>';
    });
    html += '</select>';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-xs-12 secondary_projects_div" style="display:none;">';
    html += '<div class="form-group">';
    html += '<label for="variations_id" class="control-label">' + secondary_project_label + '</label>';
    html += '<div class="data">';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-xs-12 variation_selection" style="display:none;">';
    html += '<div class="form-group">';
    html += '<label for="variation_id" class="control-label">' + variation_label + '<span style="color:red">*</span></label>';
    html += '<select name="variation_id[]" id="" class="form-control">';
    html += '</select>';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-xs-12 product_price" style="display:none;">';
    html += '<div class="form-group">';
    html += '<label for="product_price" class="product_price_label control-label">' + price_label + ' &nbsp;<span style="color:green"><strong></strong></span></label>';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-md-12 mb-2">';
    html += '<label for="quantity" class="control-label">' + quantity_label + ' <small class="asterik" style="color:red">*</small></label>';
    html += '<input required type="number" min="1" class="form-control" name="quantity[]" />';
    html += '</div>';
    html += '</div>';

    $('#product_section_column').append(html);
});

$('body').on('click', '.remove-product', function() {
    $(this).closest('.product_row').remove();
})