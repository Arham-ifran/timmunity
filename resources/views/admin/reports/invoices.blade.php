@extends('admin.layouts.app')
@section('title',  __('Invoices'))
@section('styles')
<link href="{{ asset('backend/plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend/plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<!-- Daterange picker -->
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/bower_components//morris.js/morris.css') }}">
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
<style>
    .nav.navbar-nav li a {
        padding-right: 15px;
        padding-left: 15px;
    }
    input#date_filter,
    select[name=customer_id],
    select[name=salesperson_id],
    select[name=currency],
    select[name=salesteam_id]{
        border: none;
    }
    .ranges li {
        color: #009a71;
    }
    .ranges li:hover,.ranges li.active,.daterangepicker td.active, .daterangepicker td.active:hover {
        background-color: #009a71;
        border-color: #009a71;
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
    .daterangepicker td.in-range {
        background-color: #009a7152;
    }
    #quotationTable tbody td{
        cursor: pointer;
    }
    #quotationTable tbody tr:hover{
        background: #009a7140;
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
                    {{ __('Invoices Analysis') }}
                </h2>
            </div>
            <div class="col-md-3">
            </div>
        </div>
    </section>
    <!-- Content Header (Page header) -->
    <section class="content-header  dashboard-top">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" id="date_filter_invoice" class="form-control" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" id="date_filter_invoice_update" class="form-control" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" name="quotation_id" id="quotation_id" value="" placeholder="Enter Order Number" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="customer_id" id="customer_id" class="form-control">
                                <option value="">---{{ __('Select a customer') }}---</option>
                                @foreach ($customers as $cust)
                                    <option value="{{ $cust->id }}">{{ $cust->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="product_id" id="product_id" class="form-control">
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
                            <select name="manufacturer_id" id="manufacturer_id" class="form-control">
                                <option value="">---Select Manufacturer---</option>
                                @foreach($manufacturers as $ind => $manufacture)
                                <option data-manufacturer_id="{{ $manufacture['id'] }}">{{$manufacture['manufacturer_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="invoice_status" id="invoice_status" class="form-control">
                                <option value="">---Select Invoice Status---</option>
                                <option value="Paid">Paid</option>
                                <option value="Partially Paid">Partially Paid</option>
                                <option value="Not Paid">Not Paid</option>
                                <option value="Refunded">Refunded</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="invoice_payment" id="invoice_payment" class="form-control">
                                <option value="">---Select Invoice Payment Method---</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Online Payment">Online Payment</option>
                            </select>
                        </div>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <div class="col-md-12">
                <!-- Table content -->
                <div class="box pt-1">
                    <div class="row box-body">
                    <table id="invoiceTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Invoice Number') }}</th>
                                <th>{{ __('Order Number') }}</th>
                                <th>{{ __('Creation Date') }}</th>
                                <th>{{ __('Updation Date') }}</th>
                                <th>{{ __('Amount')}}</th>
                                <th>{{ __('Status')}}</th>
                                <th>{{ __('Invoice Payment Method') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.content -->
            </div>
        </div>
    </section>

</div>
@endsection
@section('scripts')
<!-- Daterange picker -->
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
    ajax_data['customer_id'] = '';
    ajax_data['quotation_id'] = '';
    ajax_data['invoice_payment'] = '';
    ajax_data['product_id'] = '';
    ajax_data['manufacturer_id']='';
    ajax_data['start_date_update'] = '';
    ajax_data['end_date_update'] = '';
    ajax_data['invoice_status'] = '';
    tableajaxurl = "{{ route('admin.reports.invoices') }}"
    // AREA CHART
    // var area_chart = new Morris.Area({
    //     element     : 'revenue-chart',
    //     resize      : true,
    //     xkey        : 'date',
    //     ykeys       : ['sales'],
    //     labels      : ['Sales'],
    //     lineColors  : ['#31daad'],
    //     hideHover   : 'auto',
    //     parseTime   : false,
    //     xLabelAngle : "80"
    // });


    $('#date_filter_invoice').daterangepicker({
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
        $("#start_date").val(ajax_data['start_date'])
        ajax_data['end_date'] = end.format('YYYY-MM-DD');
        $("#end_date").val(ajax_data['end_date'])
        refresh_graph_ajax();
    });

    $('#date_filter_invoice_update').daterangepicker({
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
        $("#start_date").val(ajax_data['start_date'])
        ajax_data['end_date_update'] = end.format('YYYY-MM-DD');
        $("#end_date").val(ajax_data['end_date'])
        refresh_graph_ajax();
    });

    $("#date_filter_invoice").val("{{ __('Creation Date Range') }}");
    $("#date_filter_invoice_update").val("{{ __('Updation Date Range') }}");

    $("body").on('change','select[name=customer_id]',function(){
        ajax_data['customer_id'] = $(this).val();
        refresh_graph_ajax();
    });

    // $("body").on('input','input[name=quotation_id]',function(){
    //     ajax_data['quotation_id'] = $(this).val();
    //     refresh_graph_ajax();
    // });

    $("body").on('input','input[name=quotation_id]',function(){
        ajax_data['quotation_id'] = $(this).val();
        refresh_graph_ajax();
    });

    $("body").on('change','select[name=invoice_payment]',function(){
        ajax_data['invoice_payment'] = $(this).val();
        refresh_graph_ajax();
    });

    $('body').on('change', 'select[name=product_id]', function(){
        ajax_data['product_id'] = $('option:selected',this).attr('data-product_id');
        ajax_data['variation_id'] = $('option:selected',this).attr('data-variation_id');

        refresh_graph_ajax();
    });

    $('body').on('change', 'select[name=manufacturer_id]', function(){
        ajax_data['manufacturer_id'] = $('option:selected',this).attr('data-manufacturer_id');

        refresh_graph_ajax();
    });
    // Invoice Status
    $("body").on('change','select[name=invoice_status]',function(){
        ajax_data['invoice_status'] = $(this).val();
        refresh_graph_ajax();
    });

    // $("body").on('change','select[name=currency]',function(){
    //     ajax_data['currency'] = $(this).val();
    //     $("#currency_id").val(ajax_data['currency'])
    //     refresh_graph_ajax();
    // });


    function refresh_graph_ajax(){
        $("#ajax_loader").show();

        $.ajax({
            type: "GET",
            url: "{{route('admin.reports.invoices')}}", // This is the URL to the API
            data: {
                start_date: ajax_data['start_date'],
                end_date: ajax_data['end_date'],
                customer_id:ajax_data['customer_id'],
                quotation:ajax_data['quotation_id'],
                invoice_payment:ajax_data['invoice_payment'],
                product_id: ajax_data['product_id'],
                variation_id: ajax_data['variation_id'],
                manufacturer_id:ajax_data['manufacturer_id'],
                start_date_update: ajax_data['start_date_update'],
                end_date_update: ajax_data['end_date_update'],
                invoice_status: ajax_data['invoice_status'],
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
    var table = $('#invoiceTable').DataTable({
        // "dom": '<"top"fi>rt<"bottom"lp><"clear">',
        orderCellsTop: true,
        lengthChange: false,
        responsive: true,
        serverSide: true,
        searching:false,
        "aaSorting": [],
        // ajax: tableajaxurl,
        dom: 'Bfrtip',
            buttons: [
              'copy', 'csv', 'excel', 'pdf'
            ],
        "ajax": {
            "url": tableajaxurl,
            "data": function(d){
                d.start_date = ajax_data['start_date'];
                d.end_date = ajax_data['end_date'];
                d.customer_id = ajax_data['customer_id'];
                d.quotation  =ajax_data['quotation_id'];
                d.invoice_payment=ajax_data['invoice_payment'];
                d.product_id = ajax_data['product_id'];
                d.variation_id = ajax_data['variation_id'];
                d.manufacturer_id = ajax_data['manufacturer_id'];
                d.start_date_update = ajax_data['start_date_update'];
                d.end_date_update = ajax_data['end_date_update'];
                d.invoice_status = ajax_data['invoice_status'];
            }
        },
        columns: [
            {
                data: 'invoice_number',
                name: 'invoice_number'
            },
            {
                data: 'order_number',
                name: 'order_number'
            },
            {
                data: 'created_date',
                name: 'created_date'
            },
            {
                data: 'update_date',
                name: 'update_date'
            },
            {
                data: 'total',
                name: 'total'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'invoice_payment',
                name: 'invoice_payment'
            }
        ]
    });



    refresh_graph_ajax();
</script>

@endsection
