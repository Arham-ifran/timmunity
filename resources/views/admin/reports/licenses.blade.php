@extends('admin.layouts.app')
@section('title', __('Licenses'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<style>
    .nav.navbar-nav li a {
        padding-right: 15px;
        padding-left: 15px;
    }
    td.details-control {
        background: url("{{ asset('backend/dist/img/details_open.png') }}") no-repeat center center;
        cursor: pointer;
    }
    .ranges li {
            color: #009a71;
        }
        .ranges li:hover,.ranges li.active,.daterangepicker td.active, .daterangepicker td.active:hover {
            background-color: #009a71;
            border-color: #009a71;
        }
    tr.shown td.details-control {
        background: url("{{ asset('backend/dist/img/details_close.png') }}") no-repeat center center;
    }
    .btn.btn-secondary.buttons-html5 {
        border: 1px solid #009a71;

    }

    .btn.btn-secondary.buttons-html5:hover {
        border: 1px solid #009a71;
        background: #009a71;
        color:#fff;
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
                        {{ __('Licenses') }}
                    </h2>
                </div>
                @can('Import License Keys')
                <div class="col-md-6 text-right" >
                    <h2>
                        <a type="button" class="btn skin-green-light-btn" data-toggle="modal" data-target="#import-license-modal">{{ __('Import License Keys') }}</a>
                    </h2>
                </div>
                @endcan
            </div>
        </section>
        @can('Advance Filter Licenses')
        <section class="pt-2">
            <div class="row">
                <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control" id="date_filter" name="date_filter" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text"  name="sku" placeholder="{{ __('Enter SKU') }}" autocomplete="off" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text"  name="license_key" placeholder="{{ __('Enter License Key') }}" autocomplete="off" class="form-control">
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
                            <select name="manufacturer_id" class="form-control">
                                <option value="">---{{ __('Select a Manufacturer') }}---</option>
                                @foreach($manufacturers as $manufacturer)
                                    <option
                                        value="{{ $manufacturer->id }}"

                                        >
                                        {{ $manufacturer->manufacturer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="status" class="form-control">
                                <option value="">---{{ __('Select a status') }}---</option>
                                <option value="1">{{ __('Active') }}</option>
                                <option value="0">{{ __('Inactive') }}</option>
                                <option value="2">{{ __('Expired') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="usage_status" class="form-control">
                                <option value="">---{{ __('Select a usage status') }}---</option>
                                <option value="1">{{ __('Used') }}</option>
                                <option value="0">{{ __('Un-used') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text"  name="name_email" placeholder="Enter Reseller Name/Email" value="" autocomplete="off" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text"  name="customer_name_email" placeholder="Enter Customer Name/Email" value="" autocomplete="off" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text"  name="voucher_code" placeholder="{{ __('Enter Voucher Code') }}" autocomplete="off" class="form-control">
                        </div>
                    </div>

                </div>
            </div>
            </div>
        </section>
        @endcan
        <!-- Table content -->
        <section class="content">
          <div class="box pt-1">
             @include('frontside.layouts.partials.message')
             <div class="row box-body ">
                 <div class="table-responsive">
                     <table id="license" class="table table-striped table-bordered nowrap no-footer dataTable" style="width:100%">
                         <thead>
                             <tr role="row">
                                 <th>{{ __('License Key') }}</th>
                                 <th>{{ __('Product') }}</th>
                                 <th>{{ __('Reseller') }}</th>
                                 <th>{{ __('Status') }}</th>
                                 <th>{{ __('Usage') }}</th>
                                 <th>{{ __('Customer') }}</th>
                                 <th>{{ __('Voucher') }}</th>
                                 <th>{{ __('Sales Order') }}</th>
                             </tr>
                         </thead>
                         <tbody>
                         </tbody>
                     </table>
                 </div>
             </div>
             <!-- /.box-body -->
          </div>
       </section>
        <!-- /.content -->
    </div>
    <div class="modal fade" id="import-license-modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Import License Keys') }}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
                    </button>
                </div>
                <div class="modal-body ">
                    <div class="row">
                        <form id="importform" action="{{ route('admin.license.import') }}" method="POST" enctype="multipart/form-data">
                            {{-- <div class="col-md-6">
                                <select name="product-select" class="form-control">
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
                            </div> --}}
                            {{-- <input type="hidden" name="product_id">
                            <input type="hidden" name="variation_id"> --}}
                            {{ csrf_field() }}
                            <h2 class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" multiple="" name="file[]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <input type="submit" class="btn btn-success" value="{{ __('Import License') }}"/>
                                    <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                   href="{{ asset('backend\download-license-sample\TIMmunity License Sample.csv') }}" download>{{ __('Download Sample') }}</a>
                                </div>
                            </h2>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
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
    <script>
        var ajax_data = [];
        ajax_data['product_id'] = null;
        ajax_data['variation_id'] = null;
        ajax_data['license_key'] = null;
        ajax_data['status'] = null;
        ajax_data['product_id'] = null;
        ajax_data['usage_status'] = null;
        ajax_data['customer'] = null;
        ajax_data['reseller'] = null;
        ajax_data['start_date'] = null;
        ajax_data['end_date'] = null;
        ajax_data['sku'] = null;
        ajax_data['customer_name_email'] = null;
        ajax_data['name_email'] = null;
        ajax_data['manufacturer_id'] = null;
        ajax_data['voucher_code'] = null;



        tableajaxurl = '{{ route("admin.license.index") }}';
        function format ( d ) {
            return '<table  class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable" style="width:100%">'+
                '<tr>'+
                    '<th>Customer</th>'+
                    '<th>Voucher Code</th>'+
                    '<th>Quotation</th>'+
                '</tr>'+
                '<tr>'+
                    '<td>'+d.customer_detail+'</td>'+
                    '<td>'+d.voucher_code+'</td>'+
                    '<td>'+d.quotation_number+'</td>'+
                '</tr>'+
                '<tr>'+
                    // '<th>Actions</th>'+
                    // '<td>'+d.action+'</td>'+
                '</tr>'+
            '</table>';
        }
        var table = $("#license").DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: ':visible:not(.notexport)',
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':visible:not(.notexport)',
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: ':visible:not(.notexport)',
                    }
                },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: ':visible:not(.notexport)',
                    }
                },
            ],
            "order": [],
            lengthChange: false,
            scrollX: true,
            orderCellsTop: true,
            serverSide: true,
            searching:false,
            "aaSorting": [],
            "ajax": {
                "url": tableajaxurl,
                "data": function(d){
                    d.product_id = ajax_data['product_id'];
                    d.variation_id =  ajax_data['variation_id'];
                    d.license_key = ajax_data['license_key'];
                    d.status = ajax_data['status'];
                    d.usage_status = ajax_data['usage_status'];
                    d.reseller = ajax_data['reseller'];
                    d.customer = ajax_data['customer'];
                    d.start_date = ajax_data['start_date'];
                    d.end_date = ajax_data['end_date'];
                    d.sku = ajax_data['sku'];
                    d.customer_name_email  = ajax_data['customer_name_email'];
                    d.name_email  = ajax_data['name_email'];
                    d.manufacturer_id =ajax_data['manufacturer_id'];
                    d.voucher_code = ajax_data['voucher_code'];
                },
                "beforeSend": function() {
                    if (table && table.hasOwnProperty('settings')) {
                        table.settings()[0].jqXHR.abort();
                    }
                }
            },
            columns: [
                {
                    data: 'license_key',
                    name: 'license_key'
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'reseller_detail',
                    name: 'reseller_detail'
                },
                {
                    data: 'statuss',
                    name: 'statuss'
                },
                {
                    data: 'used',
                    name: 'used'
                },

                {
                    data: 'customer_detail',
                    name: 'customer_detail'
                },
                {
                    data: 'voucher_code',
                    name: 'voucher_code'
                },
                {
                    data: 'quotation_number',
                    name: 'quotation_number'
                }
            ]
        });

        // $('#license tbody').on('click', 'td', function () {
        //     var tr = $(this).closest('tr');
        //     var row = table.row( tr );

        //     if ( row.child.isShown() ) {
        //         // This row is already open - close it
        //         row.child.hide();
        //         tr.removeClass('shown');
        //     }
        //     else {
        //         // Open this row
        //         row.child( format(row.data()) ).show();
        //         tr.addClass('shown');
        //     }
        // } );
        /***  Filters JQuery Start ***/


        // Selecting the product Start
        $('body').on('change', 'select[name=product]', function(){
            ajax_data['product_id'] = $('option:selected',this).attr('data-product_id');
            ajax_data['variation_id'] = $('option:selected',this).attr('data-variation_id');
            refreshDataTable();
        });

        $('body').on('change', 'select[name=manufacturer_id]', function(){
            ajax_data['manufacturer_id'] = $(this).val();
            refreshDataTable();
        });
        // Selecting the product End
        // Selecting the Status Start
        $('body').on('change', 'select[name=status]', function(){
            ajax_data['status'] = $(this).val();
            refreshDataTable();
        });
        // Selecting the Status End
        // Selecting the Usage Status Start
        $('body').on('change', 'select[name=usage_status]', function(){
            ajax_data['usage_status'] = $(this).val();
            refreshDataTable();
        });
        // Selecting the Usage Status End
        // Selecting the Customer Start
        $('body').on('change', 'select[name=customer_id]', function(){
            ajax_data['customer'] = $(this).val();
            refreshDataTable();
        });
        // Selecting the Customer End
        // Selecting the Reseller Start
        $('body').on('change', 'select[name=reseller_id]', function(){
            ajax_data['reseller'] = $(this).val();
            refreshDataTable();
        });
        // Selecting the Reseller End
        // Input the Name/Email Start
        $('body').on('input', 'input[name=license_key]', function(){
            ajax_data['license_key'] = $(this).val();
            refreshDataTable();
        });

        // Customer Name Email
        $('body').on('input', 'input[name=customer_name_email]', function(){
            ajax_data['customer_name_email'] = $(this).val();
            refreshDataTable();
        });

        // Reseller Name Email
        $('body').on('input', 'input[name=name_email]', function(){
            ajax_data['name_email'] = $(this).val();
            refreshDataTable();
        });
        // Input the Name/Email End
        // Input the SKU Start
        $('body').on('input', 'input[name=sku]', function(){
            ajax_data['sku'] = $(this).val();
            refreshDataTable();
        });
        // Input the SKU End
        // Input the Voucher Code Start
        $('body').on('input', 'input[name=voucher_code]', function(){
            ajax_data['voucher_code'] = $(this).val();
            refreshDataTable();
        });
        // Input the Voucher Code End
        $('#date_filter').daterangepicker({
            "showDropdowns": true,
            "autoApply": true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "alwaysShowCalendars": true,
            locale: {
                cancelLabel: 'Clear'
            }
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
            ajax_data['start_date'] = start.format('YYYY-MM-DD');
            ajax_data['end_date'] = end.format('YYYY-MM-DD');
            refreshDataTable();
        });
        $('#date_filter').val("{{__('Select Redeemed Date Range')}}");
        function refreshDataTable(){
            table.ajax.reload();
        }

        $('body').on('change','select[name=product-select]',function(){
            product_id = $('option:selected',this).attr('data-product_id');
            variation_id = $('option:selected',this).attr('data-variation_id');
            $('#import-license-modal [name=product_id]').val(product_id);
            $('#import-license-modal [name=variation_id]').val(variation_id);
        });
        $('#importform').validate({
        ignore: [],
        onkeyup: false,
        onclick: false,
        onfocusout: false,
        rules: {
            "product-select":{
                required:true
            },
            "file[]":{
                required:true,
                // accept: ".csv"
            },
        },
    });
    </script>
@endsection
