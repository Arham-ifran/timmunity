@extends('admin.layouts.app')
@section('title', __('Settings'))
@section('styles')
    <link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('header')
    <!-- Top Header Section -->
    <section class="content-header top-header" style="margin: 0px; border: 1px solid #ddd;">
        <div class="row">
            <div class="col-md-12">
                <h2>
                    {{ __('Settings') }} / {{ __('Sales') }}
                </h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
                @can('View Sales Settings')
                <div class="row">
                    <div class="col-md-4">
                        <a class="skin-gray-light-btn btn save-btn" href="#">{{ __('Save') }}</a>
                        <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="#">{{ __('Discard') }}</a>
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </section>
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper setting-page-content-wrapper">
        <div class="loader-parent" id="ajax_loader">
            <div class="loader setting-loader">
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
        <!-- Main content -->
        <section class="content">
            <form action="{{ route('admin.sales.settings.update') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Products -->
                    <div class="col-sx-12" id="Products">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">{{ __('Products Catalog') }}</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <!-- /.box-tools -->
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="contact-box col-md-6" style="max-width: 45%;">
                                                <div class="form-group">
                                                    <input type="checkbox" name="product_catalog_variants" @if(@$sales_settings['product_catalog_variants'] == 1) checked="checked" @endif>
                                                    <label>{{ __('Variants') }}</label>
                                                    <span class="setting-status-span" style="display: block;">{{ __('Sell Variants of a product using attributes (Size, Color, etc)') }}</span>
                                                    <a href="{{ route('admin.attribute.index') }}">
                                                        <i class="fa fa-arrow-right"></i>
                                                        {{ __('Attributes') }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="contact-box col-md-6 ml-2"
                                                style="margin-left: 45px; padding-bottom: 9px;">
                                                <div class="form-group">
                                                    <input type="checkbox" name="product_catalog_deliver_content_email" @if(@$sales_settings['product_catalog_deliver_content_email'] == 1) checked="checked" @endif>
                                                    <label>{{ __('Deliver Content By Email') }}</label>
                                                    <span class="setting-status-span" style="display: block;">{{ __('Send a product-specific email once the invoice is validated') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="col-sx-12" id="Pricing">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">{{ __('Pricing') }}</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <!-- /.box-tools -->
                                    </div>

                                    <div class="box-body">
                                        <div class="row">
                                            {{-- <div class="contact-box col-md-6" style="max-width: 45%;">
                                                <div class="form-group">
                                                    <input type="checkbox" name="pricing_discount" @if(@$sales_settings['pricing_discount'] == 1) checked="checked" @endif>
                                                    <label>{{ __('Discounts') }}</label>
                                                    <span class="setting-status-span" style="display: block;">{{ __('Grant discounts on sales order lines') }}</span>
                                                </div>
                                            </div> --}}
                                            <div class="contact-box col-md-6">
                                                <div class="form-group">
                                                    <input type="checkbox" name="pricing_pricelist" @if(@$sales_settings['pricing_pricelist'] == 1) checked="checked" @endif>
                                                    <label>{{ __('Price Lists') }}</label>
                                                    <span class="setting-status-span" style="display: block;">{{ __('Apply specific prices per country, dicounts, etc')}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>
                    </div>

                    <!-- Quotations & Orders -->

                    <div class="col-sx-12" id="Shipping">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">{{ __('Quotations and Orders') }}</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <!-- /.box-tools -->
                                    </div>

                                    <div class="box-body">
                                        <div class="row">
                                            <div class="contact-box col-md-6" style="max-width: 45%;">
                                                {{-- <div class="form-group">
                                                    <input type="checkbox" name="orders_online_signature" @if(@$sales_settings['orders_online_signature'] == 1) checked="checked" @endif>
                                                    <label>{{ __('Online Signature') }}</label>
                                                    <span class="setting-status-span" style="display: block;">
                                                        {{ __('Request an online signature to confirm orders') }}
                                                    </span>
                                                </div> --}}
                                                {{-- <div class="form-group">
                                                    <input type="checkbox" name="orders_online_payment" @if(@$sales_settings['orders_online_payment'] == 1) checked="checked" @endif>
                                                    <label>{{ __('Online Payment') }}</label>
                                                    <span class="setting-status-span" style="display: block;">
                                                        {{ __('Request an online payment to confirm orders') }}
                                                    </span>
                                                </div> --}}
                                                <div class="form-group">
                                                    <input type="checkbox" name="orders_proforma_invoice" @if(@$sales_settings['orders_proforma_invoice'] == 1) checked="checked" @endif>
                                                    <label>{{ __('Pro-Forma Invoice') }}</label>
                                                    <span class="setting-status-span" style="display: block;">{{ __('Allows you to send Pro-Forma Invoice to your customers') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="contact-box col-md-6 ml-2"
                                                style="margin-left: 45px; padding-bottom: 9px;">
                                                <div class="form-group">
                                                    <input type="checkbox" name="orders_customer_address" @if(@$sales_settings['orders_customer_address'] == 1) checked="checked" @endif>
                                                    <label>{{ __('Customer Address') }}</label>
                                                    <span class="setting-status-span" style="display: block;">
                                                        {{ __('Select specific invoice and delivery addresses') }}
                                                    </span>
                                                </div>
                                                <div class="form-group">
                                                    <input type="checkbox" name="orders_lock_confirmed_sale" @if(@$sales_settings['orders_lock_confirmed_sale'] == 1) checked="checked" @endif>
                                                    <label>{{ __('Lock Confirmed Sale') }}</label>
                                                    <span class="setting-status-span" style="display: block;">
                                                        {{ __('No longer edit orders once confirmed') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>
                    </div>

                    <!-- Invoicing -->
                    <div class="col-sx-12" id="products">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">{{ __('Invoicing') }}</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <!-- /.box-tools -->
                                    </div>

                                    <div class="box-body">
                                        <div class="row">
                                            <div class="contact-box col-md-6" style="max-width: 45%;">
                                                <h3 class="setting-status-h3">{{ __('Invoicing Policy') }}</h3>
                                                <span class="setting-status-span">{{ __('Issue invoice to customers') }}</span>
                                                <label class="custom-radio-button">
                                                    <input type="radio" value="0" name="invoicing_policy" @if(@$sales_settings['invoicing_policy'] == 0) checked="checked" @endif>
                                                    <span class="checkmark"></span>
                                                    {{ __('Invoice what is ordered') }}
                                                </label>
                                                {{-- <label class="custom-radio-button">
                                                    <input type="radio" value="1" name="invoicing_policy" @if(@$sales_settings['invoicing_policy'] == 1) checked="checked" @endif>
                                                    <span class="checkmark"></span>
                                                    {{ __('Invoice what is delivered') }}
                                                </label> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $('body').on('click', '.save-btn', function(){
            $('form').submit();
        })
    </script>
@endsection
