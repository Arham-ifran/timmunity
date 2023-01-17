@extends('admin.layouts.app')
@section('title', __('Voucher Payments'))
@section('styles')
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/bower_components//morris.js/morris.css') }}">
    <link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
    <style>
         .nav.navbar-nav li a {
            padding-right: 15px;
            padding-left: 15px;
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
        .select2-selection.select2-selection--single{
            display: block;
            width: 100%;
            height: 34px;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
            box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
            -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
            -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            border-radius: 0;
            box-shadow: none;
            border-color: #d2d6de;
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
                    {{ __('Voucher Payments') }}
                </h2>
            </div>
        </div>
    </section>
    <!-- Table content -->
    <section class="content">
        <div class="box">
            <div class="row mt-2" style="padding-left: 15px;">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" id="order_number" placeholder="Enter Invoice Number" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">

                                <input type="text" id="date_filter" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">

                                <input type="text" id="date_filter_update" class="form-control" autocomplete="off">
                            </div>
                        </div>

                        {{-- <div class="col-md-3 product_selection">
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
                        </div> --}}
                        {{-- <div class="col-md-3">
                            <div class="form-group">
                                <select name="manufacturer_id" class="form-control">
                                    <option value="">---{{ __('Select a Manufacturer') }}---</option>
                                    @foreach($manufacturers as $manufacturer)
                                        <option
                                        data-manufacturer_id="{{ $manufacturer->id }}">
                                            {{ $manufacturer->manufacturer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text"  name="name_email" placeholder="Enter Reseller Name/Email" value="" autocomplete="off" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="invoice_payment" id="invoice_payment" class="form-control">
                                    <option value="">---Select Payment Method---</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Online Payment">Online Payment</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="payment_status" id="payment_status" class="form-control">
                                    <option value="">---Select Payment Status---</option>
                                    <option value="1">Paid</option>
                                    <option value="0">Not Paid</option>
                                    <option value="3">Partially Paid</option>
                                </select>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
            <!-- /.box-header -->
            <div class="row box-body load-hidden">
                <div class="row">
                    <div class="tab-content">
                        <!-- Morris chart - Sales -->
                        <div id="revenue-chart-man"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box load-hidden" style="border-top: 2px solid #f9f9f9 !important;">
            <table id="voucherPayment" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('Invoice Number') }}</th>
                        <th>{{ __('Payment Status') }}</th>
                        <th>{{ __('Reseller') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Updated At') }}</th>
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
    ajax_data['order_number'] = '';
    ajax_data['start_date'] = '';
    ajax_data['end_date'] = '';
    ajax_data['start_date_update'] = '';
    ajax_data['end_date_update'] = '';
    ajax_data['product_id'] = '';
    ajax_data['payment_status']='';
    ajax_data['variation_id'] = '';
    ajax_data['manufacturer_id'] = '';
    ajax_data['name_email'] = '';
    ajax_data['invoice_payment'] ='';
    // ajax_data['customer_id'] = '';
    // ajax_data['sales_person_id'] = '';
    // ajax_data['sales_team_id'] = $('[name=salesteam_id]').val();
    // ajax_data['currency'] = $('[name=currency]').val();
    // ajax_data['country_id'] = '';
    // ajax_data['product_id'] = '';
    // ajax_data['invoice_status'] = '';


    tableajaxurl = "{{ route('admin.reports.voucher.payment') }}"
    // AREA CHART
    // var area_chart = new Morris.Area({
    //     element     : 'revenue-chart-man',
    //     resize      : true,
    //     xkey        : 'date',
    //     ykeys       : ['count'],
    //     labels      : ['Manufacturer'],
    //     lineColors  : ['#31daad'],
    //     hideHover   : 'auto',
    //     parseTime   : false,
    //     xLabelAngle : "30"
    // });



    $('#date_filter').daterangepicker({
        "showDropdowns": true,
        "autoApply": true,
        'opens': 'right',
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

    $('#date_filter_update').daterangepicker({
        "showDropdowns": true,
        "autoApply": true,
        'opens': 'left',
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
    $("#date_filter_update").val("{{ __('Updation Date Range') }}");

    $("body").on('input','input[id=order_number]',function(){
            ajax_data['order_number'] = $(this).val();
            refresh_graph_ajax();
        });
    $('body').on('change', 'select[name=manufacturer_id]', function(){
        ajax_data['manufacturer_id'] = $('option:selected',this).attr('data-manufacturer_id');
        refresh_graph_ajax();
    });

    $('body').on('change', 'select[name=invoice_payment]', function(){
        ajax_data['invoice_payment'] = $(this).val();
        refresh_graph_ajax();
    });

    $("body").on('input','input[name=name_email]',function(){
        ajax_data['name_email'] = $(this).val();
        refresh_graph_ajax();
    });

    function refresh_graph_ajax(){
        $("#ajax_loader").show();

        $.ajax({
            type: "GET",
            url: "{{route('admin.reports.voucher.payment')}}", // This is the URL to the API
            data: {
                order_number: ajax_data['order_number'],
                start_date: ajax_data['start_date'],
                end_date: ajax_data['end_date'],
                start_date_update:ajax_data['start_date_update'],
                end_date_update:ajax_data['end_date_update'],
                product_id:ajax_data['product_id'],
                payment_status: ajax_data['payment_status'],
                manufacturer_id:ajax_data['manufacturer_id'],
                name_email:ajax_data['name_email'],
                invoice_payment:ajax_data['invoice_payment'],
            }
        })
        .done(function( data ) {

            console.log(data);
            // When the response to the AJAX request comes back render the chart with new data


            table.ajax.reload();
            $("#ajax_loader").hide();
        })
        .fail(function() {
            // If there is no communication between the server, show an error
            alert( "error occured" );
            $("#ajax_loader").hide();
        });
    }
    var table = $('#voucherPayment').DataTable({

        lengthChange: false,
        responsive: true,
        serverSide: true,
        searching:false,
        ordering:false,
        "order": [[ 1, "desc" ]],
        // ajax: tableajaxurl,
        dom: 'Bfrtip',
            buttons: [
              'copy', 'csv', 'excel', 'pdf'
            ],
        "ajax": {
            "url": tableajaxurl,
            "data": function(d){
                d.order_number = ajax_data['order_number'];
                d.start_date = ajax_data['start_date'];
                d.end_date = ajax_data['end_date'];
                d.start_date_update = ajax_data['start_date_update'];
                d.end_date_update = ajax_data['end_date_update'];
                d.product_id = ajax_data['product_id'];
                d.variation_id = ajax_data['variation_id'];
                d.payment_status = ajax_data['payment_status'];
                d.manufacturer_id = ajax_data['manufacturer_id'];
                d.name_email = ajax_data['name_email'];
                d.invoice_payment = ajax_data['invoice_payment'];

            }
        },
        columns: [

            {
                data: 'invoice_no',
                name: 'invoice_no'
            },

            {
                data: 'statuss',
                name: 'statuss'
            },
            {
                data: 'reseller',
                name: 'reseller'
            },
            {
                data: 'quantity',
                name: 'quantity'
            },
            {
                data: 'amount',
                name: 'amount'
            },
            {
                data: 'created_at',
                name: 'created_at'
            },

            {
                data: 'updated_at',
                name: 'updated_at'
            },

        ]
    });

    $('body').on('change', 'select[name=product_id]', function(){
        ajax_data['product_id'] = $('option:selected',this).attr('data-product_id');
        ajax_data['variation_id'] = $('option:selected',this).attr('data-variation_id');
        refresh_graph_ajax()
    });

    $('body').on('change', 'select[name=payment_status]', function(){
        ajax_data['payment_status'] = $(this).val();

        refresh_graph_ajax()
    });



    refresh_graph_ajax();
</script>
@endsection
