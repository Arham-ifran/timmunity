@extends('admin.layouts.app')
@section('title', 'Distributor Invoices')
@section('styles')
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<style>
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
            select[name=status]{
        border: none;
    }
</style>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header top-header">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                        {{ __('Distributor Payments') }}
                    </h2>
                </div>

            </div>

        </section>
        <!-- Table content -->
         <section class="content">
            @include('frontside.layouts.partials.message')
          <div class="box pt-1">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text"  name="name_email" placeholder="Enter Distributor Name/Email" value="" autocomplete="off" class="form-control">
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
             <div class="row box-body">
                <table id="vouchers" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                    <thead>
                        <tr role="row">
                            @canany(['Make Voucher Payment','View Payment Voucher Invoice'])
                            <th>{{ __('Action') }}</th>
                            @endcanany
                            <th>{{ __('Invoice Number') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Distributor') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
             </div>
             <!-- /.box-body -->
          </div>
       </section>
    </div>
@endsection
@section('scripts')
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
<script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>
        // var ajax_data = [];
        // ajax_data['status'] = '';
        // ajax_data['code'] = '';
        // tableajaxurl = '{{ $ajax_url }}';
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

        var table = $("#vouchers").DataTable({
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            lengthChange: false,
            responsive: true,
            orderCellsTop: true,
            serverSide: true,
            searching: false,
            "ajax": {
                "url": '{{ $ajax_url }}',
                "data": function(d){
                    d.order_number = ajax_data['order_number'];
                    d.payment_status = ajax_data['payment_status'];
                    d.manufacturer_id = ajax_data['manufacturer_id'];
                    d.name_email = ajax_data['name_email'];
                    d.invoice_payment = ajax_data['invoice_payment'];
                },
                "beforeSend": function() {
                    if (table && table.hasOwnProperty('settings')) {
                        table.settings()[0].jqXHR.abort();
                    }
                }
            },
            columns: [
            @canany(['Make Voucher Payment','View Payment Voucher Invoice'])
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            @endcanany
                {
                    data: 'invoicenumber',
                    name: 'invoicenumber'
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
            ]
        });
    
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


        $('body').on('change', 'select[name=invoice_payment]', function(){
            ajax_data['invoice_payment'] = $(this).val();
            refresh_graph_ajax();
        });

        $("body").on('input','input[name=name_email]',function(){
            ajax_data['name_email'] = $(this).val();
            refresh_graph_ajax();
        });
        

        $('body').on('change', 'select[name=payment_status]', function(){
            ajax_data['payment_status'] = $(this).val();

            refresh_graph_ajax()
        });
        function refresh_graph_ajax(){
            table.ajax.reload();
        }
    </script>
@endsection
