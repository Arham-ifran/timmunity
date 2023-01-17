@extends('admin.layouts.app')
@section('title', __('Price Lists'))
@section('content')
<div class="content-wrapper">
    <section class="content-header top-header">
        <div class="row">
            <div class="col-md-12">
                <h2>
                    {{ __('Price List') }} /
                    <small>@if(@$action == "Edit") {{ __('Edit') }} @else {{ __('Add') }} @endif</small>
                </h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-4">
                        <a class="skin-gray-light-btn btn save-pricelist-d" href="javascript:void(0)">@if(@$action == "Edit") {{ __('Update') }} @else {{ __('Save') }} @endif</a>
                        <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{route('admin.price-lists.index')}}">{{ __('Discard') }}</a>
                        @can('Archive / Unarchive Price List')
                            @if(@$action == "Edit")
                            <a class="btn status-pricelist-d" href="javascript:void(0)" style="{{ $model->is_active == 1 ?  'color:red;' : ''}}">{{ $model->is_active == 1 ? __('Archive') : __('Activate') }}</a>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-validate" id="pricelist-form" method="POST" action="{{ route('admin.price-lists.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $id }}">
                            <input type="hidden" name="is_active" value="1">
                            <input type="hidden" name="action" value="{{ $action }}">
                            <div class="row">
                                <div class="row">
                                    <div class="col-md-6 pl-0">
                                        <div class="col-sm-6" style="padding-left: 0px;">
                                            <div class="form-group col-sm-12">
                                                <h3>{{ __('Name') }}<small class="asterik" style="color:red">*</small></h3>
                                                <input type="text" name="name" class="form-control" placeholder="{{ __('e.g. USD Retailers') }}" value="{{@$model->name}}" />
                                            </div>
                                        </div>
                                    </div>
                                    @if(@$model->type == 1)
                                    <div class="col-md-4 pl-0">
                                    @else
                                    <div class="col-md-6 pl-0">
                                    @endif
                                        <div class="form-group col-sm-12">
                                            <h3>{{ __('PriceList Type') }}<small class="asterik" style="color:red">*</small></h3>
                                            <select name="type" id="price_list_type" class="form-control">
                                                <option value="0" @if(@$model->type == 0) selected @endif>{{'Simple'}}</option>
                                                <option value="1" @if(@$model->type == 1) selected @endif>{{'Prefix Based'}}</option>
                                            </select>
                                        </div>
                                    </div>

                                    @if(@$model->type == 1)
                                        <p class="col-md-2 btn btn-primary mt-4" data-toggle="modal" data-target="#childrens-modal">{{ __('View Sub Pricelists') }}</p>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="custom-tabs mt-3">
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a data-toggle="tab" href="#price-rule">{{ __('Price Rule') }}</a></li>
                                                <li><a data-toggle="tab" href="#Configration">{{ __('Configuration') }}</a></li>
                                            </ul>
                                            <div class="tab-content">
                                                <div id="price-rule" class="tab-pane fade in active">
                                                    @if(isset($model))
                                                        @php
                                                            $price_list_rule_ids = '';
                                                        @endphp
                                                        @foreach(@$model->rules as $r)
                                                            @php
                                                                $price_list_rule_ids .= $r->id.',';
                                                            @endphp
                                                        @endforeach
                                                    @endif
                                                    <input type="hidden" name="pricelist_rule_ids" value="{{ @$price_list_rule_ids }}">
                                                    <div class="row">
                                                        <table id="example1" class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('Applicable on') }}</th>
                                                                    <th>{{ __('Min. Quantity') }}</th>
                                                                    <th>{{ __('Amount') }}</th>
                                                                    <th>{{ __('Start Date') }}</th>
                                                                    <th>{{ __('End Date') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if(isset($model))
                                                                    @foreach(@$model->rules as $price_rule)
                                                                    <tr class="rule_line" data-id="{{ $price_rule->id }}">
                                                                        <td class="apply_on">
                                                                            @switch($price_rule->apply_on)
                                                                                @case(0)
                                                                                    {{ __('All Products') }}
                                                                                    @break
                                                                                @case(1)
                                                                                    {{ __('Product Category') }}
                                                                                    @break
                                                                                @case(2)
                                                                                    {{ __('Product') }}
                                                                                    @break
                                                                                @case(3)
                                                                                    {{ __('Product Variant') }}
                                                                                    @break
                                                                            @endswitch
                                                                        </td>
                                                                        <td class="min_qty">{{ $price_rule->min_qty }}</td>
                                                                        <td class="amount">
                                                                            @switch($price_rule->price_computation)
                                                                                @case(0)
                                                                                    {{ $price_rule->fixed_value }}
                                                                                    @break
                                                                                @case(1)
                                                                                    {{ $price_rule->percentage_value }} %
                                                                                    @break
                                                                                @default

                                                                            @endswitch
                                                                        </td>
                                                                        <td class="starte_date">
                                                                            {{ \Carbon\Carbon::parse( $price_rule->start_date )->format('d/M/y') }}
                                                                        </td>
                                                                        <td class="end_date">
                                                                            {{ \Carbon\Carbon::parse( $price_rule->end_date )->format('d/M/y') }}
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                @endif
                                                                <tr>
                                                                    <td><a href="#." id="addPriceRuleBtn">{{ __('Add New Rule') }}</a></td>

                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div id="Configration" class="tab-pane fade">
                                                    <div class="row tab-form" >
                                                        <div class="col-md-6 ">
                                                            <div class="row">
                                                                <h3 class="col-md-12">
                                                                    {{ __('Availability') }}
                                                                </h3>
                                                                <div class="col-sm-4">
                                                                    <h4>{{ __('Country Group') }}</h4>
                                                                </div>
                                                                <div class="col-sm-8 form-group">
                                                                    <select class="form-control" name="config[country_group_id]">
                                                                        <option value="">---{{ __('Select a country group') }}---</option>
                                                                        @foreach($country_group as $c_group)
                                                                            <option @if(@$model->configuration->country_group_id == $c_group->id) selected="selected" @endif value="{{Hashids::encode($c_group->id)}}">{{$c_group->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6" style="margin-top: 53px;">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <h4 id="code_heading">{{ __('E-commerce Promotion Code') }}</h4>
                                                                </div>
                                                                <div class="col-sm-6 form-group">
                                                                    <input type="text" name="config[promotion_code]" class="form-control" value="{{ @$model->configuration->promotion_code }}"/>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <h4>{{ __('Selectable') }}</h4>
                                                                </div>
                                                                <div class="col-sm-6 form-group">
                                                                    <input type="checkbox" name="config[selectable]" value="1" @if(@$model->configuration->selectable) checked="checked" @endif />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 ">
                                                            <div class="row">
                                                                <div class="col-sm-4">
                                                                    <h4>{{ __('Country') }}</h4>
                                                                </div>
                                                                <div class="col-sm-8 form-group">
                                                                    <select class="form-control" name="config[country_id]">
                                                                        <option value="">---{{ __('Select a country') }}---</option>
                                                                        @foreach($countries as $country)
                                                                            <option @if(@$model->configuration->country_id == $country->id) selected="selected" @endif value="{{Hashids::encode($country->id)}}">{{$country->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Add Language Model -->
<div class="modal fade" id="price_rule_modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title col-md-9 pl-0" id="exampleModalLabel">{{ __('Price List Rule') }}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="rule_form">
                    <input type="hidden" name="rule_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <h4 class="col-md-4">{{ __('Apply On') }}</h4>
                                <div class="col-md-8">
                                    <ul style="list-style:none;">
                                        <li>
                                            <label><input type="radio" name="apply_on" value="0"> {{ __('All Products') }}</label>
                                        </li>
                                        <li>
                                            <label><input type="radio" name="apply_on" value="1"> {{ __('Product Category') }}</label>
                                        </li>
                                        <li>
                                            <label><input type="radio" name="apply_on" value="2"> {{ __('Product') }}</label>
                                        </li>
                                        <li>
                                            <label><input type="radio" name="apply_on" value="3"> {{ __('Product Variant') }}</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="form-group" id="product-section" style="display:none">
                                <label for="product_id" class="col-md-6">{{ __('Product') }}</label>
                                <select name="product_id" class="form-control">
                                    <option value="">---{{ __('Select a product') }}---</option>
                                    @foreach($products as $product)
                                        <option value="{{ Hashids::encode($product->id) }}"> {{ $product->product_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="product-category-section" style="display:none">
                                <label for="category_id" class="col-md-6">{{ __('Product Category') }}</label>
                                <select class="form-control" name="category_id">
                                    @foreach($product_category as $p_category)
                                        <option value="{{ Hashids::encode($p_category->id) }}">{{ $p_category->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="product-variants-section" style="display:none">
                                <label for="variation_id" class="col-md-6">{{ __('Product Variant') }}</label>
                                <select class="form-control" name="variation_id">
                                    @foreach($product_variants as $p_variant)
                                        <option value="{{ Hashids::encode($p_variant->id) }}">
                                            {{ $p_variant->product->product_name }}
                                            (
                                                @foreach( $p_variant->variation_details as $ind => $p_variant_detail)
                                                    {{ $p_variant_detail->attribute_value }}
                                                @endforeach
                                            )
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_qty">{{ __('Min. Quantity') }}</label>
                                <input type="number" class="form-control" name="min_qty" min='1'/>
                            </div>
                            <div class="form-group">
                                <label for="start_date">{{ __('Start Time') }}</label>
                                <input type="date" class="form-control" name="start_date"/>
                            </div>
                            <div class="form-group">
                                <label for="end_date">{{ __('End Time') }}</label>
                                <input type="date" class="form-control" name="end_date"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <h4 class="col-md-12">{{ __('Price Computation') }}</h4>
                        <div class="col-md-6">
                            <p >{{ __('Compute Price') }}</p>
                            <ul style="list-style:none">
                                <li>
                                    <label><input type="radio" name="price_computation" value="0"> {{ __('Fixed') }}</label>
                                </li>
                                <li>
                                    <label><input type="radio" name="price_computation" value="1"> {{ __('Percentage') }}</label>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">{{ __('Amount') }}</label>
                                <input type="number" class="form-control" step="0.1" name="amount" id="">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-danger" id="remove-rule-btn">{{ __('Remove') }}</button>
                <button type="button" class="btn btn-success" id="add-rule-btn">{{ __('Add') }}</button>
            </div>
        </div>
    </div>
</div>
<div class="modal  fade" id="childrens-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title col-md-9 pl-0" id="exampleModalLabel">{{ __('Child Price Lists') }}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        @isset($model)
                            <ul>
                                @foreach ($model->childs as $child )
                                    <li>{{ $child->name }}</li>
                                @endforeach
                            </ul>
                        @endisset
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('OK') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('backend/dist/js/custom.js') }}"></script>
<script>
    $('body').on('change','#price_list_type',function(){
        if($(this).val() == 0){
            $('#code_heading').html('{{__("E-commerce Promotion Code")}}');
        }else{
            $('#code_heading').html('{{__("E-commerce Promotion Code Prefix")}}');
        }
    })
    // Submit the PriceList Create Form
    $('body').on('click','.save-pricelist-d',function(){
        $('#pricelist-form').submit();
    });
    $('body').on('click','.status-pricelist-d',function(){
        $('input[name=is_active]').val({{ @$model->is_active == 1 ? 0 : 1 }});
        $('#pricelist-form').submit();
    });

    // Open Create New Price Rule Modal
    $('body').on('click','#addPriceRuleBtn',function(){
        reset_form();
        $('#remove-rule-btn').hide();
        $('#price_rule_modal').modal('show');
    });

    // Open Create View/Edit Price Rule Modal
    $('body').on('click','tr.rule_line',function(){
        reset_form();
        rule_id = $(this).attr('data-id');
        $('#remove-rule-btn').show();
        $('#price_rule_modal input[name=rule_id]').val(rule_id);

        $.ajax({
            url: "{{ route('admin.pricelist.rule.get') }}",
            data: {
                pricelist_rule_id : rule_id
                },
            type: 'POST',
            success: function (data) {
                amount = data.price_computation == 0 ? data.fixed_value : data.percentage_value;
                $('#price_rule_modal input[name=apply_on][value='+data.apply_on+']').click();
                $('#price_rule_modal input[name=min_qty]').val(data.min_qty);
                $('#price_rule_modal input[name=start_date]').val(data.start_date);
                $('#price_rule_modal input[name=end_date]').val(data.end_date);
                $('#price_rule_modal input[name=amount]').val(amount);
                $('#price_rule_modal select[name=product_id]').val(data.product_id);
                $('#price_rule_modal select[name=category_id]').val(data.category_id);
                $('#price_rule_modal select[name=variation_id]').val(data.variation_id);
                $('#price_rule_modal input[name=price_computation][value='+data.price_computation+']').click();
                $('#price_rule_modal').modal('show');

            },
            complete:function(data){
                // Hide loader container
            }
        })
    });
    // Open Create View/Edit Price Rule Modal
    $('body').on('click','#remove-rule-btn',function(){
        rule_id =$('#price_rule_modal input[name=rule_id]').val();

        $.ajax({
            url: "{{ route('admin.pricelist.rule.remove') }}",
            data: {
                pricelist_rule_id : rule_id
                },
            type: 'POST',
            success: function (data) {
                $('input[name=pricelist_rule_ids]').val(data);
                $('tr.rule_line[data-id='+rule_id+']').remove();
                $('#price_rule_modal').modal('hide');
            },
            complete:function(data){
                // Hide loader container
            }
        })
    });

    //
    $('body').on('click','input[name=apply_on]',function(){

        switch( $(this).val() ){
            case "0":
                $('#product-section').hide();
                $('#product-variants-section').hide();
                $('#product-category-section').hide();

            break;
            case "1":
                $('#product-section').hide();
                $('#product-variants-section').hide();
                $('#product-category-section').show();

            break;
            case "2":
                $('#product-section').show();
                $('#product-variants-section').hide();
                $('#product-category-section').hide();

            break;
            case "3":
                $('#product-section').hide();
                $('#product-variants-section').show();
                $('#product-category-section').hide();

            break;

        }
    });

    //
    $('body').on('click','#add-rule-btn',function(){
        if( $('input[name=apply_on]').is(":checked")
            && $('input[name=min_qty]').val() != ''
            && $('input[name=start_date]').val() != ''
            && $('input[name=end_date]').val() != ''
            && $('input[name=amount]').val() != ''
            && $('input[name=price_computation]').is(":checked")
            ){
            if($('input[name=end_date]').val()  < $('input[name=start_date]').val()){
                $('.modal-footer').prepend('<div style="color:red" class="rule-error">{{ __('Start date must be less the end date.') }}</div>');
                setTimeout(function(){
                    $('.rule-error').remove();
                },3000);
            }else if($('input[name=min_qty]').val() < 1){
                $('.modal-footer').prepend('<div style="color:red" class="rule-error">{{ __('Quantity must be greater than 1.') }}</div>');
                setTimeout(function(){
                    $('.rule-error').remove();
                },3000);
            }else{
                rule_id = $('#price_rule_modal input[name=rule_id]').val();

                $.ajax({
                    url: "{{ route('admin.pricelist.rule.insert') }}",
                    data: {
                        rule_id : rule_id,
                        apply_on : $('input[name=apply_on]:checked').val(),
                        min_qty : $('input[name=min_qty]').val(),
                        start_date : $('input[name=start_date]').val(),
                        end_date : $('input[name=end_date]').val(),
                        price_computation : $('input[name=price_computation]:checked').val(),
                        amount : $('input[name=amount]').val(),
                        product_id : $('select[name=product_id]').val(),
                        category_id : $('select[name=category_id]').val(),
                        variation_id : $('select[name=variation_id]').val(),
                        _token : $('input[name=_token]').val(),
                        },
                    type: 'POST',
                    success: function (data) {
                        if(rule_id == null || rule_id == ''){
                            rdata = [];
                            rdata['id'] = data;
                            rdata['applicable_on'] = $('input[name=apply_on]:checked').val();
                            rdata['min_qty'] = $('input[name=min_qty]').val();
                            rdata['start_date'] = $('input[name=start_date]').val();
                            rdata['end_date'] = $('input[name=end_date]').val();
                            rdata['price_computation'] = $('input[name=price_computation]:checked').val();
                            rdata['amount'] = $('input[name=amount]').val();
                            html = make_rule_row(rdata);
                            $('#example1 tbody tr:first').before(html);

                            if( $('input[name=pricelist_rule_ids]').val() == null || $('input[name=pricelist_rule_ids]').val() == '' ){
                                $('input[name=pricelist_rule_ids]').val(data);
                            }else{
                                $('input[name=pricelist_rule_ids]').val($('input[name=pricelist_rule_ids]').val()+','+data);
                            }
                        }
                        reset_form();
                            $('#price_rule_modal').modal('hide');
                    },
                    complete:function(data){
                        // Hide loader container
                    }
                })
            }
        }else{
            $('.modal-footer').prepend('<div style="color:red" class="rule-error">{{ __('Some of the fields are required.') }}</div>');
            setTimeout(function(){
                $('.rule-error').remove();
            },3000);
        }
    });

    function make_rule_row(data){
        html ='<tr class="rule_line" data-id="'+data['id']+'">';
            html += '<td>';
                switch(data['applicable_on']){
                    case "0":
                        html += "{{ __('All Products') }}";
                    break;
                    case "1":
                        html += "{{ __('Product Category') }}";
                    break;
                    case "2":
                        html += "{{ __('Product') }}";
                    break;
                    case "3":
                        html += "{{ __('Product Variant') }}";
                    break;
                }
            html += '</td>';
            html += '<td>';
                html += data['min_qty'];
            html += '</td>';
            html += '<td>';
                html += data['amount'];
                if(data['price_computation'] == '1'){
                    html += "%";
                }
            html += '</td>';
            html += '<td>';
                html += data['start_date'];
            html += '</td>';
            html += '<td>';
                html += data['end_date'];
            html += '</td>';
        html += '</tr>';
        return html;
    }
    function reset_form() {
       document.getElementById("rule_form").reset();
       $('#price_rule_modal input[name=rule_id]').val('');
       $('#product-section').hide();
                $('#product-variants-section').hide();
                $('#product-category-section').hide();
    }
    $('#pricelist-form').validate({
        ignore: [],
        onkeyup: false,
        onclick: false,
        onfocusout: false,
        rules: {
            "name":{
                required:true
            }
        },
        messages: {
            "name":{
                required:"{{ __('Price List Name is required') }}"
            }
        },
        errorPlacement: function(error, element) {
            toastr.error(error);
        },
    });
</script>
@endsection
