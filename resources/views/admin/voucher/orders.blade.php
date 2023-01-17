@extends('admin.layouts.app')
@section('title', __('Voucher Orders'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />

<style>
    input#date_filter,
    select[name=product],
    select[name=status],
    input[name=name-email]{
        border: none;
    }
    .ranges li {
        color: #009a71;
    }
    .ranges li:hover,.ranges li.active,.daterangepicker td.active, .daterangepicker td.active:hover {
        background-color: #009a71;
        border-color: #009a71;
    }

    .daterangepicker td.in-range {
        background-color: #009a7152;
    }
    .status-action-btn {
        padding: 0 15px;
    }
    .btn.btn-secondary.buttons-html5 {
        border: 1px solid #009a71;
    }
    .btn.btn-secondary.buttons-html5:hover {
        border: 1px solid #009a71;
        background: #009a71;
        color: #fff;
    }
    .row.product_row {
        border: 1px solid #009a71;
        border-radius: 5px;
    }
    .remove-product{
        float: right;
        cursor:pointer;
        color:red;
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
        <section class="content-header top-header">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                        {{ __('Voucher Orders') }}
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <a class="skin-green-light-btn btn ml-2 mb-2" data-toggle="modal"
                        data-target="#getVoucherModal"><i class="fa fa-plus" aria-hidden="true"></i></a>
                </div>
                <div class="col-md-4 ">
                </div>
                <div class="col-md-2 ">
                </div>
                <div class="col-md-2 pull-right">
                    <div class="form-group text-right">
                        {{-- <label for="" class="control-label">Select Currency</label> --}}
                        <select class="form-control" name="currency" id="currency">
                            <option value="">{{ __('All Currencies') }}</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency }}">{{ $currency }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </section>
        <!-- Table content -->
        @can('Advance Filter Voucher Orders')
        <section class="pt-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" id="date_filter" class="form-control" name="date_range" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="product" class="form-control">
                                    <option value="">---{{ __('Select a product') }}---</option>
                                    @foreach($products as $ind => $product)
                                        <option
                                            data-variation_id="{{ $product['variation_id'] }}"
                                            data-product_id="{{ $product['product_id'] }}"
                                            >
                                            {{ $product['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">---{{ __('Select a status') }}---</option>
                                    <option value="0">{{ __('Pending') }}</option>
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="2">{{ __('Rejected') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text"  name="name-email" placeholder="Enter Reseller Name/Email "value="{{ isset($reseller_email)? $reseller_email : "" }}" autocomplete="off" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text"  name="voucher_code" placeholder="Enter Voucher Code" value="" autocomplete="off" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text"  name="customer_name_email" placeholder="Enter Customer Name/Email" value="" autocomplete="off" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="vendor_type" class="form-control">
                                    <option value="">---{{ __('Select Vendor Type') }}---</option>
                                    <option value="1">{{ __('Reseller Orders') }}</option>
                                    <option value="2">{{ __('Distributor Orders') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endcan
        <section class="content kks-subscription-box-sections">
            <div class="box pt-1">
                <div class="row box-body">
                    <div class="col-xs-12">
                        @include('frontside.layouts.partials.message')
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="vouchers" class="table table-striped table-bordered  nowrap no-footer dataTable" style="width:100%">
                                    <thead>
                                        <tr role="row">
                                            <th class="no-sort">{{__('Order ID')}}</th>
                                            @canany(['Vouchers Listing','Vouchers Payment','Download Vouchers'])
                                            <th>{{ __('Actions') }}</th>
                                            @endcanany
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Active Status') }}</th>
                                            <th>{{ __('Reseller') }}</th>
                                            <th>{{ __('Product') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Used') }}</th>
                                            <th>{{ __('Remaining') }}</th>
                                            <th>{{ __('Discount (%)') }}</th>
                                            <th>{{ __('Unit Price') }}</th>
                                            <th>{{ __('Taxes') }}</th>
                                            <th>{{ __('Total Payable Amount') }}</th>
                                            <th>{{ __('Pending Payment') }}</th>
                                            @canany(['Voucher Approved','Voucher Reject'])
                                            <th>{{ __('Action') }}</th>
                                            @endcanany
                                            @can('Voucher Activated / Inactive')
                                            <th>{{ __('Active Action') }}</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <div class="modal fade" id="getVoucherModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="voucher_form" action="{{ route('admin.voucher.orderVoucherPost') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                                class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="myModalLabel">{{ __('Order Voucher') }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="reseller_id" class="control-label">{{ __('Resellers') }}<span style="color:red">*</span></label>
                                    <select required name="reseller_id" id="" class="form-control">
                                        <option value="">{{ __('Select Reseller') }}</option>
                                        @foreach( $resellers as $reseller )
                                            <option value="{{ Hashids::encode($reseller->user->id) }}"
                                                    data-vat="{{ @$reseller->contact_countries->vat_in_percentage }}"
                                                    data-country-id="{{ Hashids::encode(@$reseller->contact_countries->id) }}"
                                                    data-name="{{ $reseller->name }}"
                                                    data-email="{{ $reseller->email }}"
                                                    data-phone="{{ $reseller->phone }}"
                                                    data-address="{{ $reseller->street_1.' '.$reseller->city }}"
                                                    data-city="{{ $reseller->city }}"
                                                    data-zipcode="{{ $reseller->zipcode }}"
                                                    >{{ $reseller->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="reseller_name" class="control-label">{{ __('Name') }}<span style="color:red">*</span></label>
                                <input required type="text" readonly class="form-control" name="reseller_name" value="">
                            </div>
                            <div class="col-md-6">
                                <label for="reseller_email" class="control-label">{{ __('Email') }}<span style="color:red">*</span></label>
                                <input required type="email" readonly class="form-control" name="reseller_email" value="">
                            </div>
                            <div class="col-xs-12">
                                <label for="phone" class="control-label">{{ __('Phone no') }}</label>
                                <input type="number"  class="form-control mb-2" name="reseller_phone">
                            </div>
                            <div class="col-xs-12">
                                <label for="address" class="control-label">{{ __('Street Address') }}<span style="color:red">*</span></label>
                                <input required type="text"  class="form-control" name="address" >
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="control-label">{{ __('City') }}<span style="color:red">*</span></label>
                                <input required type="text"  class="form-control" name="city" >
                            </div>
                            <div class="col-md-6">
                                <label for="zip_code" class="control-label">{{ __('Zip Code') }}</label>
                                <input type="text"  class="form-control" name="zip_code" >
                            </div>
                            <div class="col-md-12">
                                <label for="country_id" class="control-label">{{ __('Country') }}<span style="color:red">*</span></label>
                                <select required name="country_id" id="" class="form-control">
                                    <option value="">{{ __('Select Country') }}</option>
                                    @foreach ($countries as $country)
                                        <option
                                            data-vat_vercentage="{{ $country->vat_in_percentage }}"
                                            data-is_default_vat="{{ $country->is_default_vat }}"
                                            data-default_vat="{{ $default_vat }}"
                                            value="{{ Hashids::encode($country->id) }}"

                                            >
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small><span class="vat_percentage">0</span>{{__('% VAT will be applied')}}</small>
                            </div>
                            <input type="hidden" name="vat_percentage" value="0">
                            <div class="col-xs-12" id="product_section_column">
                                <div class="row product_row mb-2 mt-2 pb-2">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="product_id" class="control-label">{{ __('Product') }}<span style="color:red">*</span></label>
                                            <select required name="product_id[]" id="" class="form-control">
                                                <option value="">{{ __('Select Product') }}</option>
                                                @foreach( $products_voucher_order as $product )
                                                    <option value="{{ Hashids::encode($product->id) }}" data-secondary-projects='@json($product->secondary_projects_array)' data-variation-count="{{ $product->variations_count }}">{{ $product->product_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 secondary_projects_div" style="display:none;">
                                        <div class="form-group">
                                            <label for="variations_id" class="control-label">{{ __('Secondary Products') }}</label>
                                            <div class="data">
            
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 variation_selection" style="display:none;">
                                        <div class="form-group">
                                            <label for="variation_id" class="control-label">{{ __('Variation') }}<span style="color:red">*</span></label>
                                            <select name="variation_id[]" id="" class="form-control">
            
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 product_price" style="display:none;">
                                        <div class="form-group">
                                            <label for="product_price" class="product_price_label control-label">{{ __('Price') }} &nbsp;<span style="color:green"><strong></strong></span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label for="quantity" class="control-label">{{ __('Quantity') }} <small class="asterik" style="color:red">*</small></label>
                                        <input required type="number" min="1" class="form-control" name="quantity[]" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 text-right">
                                <p class="btn btn-primary" id="add_new_product_btn" title="{{ __('Add new product') }}"><i class="fa fa-plus"></i></p>
                            </div>
                            <div class="col-md-12">
                                <label for="message" class="control-label">{{ __('Message') }} <small>{{ __('(If any)') }}</small></label>
                                <textarea class="form-control" name="message" ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Place Order') }}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('backend\plugins\datatables\jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\jszip.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\pdfmake.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\vfs_fonts.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\buttons.html5.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\buttons.print.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend/bower_components/moment/moment.js') }}"></script>
    <script src="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script src="{{ asset('frontside\dist\js\reseller.js') }}"></script>
    <script>
        var ajax_data = [];
        ajax_data['start_date'] = '';
        ajax_data['end_date'] = '';
        ajax_data['product_id'] = '';
        ajax_data['variation_id'] = '';
        ajax_data['name_email'] = '';
        ajax_data['status'] = '';
        ajax_data['currency'] = '';
        ajax_data['voucher_code'] = '';
        ajax_data['customer_name_email'] = '';
        ajax_data['vendor_type'] = '';
        tableajaxurl = '{{ route("admin.voucher.orders") }}';
        @php
            foreach($products_voucher_order as $ind => $product){
                $products_voucher_order[$ind]->hashed_id = Hashids::encode($product->id);
                $products_voucher_order[$ind]->secondary_projects = $product->secondary_projects_array;
            }
        @endphp

        var products = @json($products_voucher_order);
        var product_label = "{{ __('Product') }}";
        var select_product_label = "{{ __('Select Product') }}";
        var secondary_project_label = "{{ __('Secondary Products') }}";
        var variation_label = "{{ __('Variation') }}";
        var price_label = "{{ __('Price') }}";
        var quantity_label = "{{ __('Quantity') }}";
        var url = "{{ route('get-product-variations', ':id') }}";
        var variation_select_default_text = "{{__('Select Variation')}}";
        var detail_url = "{{ route('get-product-variation-detail', ':id') }}";
        var currency = "{{ Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol') }}";
        var currency_code = "{{ Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code') }}";

        var table = $("#vouchers").DataTable({
            "order": [],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                },

              'copy', 'csv', 'excel'
            ],
            serverSide: true,
            scrollX: true,            
            stateSave: true,
            "ajax": {
                "url": tableajaxurl,
                "data": function(d){
                    d.start_date = ajax_data['start_date'];
                    d.end_date = ajax_data['end_date'];
                    d.product_id = ajax_data['product_id'];
                    d.variation_id =  ajax_data['variation_id'];
                    d.name_email = ajax_data['name_email'];
                    d.status = ajax_data['status'];
                    d.currency = ajax_data['currency'];
                    d.voucher_code = ajax_data['voucher_code'];
                    d.customer_name_email= ajax_data['customer_name_email'];
                    d.vendor_type= ajax_data['vendor_type'];
                },
            },
            columns: [
                // {
                //     data: 'ids',
                //     name: 'ids',
                //     orderable: false,
                //     searchable: false
                // },
                {
                    data:'order_id',
                    name:'order_id',
                    orderable: false,
                    searchable: false
                },
                @canany(['Vouchers Listing','Vouchers Payment','Download Vouchers'])
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                @endcanany
                {
                    data: 'statuss',
                    name: 'statuss'
                },
                {
                    data: 'active_status',
                    name: 'active_status'
                },
                {
                    data: 'reseller',
                    name: 'reseller'
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'used_quantity',
                    name: 'used_quantity'
                },
                {
                    data: 'remaining_quantity',
                    name: 'remaining_quantity'
                },
                {
                    data: 'discount',
                    name: 'discount'
                },
                {
                    data: 'unit_price',
                    name: 'unit_price'
                },
                {
                    data: 'taxes',
                    name: 'taxes'
                },
                {
                    data: 'total_payable_amount',
                    name: 'total_payable_amount'
                },
                {
                    data: 'pending_payment',
                    name: 'pending_payment'
                },
                @canany(['Voucher Approved','Voucher Reject'])
                {
                    data: 'status_action',
                    name: 'status_action'
                },
                @endcanany
                @can('Voucher Activated / Inactive')
                {
                    data: 'active_status_action',
                    name: 'active_status_action'
                }
                @endcan
            ],
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ]
        });
        /***  Filters JQuery Start ***/
        // Date Range Picker Start
        $('#date_filter').daterangepicker({
            "showDropdowns": true,
            "autoApply": true,
            ranges: {
                '{{ __("Today") }}': [moment(), moment()],
                '{{ __("Yesterday") }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '{{ __("Last 7 Days") }}': [moment().subtract(6, 'days'), moment()],
                '{{ __("Last 30 Days") }}': [moment().subtract(29, 'days'), moment()],
                '{{ __("This Month") }}': [moment().startOf('month'), moment().endOf('month')],
                '{{ __("Last Month") }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "alwaysShowCalendars": true,
            locale: {
                cancelLabel: '{{ __("Clear") }}',
                "customRangeLabel": "{{ __('Custom') }}",
            }
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
            ajax_data['start_date'] = start.format('YYYY-MM-DD');
            ajax_data['end_date'] = end.format('YYYY-MM-DD');
            refreshDataTable();
        });
        $("#date_filter").val('Select Date Range');
        // Date Rage Picker End

        // Selecting the product Start
        $('body').on('change', 'select[name=product]', function(){
            ajax_data['product_id'] = $('option:selected',this).attr('data-product_id');
            ajax_data['variation_id'] = $('option:selected',this).attr('data-variation_id');
            refreshDataTable();
        });
        // Selecting the product End
        // Selecting the Status Start
        $('body').on('change', 'select[name=status]', function(){
            ajax_data['status'] = $(this).val();
            refreshDataTable();
        });
        // Selecting the Status End
        // Selecting the vendor_type Start
        $('body').on('change', 'select[name=vendor_type]', function(){
            ajax_data['vendor_type'] = $(this).val();
            refreshDataTable();
        });
        // Selecting the vendor_type End
        // Selecting the Currency Start
        $('body').on('change', 'select[name=currency]', function(){
            ajax_data['currency'] = $(this).val();
            refreshDataTable();
        });
        // Selecting the Cuurency End
        // Input the Name/Email Start
        $('body').on('input', 'input[name=name-email]', function(){
            ajax_data['name_email'] = $(this).val();
            refreshDataTable();
        });

        // Voucher Code Event
        $('body').on('input', 'input[name=voucher_code]', function(){
            ajax_data['voucher_code'] = $(this).val();
            refreshDataTable();
        });

        $('body').on('input', 'input[name=customer_name_email]', function(){
            ajax_data['customer_name_email'] = $(this).val();
            refreshDataTable();
        });
        @isset($reseller_email)
        @if($reseller_email != null && $reseller_email != '')
        $('input[name=name-email]').trigger('input');
        @endif
        @endisset
        // Input the Name/Email End
        function refreshDataTable(){
            table.ajax.reload();
        }

        //On Reseller Selection
        $('body').on('change','select[name=reseller_id]',function(){
            vat_percentage = $(this).find(':selected').data('vat');
            name = $(this).find(':selected').data('name');
            email = $(this).find(':selected').data('email');
            country = $(this).find(':selected').data('country-id');
            phone = $(this).find(':selected').data('phone');
            address = $(this).find(':selected').data('address');
            city = $(this).find(':selected').data('city');
            zipcode = $(this).find(':selected').data('zipcode');
            $('[name=vat_percentage]').val(vat_percentage);
            $('[name=reseller_email]').val(email);
            $('[name=reseller_name]').val(name);
            $('[name=reseller_phone]').val(phone);
            $('[name=city]').val(city);
            $('[name=address]').val(address);
            $('[name=zip_code]').val(zipcode);
            $('[name=country_id]').val(country);
            $('[name=country_id]').trigger('change');
            $('[name=product_id]').trigger('change');
        });
        // // On product Change from get more vouchers form
        // $('body').on('change', 'select[name=product_id]', function(){
        //     variation_count = $(this).find(':selected').data('variation-count');
        //         url = "{{ route('get-product-variations', ':id') }}";
        //         url = url.replace(":id", $(this).val());
        //         $.ajax({
        //             url: url,
        //             type: 'GET',
        //             data: {
        //                 country_id : $('#getVoucherModal [name=country_id]').val(),
        //                 reseller_id : $('select[name=reseller_id]').val()
        //             },
        //             success: function (data) {
        //                 if(data['success'] == 'true'){
        //                     // console.log(data)
        //                     if(variation_count > 0){
        //                         variations = data.data.variations;
        //                         $('select[name=variation_id]').find('option').remove().end()
        //                         $.each(variations,function(index,value){
        //                             $('select[name=variation_id]').append('<option value="'+value.hashedid+'">'+data.data.product_name+' '+value.variation_name+'</option>')
        //                         });
        //                         $('select[name=variation_id]').trigger('change');
        //                         $('.variation_selection').show();
        //                     }else{
        //                         $('.variation_selection').hide();
        //                     }
        //                     product_price = data.data.product_price;
        //                     if(data.data.end_product_price != 0){
        //                         product_price += ' - '+data.data.end_product_price;
        //                     }
        //                     $('.product_price').show();
        //                     $('.product_price_label strong').html(product_price);
        //                 }
        //             },
        //             complete:function(data){

        //             }
        //         })
        //     html = '';
        //     length = $('option:selected',this).data('secondary-projects').length;
        //     $('option:selected',this).data('secondary-projects').forEach(function(val, ind){
        //         html += val;
        //         if(ind < length - 1){
        //             html += ', ';
        //         }
        //     });
        //     if(length > 0){
        //         $('.secondary_projects_div').show();
        //         $('.secondary_projects_div .data').html(html);
        //     }else{
        //         $('.secondary_projects_div').hide();
        //     }
        // });
        // // On changing the country
        // $('body').on('change', '[name=country_id]',function(){
        //     vat_percentage = 0;
        //     selected_option = $('option:selected',this);
        //     if(selected_option.data('is_default_vat') == 1){
        //         vat_percentage = selected_option.data('default_vat');
        //     }else{
        //         vat_percentage =  selected_option.data('vat_vercentage');
        //     }
        //     $("[name=vat_percentage]").val(vat_percentage)
        //     $(".vat_percentage").html(vat_percentage)
        //     if($('select[name=variation_id]').is(':visible')){
        //         $('select[name=variation_id]').trigger('change')
        //     }
        //     else
        //     {
        //         $('select[name=product_id]').trigger('change')
        //     }
        // })
        // $('body').on('change', 'select[name=variation_id]', function(){
        //     url = "{{ route('get-product-variation-detail', ':id') }}";
        //         url = url.replace(":id", $(this).val());
        //         $.ajax({
        //             url: url,
        //             data: {
        //                 country_id : $('#getVoucherModal [name=country_id]').val(),
        //                 reseller_id : $('select[name=reseller_id]').val()
        //             },
        //             type: 'GET',
        //             success: function (data) {
        //                 if(data['success'] == 'true'){
        //                     // console.log(data)
        //                     product_price = data.data;
        //                     $('.product_price').show();
        //                     $('.product_price_label strong').html(product_price);
        //                 }
        //             },
        //             complete:function(data){

        //             }
        //         })
        // });
        $('#voucher_form').validate();
    </script>
@endsection
