@extends('admin.layouts.app')
@section('title', __('Product'))
@section('styles')
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #499a72;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
        color:white;
    }
    #imagePreview{
        margin:auto;
    }
    /* div#search-suggestion { */
    ul#search-suggestion {
        background: white;
        overflow: auto;
        max-height: 100px;
    }
    /* div#search-suggestion ul li { */
    ul#search-suggestion li {
        line-height: 25px;
        border-bottom: 1px solid #ccc;
        padding: 2px 5px;
    }
    /* div#search-suggestion ul li:hover { */
    ul#search-suggestion li:hover {
        background: #009a71;
        color: white;
    }
    .form-control.error{
        border: 1px solid red;
    }
    .eccomerce_image_div {
        position: relative;
    }
    .eccomerce_image_div img {
        object-fit: contain;
        width: 100%;
        border: 1px solid #009a71;
    }
    .eccomerce_image_div span {
        content: 'x';
        position: absolute;
        top: 5px;
        right: 20px;
        background: white;
        color: red;
        width: 15px;
        height: 15px;
        line-height: 15px;
        text-align: center;
        border-radius: 50%;
        border: 1px solid red;
        cursor: pointer;
    }
    .eccomerce_image_div span:hover {
        background: red;
        color: white;
    }
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <div class="loader-parent" id="ajax_loader">
       <div class="loader">
         <div class="square"></div>
            <div class="path">
             <div></div>
             <div></div>
             <div></div>
             <div></div>
             <div></div>
             <div></div>
             <div></div>
            </div>
        </div>
    </div>
    <!-- Content Header (Page header) -->
    <section class="content-header top-header">
        <div class="row">
            <div class="col-md-12">
                <h2>
                    {{ __('Product') }} /
                    <small>@if(@$action == "Edit") {{ __('Edit') }} @else {{ __('Add') }} @endif</small>
                </h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-4 save-btn-div">
                        <a class="skin-gray-light-btn btn save-product-d" href="javascript:void(0)">@if(@$action == "Edit") {{ __('Update') }} @else {{ __('Save') }} @endif</a>
                        <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{route('admin.products.index')}}">{{ __('Discard') }}</a>
                        @can('Configure Variants Listing')
                        @if(isset($model) && $model->attributes_count > 0)
                            <a style="margin-left: 10px;" class=" ml-2" href="{{route('admin.products.configure.variants', Hashids::encode($model->id))}}">{{ __('Configure Variants') }}</a>
                        @endif
                        @endcan
                    </div>
                    @canany(['Delete Product','Archive / Unarchive Product'])
                    @if(@$action == "Edit")
                    <div class="quotation-right-side">
                        <div class="btn-flat filter-btn dropdown custom-dropdown-buttons action-btn">
                            <i class="fa fa-bars" aria-hidden="true"></i>
                            <a class="{{@$model->is_active}} dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('Actions') }} <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                @if(@$model->is_active == 0)
                                    @can('Archive / Unarchive Product')
                                    <a class="dropdown-item un-archive-btn" href="#">{{ __('Unarchive') }}</a>
                                    @endcan
                                @else
                                    @can('Archive / Unarchive Product')
                                    <a class="dropdown-item archive-btn" href="#">{{ __('Archive') }}</a>
                                    @endcan
                                @endif
                                @can('Delete Product')
                                    <a class="dropdown-item delete-btn" href="#">{{ __('Delete') }}</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    @endif
                    @endcanany
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-validate" id="product-form" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="action" value="{{ $action}}">
                            <input type="hidden" name="id" value="{{ Hashids::encode($id) }}">
                            <input type="hidden" name="orig_id" value="{{ $id }}">
                            <div class="row">
                                <div class="row">
                                    <div class="col-md-8 product-variants-content">
                                        <div class="col-md-8 pl-0">
                                            <div class="form-group">
                                                <h3>{{ __('Product Name') }}<small class="asterik" style="color:red">*</small></h3>
                                                <input type="text"  name="product_name" class="form-control" placeholder="{{ __('Product Name') }}" value="@if(isset($model)) {{ $model->product_name }}@endif" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group prefix_div">
                                                <h3>{{ __('Prefix') }} <small>{{ __('For Vouchers') }}</small><small class="asterik" style="color:red">*</small></h3>
                                                <input type="text" name="prefix" class="form-control" placeholder="{{ __('Product Prefix') }}" value="@if(isset($model)) {{ $model->prefix }}@endif" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 pl-0 pt-1">
                                                <div class="can_be_sold">
                                                    <input type="checkbox" @if(isset($model)) @if($model->can_be_sale == 1) checked="checked" @endif @else checked="checked" @endif name="can_be_sale" value="1" />
                                                    <span class="color-black">{{ __('Can be Sold') }}</span>
                                                </div>
                                                <div class="can_be_purchased pt-1">
                                                    <input type="checkbox" @if(isset($model)) @if($model->can_be_purchase == 1) checked="checked" @endif @else checked="checked" @endif name="can_be_purchase" value="1" />
                                                    <span class="color-black">{{ __('Can be Purchased') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 pl-0 pt-1">
                                                <div class="saas_based">
                                                    <input type="radio" @if(isset($model)) @if(@$model->product_type == 0) checked="checked" @endif  @else checked="checked"  @endif name="product_type" value="0" />
                                                    <span class="color-black">{{ __('Licensable Product') }}</span>
                                                </div>
                                                <div class="saas_based pt-1">
                                                    <input type="radio" @if(isset($model)) @if(@$model->product_type == 2) checked="checked" @endif  @else checked="checked"  @endif name="product_type" value="2" />
                                                    <span class="color-black">{{ __('API-Based Licensable Product') }}</span>
                                                </div>
                                                <div class="saas_based pt-1">
                                                    <input type="radio" @if(@$model->product_type == 1) checked="checked" @endif name="product_type" value="1" />
                                                    <span class="color-black">{{ __('SaaS Based Product') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 pl-0 pt-1">
                                                <div class="form-group project_div">
                                                    <h3 class="mt-0">{{ __('Main Project') }} <small class="asterik" style="color:red">*</small></h3>
                                                    <select name="project_id" id="" class="form-control">
                                                        <option value="">{{ __('Select Project') }}</option>
                                                        @foreach($projects as $project)
                                                        <option @if(@$model->project_id == $project->id) selected="selected" @endif data-prefix="{{ $project->prefix }}" value="{{ Hashids::encode($project->id) }}">{{ $project->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6 pl-0 pt-1">
                                                <div class="form-group secondary_project_div">
                                                    <h3 class="mt-0">{{ __('Secondary Project') }} </h3>
                                                    <select multiple="multiple" name="secondary_project_id[]" id="" class="select2 form-control">
                                                        {{-- <option value="">{{ __('Select Secondary Project') }}</option> --}}
                                                        @foreach($projects as $project)
                                                            <option
                                                                @if(isset($model))
                                                                    @if(in_array($project->id,$model->secondary_project_id_array)  )
                                                                        selected="selected"
                                                                    @endif
                                                                @endif
                                                                data-prefix="{{ $project->prefix }}" value="{{ Hashids::encode($project->id) }}">{{ $project->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 pl-0 pt-1">
                                                <div class="form-group service_div">
                                                    <h3 class="mt-0">{{ __('Services') }} <small class="asterik" style="color:red">*</small></h3>
                                                    <select name="service_id" id="" class="form-control">
                                                        <option value="">{{ __('Select Service') }}</option>
                                                        @foreach($services as $service)
                                                            <option @if(@$model->service_id == $service->id) selected="selected" @endif value="{{ $service->id }}">{{ $service->service_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row saas_discount_percentage_div">
                                            <div class="col-md-6 pl-0 pt-1">
                                                <div class="form-group ">
                                                    <h3 class="mt-0">{{ __('Main Platform Discount Percentage') }} </h3>
                                                    <input type="number" step="0.01" name="saas_discount_percentage" value="{{ @$model_general_info->saas_discount_percentage }}"class="form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- FILE UPLOAD -->
                                    <div class="col-md-4 pull-right">
                                        <div class="avatar-upload form-group">
                                        <div class="avatar-fileds hover-effect">
                                            <div class="avatar-edit">
                                            <input type="file" class="form-control" id="imageUpload" name="product_image" value="{{ old('product_image', @$model->image ?? '')}}" />
                                            <label for="imageUpload"></label>
                                            </div>
                                        </div>
                                        <div class="avatar-preview">
                                        <img id="imagePreview"
                                            src="{!!checkImage(asset('storage/uploads/sales-management/products/' .@$model->image),'placeholder-products.jpg')!!}" width="100%" height="100%" />
                                                @error('image')
                                                <div id="image-error" class="invalid-feedback animated fadeInDown">
                                                {{-- {{ $message }} --}}
                                                </div>
                                            @enderror
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="custom-tabs mt-3">
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a data-toggle="tab" id="GeneralTabBtn" href="#gernal-information">{{ __('General Information') }}</a></li>
                                            <li><a data-toggle="tab" id="variation_tab_btn" href="#Variants">{{ __('Variants') }}</a></li>
                                            <li id="SalesTabBtn"><a data-toggle="tab" href="#Sales">{{ __('Sales') }}</a></li>
                                            <li><a data-toggle="tab" href="#eCommerce">{{ __('eCommerce') }}</a></li>
                                            <li id="PurchaseTabBtn"><a data-toggle="tab" href="#Purchase">{{ __('Purchase') }}</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <!-- Gernal information -->
                                            <div id="gernal-information" class="tab-pane fade in active">
                                                <div class="row tab-form pt-3">
                                                    <!-- Gernal Tab Col No 01 -->
                                                    <div class="col-md-6 ">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Product Type') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <select class="form-control" data-placeholder="{{ __('Select') }}" name="general[product_type_id]">
                                                                    @foreach($product_type as $p_type)
                                                                        <option
                                                                            @if(isset($model_general_info) && $model_general_info->product_type_id == $p_type->id) selected="selected" @endif
                                                                            value="{{ Hashids::encode($p_type->id) }}">
                                                                            {{ $p_type->title }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Product Category') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <select class="form-control" data-placeholder="{{ __('Select') }}" name="general[product_category_id]">
                                                                @foreach($product_category as $p_category)
                                                                        <option
                                                                            @if(isset($model_general_info) && $model_general_info->product_category_id == $p_category->id) selected="selected" @endif
                                                                            value="{{ Hashids::encode($p_category->id) }}">
                                                                            {{ translation( $p_category->id,26,app()->getLocale(),'title', $p_category->title) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Internal Reference') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="text" name="general[internal_reference]"  @if(isset($model_general_info)) value="{{ $model_general_info->internal_reference }}" @endif class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Barcode') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="text" name="general[barcode]" @if(isset($model_general_info)) value="{{ $model_general_info->barcode }}" @endif class="form-control" />
                                                            </div>
                                                        </div>
                                                        <!-- Download Link Input Field -->
                                                        <div class="row download">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Download Link') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="url" @if(isset($model_general_info)) value="{{ $model_general_info->download_link }}" @endif  id="download" name="general[download_link]" class="form-control" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Gernal Tab Col No 02 -->
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Sales Price') }}<small class="asterik" style="color:red">*</small></h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="number" step="0.01"required="required" name="general[sales_price]" @if(isset($model_general_info)) value="{{ number_format($model_general_info->sales_price,2) }}" @endif  class="form-control" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                {{-- <h4>{{ __('Voucher Discount (%)') }}<small class="asterik" style="color:red">*</small></h4> --}}
                                                                <h4>{{ __('Voucher Discount (%)') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="number" step="0.01"  min="0" max="100"  name="general[voucher_discount_percentage]" @if(isset($model_general_info)) value="{{ number_format($model_general_info->voucher_discount_percentage,2) }}" @endif  class="form-control" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Customer Taxes') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <select id="customerTaxes" multiple="" class="form-control" data-placeholder="{{ __('Select') }}" name="general[customer_taxes]">
                                                                    <option>{{ __('Select tax') }}</option>
                                                                    @foreach( $customer_taxes as $customer_tax )
                                                                        <option @if(isset($model_customer_taxes) && in_array($customer_tax->id, $model_customer_taxes)) selected = "selected"  @endif value="{{ Hashids::encode($customer_tax->id) }}">{{  translation( $customer_tax->id,9,app()->getLocale(),'name',$customer_tax->name)  }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Cost') }}<small class="asterik" style="color:red">*</small></h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="number" step="0.01" @if(isset($model_general_info)) value="{{ number_format($model_general_info->cost_price,2) }}" @else value="0" @endif  required="required" name="general[cost_price]" class="form-control" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Manufacturers') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <select name="manufacturer" class="form-control" id="">
                                                                   <option disabled hidden selected>-- Select Manufacturer --</option>
                                                                   @foreach($manufacturers as $manufacture)
                                                                    <option value="{{$manufacture->id}}" @if(@$model->manufacturer->manufacturer_name == @$manufacture->manufacturer_name) selected @endif>{{$manufacture->manufacturer_name}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <!-- <input type="text" step="0.01" @if(isset($model_general_info)) value="{{ number_format($model_general_info->cost_price,2) }}" @else value="" @endif  required="required" name="general[manufacturer]" class="form-control" /> -->
                                                            </div>
                                                        </div>


                                                    </div>
                                                    <!-- Internal Note -->
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Internal Note') }}</h3>
                                                            <div class="col-sm-12 form-group">
                                                                <input class="form-control textarea" @if(isset($model_general_info)) value="{{ translation( $model_general_info->id,10,app()->getLocale(),'internal_notes', $model_general_info->internal_notes) }}" @endif  type="textarea" name="general[internal_notes]" placeholder="{{ __('Internal Note') }}" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Variants -->
                                            <div id="Variants" class="tab-pane fade">
                                                @if($action == 'Add')
                                                    @include('admin.sales.products.partials.new_product_form_variation_tab')
                                                @else
                                                    @include('admin.sales.products.partials.edit_product_form_variation_tab')
                                                @endif

                                            </div>
                                            <!-- Sales -->
                                            <div id="Sales" class="tab-pane fade">
                                                <div class="row tab-form">
                                                    <!-- Gernal Tab Col No 01 -->
                                                    <div class="col-md-6 ">
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Invoicing') }}</h3>
                                                            <div class="col-sm-4">
                                                                <h4>{{ __('Invoicing Policy') }}</h4>
                                                            </div>
                                                            <div class="col-sm-8 form-group">
                                                                <div class=" pt-1 customer-radio-button">
                                                                    <input type="radio" @if(isset($model_sales) && $model_sales->invoice_policy == 0) checked="checked" @elseif(@$sales_settings['invoicing_policy'] == 0) checked="checked" @endif id="individual" name="sales[invoice_policy]" value="0">
                                                                    <label for="individual">{{ __('Ordered Quantity') }}</label>
                                                                    {{-- <input type="radio" @if(isset($model_sales) && $model_sales->invoice_policy == 1) checked="checked" @elseif(@$sales_settings['invoicing_policy'] == 1) checked="checked" @endif id="company" name="sales[invoice_policy]" value="1">
                                                                    <label for="company">{{ __('Delivered Quantity') }}</label> --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Automatically Email at Invoice') }}</h3>
                                                            <div class="col-sm-4">
                                                                <h4>{{ __('Email Template') }}</h4>
                                                            </div>
                                                            <div class="col-sm-8 form-group">
                                                                <select class="form-control" name="sales[email_template_id]" data-placeholder="{{ __('Select') }}">
                                                                    <option value="">---{{ __('Select a template') }}---</option>
                                                                    @foreach($email_templates as $email_template)
                                                                    <option @if(isset($model_sales) && $model_sales->email_template_id == $email_template->id ) selected="selected" @endif value="{{ Hashids::encode($email_template->id) }}">{{ $email_template->subject }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Display Order') }}</h3>
                                                            <div class="col-sm-4">
                                                                <h4>{{ __('Order Number') }}</h4>
                                                            </div>
                                                            <div class="col-sm-8 form-group">
                                                                <input type="number" name="order_number" id="order_number" class="form-control">
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <!-- Description -->
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Short Description') }}</h3>
                                                            <div class="col-sm-12 form-group">
                                                                <textarea class="summernote form-control " value="{{ translation( @$model_sales->id,11,app()->getLocale(),'description', @$model_sales->description) }}"  name="sales[description]" placeholder="{{ __('This note is added to sales Description') }}">{{ @$model_sales->description }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Long Description -->
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Long Description') }}</h3>
                                                            <div class="col-sm-12 form-group">
                                                                <textarea class="summernote form-control " value="{{ translation( @$model_sales->id,11,app()->getLocale(),'long_description', @$model_sales->long_description) }}"  name="sales[long_description]" placeholder="{{ __('This note is added to long  Description') }}">{{ @$model_sales->long_description }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Description -->
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Channel Pilot Description') }}</h3>
                                                            <div class="col-sm-12 form-group">
                                                                <textarea class="summernote form-control " value="{{ translation( @$model_sales->id,11,app()->getLocale(),'channel_pilot_long_description', @$model_sales->channel_pilot_long_description) }}"  name="sales[channel_pilot_long_description]" placeholder="{{ __('This note is added to sales Description') }}">{{ @$model_sales->channel_pilot_long_description }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--  Eccomerce -->
                                            <div id="eCommerce" class="tab-pane fade">
                                                <div class="row tab-form">
                                                    <!-- Gernal Tab Col No 01 -->
                                                    <div class="col-md-12 ">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Alternative Products') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <select id="eccomerceAlternativeProducts" name="eccomerce[alternative_products][]" class="form-control select2" multiple="multiple" data-tags="true" data-placeholder="Select optional products" style="width: 100%">
                                                                    @foreach($products as $alterative_product)
                                                                        <option @if(isset($model)) @if(in_array($alterative_product->id,@$model_alternative_product)) selected="selected" @endif @endif  value="{{ Hashids::encode($alterative_product->id) }}">{{ @$alterative_product->product_name }}</option>
                                                                    @endforeach
                                                            </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Accessory Product') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <select id="eccomerceAccessaryPoducts" name="eccomerce[accessary_products][]" multiple="multiple" class="form-control" data-placeholder="{{ __('Select Accessary Products') }}" data-tags="true" style="width:100%" >
                                                                    @foreach($accessary_products as $acces_prod)
                                                                        <option @if(isset($model)) @if(in_array($acces_prod->id,@$model_accessary_product)) selected="selected" @endif @endif value="{{ Hashids::encode($acces_prod->id) }}">
                                                                            {{ @$acces_prod->product->product_name }}
                                                                            (
                                                                                @foreach( $acces_prod->variation_details as $ind => $acces_prod_detail)
                                                                                    {{ @$acces_prod_detail->attached_attribute->attribute_name .' : '. @$acces_prod_detail->attribute_value }}
                                                                                @endforeach
                                                                            )
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Extra media add') }}</h3>
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Add a media') }}</h4>
                                                            </div>
                                                            <div class="form-group col-sm-6">
                                                                <div class="input-group">
                                                                    <input type="file" id="extrafiles" name="eccomerce_images[]" multiple>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 ">
                                                        @if(isset($model_eccomerce_images))
                                                        <div class="row">
                                                            @foreach($model_eccomerce_images as $img)
                                                            <div class="col-md-2 eccomerce_image_div" >
                                                                <span data-id="{{ HAshids::encode($img->id) }}">x</span>
                                                                <img  src="{{ url('storage/uploads/sales-management/products/eccomerce').'/'.$img->image }}" alt="">
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Purchase -->
                                            <div id="Purchase" class="tab-pane fade">
                                                <div class="row tab-form">
                                                    <!-- Gernal Tab Col No 01 -->
                                                    <div class="col-md-6 ">
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Vendor Bills') }}</h3>
                                                            <div class="col-sm-4">
                                                                <h4>{{ __('Vendor Taxes') }}</h4>
                                                            </div>
                                                            <div class="col-sm-8 form-group">
                                                                <select id="vendorTaxes" multiple="" class="form-control" name="vendor[taxes]" data-placeholder="Select Vendor Taxes" data-tags="true" style="width:100%" >
                                                                    <option>{{ __('Select tax') }}</option>
                                                                    @foreach( $vendor_taxes as $vendor_tax )
                                                                        <option @if(isset($model_vendor_taxes) && in_array($vendor_tax->id, $model_vendor_taxes)) selected = "selected"  @endif value="{{ Hashids::encode($vendor_tax->id) }}">{{ translation( $vendor_tax->id,9,app()->getLocale(),'name', $vendor_tax->name) }}</option>
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
                        </form>
                    </div>
                    <!-- Tabs section -->
                </div>
            </div>
        </div>
         <!-- Bottom- section -->
        @if(@$action == "Edit")
            @canany(['Add Note','View Log Note','Add Schedule Activity','View Schedule Activity','Send Message','View Send Messages'])
            <section class="bottom-section">
                <div class="row">
                <div class="row activity-back-color">
                    <div class="col-md-12">
                        <div class="custom-tabs mt-3 mb-2">
                        <div class="row">
                            <div class="col-md-8">
                            @canany(['View Send Messages','Send Message','View Log Note','Add Note','View Schedule Activity','Add Schedule Activity'])
                            <ul class="nav nav-tabs">
                                @canany(['View Send Messages','Send Message'])
                                <li class="active"><a data-toggle="tab" href="#send_message">{{ __('Send Message') }}</a></li>
                                @endcanany
                                @canany(['View Log Note','Add Note'])
                                <li @if(!auth()->user()->can('View Send Messages') && !auth()->user()->can('Send Message')) class="active" @endif><a data-toggle="tab" href="#log_note">{{ __('Log Note') }}</a></li>
                                @endcanany
                                @canany(['View Schedule Activity','Add Schedule Activity'])
                                <li @if(!auth()->user()->can('View Send Messages') && !auth()->user()->can('Send Message') && !auth()->user()->can('View Log Note') && !auth()->user()->can('Add Note')) class="active" @endif><a data-toggle="tab" href="#schedual_activity">{{ __('Schedule Activity') }}</a></li>
                                @endcanany
                            </ul>
                            @endcanany
                            </div>
                            <div class="col-md-4 pull-right text-right follower-icons">
                            <!-- Attachments View -->
                            {!! $attachments_partial_view !!}
                            @if($is_following == 1 )
                                <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="5" id="following"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                                <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="5" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                            @else
                                <a class="followButton" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="5"id="followBtn" >{{ __('Follow') }}</a>
                                    <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="5" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                            @endif
                            <a class="dropdown-toggle" href="javascript:void(0)" title="Show Followers"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;<span id="follower_counter">{{ $followers->count() }} </span></a>
                            <!-- Follower List -->
                            <ul class="follower_list" id="f_list">
                                @forelse ($followers as $follower)
                                <li><a href="{{ route('admin.contacts.edit',['contact'=> Hashids::encode($follower->contacts->id)]) }}" target="_blank">{{ $follower->contacts->name }}</a></li>
                                @empty
                                <li><div class="text-center">{{ __("Currently there's no follower") }}</div></li>
                                @endforelse
                            </ul>
                            </div>
                        </div>
                            <div class="tab-content custom-tabs-style custom-tabs-pd-set">
                            <!--  Send Messages -->
                            @canany(['Send Message','View Send Messages'])
                            <div id="send_message" class="tab-pane fade active in">
                                <div class="row tab-form pt-3">
                                <div class="row">
                                    <div class="col-md-3">
                                    @can('Send Message')
                                    <a class="skin-green-light-btn btn" type="button" data-toggle="modal"  data-target="#send-message-model" onclick="clearMessageForm()"><i class="fa fa-paper-plane"></i>&nbsp;{{ __('Send Message') }}</a>
                                    {!! $send_messages_view !!}
                                    @endcan
                                    </div>
                                </div>
                                @can('View Send Messages')
                                {!! $send_message_tab_partial_view !!}
                                @endcan
                                </div>
                            </div>
                            @endcanany
                            <!-- Log Note -->
                            @canany(['Add Note','View Log Note'])
                            <div id="log_note" class="tab-pane fade">
                                <div class="row tab-form pt-3">
                                <div class="row">
                                    <div class="col-md-3">
                                    @can('Add Note')
                                    <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#log-note-model" onclick="clearNoteForm()"><i class="fa fa-plus"></i>&nbsp;{{ __('Add Note') }}</a>
                                    {!! $log_notes_view !!}
                                    @endcan
                                    </div>
                                </div>
                                @can('View Log Note')
                                {!! $notes_tab_partial_view !!}
                                @endcan
                                </div>
                            </div>
                            @endcanany
                            <!-- Schedule Activity -->
                            @canany(['Add Schedule Activity','View Schedule Activity'])
                            <div id="schedual_activity" class="tab-pane fade">
                                <div class="row tab-form pt-3">
                                <div class="row">
                                    <div class="col-md-3">
                                    @can('Add Schedule Activity')
                                    <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#schedule-activity-model" onclick="ClearScheduleActivity()"><i class="fa fa-clock-o"></i>&nbsp;{{ __('Add Schedule Activity') }}</a>
                                    {!! $schedual_activities_view !!}
                                    @endcan
                                    </div>
                                </div>
                                @can('View Schedule Activity')
                                {!! $schedual_activity_tab_partial_view !!}
                                @endcan
                                </div>
                            </div>
                            @endcanany
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </section>
            @endcanany
        @endif
    </section>
    <!-- /.content -->
</div>
<div class="product-attribute-modalbox-d modal fade" id="product-attribte-modal"></div>
<form id="actionForm" action="" method="POST">
    @csrf

    <input type="hidden" name="ids"/>
</form>
@endsection

@section('scripts')
<script src="{{ asset('backend/dist/js/custom.js') }}"></script>
<script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script>
    var product_id = 0;
    @if(isset($model))
    product_id = '{{ $model->id }}';
    @endif
    $('[name="secondary_project_id[]"]').select2();
    deleteurl = "{{ route('admin.products.delete') }}";
    archiveurl = "{{ route('admin.products.archive') }}";
    unarchiveurl = "{{ route('admin.products.unarchive') }}";
    var attributes_url = '{{ route('admin.attribute.search') }}';

    $('body').on('click', '.saas_based input[type=radio]', function(){
        $value = $(this).val();
        switch($value){
            case "0":
                $('#variation_tab_btn').show();
                $('.saas_discount_percentage_div').hide();
                $('.project_div').hide();
                $('.service_div').hide();
                $('.project_div').closest('.col-md-6').hide()
                $('.secondary_project_div').show();
                $('.download').show();
                $('#download').prop('required',false);
            break;
            case "2":
                $('#variation_tab_btn').show();
                $('.saas_discount_percentage_div').hide();
                $('.project_div').hide();
                $('.service_div').show();
                $('.project_div').closest('.col-md-6').hide()
                $('.secondary_project_div').show();
                $('.download').show();
                $('#download').prop('required',false);
            break;
            case "1":
                $('#GeneralTabBtn').click();
                $('.saas_discount_percentage_div').show();
                $('#variation_tab_btn').hide();
                $('.project_div').show();
                $('.service_div').hide();
                $('.project_div').closest('.col-md-6').show()
                $('.secondary_project_div').show();
                $('.download').hide();
                $('#download').prop('required',false);
            break;
        }
    });
    $('body').on('change', 'select[name=project_id]', function(){
        $('input[name=prefix]').val($('option:selected',this).data('prefix'));
    });
    $('.saas_based input[type=radio]:checked').trigger('click');


    @if(isset($model_attached_attributes))
        attrbiute_count = {{ count($model_attached_attributes) }};        // This must be less than 3 ( Only 3 attributes are allowed )
    @else
        attrbiute_count = 0;        // This must be less than 3 ( Only 3 attributes are allowed )
    @endif

    $('body').on('click','.delete-btn', function(){
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('Are you sure that you want to delete this record?') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Yes, delete it!') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                favorite = [];
                favorite.push($("input[name=orig_id]").val());
                $("input[name=ids]").val(favorite.join(','));
                $("#actionForm").attr('action', deleteurl);
                $("#actionForm").submit();
            }
        });
    });
    $('body').on('click','.archive-btn', function(){
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('Are you sure that you want to un archive this record?') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Yes!') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                favorite = [];
                favorite.push($("input[name=orig_id]").val());
                $("input[name=ids]").val(favorite.join(','));
                $("#actionForm").attr('action', archiveurl);
                $("#actionForm").submit();
            }
        });
    });
    $('body').on('click','.un-archive-btn', function(){
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('Are you sure that you want to archive this record?') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Yes!') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                favorite = [];
                favorite.push($("input[name=orig_id]").val());
                $("input[name=ids]").val(favorite.join(','));
                $("#actionForm").attr('action', unarchiveurl);
                $("#actionForm").submit();
            }
        });

    });
    $('body').on('click','.save-product-d',function(){
        $('#product-form').submit();
    });
    $('body').on('click','input[name="can_be_sale"]',function(){
        if($(this).is(':checked')){
            $('#SalesTabBtn').show();
        }else{
            if($('#SalesTabBtn').hasClass('active')){
                $('#GeneralTabBtn').click();
            }
            $('#SalesTabBtn').hide();
        }
    });
    $('body').on('click','input[name="can_be_purchase"]',function(){
        if($(this).is(':checked')){
            $('#PurchaseTabBtn').show();
        }else{
            if($('#PurchaseTabBtn').hasClass('active')){
                $('#GeneralTabBtn').click();
            }
            $('#PurchaseTabBtn').hide();
        }
    });
    //On Selecting the Attribute on the Attribute Model Fill the Attribute Values
    $('body').on('change','#attribute_name_select',function(){
        var attribute = [];
        attribute['id'] = $(this).val();
        attribute_values = get_attribute_values(attribute['id']);

            $('#attribute_value_select')
                .find('option')
                .remove();

        $.each( attribute_values, function(index,value){
            $('#attribute_value_select').append(new Option(value.attribute_value,value.id,true,true));
            $('#attribute_value_select').change();
        });

    });
    $('body').on('click','input[name="attribute_name[]"]',function(){
        value = $(this).val();
        $('input[name="attribute_name[]"]').trigger('keyup');
        // if(value == null || value == ''){
        //     $('#search-suggestion').html('');
        //     $('#search-suggestion').hide();
        //     return false;
        // }
    });
    // On Typing the attributte name in the attribute field show the search suggestions
    $('body').on('keyup','input[name="attribute_name[]"]',function(){
        value = $(this).val();
        // if(value == null || value == ''){
        //     $('#search-suggestion').html('');
        //     $('#search-suggestion').hide();
        //     return false;
        // }
        $.ajax({
            url: attributes_url,
            data: { q : value },
            type: 'GET',
            success: function (data) {
                selected_values = $("input[name='attribute_id[]']")
                        .map(function(){return $(this).val();}).get();
                // html = '<ul style="list-style: none;margin: auto;padding: 5px;">';
                html = '';
                $.each( data, function(index,attribute){
                    if(!selected_values.includes(attribute.id.toString())){
                        html += '<li style="cursor:pointer" data-id="'+attribute.id+'">'+ attribute.attribute_name +'</li>';
                    }
                });
                // html += '</ul>';
                $('#search-suggestion').html(html);
                if($('#search-suggestion li').length>0){
                    $('#search-suggestion').show();
                    $("#search-suggestion").focus()

                }
            },
            complete:function(data){
                // Hide loader container
            }
        })
    });
    // On clicking the search suggestion item add a new row in the attributes column
    $('body').on('click','ul#search-suggestion li',function(){
        var attribute = [];
        attribute['id'] = $(this).attr('data-id');
        attribute['name'] = $(this).html();

        html = make_attribute_row(attribute);

        $('#attributesTableBody tr:first').before(html);
        clear_attribute_search();
        initateSelect2tokensAttributes('attribueValues'+attribute['id'],attribute['id']);

        attrbiute_count += 1;
        if( attrbiute_count == 3 )  // Three attributes added
        {
            $('input[name="attribute_name[]"]:last').parents('tr').hide();   // Hide the Add attribute input row
        }
        else
        {
            $('input[name="attribute_name[]"]:last').parents('tr').show();   // Show the Add attribute input row
        }
    });
    // On Deleting the attribute row
    $('body').on('click', '#attributesTableBody .fa.fa-trash', function() {
        // $(this).parents('tr').remove();
        attrbiute_count -= 1;
        attrbiute_count = attrbiute_count < 0 ? 0 : attrbiute_count;

        if( attrbiute_count < 3 )  // If attribute count is less than 3
        {
            $('input[name="attribute_name[]"]:last').parents('tr').show();   // Show the Add attribute input row
        }
        thisrow = $(this);
        attribute_id = $(this).parents('tr').find('input[name="attribute_id[]"]').val();
        $.ajax({
            url: "{{ route('admin.products.check.attribute.value.usage') }}",
            type: "POST",
            async: true,
            data: {
                attribute_id: attribute_id,
                product_id: $('input[name=orig_id]').val(),
            },
            success: function (response) {
                // If the attribute is used in any quotation
                if(response == "true"){
                    Swal.fire("{{ __('Failure') }}","{{ __('Cannot remove the attribute as it has been used to make a quotation.') }}");
                }else{
                    thisrow.parents('tr').remove();
                }
            },
        });
    });
    $('body').on('click', '.eccomerce_image_div span', function(){
        this_element = $(this);
        eccomerce_image_id = this_element.attr('data-id');
        url = "{{ route('admin.products.remove.eccomerceimage', ':id') }}",
        url = url.replace(':id',eccomerce_image_id)
        $.ajax({
            url: url,
            type: "POST",
            async: true,
            data: {
                image_id: eccomerce_image_id,
                token: $('input[name=_token]').val(),
            },
            success: function (response) {
                // If the attribute is used in any quotation
                if(response == "false"){
                    Swal.fire("{{ __('Failure') }}","{{ __('Cannot remove the Image.') }}");
                }else{
                    Swal.fire("{{ __('Success') }}","{{ __('Image removed successfully.') }}");
                    this_element.parents('.eccomerce_image_div').remove();
                }
            },
        });
    });
    function make_attribute_row(attribute){
        attribute_values = get_attribute_values(attribute['id']);

        html ='<tr>';

            html += '<td>';
                html +='<div class="row">';
                    html +='<div class="col-md-4">';
                        html += '<input name="attribute_id[]" value="'+attribute['id']+'" type="hidden">';
                        html += '<input class="form-control" name="attribute_name[]" readonly value="'+attribute['name']+'"type="text">';
                    html +='</div>';
                html +='</div>';
            html += '</td>';
            html += '<td>';
                html += '<select id="attribueValues'+attribute['id']+'" class="form-control select2" multiple="multiple" data-tags="true" name="attribute_value'+attribute['id']+'[]" style="width: 100%">';
                    $.each( attribute_values, function(index,value){
                        html += '<option value="'+value.id+'">'+value.attribute_value+'</option>';
                    });
                    html += '</select>';
            html += '</td>';
            html += '<td><i class="fa fa-trash"></i></td>';
        html += '</tr>';
        return html;
    }
    function clear_attribute_search(){
        $('#search-suggestion').html('');
        $('#search-suggestion').hide();
        $('#attributesTableBody tr:last input[name="attribute_name[]"]').val('');
    }
    function get_attribute_values(product_attribute_id){
        result = '';
        $.ajax({
            url: "{{route('admin.attribute.values')}}",
            data: { product_attribute_id : product_attribute_id },
            type: 'GET',
            async: false,
            success: function (data) {
                result = data;
            }
        });
        return result;
    }
    function initateSelect2tokensAttributes(select_id, attribute_id){
        // $('#'+select_id).select2({
        //     maximumSelectionLength: 10
        // });
        $('#'+select_id).select2();
    }
    @if(isset($model_attached_attributes))
        @foreach( $model_attached_attributes as $model_attached_attribute)
            $('#attribueValues{{ $model_attached_attribute->attribute_id }}').select2({
                maximumSelectionLength: 5
            });

            $('#attribueValues{{ $model_attached_attribute->attribute_id }}').on('select2:unselecting', function (e) {
                values = $('#attribueValues{{ $model_attached_attribute->attribute_id }}').val();
                $.ajax({
                    url: "{{ route('admin.products.check.attribute.value.usage') }}",
                    type: "POST",
                    async: true,
                    data: {
                        attribute_value_id: e.params.args.data.id,
                        product_id: $('input[name=orig_id]').val(),
                    },
                    success: function (response) {
                        // If the attribute is used in any quotation
                        if(response == "true"){
                            $('#attribueValues{{ $model_attached_attribute->attribute_id }}').val(values).trigger('change');
                            Swal.fire("{{ __('Failure') }}","{{ __('Cannot remove the attribute as it has been used to make a quotation.') }}");
                        }else{

                        }
                    },
                })
            });
        @endforeach
    @endif
    $('#vendorTaxes').select2();
    $('#customerTaxes').select2();
    $('#salesOptionalProducts').select2();
    $('#eccomerceAlternativeProducts').select2();
    $('#eccomerceAccessaryPoducts').select2();
    $('#dynamicAlternativeProducts').select2();
    jQuery.validator.addMethod("sales_required", function(value, element) {
        is_sales_checked = $('input[name=can_be_sale]').is(':checked');
        name = $(element).attr('name');
        // If Can be Sold is checked
        if( is_sales_checked )
        {
            if(name == "sales[invoice_policy]")
            {
                if($('input[name="'+name+'"]:checked').length == 0)
                {
                    return false;
                }
                return true;
            }
            if(name == "sales[email_template_id]")
            {
                if($('select[name="sales[email_template_id]"]').val() == '')
                {
                    return false;
                }
                return true;
            }
        }
        // If Can be Sold is unchecked
        else
        {
            return true;        //  Return true and let the form be subimitted
        }

        // return this.optional(element) || /^http:\/\/mycorporatedomain.com/.test(value);
    }, "{{ __('This field is required') }}");
    $.validator.addMethod("greaterThanCost",
        function (value, element, param) {
            var $otherElement = $(param);
            return parseFloat(value, 10) > parseFloat($otherElement.val(), 10);
    }, "{{ __('Sales price must be greater than cost price.') }}");
    $.validator.addMethod("lessThanSale",
        function (value, element, param) {
            var $otherElement = $(param);
            return parseFloat(value, 10) < parseFloat($otherElement.val(), 10);
    }, "{{ __('Cost price must be less than sales price.') }}");
    $.validator.addMethod("prefix_required",
        function (value, element, param) {
            var $otherElement = $(param);
            type = $('.saas_based input[type=radio]:checked').val();
            if(type == "0"){
                if($(element).val() == ''){
                    return false;
                }else{
                    return true;
                }
            }
            else{
                return true;
            }
    }, "{{ __('Product Prefix is required') }}");
    $.validator.addMethod("project_required",
        function (value, element, param) {
            var $otherElement = $(param);
            type = $('.saas_based input[type=radio]:checked').val();
            if(type == "1"){
                if($(element).val() == ''){
                    return false;
                }else{
                    return true;
                }
            }
            else{
                return true;
            }
    }, "{{ __('Project is required') }}");
    $('#product-form').validate({
        ignore: [],
        onkeyup: false,
        onclick: false,
        onfocusout: false,
        rules: {
            "product_name":{
                required:true
            },
            "prefix":{
                prefix_required:true
            },
            "project_id":{
                project_required:true
            },
            "general[product_category_id]":{
                required:true
            },
            "general[sales_price]":{
                required:true,
                greaterThanCost: "input[name='general[cost_price]']",
                min:1,
                step:0.01
            },
            "general[voucher_discount_percentage]":{
                min:0,
                step:0.01
            },
            "general[cost_price]":{
                required:true,
                lessThanSale: "input[name='general[sales_price]']",
                min:0,
                step:0.01
            },
            "general[product_type_id]":{
                required:true
            },
            "sales[invoice_policy]":{
                sales_required:true
            },
            "order_number":{
                remote: {
                    url: "{{ route('admin.products.check.order.number') }}",
                    type: "get",
                    data: {
                        order_number: function() {
                            return $("#order_number").val();
                        },
                        product_id: function() {
                            return product_id;
                        }
                    }
                }
            },
        },
        messages: {
            "product_name":{
                required:"{{ __('Product Name is required') }}"
            },
            "project_id":{
                project_required:"{{ __('Project is required') }}"
            },
            "prefix":{
                prefix_required:"{{ __('Product Prefix is required') }}"
            },
            "general[product_category_id]":{
                required:"{{ __('Select a product category') }}"
            },
            "general[sales_price]":{
                required:"{{ __('Sales price is required') }}",
                min:"{{__('Sales price must be greater than 1')}}",
                step:"{{__('Sales price must be multiple of 0.01')}}"
            },
            "general[cost_price]":{
                required:"{{ __('Cost price is required') }}",
                min:"{{__('Cost price must be greater than 1')}}",
                step:"{{__('Cost price must be multiple of 0.01')}}"
            },
            "general[voucher_discount_percentage]":{
                min:"{{__('Voucher discount percentage must be grater than 1')}}",
                step:"{{__('Voucher discount percentage  must be multiple of 0.01')}}"
            },
            "general[product_type_id]":{
                required:"{{ __('Select a product type') }}"
            },
            "sales[invoice_policy]":{
                sales_required:"{{ __('Select invoicing policy in sales') }}"
            },
            "sales[email_template_id]":{
                sales_required:"{{ __('Select email template in sales') }}"
            },
            "order_number":{
                remote:"{{ __('Order number already taken') }}"
                
            }

        },
        errorPlacement: function(error, element) {
            name = $(element).attr('name');
            // $(element).css('border', '1px solid red')
            if(name == "sales[invoice_policy]"){
                $(element).parent().append(error)
            }else{
                // $(element).append(error)
                error.insertAfter(element);
            }
            if($('.form-main-error:visible').length == 0)
            {
                $('.save-btn-div').append('<small class="form-main-error">"{{ __('Some of the form fields are required') }}"</small>');
                setTimeout(function(){
                    $('.form-main-error:visible').css('display','none');
                },4000);
            }

            toastr.error(error);
        },
    });
    var add_new_contact_url = '{{ route('admin.log.add-new-contact') }}';
    var do_follow_url = '{{ route('admin.log.user-following') }}';
    var do_unfollow_url = '{{ route('admin.log.user-un-follow') }}';
</script>
{{-- Edit Product Form Variation JS --}}
@if($action == "Edit")
<script src="{{ asset('backend\plugins\datatables\jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.js') }}"></script>
<script>
    var table = $('#productsTable').DataTable({
        "dom": '<"top"fi>rt<"bottom"lp><"clear">',
        orderCellsTop: true,
        lengthChange: false,
        searching: false,
        serverSide: true,
        ajax: "{{ route('admin.product-variant.index') }}?product_id={{Hashids::encode($id)}}",
        "aaSorting": [],
        columns: [
            {
                data: 'sku',
                name: 'sku'
            },
            {
                data: 'productName',
                name: 'productName'
            },
            {
                data: 'product_form_action',
                name: 'product_form_action',
                orderable: false,
                searchable: false
            }
        ]
    });
    $('body').on('click','.variant-delete-button',function(){
        id = $(this).data('id');
        url = "{{ route('admin.product-variant.destroy', ':id') }}";
        url = url.replace(':id',id);

        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: '{{ __("You will not be able to revert this!") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Yes, delete it!') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    success: function(resp) {
                        if (resp == "true") {
                            Swal.fire("{{ __('Success') }}", "Variation Deleted", "success");
                            table.ajax.reload();
                        } else {
                            Swal.fire("{{ __('Warning') }}", "Try Again", "warning");
                        }
                    }
                });
            }
        });
    });

    $('body').on('click','#add-variant-btn',function(e){
        e.preventDefault();
        attributes = [];
        attribute_count = $('[name=attribute_count]').val();
        for( i = 0; i < attribute_count; i++ )
        {
            attribute_id = $("#attribueID"+i).val();
            attribute_value_id = $("#attribueValue"+i).val();
            attributes[attribute_id] = attribute_value_id
        }


        $.ajax({
            url: "{{ route('admin.products.add_one_variant', Hashids::encode($id)) }}",
            data: {
                attributes : attributes,
                variation_sales_price : $('[name=variation_sales_price]').val(),
                variation_cost_price : $('[name=variation_cost_price]').val(),
                reseller_sales_price : $('[name=reseller_sales_price]').val(),
                sku : $('[name=sku]').val(),
                ean : $('[name=ean]').val(),
                gtin : $('[name=gtin]').val(),
                mpn : $('[name=mpn]').val()
            },
            type: 'POST',
            success: function (data) {
                if(data == 'true'){
                    table.ajax.reload();
                    Swal.fire({
                        title: "{{ __('Variation Added') }}",
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "{{ __('OK') }}"
                    });
                    $('#add_variant').modal('hide');
                }else{
                    Swal.fire({
                        title: "{{ __('Variation Already Exists') }}",
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "{{ __('OK') }}"
                    });
                }
            },
            complete:function(data){
                // Hide loader container
            }
        })
    });
</script>
@endif
<script src="{{ asset('backend/dist/js/common.js') }}"></script>
@endsection
