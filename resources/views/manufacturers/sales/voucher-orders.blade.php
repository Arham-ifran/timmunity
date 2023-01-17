@extends('manufacturers.layouts.app')
@section('title', __('Manufacturer Voucher Orders'))
@section('styles')
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/bower_components//morris.js/morris.css') }}">
    <link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
    <style>
        input#date_filter,
        select[name=customer_id],
        select[name=salesperson_id],
        select[name=currency],
        select[name=salesteam_id],
        select[name=product_id],
        select[name=country_id]{
            border: none;
            background: transparent;
        }
        /* .load-hidden{
            display:none;
        } */
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
        .select{
            border:1px solid #d2d6de !important;
        }
        .dt-buttons{

            padding:20px !important;
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
                    {{ __('Voucher Orders') }}
                </h2>
            </div>
        </div>
    </section>
    <!-- Table content -->
    <section class="content">
        <div class="box">
            <div class="box-header" style="padding-left: 15px;">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" id="date_filter" class="form-control select" autocomplete="off">
                            {{-- <input type="text" id="date_filter" autocomplete="off"> --}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" id="date_filter_update" class="form-control select" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="product_id" id="product_id" class="form-control select">
                                <option value="">---Select Product---</option>
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
                            <select required name="reseller_id" id="" class="form-control select">
                                <option value="">{{ __('Select Reseller') }}</option>
                                @foreach( $resellers as $reseller )
                                    <option value="{{ $reseller->user->id }}" data-vat="{{ @$reseller->contact_countries->vat_in_percentage }}"  data-country-id="{{ Hashids::encode(@$reseller->contact_countries->id) }}" data-name="{{ $reseller->name }}" data-email="{{ $reseller->email }}" >{{ $reseller->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


            </div>
            <!-- /.box-header -->

        </div>
        <!-- /.box-body -->
        <div class="box load-hidden" style="border-top: 2px solid #f9f9f9 !important;">
            <table id="manufacturerTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('Order Number') }}</th>
                        <th>{{ __('Reseller') }}</th>
                        <th>{{ __('Product Name') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th> {{__('Quantity')}}</th>
                        <th>{{ __('Used') }}</th>
                        <th>{{ __('Remaining') }}</th>
                        <!-- <th>{{ __('Discount (%)') }}</th>
                        <th>{{ __('Unit Price') }}</th>
                        <th>{{ __('Taxes') }}</th>
                        <th>{{ __('Total') }}</th> -->

                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </section>
</div>
@endsection
@section('scripts')
<script src="{{ asset('backend/bower_components/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('backend/bower_components/morris.js/morris.min.js') }}"></script>
<script src="{{ asset('backend/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
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
<script>
    ajax_data = [];
    ajax_data['start_date'] = '';
    ajax_data['end_date'] = '';
    ajax_data['product_id'] = '';
    ajax_data['manufacturer_id']='';
    ajax_data['start_date_update']='';
    ajax_data['end_date_update']='';
    ajax_data['reseller_id']='';


    tableajaxurl = "{{ route('manufacturers.voucher.orders.manufacturer.product') }}"


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
        refresh_graph_ajax();
    });

    $("#date_filter").val("{{ __('Creation Date Range') }}");

    $('#date_filter_update').daterangepicker({
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
        ajax_data['start_date_update'] = start.format('YYYY-MM-DD');
        ajax_data['end_date_update'] = end.format('YYYY-MM-DD');
        refresh_graph_ajax();
    });

    $("#date_filter_update").val("{{ __('Updation Date Range') }}");

    $('body').on('change', 'select[name=manufacturer_id]', function(){
        ajax_data['manufacturer_id'] = $('option:selected',this).attr('data-manufacturer_id');

        refresh_graph_ajax();
    });
    $('body').on('change', 'select[name=reseller_id]', function(){
        ajax_data['reseller_id'] = $(this).val();
        refresh_graph_ajax();
    });
    $('body').on('change', 'select[name=product_id]', function(){
        ajax_data['product_id'] = $('option:selected',this).attr('data-product_id');
        ajax_data['variation_id'] = $('option:selected',this).attr('data-variation_id');
        refresh_graph_ajax();
    });

    function refresh_graph_ajax(){
        table.ajax.reload();
    }
    var table = $('#manufacturerTable').DataTable({
        lengthChange: false,
        responsive: true,
        serverSide: true,
        searching:false,
        ordering:false,
        "order": [[ 1, "desc" ]],
        dom: 'Bfrtip',
            buttons: [
              'copy', 'csv', 'excel', 'pdf'
            ],
        "ajax": {
            "url": tableajaxurl,
            "data": function(d){
                d.start_date = ajax_data['start_date'];
                d.end_date = ajax_data['end_date'];
                d.product_id = ajax_data['product_id'];
                d.manufacturer_id = ajax_data['manufacturer_id'];
                d.start_date_update = ajax_data['start_date_update'];
                d.end_date_update = ajax_data['end_date_update'];
                d.reseller_id = ajax_data['reseller_id'];
            }
        },
        columns: [

           {
               data: 'order_no',
               name: 'order_no'
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
               data: 'date',
               name: 'date'
           },

           {
               data: 'quantity',
               name: 'quantity'
           },
           {
               data: 'used',
               name: 'used'
           },
           {
               data: 'remaining',
               name: 'remaining'
           },
        //    {
        //        data: 'discount',
        //        name: 'discount'
        //    },
        //    {
        //        data: 'unit_price',
        //        name: 'unit_price'
        //    },
        //    {
        //        data: 'taxes',
        //        name: 'taxes'
        //    },
        //    {
        //        data: 'total',
        //        name: 'total'
        //    },

        ]
    });

    refresh_graph_ajax();
</script>
@endsection
