@extends('admin.layouts.app')
@section('title', __('Reseller Packages'))
@section('styles')
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                    {{ __('Reseller Packages') }} / @if ($action == 'Add') {{ __('Add') }} @else {{ __('Edit') }}
                        @endif
                    </h2>
                </div>
            </div>
        </section>
        <!-- Table content -->
        <section class="content">
            <form class="timmunity-custom-dashboard-form mt-2 form-validate" id="package_form" action="{{ route('admin.reseller-package.store') }}" method="post">
                <div class="main-box box">
                    <div class="row mt-3">
                        <div class="col-xs-12">
                            <div class="box box-success box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">
                                    @if ($action == 'Add') {{ __('Add New Reseller Package') }} @else
                                            {{ __('Edit Reseller Package') }} @endif
                                    </h3>

                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="row">
                                    <div class="col-md-12">
                                        @csrf
                                        <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                                        <input type="hidden" name="action" value="{!! $action !!}">
                                        <div class="row mt-2 pt-2">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="name">{{ __('Package Name') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input type="text"
                                                        class="form-control @error('package_name') is-invalid @enderror"
                                                        id="package_name" name="package_name"
                                                        value="{{ old('package_name', $model->package_name ?? '') }}" maxlength="150"
                                                        aria-describedby="package_name" required />
                                                    @error('package_name')
                                                        <div id="package_name-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="type">{{ __('Default Model') }}</label>
                                                    <select id="type" name="model"
                                                        class="form-control @error('model') is-invalid @enderror"
                                                        aria-describedby="model" required>

                                                        <option selected>---{{ __('Select a package type') }}---</option>
                                                        <option value="0" @if (isset($model) && 0 == $model->model) selected @endif>{{ __('Increase') }}</option>
                                                        <option value="1" @if (isset($model) && 1 == $model->model) selected @endif>{{ __('Discount') }}</option>
                                                    </select>
                                                    @error('model')
                                                        <div id="model-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="percentage">{{ __('Default Percentage') }}</label>
                                                    <input type="number"
                                                        class="form-control @error('percentage') is-invalid @enderror"
                                                        id="percentage" name="percentage"
                                                        value="{{ old('percentage', $model->percentage ?? '') }}" step='0.01'
                                                        aria-describedby="percentage" required />
                                                    @error('percentage')
                                                        <div id="percentage-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="is_active">{{ __('Status') }}</label>
                                                    <select id="is_active" name="is_active"
                                                        class="form-control @error('is_active') is-invalid @enderror"
                                                        aria-describedby="is_active" required>

                                                        <option selected>---{{ __('Select Status') }}---</option>
                                                        <option value="0" @if (isset($model) && 0 == $model->is_active) selected @endif>{{ __('In-Active') }}</option>
                                                        <option value="1" @if (isset($model) && 1 == $model->is_active) selected @endif>{{ __('Active') }}</option>
                                                    </select>
                                                    @error('model')
                                                        <div id="is_active-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        {{-- //////////////////// --}}
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="custom-tabs mt-3">
                                                    <ul class="nav nav-tabs">
                                                        <li class="active"><a data-toggle="tab" href="#price-rule">{{ __('Package Rule') }}</a></li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div id="price-rule" class="tab-pane fade in active">
                                                            <input type="hidden" name="rule_ids" @if(@$rule_ids) value="{{ implode(',',@$rule_ids) }}" @endif>
                                                            <div class="row">
                                                                <table id="example1" class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>{{ __('Applicable on') }}</th>
                                                                            <th>{{ __('Product') }}</th>
                                                                            <th>{{ __('Default') }}</th>
                                                                            <th>{{ __('Model') }}</th>
                                                                            <th>{{ __('Percentage') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        {{-- @php
                                                                            dd($model->rules);
                                                                        @endphp --}}
                                                                        @if(isset($model))
                                                                            @foreach(@$model->rules as $rule)
                                                                            <tr class="rule_line" data-id="{{ $rule->id }}">
                                                                                <td class="apply_on">
                                                                                    @switch($rule->apply_on)
                                                                                        @case(0)
                                                                                            {{ __('All Products') }}
                                                                                            @break
                                                                                        @case(1)
                                                                                            {{ __('Product') }}
                                                                                            @break
                                                                                        @case(2)
                                                                                            {{ __('Product Variant') }}
                                                                                            @break
                                                                                    @endswitch
                                                                                </td>
                                                                                <td >
                                                                                    @if($rule->variation)
                                                                                        {{ $rule->variation->product->product_name.' '.$rule->variation->variation_name }}
                                                                                    @elseif($rule->product)
                                                                                        {{ $rule->product->product_name }}
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @if($rule->use_default == 1)
                                                                                        {{ ('Yes') }}
                                                                                    @else
                                                                                        {{ ('No') }}
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @if($rule->model !== null)
                                                                                        @if($rule->model == "0")
                                                                                            {{ ('Incremental') }}
                                                                                        @elseif($rule->model == "1")
                                                                                            {{ ('Decremental') }}
                                                                                        @endif
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @if($rule->model !== null)
                                                                                        {{ $rule->percentage }} %
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            @endforeach
                                                                        @endif
                                                                        <tr>
                                                                            <td><a href="#." id="addRuleBtn">{{ __('Add New Rule') }}</a></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- //////////////////// --}}
                                        <div class="row">
                                            <div class="col-md-6 mt-3">
                                                <div class="form-group">
                                                    <button type="submit" class="skin-green-light-btn btn ">{{ __('Save') }}</button>
                                                    <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                                        href="{{ route('admin.reseller-package.index') }}">{{ __('Discard') }}</a>
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
        </section>
        <div class="modal fade" id="rule_modal" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title col-md-9 pl-0" id="exampleModalLabel">{{ __('Reseller Package Rule') }}</h3>
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
                                                    <label><input type="radio" name="apply_on" value="1"> {{ __('Product') }}</label>
                                                </li>
                                                <li>
                                                    <label><input type="radio" name="apply_on" value="2"> {{ __('Product Variant') }}</label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h4 class="col-md-4">{{ __('Model') }}</h4>
                                        <div class="col-md-8">
                                            <ul style="list-style:none;">
                                                {{-- <li>
                                                    <label><input type="radio" name="rule_model" value="-1"> {{ __('Use default package') }}</label>
                                                </li> --}}
                                                <li>
                                                    <label><input type="radio" name="rule_model" value="0"> {{ __('Incremental') }}</label>
                                                </li>
                                                <li>
                                                    <label><input type="radio" name="rule_model" value="1"> {{ __('Decremental') }}</label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="product-section" style="display:none">
                                        <label for="product_id" class="col-md-6">{{ __('Product') }}</label>
                                        <select name="product_id" class="form-control">
                                            <option value="">---{{ __('Select a product') }}---</option>
                                            @foreach($products as $product)
                                                <option value="{{ Hashids::encode($product->id) }}"> {{ $product->product_name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" id="product-variants-section" style="display:none">
                                        <label for="variation_id" class="col-md-6">{{ __('Product Variant') }}</label>
                                        <select class="form-control" name="variation_id">
                                            @foreach($product_variants as $p_variant)
                                                <option value="{{ Hashids::encode($p_variant->id) }}">
                                                    {{ $p_variant->product->product_name.' '.@$p_variant->variation_name }}
                                                    {{-- (
                                                        @foreach( $p_variant->variation_details as $ind => $p_variant_detail)
                                                            {{ $p_variant_detail->attribute_value }}
                                                        @endforeach
                                                    ) --}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                  
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group percentageGroup">
                                        <label for="amount">{{ __('Percentage') }}</label>
                                        <input type="number" class="form-control" step="0.1" name="rule_percentage" id="">
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
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>
        let action = 'Add';
        // Open New Rule Modal
        $('body').on('click','#addRuleBtn',function(){
            reset_form();
            action = 'Add';
            $('#remove-rule-btn').hide();
            $("#add-rule-btn").html('Add')
            $('#rule_modal').modal('show');
        });
        // Changing Apply On option
        $('body').on('click','input[name=apply_on]',function(){
            switch( $(this).val() ){
                case "0":
                    $('#product-section').hide();
                    $('#product-variants-section').hide();

                break;
                case "1":
                    $('#product-section').show();
                    $('#product-variants-section').hide();

                break;
                case "2":
                    $('#product-section').hide();
                    $('#product-variants-section').show();

                break;

            }
        });
        // Changing Model option
        $('body').on('click','input[name=model]',function(){
            switch( $(this).val() ){
                case "-1":
                    $('.percentageGroup').hide();
                    break;
                default:
                    $('.percentageGroup').show();
                    break;
            }
        });
        // Submit Add/Edit Rule Form
        $('body').on('click','#add-rule-btn',function(){
            if( $('input[name=apply_on]').is(":checked") && $('input[name=rule_model]').is(":checked")){
                
                rule_id = $('#rule_modal input[name=rule_id]').val();

                $.ajax({
                    url: "{{ route('admin.reseller-package.store-rule') }}",
                    data: {
                        action: action,
                        package_rule_id : rule_id,
                        apply_on : $('input[name=apply_on]:checked').val(),
                        model : $('input[name=rule_model]:checked').val() == '0' || $('input[name=rule_model]:checked').val() == '1' ? $('input[name=rule_model]:checked').val() : null,
                        percentage : $('input[name=rule_percentage]').val(),
                        product_id : $('input[name=apply_on]:checked').val() == "1" ? $('select[name=product_id]').val() : null,
                        variation_id : $('input[name=apply_on]:checked').val() == "2" ? $('select[name=variation_id]').val() : null,
                        use_default : $('input[name=rule_model]:checked').val() == '-1' ? 1 : 0,
                        _token : $('input[name=_token]').val(),
                    },
                    type: 'POST',
                    success: function (data) {
                        if(rule_id != null && rule_id != ''){
                            $('tr.rule_line[data-id='+rule_id+']').remove();
                        }
                        rdata = [];
                        rdata['id'] = data.id;
                        rdata['applicable_on'] = data.apply_on;
                        rdata['model'] = data.model;
                        rdata['percentage'] = data.percentage;
                        rdata['product'] = data.product_name;
                        rdata['use_default'] = data.use_default;
                        
                        html = make_rule_row(rdata);
                        $('#example1 tbody tr:first').before(html);

                        if( $('input[name=rule_ids]').val() == null || $('input[name=rule_ids]').val() == '' ){
                            $('input[name=rule_ids]').val(data.id);
                        }else{
                            $('input[name=rule_ids]').val($('input[name=rule_ids]').val()+','+data.id);
                        }
                        reset_form();
                        $('#rule_modal').modal('hide');
                    },
                    complete:function(data){
                        // Hide loader container
                    }
                })
            }else{
                $('.modal-footer').prepend('<div style="color:red" class="rule-error">{{ __('Some of the fields are required.') }}</div>');
                setTimeout(function(){
                    $('.rule-error').remove();
                },3000);
            }
        });
        // Open Create View/Edit Rule Modal
        $('body').on('click','tr.rule_line',function(){
            reset_form();
            action = 'Edit';
            $("#add-rule-btn").html('Update')
            rule_id = $(this).attr('data-id');
            $('#remove-rule-btn').show();
            $('#rule_modal input[name=rule_id]').val(rule_id);

            $.ajax({
                url: "{{ route('admin.reseller.package.rule.get') }}",
                data: {
                    rule_id : rule_id
                    },
                type: 'POST',
                success: function (data) {
                    model = data.model == null ? '-1' : data.model;
                    $('#rule_modal input[name=apply_on][value='+data.apply_on+']').click();
                    $('#rule_modal input[name=rule_model][value='+model+']').click();
                    $('#rule_modal input[name=rule_percentage]').val(data.percentage);
                    $('#rule_modal select[name=product_id]').val(data.product_id);
                    $('#rule_modal select[name=variation_id]').val(data.variation_id);
                    $('#rule_modal').modal('show');
                    
                },
                complete:function(data){
                    // Hide loader container
                }
            })
        });
        // Remove Rule Modal
        $('body').on('click','#remove-rule-btn',function(){
            rule_id =$('#rule_modal input[name=rule_id]').val();

            $.ajax({
                url: "{{ route('admin.reseler.package.rule.remove') }}",
                data: {
                    rule_id : rule_id
                    },
                type: 'POST',
                success: function (data) {
                    $('input[name=rule_ids]').val(data);
                    $('tr.rule_line[data-id='+rule_id+']').remove();
                    $('#rule_modal').modal('hide');
                },
                complete:function(data){
                    // Hide loader container
                }
            })
        });
        function make_rule_row(data){
            
            html ='<tr class="rule_line" data-id="'+data['id']+'">';
                html += '<td>';
                    switch(data['applicable_on']){
                        case "0":
                            html += "{{ __('All Products') }}";
                        break;
                        case "1":
                            html += "{{ __('Product') }}";
                        break;
                        case "2":
                            html += "{{ __('Product Variant') }}";
                        break;
                    }
                html += '</td>';
                html += '<td>';
                    html += data['product'];
                html += '</td>';
                html += '<td>';
                    switch(data['use_default']){
                        case 1:
                            html += "{{ __('Yes') }}";
                        break;
                        case 0:
                            html += "{{ __('No') }}";
                        break;
                    }
                html += '</td>';
                html += '<td>';
                    switch(data['model']){
                        case "0":
                            html += "{{ __('Incremental') }}";
                        break;
                        case "1":
                            html += "{{ __('Decremental') }}";
                        break;
                    }
                html += '</td>';
                html += '<td>';
                    if(data['use_default'] == 0){
                        html += data['percentage']+' %';
                    }
                html += '</td>';
            html += '</tr>';
            return html;
        }
        function reset_form() {
            document.getElementById("rule_form").reset();
            
            $('#rule_modal input[name=rule_id]').val('');
            $('#product-section').hide();
            $('#product-variants-section').hide();
        }

        $('#package_form').validate({
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
