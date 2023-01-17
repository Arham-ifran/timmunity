@extends('admin.layouts.app')
@section('title', __('Sales'))
@section('styles')
    <link rel="stylesheet" href="{{ asset('backend/bower_components//morris.js/morris.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/bower_components/jvectormap/jquery-jvectormap.css') }}">
    <link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
    <style>
        .info-box .small-box-footer {
            position: relative;
            text-align: center;
            padding: 3px 0;
            color: #fff;
            color: rgba(255,255,255,0.8);
            display: block;
            z-index: 10;
            background: rgba(0,0,0,0.1);
            text-decoration: none;
        }
        .info-box-icon {
            height:40px;
        }
        .info-box-icon {
            line-height: 0;
        }
        .info-box:hover .small-box-footer {
            color: #fff;
            background: rgba(0,0,0,0.15);
        }
        .info-box:hover .ion{
            font-size: 55px;
        }
        .info-box .ion{
            transition: all .3s linear;
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
        <section class="content-header top-header dashboard-top">
            <div class="row">
                <div class="col-md-4">
                    <h2>
                        {{ __('Sales Dashboard') }}
                    </h2>
                </div>
                <div class="col-md-4">
                </div>
                <div class="col-md-2 pt-2">
                    <a href="{{route('admin.sales-report')}}?currency={{@$currencies[0]}}" target="_blank" id="exportBtn" class="btn btn-primary mt-2">Download Report</a>
                </div>
                <div class="col-md-2">
                    <div class="form-group ">
                        <label for="" class="control-label">Select Currency</label>
                        <select class="form-control" name="currency" id="currency">
                            {{-- <option value="{{ $currency }}">{{ $currency }}</option> --}}
                            @foreach($currencies as $symbol=>$currency)
                                <option value="{{ $currency }}" data-symbol="{{ $symbol }}">{{ $currency }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            {{-- <h3 id="quotation_count">{{ $quotation_count }}</h3> --}}
                            <h3 id="quotation_count"></h3>
                            <p>{{ __('Quotations') }} </p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-quote"></i>
                        </div>
                        <a href="{{ route('admin.quotations.index') }}" target="_blank"
                            class="small-box-footer">{{ __('More info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-green">
                        <div class="inner">
                            {{-- <h3>{{ $salesorders_count }}<sup style="font-size: 20px">K</sup></h3> --}}
                            {{-- <h3 id="salesorders_count">{{ $salesorders_count }}</h3> --}}
                            <h3 id="salesorders_count"></h3>

                            <p>{{ __('Sales Order') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ route('admin.quotation.sales.orders') }}" target="_blank"
                            class="small-box-footer">{{ __('More info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-2 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="customer_count">{{ $customer_count }}</h3>
                            {{-- <h3 id="customer_count"></h3> --}}

                            <p>{{ __('Customers') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{ route('admin.customers.index') }}" target="_blank"
                            class="small-box-footer">{{ __('More info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-2 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3 id="reseller_count">{{ $reseller_count }}</h3>
                            {{-- <h3 id="reseller_count"></h3> --}}

                            <p>{{ __('Resellers') }} </p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-stalker"></i>
                        </div>
                        <a href="{{ route('admin.website.resellers') }}" target="_blank"
                            class="small-box-footer"> {{ __('More info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-2 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="guest_count">{{ $guest_count }}</h3>
                            {{-- <h3 id="guest_count"></h3> --}}

                            <p>{{ __('Guests') }} </p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-stalker"></i>
                        </div>
                        <a href="{{ route('admin.contacts.index')."?type=4" }}" target="_blank"
                            class="small-box-footer">{{ __('More info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->

            <!-- second row -->

            <!--  row starting here -->
            <div class="row">
                <!-- Left col -->
                <div class="col-md-9">
                    <!-- MAP & BOX PANE -->
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-users"></i> {{ __('Total Customers') }}</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                                </button>
                                {{-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> --}}
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body " style="">
                            <div class="row">
                                <div class="col-md-9 col-sm-8">
                                    <div class="box-body">

                                        <div class="pad">
                                            <!-- Map will be created here -->
                                            <div id="customer-map-markers" style="height: 325px;"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col -->
                                <div class="col-md-3 col-sm-4">
                                    <div class="pad box-pane-right bg-green" style="min-height: 280px">
                                        <div class="description-block margin-bottom">
                                            <div class="sparkbar pad" data-color="#fff">90,70,90,70,75,80,70</div>
                                            {{-- <h5 class="description-header">{{ $total_taxes }}</h5> --}}
                                            <h5 class="description-header" id="total_taxes"></h5>
                                            <span class="description-text">{{ __('TAXES') }}</span>
                                        </div>
                                        <!-- /.description-block -->
                                        <div class="description-block margin-bottom">
                                            <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
                                            {{-- <h5 class="description-header">{{ $all_quotation_total }}</h5> --}}
                                            <h5 class="description-header" id="all_quotation_total">{{ $all_quotation_total }}</h5>
                                            <span class="description-text">{{ __('TOTAL') }}</span>
                                        </div>
                                        <!-- /.description-block -->
                                        <div class="description-block">
                                            <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
                                            <h5 class="description-header" id="all_quotation_untaxed_total">{{ $all_quotation_untaxed_total }}</h5>
                                            <span class="description-text">{{ __('UNTAXED AMOUNT') }}</span>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.box-body -->
                    </div>

                </div>
                <!-- /.col -->

                <div class="col-md-3">
                    <!-- /.info-box -->
                    <div class="info-box bg-green custom-style">
                        <span class="info-box-icon"><i class="ion ion-card"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('Orders To Be Invoiced') }}</span>
                            {{-- <span class="info-box-number">{{ $order_to_invoice }}</span> --}}
                            <span class="info-box-number" id="order_to_invoice"></span>

                            {{-- <div class="progress">
                                <div class="progress-bar" style="width: 20%"></div>
                            </div> --}}
                        </div>
                        <a href="{{ route('admin.quotation.sales.orders.toinvoice') }}" target="_blank"
                            class="small-box-footer">{{ __('More info') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>

                    <!-- /.info-box -->
                    <div class="info-box bg-aqua custom-style">
                        <span class="info-box-icon"><i class="ion-cash"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('Total Amount Invoiced') }}</span>
                            {{-- <span class="info-box-number">{{ $total_invoiced_amount }}</span> --}}
                            <span class="info-box-number" id="total_invoiced_amount"></span>

                            {{-- <div class="progress">
                                <div class="progress-bar" style="width: 40%"></div>
                            </div> --}}
                        </div>
                        <a href="{{ route('admin.invoices.index') }}" target="_blank"
                            class="small-box-footer">{{ __('More info') }} <i class="fa fa-arrow-circle-right"></i></a>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                    <div class="info-box bg-red custom-style">
                        <span class="info-box-icon"><i class="ion-cash"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('Total Amount Refunded') }}</span>
                            {{-- <span class="info-box-number">{{ $total_invoiced_amount }}</span> --}}
                            <span class="info-box-number" id="total_refunded_amount"></span>

                            {{-- <div class="progress">
                                <div class="progress-bar" style="width: 40%"></div>
                            </div> --}}
                        </div>
                        <a href="{{ route('admin.invoices.index') }}" target="_blank"
                            class="small-box-footer">{{ __('More info') }} <i class="fa fa-arrow-circle-right"></i></a>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- Ending here -->

            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <section class="col-lg-12 connectedSortable">
                    <!-- Custom tabs (Charts with tabs)-->
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs pull-right">
                            <li class="active"><a href="#revenue-chart" data-toggle="tab">{{ __('Area') }}</a>
                            </li>
                            <li><a href="#sales-chart" data-toggle="tab" id="donut-btn">{{ __('Donut') }}</a></li>
                            <li class="pull-left header">{{ __('Last 5 Months Sale') }}</li>
                        </ul>
                        <div class="tab-content ">
                            <!-- Morris chart - Sales -->
                            <div class="chart tab-pane active" id="revenue-chart"
                                style="position: relative; height: 300px;"></div>
                            <div class="chart tab-pane" id="sales-chart">
                                <div id="sales-chart-donut" style="position: relative; height: 300px;">

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.nav-tabs-custom -->

                </section>

            </div>
            <!-- /.row (main row) -->

            <div class="row">
                <div class="col-lg-7">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ __('Last 10 Sales Orders') }}</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                                </button>
                                {{-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> --}}
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="last_10 table no-margin">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Order Reference') }}</th>
                                            <th>{{ __('Total') }}</th>
                                            <th>{{ __('Creation Date') }}</th>
                                            <th>{{ __('Customer') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($last_10_sales) > 0)
                                            @foreach ($last_10_sales as $sale)
                                                <tr>
                                                    <td>S{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
                                                    <td>{{ $sale->total }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('Y/m/d') }}
                                                    </td>
                                                    <td>{{ $sale->customer->name }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    {{ __('No sales order has been created') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer ">
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ route('admin.quotations.create') }}" target="_blank"
                                        class="btn btn-sm btn-info btn-flat pull-left">{{ __('Place New Order') }}</a>
                                    <a href="{{ route('admin.quotation.sales.orders') }}" target="_blank"
                                        class="btn btn-sm btn-default btn-flat pull-right">{{ __('View All Orders') }}</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-footer -->
                    </div>
                </div>

                <div class="col-lg-5">
                    <!-- right col (We are only adding the ID to make the widgets sortable)-->
                    <section class="connectedSortable">

                        <!-- solid sales graph -->
                        <div class="box box-solid bg-teal-gradient">
                            <div class="box-header">
                                <i class="fa fa-th"></i>

                                <h3 class="box-title">{{ __('Top Sales') }}</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                    </button>
                                    {{-- <button type="button" class="btn bg-teal btn-sm" data-widget="remove"><i class="fa fa-times"></i> --}}
                                    </button>
                                </div>
                            </div>
                            <div class="box-body border-radius-none">
                                <div class="chart" id="line-chart" style="height: 250px;"></div>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer no-border">
                                <div class="row">
                                    <div class="col-xs-12 text-center" style="border-right: 1px solid #f4f4f4">
                                        <input type="text" class="knob" data-readonly="true"
                                            {{-- value="{{ $top_10_sale_products_sale_count[0]['sales'] }}" data-width="60" --}}
                                            data-width="60"
                                            data-height="60" data-fgColor="#39CCCC">

                                        <div class="knob-label">{{ __('Sales') }}</div>
                                    </div>
                                    <!-- ./col -->
                                    {{-- <div class="col-xs-6 text-center" style="border-right: 1px solid #f4f4f4">
                                    <input type="text" class="knob" data-readonly="true" value="50" data-width="60" data-height="60" data-fgColor="#39CCCC">

                                    <div class="knob-label">Websites</div>
                                </div> --}}

                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.box-footer -->
                        </div>
                        <!-- /.box -->
                    </section>
                    <!-- right col -->
                </div>
            </div>

        </section>
        <!-- /.content -->
    </div>
@endsection
@section('scripts')

    <script src="{{ asset('backend/bower_components/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('backend/bower_components/morris.js/morris.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('backend/bower_components/jquery-knob/dist/jquery.knob.min.js') }}"></script>
    <script>
        export_url = "{{route('admin.sales-report')}}";
        ajax_data=[];
        ajax_data['currency'] = $('select[name=currency]').val();
        ajax_data['currency_symbol'] = $("[name=currency] option:selected").data('symbol');
        donut_data = [];
        var donut;
        var area;
        var line;
        var monthly_sale, monthly_sales_dougnut_data = null;
        /* jQueryKnob */
        $('.knob').knob();
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr("href") // activated tab

            switch (target) {
                case "#sales-chart":
                    donut.redraw();
                    $(window).trigger('resize');
                    break;
                case "#revenue-chart":
                    area.redraw();
                    $(window).trigger('resize');
                    break;
            }
        });

        @if (count($customer_map_stats) > 0)
            customer_map_stats = @json($customer_map_stats);
        @else
            customer_map_stats = [];
        @endif

        try {
            $('#customer-map-markers').vectorMap({
                map: 'world_mill_en',
                normalizeFunction: 'polynomial',
                hoverOpacity: 0.7,
                // hoverColor       : false,
                backgroundColor: 'transparent',
                regionStyle: {
                    initial: {
                        fill: 'rgba(210, 214, 222, 1)',
                        'fill-opacity': 1,
                        stroke: 'none',
                        'stroke-width': 0,
                        'stroke-opacity': 1
                    },
                    hover: {
                        fill: '#009a71',
                        'fill-opacity': 0.7,
                        cursor: 'pointer'
                    },
                    selected: {
                        fill: 'yellow'
                    },
                    selectedHover: {}
                },
                markerStyle: {
                    initial: {
                        fill: '#009a71',
                        stroke: '#111'
                    },
                    hover: {
                        'fill-opacity': 0.7,
                        'fill': '#fff',
                        cursor: 'pointer'
                    }
                },
                markers: customer_map_stats
            });
        } catch (e) {

        }

        $("body").on('change','select[name=currency]',function(){
            ajax_data['currency'] = $(this).val();
            ajax_data['currency_symbol'] = $(this).find('option:selected').data('symbol');
            // export_url = "{{route('admin.sales-report')}}?currency={{@$currencies[0]}}"
            $('#exportBtn').attr('href', export_url+'?currency='+ajax_data['currency']);
            refresh_graph_ajax();
        });
        $('body').on('click', '#donut-btn', function(){
            $("#sales-chart-donut").empty();
            var donut = new Morris.Donut({
                element : 'sales-chart-donut',
                resize : true,
                colors : ['#3c8dbc', '#f56954', '#009a71'],
                data : donut_data ,
                hideHover: 'auto',
                parseTime: false
            });
        })
        function refresh_graph_ajax(){
            // guest_count,reseller_count,customer_count,salesorders_count,quotation_count
            $.ajax({
                type: "GET",
                url: "{{route('admin.sales-dashboard')}}", // This is the URL to the API
                data: {
                    currency: ajax_data['currency']
                }
            })
            .done(function( data ) {
                ajax_data['currency'] = ajax_data['currency'] == null ? '' : ajax_data['currency'];
                $('#salesorders_count').html(data['salesorders_count']);
                $('#quotation_count').html(data['quotation_count']);
                $('#total_taxes').html(ajax_data['currency_symbol']+' '+data['total_taxes']+' <strong>'+ajax_data['currency']+'</strong>');
                $('#all_quotation_untaxed_total').html(ajax_data['currency_symbol']+' '+data['all_quotation_untaxed_total']+' <strong>'+ajax_data['currency']+'</strong>');
                $('#all_quotation_total').html(ajax_data['currency_symbol']+' '+data['all_quotation_total']+' <strong>'+ajax_data['currency']+'</strong>');
                $('#total_invoiced_amount').html(ajax_data['currency_symbol']+' '+data['total_invoiced_amount']+' <strong>'+ajax_data['currency']+'</strong>');
                $('#total_refunded_amount').html(ajax_data['currency_symbol']+' '+data['total_refunded_amount']+' <strong>'+ajax_data['currency']+'</strong>');
                $('#order_to_invoice').html(data['order_to_invoice']);
                $('.knob').val(data['top_10_sale_products_sale_count'][0]['sales']);
                $('.knob').knob();
                $("#revenue-chart").empty();
                monthly_sale = data['monthly_sales'];
                area = new Morris.Area({
                    element : 'revenue-chart',
                    resize : true,
                    data : monthly_sale,
                    xkey : 'new_date',
                    ykeys : ['sales'],
                    labels : ['Sales'],
                    lineColors: ['#a0d0e0'],
                    hideHover : 'auto',
                    parseTime: false
                });
                $("#sales-chart-donut").empty();
                    // Donut Chart
                monthly_sales_dougnut_data = data['monthly_sales_dougnut'];
                donut_data = monthly_sales_dougnut_data;
                donut = new Morris.Donut({
                    element : 'sales-chart-donut',
                    resize : true,
                    colors : ['#3c8dbc', '#f56954', '#009a71'],
                    data : monthly_sales_dougnut_data ,
                    hideHover: 'auto',
                    parseTime: false
                });

                $("#line-chart").empty();
                var top_10_sale_products = data['top_10_sale_products'];
                line = new Morris.Line({
                    element : 'line-chart',
                    resize : true,
                    data : top_10_sale_products,
                    xkey : 'product_name',
                    ykeys : ['sales'],
                    labels : ['Sales'],
                    lineColors : ['#efefef'],
                    lineWidth : 2,
                    hideHover : 'auto',
                    gridTextColor : '#fff',
                    gridStrokeWidth : 0.4,
                    pointSize : 4,
                    pointStrokeColors: ['#efefef'],
                    gridLineColor : '#efefef',
                    gridTextFamily : 'Open Sans',
                    gridTextSize : 10,
                    parseTime: false
                });
                table = '';
                $.each(data['last_10_sales'], function( index, value ) {
                    table += '<tr>';
                        table += '<td>'+value.orderid+'</td>';
                        table += '<td>'+value.currency_symbol+' '+value.total+' '+value.currency+'</td>';
                        table += '<td>'+value.date+'</td>';
                        table += '<td>'+value.customer_name+'</td>';
                    table += '</tr>';
                });
                $('table.last_10 tbody').html(table);
                $("#ajax_loader").hide();
            })
            .fail(function() {
                // If there is no communication between the server, show an error
                alert( "error occured" );
                $("#ajax_loader").hide();
            });
            $("#ajax_loader").show();
        }
        refresh_graph_ajax();
    </script>
@endsection
