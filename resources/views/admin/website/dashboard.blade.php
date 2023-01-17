@extends('admin.layouts.app')
@section('title',  __('Website'))
@section('styles')
<link href="{{ asset('backend/plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend/plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<!-- Daterange picker -->
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/bower_components//morris.js/morris.css') }}">
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
<style>
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
    <!-- Content Header (Page header) -->
    <section class="content-header  dashboard-top">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header" style="padding-left: 15px;">
                            <div class="row">
                                <div class="quotation-right-side pull-left">
                                    <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                        <input type="text" id="date_filter" autocomplete="off">
                                    </div>
                                    <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                        <select name="customer_id">
                                            <option value="">---{{ __('Select a customer') }}---</option>
                                            @foreach ($customers as $cust)
                                                <option value="{{ $cust->id }}">{{ $cust->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                                        <i class="fa fa-money" aria-hidden="true"></i>
                                        <select name="currency">
                                            @foreach ($currencies as $symbol => $currency)
                                                <option value="{{ $currency }}" data-symbol="{{$symbol}}">{{ $currency }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="row box-body">
                            <div class="row">
                                <div class="tab-content">
                                    <!-- Morris chart - Sales -->
                                    <div id="revenue-chart" style="position: relative; height: 300px;"></div>
                                </div>

                                <div class="row row-analysis mt-3">
                                    <div class="col-md-4">
                                        <div class="box chart-box text-center">
                                            <h4>{{ __('Total Sales') }}</h4>
                                            {{-- <span id="total_sales">{{ $total_sales }}$</span> --}}
                                            <span id="total_sales"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="box chart-box text-center">
                                            <h4>{{ __('Untaxed Total') }}</h4>
                                            {{-- <span id="untaxed_total">{{ $total_tax }} $</span> --}}
                                            <span id="untaxed_total"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="box chart-box text-center">
                                            <h4>{{ __('Total Taxes') }}</h4>
                                            {{-- <span id="total_taxes">{{ $total_tax }} $</span> --}}
                                            <span id="total_taxes"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="box chart-box text-center">
                                            <h4>{{ __('Orders') }}</h4>
                                            {{-- <span id="orders">{{ $no_of_orders }}</span> --}}
                                            <span id="orders"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="box chart-box text-center">
                                            <h4>{{ __('Customers') }}</h4>
                                            {{-- <span id="customers">{{ $customer_count }}</span> --}}
                                            <span id="customers"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="box chart-box text-center">
                                            <h4>#{{ __('Lines') }}</h4>
                                            {{-- <span id="order_lines_no">{{ $no_of_lines }}</span> --}}
                                            <span id="order_lines_no"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <div class="col-md-12">
                   <!-- Table content -->
                    <div class="box pt-1">
                     <div class="row box-body">
                        <table id="quotationTable" class="table table-bordered table-striped">
                           <thead>
                              <tr>
                                <th>{{ __('Order Number') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Sales Person') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Invoice Status') }}</th>
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
    ajax_data['sales_team_id'] = '1';
    ajax_data['currency'] = $('[name=currency]').val();
    ajax_data['currency_symbol'] = $('[name=currency]').find('option:selected').data('symbol');

    tableajaxurl = "{{ route('admin.sales-team.analysis.quotation.table') }}"
    // AREA CHART
    var area_chart = new Morris.Area({
        element     : 'revenue-chart',
        resize      : true,
        xkey        : 'date',
        ykeys       : ['sales'],
        labels      : ['Sales'],
        lineColors  : ['#31daad'],
        hideHover   : 'auto',
        parseTime   : false,
        xLabelAngle : "80"
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

    $("#date_filter").val("{{ __('Select Date Range') }}");

    $("body").on('change','select[name=customer_id]',function(){
        ajax_data['customer_id'] = $(this).val();
        refresh_graph_ajax();
    });
    $("body").on('change','select[name=currency]',function(){
        ajax_data['currency'] = $(this).val();
        refresh_graph_ajax();
    });

    function refresh_graph_ajax(){
        $("#ajax_loader").show();

        $.ajax({
            type: "GET",
            url: "{{route('admin.website.dashboard')}}", // This is the URL to the API
            data: {
                start_date: ajax_data['start_date'],
                end_date: ajax_data['end_date'],
                customer_id: ajax_data['customer_id'],
                currency: ajax_data['currency']
            }
        })
        .done(function( data ) {
            // When the response to the AJAX request comes back render the chart with new data
            ajax_data['currency'] = ajax_data['currency'] == null ? '' : ajax_data['currency'];
            ajax_data['currency_symbol'] = $('[name=currency]').find('option:selected').data('symbol');
            area_chart.setData(data['sales_data']);
            $('#total_sales').html(ajax_data['currency_symbol']+' '+parseFloat(data['total_sales']).toFixed(2)+' <strong>'+ajax_data['currency']+'</strong>');
            $('#untaxed_total').html(ajax_data['currency_symbol']+' '+(parseFloat(data['total_sales'] - data['total_tax']).toFixed(2)) +' <strong>'+ ajax_data['currency']);
            $('#total_taxes').html(ajax_data['currency_symbol']+' '+parseFloat(data['total_tax']).toFixed(2)+' <strong>'+ajax_data['currency']+'</strong>');
            $('#orders').html(data['no_of_orders']);
            $('#customers').html(data['customer_count']);
            $('#order_lines_no').html(data['no_of_lines']);
            table.ajax.reload();

            $("#ajax_loader").hide();
        })
        .fail(function() {
            // If there is no communication between the server, show an error
            alert( "error occured" );
            $("#ajax_loader").hide();
        });
    }
    var table = $('#quotationTable').DataTable({
        "dom": '<"top"fi>rt<"bottom"lp><"clear">',
        orderCellsTop: true,
        lengthChange: false,
        responsive: true,
        serverSide: true,
        searching:false,
        "aaSorting": [],
        // ajax: tableajaxurl,
        "ajax": {
            "url": tableajaxurl,
            "data": function(d){
                d.start_date = ajax_data['start_date'];
                d.end_date = ajax_data['end_date'];
                d.customer_id = ajax_data['customer_id'];
                d.sales_team_id = ajax_data['sales_team_id'];
                d.currency = ajax_data['currency'];
            }
        },
        columns: [
            {
                data: 'ordernumber',
                name: 'ordernumber'
            },
            {
                data: 'customer',
                name: 'customer'
            },
            {
                data: 'salesperson',
                name: 'salesperson'
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
                data: 'invoicestatus',
                name: 'invoicestatus'
            }
        ]
    });

    $('#quotationTable tbody').on('click', 'td', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
        document.location.href = row.data().link;
    } );

    refresh_graph_ajax();
</script>

@endsection
