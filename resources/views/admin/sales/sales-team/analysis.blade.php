@extends('admin.layouts.app')
@section('title', __('Sales Analysis'))
@section('styles')
    <!-- Daterange picker -->
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
                    {{ __('Sales Team Analysis') }}
                </h2>
            </div>
            <div class="col-md-3">
            </div>
            <div class="col-md-3">
                {{-- <a href="{{route('admin.sales-team.analysis.download')}}?currency={{@$currencies[0]}}" target="_blank" id="exportBtn" class="btn btn-primary mt-3">Download Report</a> --}}
            </div>
        </div>
    </section>
    <!-- Table content -->
    <section class="content">
        <div class="box">
            @can('Advanced Sales Analysis Filters')
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
                            <i class="fa fa-user-plus" aria-hidden="true"></i>
                            <select name="salesperson_id">
                                <option value="">---{{ __('Select a sales person') }}---</option>
                                @foreach( $salespersons as $salesperson )
                                    <option value="{{ $salesperson->id }}">
                                        {{ $salesperson->firstname.' '.$salesperson->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                            <i class="fa fa-users" aria-hidden="true"></i>
                            <select name="salesteam_id">
                                <option value="">---{{  __('Select a sales team')}}---</option>
                                @foreach( $salesteams as $salesteam )
                                    <option @if(Request::get('sales_team') == $salesteam->id) selected="selected" @endif value="{{ $salesteam->id }}" >{{ $salesteam->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                            <i class="fa fa-money" aria-hidden="true"></i>
                            <select name="currency">
                                {{-- <option value="">---{{  __('Select a currency')}}---</option> --}}
                                @foreach( $currencies as $symbol => $currency )
                                    <option value="{{ $currency }}" data-symbol={{$symbol}}>{{ $currency }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="quotation-right-side pull-left">
                        <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                            <i class="fa fa-flag" aria-hidden="true"></i>
                            <select name="country_id">
                                <option value="">---{{  __('Select a country')}}---</option>
                                @foreach( $countries as $country )
                                    <option value="{{ $country->id }}" >{{ $country->name.' ( '.$country->vat_in_percentage.'%)' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                            <i class="fa fa-bars" aria-hidden="true"></i>
                            <select name="product_id">
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
                        <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                            <select name="invoice_status" >
                                <option value="">---Select Invoice Status---</option>
                                <option value="0">Not Created</option>
                                <option value="1">Partially Paid</option>
                                <option value="2">Full Paid</option>
                                <option value="3">Unpaid</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            <!-- /.box-header -->

            <div class="row box-body load-hidden">
                <div class="row">
                    <div class="tab-content">
                        <!-- Morris chart - Sales -->
                        <div id="revenue-chart" style="position: relative; height: 300px;"></div>
                    </div>

                    <div class="row row-analysis mt-3">
                        <div class="col-md-2">
                            <div class="box chart-box text-center">
                                <h4>{{ __('Total Sales') }}</h4>
                                {{-- <span id="total_sales">{{ $total_sales }}$</span> --}}
                                <span id="total_sales"></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="box chart-box text-center">
                                <h4>{{ __('Untaxed Total') }}</h4>
                                {{-- <span id="untaxed_total">{{ $total_sales - $total_tax }} $</span> --}}
                                <span id="untaxed_total"></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="box chart-box text-center">
                                <h4>{{ __('Total Taxes') }}</h4>
                                {{-- <span id="total_taxes">{{ $total_tax }} $</span> --}}
                                <span id="total_taxes"></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="box chart-box text-center">
                                <h4>{{ __('Orders') }}</h4>
                                {{-- <span id="orders">{{ $no_of_orders }}</span> --}}
                                <span id="orders"></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="box chart-box text-center">
                                <h4>{{ __('Customers') }}</h4>
                                {{-- <span id="customers">{{ $customer_count }}</span> --}}
                                <span id="customers"></span>
                            </div>
                        </div>
                        <div class="col-md-2">
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
        <div class="box load-hidden" style="border-top: 2px solid #f9f9f9 !important;">
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
    ajax_data['sales_person_id'] = '';
    ajax_data['sales_team_id'] = $('[name=salesteam_id]').val();
    ajax_data['currency'] = $('[name=currency]').val();
    ajax_data['currency_symbol'] = $('[name=currency] option:selected').data('symbol');
    ajax_data['country_id'] = '';
    ajax_data['product_id'] = '';
    ajax_data['variation_id'] = '';
    ajax_data['invoice_status'] = '';


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
        xLabelAngle : "30"
      });

      data= @json($sales_data); // [{"date":"14-06-2021","sales":21.5},{"date":"13-06-2021","sales":5.75}]
      // Set the data for the area chart
      area_chart.setData(data);
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
    $("body").on('change','select[name=salesperson_id]',function(){
        ajax_data['sales_person_id'] = $(this).val();
        refresh_graph_ajax();
    });
    $("body").on('change','select[name=salesteam_id]',function(){
        ajax_data['sales_team_id'] = $(this).val();
        refresh_graph_ajax();
    });
    $("body").on('change','select[name=currency]',function(){
        ajax_data['currency'] = $(this).val();
        ajax_data['currency_symbol'] = $(this).find('option:selected').data('symbol');
        refresh_graph_ajax();
    });
    $("body").on('change','select[name=country_id]',function(){
        ajax_data['country_id'] = $(this).val();
        refresh_graph_ajax();
    });
    $("body").on('change','select[name=invoice_status]',function(){
        ajax_data['invoice_status'] = $(this).val();
        refresh_graph_ajax();
    });
    $('body').on('change', 'select[name=product_id]', function(){
        ajax_data['product_id'] = $('option:selected',this).attr('data-product_id');
        ajax_data['variation_id'] = $('option:selected',this).attr('data-variation_id');
        refresh_graph_ajax();
    });

    function refresh_graph_ajax(){
        $("#ajax_loader").show();

        $.ajax({
            type: "GET",
            url: "{{route('admin.sales-team.analysis')}}", // This is the URL to the API
            data: {
                start_date: ajax_data['start_date'],
                end_date: ajax_data['end_date'],
                customer_id: ajax_data['customer_id'],
                sales_team_id: ajax_data['sales_team_id'],
                sales_person_id: ajax_data['sales_person_id'],
                currency: ajax_data['currency'],
                country_id: ajax_data['country_id'],
                product_id: ajax_data['product_id'],
                variation_id: ajax_data['variation_id'],
                invoice_status: ajax_data['invoice_status']
            }
        })
        .done(function( data ) {
            // When the response to the AJAX request comes back render the chart with new data
            area_chart.setData(data['sales_data']);
            $('#total_sales').html(ajax_data['currency_symbol']+data['total_sales']+' <strong>'+ajax_data['currency']+'</strong>');
            $('#untaxed_total').html(ajax_data['currency_symbol']+data['untaxed_total']+' <strong>'+ajax_data['currency']+'</strong>');
            $('#total_taxes').html(ajax_data['currency_symbol']+data['total_tax']+' <strong>'+ajax_data['currency']+'</strong>');
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
        lengthChange: false,
        responsive: true,
        serverSide: true,
        searching:false,
        ordering:false,
        "order": [[ 1, "desc" ]],
        // ajax: tableajaxurl,
        "ajax": {
            "url": tableajaxurl,
            "data": function(d){
                d.start_date = ajax_data['start_date'];
                d.end_date = ajax_data['end_date'];
                d.customer_id = ajax_data['customer_id'];
                d.sales_team_id =  ajax_data['sales_team_id'];
                d.sales_person_id = ajax_data['sales_person_id'];
                d.currency = ajax_data['currency'];
                d.country_id = ajax_data['country_id'];
                d.product_id = ajax_data['product_id'];
                d.variation_id = ajax_data['variation_id'];
                d.invoice_status = ajax_data['invoice_status'];
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

    refresh_graph_ajax();
</script>
@endsection
