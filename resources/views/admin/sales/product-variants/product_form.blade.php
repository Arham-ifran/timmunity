@extends('admin.layouts.app')
@section('title', __('Product Variants'))
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
span.tagged {
    border: 2px solid #009a71;
    border-radius: 20px;
    padding: 0px 5px;
}
#imagePreview{
    margin:auto;
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
                    {{ __('Product Variant') }} /
                    <small>
                        {{-- {{ $model->product_name }} /
                        @if(isset($variation))
                            @foreach($variation->variation_details as $v )
                                {{ $v->attribute_value}} {{$v->attached_attribute->attribute_name}}
                            @endforeach
                        @endif / --}}
                        {{ $action }}
                    </small>
                </h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-4">
                        <a class="skin-gray-light-btn btn save-product-d" href="javascript:void(0)">{{ __('Save') }}</a>
                        <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{route('admin.product-variant.index')}}">{{ __('Discard') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="box">
            <div class="row top-button-box-bar" style="justify-content: flex-end;">
                <div class="top-button-box">
                    <i class="fa fa-trello box-icon" aria-hidden="true"></i>
                    <div class="box-content"><span class="box-value">{{ $voucher_count }}</span><br>
                        <span class="box-text">{{ __('Vouchers') }}</span>
                        @if($voucher_count > 0)
                        <a href="{{ route('admin.cancel.voucher.product',[Hashids::encode($id),Hashids::encode($variation->id)]) }}">
                            {{ __('Cancel') }}
                        </a>
                        @endif
                    </div>
                </div>

            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-validate" id="product-form" method="POST" action="{{ route('admin.product-variant.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="action" value="{{ $action}}">
                            <input type="hidden" name="id" value="{{ Hashids::encode($id) }}">
                            <div class="row">
                                <div class="row">
                                    <div class="col-md-8 product-variants-content">
                                        <div class="row">
                                            <div class="col-md-8 pl-0">
                                                <div class="form-group">
                                                    <h3>{{ __('Product Name') }}<small class="asterik" style="color:red">*</small></h3>
                                                    <input type="text" required="required" name="product_name" class="form-control" placeholder="Product Name" value="@if(isset($model)) {{ $model->product_name }}@endif" />
                                                    @if(isset($variation))
                                                    <p>
                                                        @foreach($variation->variation_details as $v )
                                                        <span class="tagged"> <b>{{ $v->attribute_value}}</b> {{$v->attached_attribute->attribute_name}} </span>
                                                        @endforeach
                                                    </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h3>{{ __('Prefix') }} <small>{{ __('For Vouchers') }}</small></h3>
                                                    <input type="text" @if($action == 'Edit') readonly @endif name="prefix" class="form-control" placeholder="Product Prefix" value="@if(isset($model)) {{ $model->prefix }}@endif" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-8 pl-0 pt-1">
                                                <div class="can_be_sold">
                                                    <input type="checkbox" @if(isset($model)) @if($model->can_be_sale == 1) checked="checked" @endif @else checked="checked" @endif name="can_be_sale" value="1" />
                                                    <span class="color-black">{{ __('Can be Sold') }}</span>
                                                </div>
                                                <div class="can_be_purchased pt-1">
                                                    <input type="checkbox" @if(isset($model)) @if($model->can_be_purchase == 1) checked="checked" @endif @else checked="checked" @endif name="can_be_purchase" value="1" />
                                                    <span class="color-black">{{ __('Can be Purchased') }}</span>
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
                                            <li class="active"><a data-toggle="tab" id="VariantSpecificInformationBtn" href="#variant_specific_information">{{ __('Variant Specific Information') }}</a></li>
                                            <li ><a data-toggle="tab" id="GeneralTabBtn" href="#gernal-information">{{ __('General Information') }}</a></li>
                                            <li id="SalesTabBtn"><a data-toggle="tab" href="#Sales">{{ __('Sales') }}</a></li>
                                            <li><a data-toggle="tab" href="#eCommerce">{{ __('eCommerce') }}</a></li>
                                            <li id="PurchaseTabBtn"><a data-toggle="tab" href="#Purchase">{{ __('Purchase') }}</a></li>
                                            <li id="channel_pilot"><a href="#channelpilot" data-toggle="tab">{{ __('Channel Pilot') }}</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <!-- Variant Specific information -->
                                            <div id="variant_specific_information" class="tab-pane fade in active">
                                                <div class="row tab-form pt-3">
                                                    <!-- Gernal Tab Col No 01 -->
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Variation Sales Price') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="number" step="0.01" name="variant_specific_information[variation_sales_price]" value="@if(isset($model)){{number_format($model->variations[0]->variation_sales_price,2)}}@endif" class="form-control" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Variation Cost Price') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="number" step="0.01" name="variant_specific_information[variation_cost_price]" value="@if(isset($model)){{number_format($model->variations[0]->variation_cost_price,2)}}@endif" class="form-control" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Reseller Sales Price') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="number" step="0.01" name="variant_specific_information[reseller_sales_price]" value="@if(isset($model)){{number_format($model->variations[0]->reseller_sales_price,2)}}@endif" class="form-control" />
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="variant_specific_information[reseller_cost_price]" value="0" class="form-control" />
                                                        {{-- <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Reseller Cost Price') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="number" step="0.01" name="variant_specific_information[reseller_cost_price]" value="@if(isset($model)){{number_format($model->variations[0]->reseller_cost_price,2)}}@endif" class="form-control" />
                                                            </div>
                                                        </div> --}}
                                                    </div>
                                                    <!-- Gernal Tab Col No 02 -->
                                                    <div class="col-md-6 ">
                                                        <div class="row">
                                                            <div class="col-sm-2">
                                                                <h4>{{ __('SKU') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="text" name="sku" class="form-control" placeholder="Product Variant SKU" value="@if(isset($model)) {{ $model->variations[0]->sku }}@endif" />
                                                                <input type="hidden" name="variation_id" value="@if(isset($model)) {{ $model->variations[0]->id }}@endif" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-2">
                                                                <h4>{{ __('EAN') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="text" name="ean" class="form-control" placeholder="Product Variant EAN" value="@if(isset($model)) {{ $model->variations[0]->ean }}@endif" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-2">
                                                                <h4>{{ __('GTIN') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="text" name="gtin" class="form-control" placeholder="Product Variant GTIN" value="@if(isset($model)) {{ $model->variations[0]->gtin }}@endif" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-2">
                                                                <h4>{{ __('MPN') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="text" name="mpn" class="form-control" placeholder="Product Variant MPN" value="@if(isset($model)) {{ $model->variations[0]->mpn }}@endif" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Gernal information -->
                                            <div id="gernal-information" class="tab-pane fade">
                                                <div class="row tab-form pt-3">
                                                    <!-- Gernal Tab Col No 01 -->
                                                    <div class="col-md-6 ">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Product Type') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <select class="form-control" data-placeholder="Select" name="general[product_type_id]">
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
                                                                <select class="form-control" data-placeholder="Select" name="general[product_category_id]">
                                                                @foreach($product_category as $p_category)
                                                                        <option
                                                                            @if(isset($model_general_info) && $model_general_info->product_category_id == $p_category->id) selected="selected" @endif
                                                                            value="{{ Hashids::encode($p_category->id) }}">
                                                                            {{ $p_category->title }}
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
                                                    </div>
                                                    <!-- Gernal Tab Col No 02 -->
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Sales Price') }}<small class="asterik" style="color:red">*</small></h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="text" required="required" name="general[sales_price]" @if(isset($model_general_info)) value="{{ number_format($model_general_info->sales_price,2) }}" @endif  class="form-control" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Voucher Discount (%)') }}<small class="asterik" style="color:red">*</small></h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="number" step="0.01"  min="0" max="100" required="required" name="general[voucher_discount_percentage]" @if(isset($model_general_info)) value="{{ number_format($model_general_info->voucher_discount_percentage,2) }}" @endif  class="form-control" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Customer Taxes') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <select id="customerTaxes" multiple="" class="form-control" data-placeholder="Select" name="general[customer_taxes]">
                                                                    <option>{{ __('Select tax') }}</option>
                                                                    @foreach( $customer_taxes as $customer_tax )
                                                                        <option @if(isset($model_customer_taxes) && in_array($customer_tax->id, $model_customer_taxes)) selected = "selected"  @endif value="{{ Hashids::encode($customer_tax->id) }}">{{ $customer_tax->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Cost') }}<small class="asterik" style="color:red">*</small></h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="text" @if(isset($model_general_info)) value="{{ number_format($model_general_info->cost_price,2) }}" @else value="0" @endif  required="required" name="general[cost_price]" class="form-control" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Internal Note -->
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Internal Note') }}</h3>
                                                            <div class="col-sm-12 form-group">
                                                                <input class="form-control textarea" @if(isset($model_general_info)) value="{{ $model_general_info->internal_notes }}" @endif  type="textarea" name="general[internal_notes]" placeholder="{{ __('Internal Note') }}" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
                                                                    <input type="radio" @if(isset($model_sales) && $model_sales->invoice_policy == 0) checked="checked" @endif id="individual" name="sales[invoice_policy]" value="0">
                                                                    <label for="individual">Individual</label>
                                                                    <input type="radio" @if(isset($model_sales) && $model_sales->invoice_policy == 1) checked="checked" @endif id="company" name="sales[invoice_policy]" value="1">
                                                                    <label for="company">{{ __('Company') }}</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Automatically Email at Invoice') }}</h3>
                                                            <div class="col-sm-4">
                                                                <h4>{{ __('Email Template') }}</h4>
                                                            </div>
                                                            <div class="col-sm-8 form-group">
                                                                <select class="form-control" name="sales[email_template_id]" data-placeholder="Select">
                                                                    <option value="">---{{ __('Select a template') }}---</option>
                                                                    @foreach($email_templates as $email_template)
                                                                    <option @if(isset($model_sales) && $model_sales->email_template_id == $email_template->id ) selected="selected" @endif value="{{ Hashids::encode($email_template->id) }}">{{ $email_template->title }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Description -->
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Sales Description') }}</h3>
                                                            <div class="col-sm-12 form-group">
                                                                <input class="form-control textarea" @if(isset($model_sales)) value="{{ $model_sales->description }}" @endif type="textarea" name="sales[description]" placeholder="{{ __('This note is added to sales Description') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- / Sales -->
                                            <div id="eCommerce" class="tab-pane fade">
                                                <div class="row tab-form">
                                                    <!-- Gernal Tab Col No 01 -->
                                                    <div class="col-md-12 ">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Category') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <select class="form-control" data-placeholder="Select" name="eccomerce[category]">
                                                                    <option value="">---{{ __('Select a category') }}---</option>
                                                                    @foreach($eccomerce_categories as $e_cat)
                                                                    <option @if(isset($model_general_info) && $model_general_info->eccomerce_category == $e_cat->id) selected="selected" @endif value="{{ Hashids::encode($e_cat->id) }}">{{ $e_cat->category_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Alternative Products') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <select id="eccomerceAlternativeProducts" name="eccomerce[alternative_products][]" class="form-control select2" multiple="multiple" data-tags="true" data-placeholder="Select optional products" style="width: 100%">
                                                                    @foreach($products as $alterative_product)
                                                                        <option value="{{ Hashids::encode($alterative_product->id) }}">{{ $alterative_product->product_name }}</option>
                                                                    @endforeach
                                                            </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Accessary Product') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <select id="eccomerceAccessaryPoducts" name="eccomerce[accessary_products][]" multiple="multiple" class="form-control" data-placeholder="Select Accessary Products" data-tags="true" style="width:100%" >
                                                                    @foreach($accessary_products as $acces_prod)
                                                                        <option value="{{ Hashids::encode($acces_prod->id) }}">
                                                                            {{ $acces_prod->product->product_name }}
                                                                            (
                                                                                @foreach( $acces_prod->variation_details as $ind => $acces_prod_detail)
                                                                                    {{ $acces_prod_detail->attribute_value }}
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
                                                                    <input type="file" id="extrafiles" name="files" multiple>
                                                                </div>
                                                            </div>
                                                        </div>
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
                                                                    <option>{{ __('Select Tax') }}</option>
                                                                    @foreach( $vendor_taxes as $vendor_tax )
                                                                        <option @if(isset($model_vendor_taxes) && in_array($vendor_tax->id, $model_vendor_taxes)) selected = "selected"  @endif value="{{ Hashids::encode($vendor_tax->id) }}">{{ $vendor_tax->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Channel Pilot -->
                                            <div id="channelpilot" class="tab-pane fade">
                                                <div class="row tab-form pt-3">
                                                    <!-- Gernal Tab Col No 01 -->
                                                    <div class="col-md-6 ">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Minimum Price') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="number" step="0.01" name="minimum_price" @if(isset($model)) value="{{ number_format($model->variations[0]->minimum_price,2) }}" @endif  class="form-control" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Promotion Start Date') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="date"  name="promotion_start_date" @if(isset($model) && $model->variations[0]->promotion_start_date != null && $model->variations[0]->promotion_start_date != '') value="{{ date('Y-m-d',strtotime($model->variations[0]->promotion_start_date)) }}" @endif  class="form-control" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Gernal Tab Col No 02 -->
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Maximum Price') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="number" step="0.01"  name="maximum_price" @if(isset($model)) value="{{ number_format($model->variations[0]->maximum_price,2) }}" @endif  class="form-control" />
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Promotion End Date') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <input type="date"  name="promotion_end_date" @if(isset($model) && $model->variations[0]->promotion_end_date != null && $model->variations[0]->promotion_end_date != '') value="{{ date('Y-m-d',strtotime($model->variations[0]->promotion_end_date)) }}" @endif  class="form-control" />
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
    </section>
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
                         <a class="followButton following" data-model-id = "{{ Hashids::encode($model->variations[0]->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="6" id="following"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                         <a class="followButton following" data-model-id = "{{ Hashids::encode($model->variations[0]->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="6" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                      @else
                          <a class="followButton" data-model-id = "{{ Hashids::encode($model->variations[0]->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="6"id="followBtn" >{{ __('Follow') }}</a>
                            <a class="followButton following" data-model-id = "{{ Hashids::encode($model->variations[0]->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="6" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
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
    <!-- /.content -->
</div>
@endsection

@section('scripts')
<script src="{{ asset('backend/dist/js/custom.js') }}"></script>
<script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script>
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
    var attributes_url = '{{ route('admin.attribute.search') }}';


    $('body').on('keyup','input[name="attribute_name[]"]',function(){
        value = $(this).val();
        $.ajax({
            url: attributes_url,
            data: { q : value },
            type: 'GET',
            success: function (data) {
                selected_values = $("input[name='attribute_id[]']")
                        .map(function(){return $(this).val();}).get();
                html = '<ul style="list-style: none;margin: auto;padding: 5px;">';
                $.each( data, function(index,attribute){
                    if(!selected_values.includes(attribute.id.toString())){
                        html += '<li style="cursor:pointer" data-id="'+attribute.id+'">'+ attribute.attribute_name +'</li>';
                    }
                });
                html += '</ul>';
                $('#search-suggestion').html(html);
                $('#search-suggestion').show();
            },
            complete:function(data){
                // Hide loader container
            }
        })
    });

    $('body').on('click','#search-suggestion ul li',function(){
        var attribute = [];
        attribute['id'] = $(this).attr('data-id');
        attribute['name'] = $(this).html();
        // attribute['values'] = $(this).attr('data-value');

        html = make_attribute_row(attribute);

        $('#attributesTableBody tr:first').before(html);
        clear_attribute_search();
        initateSelect2tokensAttributes('attribueValues'+attribute['id'],attribute['id']);

    });

    $('body').on('click', '#attributesTableBody .fa.fa-trash', function() {
            $(this).parents('tr').remove();
        });


    function make_attribute_row(attribute){
        attribute_values = get_attribute_values(attribute['id']);

        html ='<tr>';
            html += '<td>';
                html += '<input name="attribute_id[]" value="'+attribute['id']+'" type="hidden">';
                html += '<input name="attribute_name[]" readonly value="'+attribute['name']+'"type="text">';
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
        $('#'+select_id).select2();
        url = '{{ route("admin.attribute.value.add", ":id") }}';
        url = url.replace(':id', attribute_id);
        $('#'+select_id).on('select2:select', function (e) {
            if (e.params.data.title == undefined) {
                $.ajax({
                    url: url,
                    dataType: 'json',
                    async: true,
                    type: "POST",
                    data: {
                        attribute_value: e.params.data.text,
                        selected_attributes: $('#'+select_id).val()
                    },
                    success: function (resp) {
                        Swal.fire("{{ __('Success') }}","{{ __('Attribute Added Successfully') }}");
                    },
                })
            }
        });
    }
    @if(isset($model_attached_attributes))
    @foreach( $model_attached_attributes as $model_attached_attribute)
        $('#attribueValues{{ $model_attached_attribute->attribute_id }}').select2();
    @endforeach
    @endif
    $('#vendorTaxes').select2();
    $('#customerTaxes').select2();
    $('#salesOptionalProducts').select2();
    $('#eccomerceAlternativeProducts').select2();
    $('#eccomerceAccessaryPoducts').select2();
    $('#dynamicAlternativeProducts').select2();
    $('#dynamicAlternativeProducts').on('select2:select', function (e) {

        if (e.params.data.newTag) {
            $.ajax({
                url: '{{ route('admin.log.add-new-contact') }}',
                dataType: 'json',
                async: true,
                type: "POST",
                data: {
                    tag: e.params.data.text,
                    selected_post_tags: $('#dynamicRecipients').val(),
                },
                success: function (resp) {
                Swal.fire("{{ __('Error') }}",resp['error'], "error");
                $("ul.select2-selection__rendered li.select2-selection__choice:nth-last-child(2)").remove();
                setTimeout(function(){
                    $("ul.select2-results__options li:last-child").remove();
                }, 6000);


                },
            })
        }
    });
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
    }, "{{ __('Cost price must be less than cost price.') }}");

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
                required:true
            },
            "general[product_category_id]":{
                required:true
            },
            "general[sales_price]":{
                required:true,
                greaterThanCost: "input[name='general[cost_price]']",
                min:1
            },
            "general[cost_price]":{
                required:true,
                lessThanSale: "input[name='general[sales_price]']",
                min:1
            },
            "general[product_type_id]":{
                required:true
            },
            "sales[invoice_policy]":{
                sales_required:true
            },
            // "sales[email_template_id]":{
            //     sales_required:true
            // }
        },
        messages: {
            "product_name":{
                required:"{{ __('Product Name is required') }}"
            },
            "prefix":{
                required:"{{ __('Product Prefix is required') }}"
            },
            "general[product_category_id]":{
                required:"{{ __('Select a product category') }}"
            },
            "general[sales_price]":{
                required:"{{ __('Sales price is required') }}"
            },
            "general[cost_price]":{
                required:"{{ __('Cost price is required') }}"
            },
            "general[product_type_id]":{
                required:"{{ __('Select a product type') }}"
            },
            "sales[invoice_policy]":{
                sales_required:"{{ __('Select invoicing policy in sales') }}"
            },
            "sales[email_template_id]":{
                sales_required:"{{ __('Select email template in sales') }}"
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

</script>

<script type="text/javascript">
// Actions URL's
var add_new_contact_url = '{{ route('admin.log.add-new-contact') }}';
var do_follow_url = '{{ route('admin.log.user-following') }}';
var do_unfollow_url = '{{ route('admin.log.user-un-follow') }}';
</script>
<script src="{{ asset('backend/dist/js/common.js') }}"></script>
@endsection
