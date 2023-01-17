@extends('manufacturers.layouts.app')
@section('title', __('Manufacturer dashboard'))
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
                        {{ __('Manufacturer Dashboard') }}
                    </h2>
                </div>
                <div class="col-md-4">
                </div>
            </div>
        </section>
        <!-- Main content -->
        <section class="content">


            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            {{-- <h3 id="quotation_count"></h3> --}}
                            <h3 id="quotation_count">{{ $count_products }}</h3>
                            <p>{{ __('Products') }} </p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-quote"></i>
                        </div>
                        <!-- <a href="#"
                            class="small-box-footer">{{ __('More info') }} <i class="fa fa-arrow-circle-right"></i></a> -->
                    </div>
                </div>
                <!-- ./col -->

                <!-- ./col -->
                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3 id="customer_count">{{ $order_products }}</h3>
                            {{-- <h3 id="customer_count"></h3> --}}

                            <p>{{ __('Sales Order') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <!-- <a href="#"
                            class="small-box-footer">{{ __('More info') }} <i class="fa fa-arrow-circle-right"></i></a> -->
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3 id="reseller_count">{{$voucher_orders}}</h3>

                            <p>{{ __('Voucher Orders') }} </p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>

                    </div>
                </div>
                <!-- ./col -->

                <!-- ./col -->
            </div>
            <!-- /.row -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ __('Products By Manufacturer') }}</h3>

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
                                            <th>{{ __('Product Name') }}</th>
                                            <th>{{ __('Internal Reference') }}</th>
                                            {{-- <th>{{ __('Sale Price') }}</th> --}}
                                            <th>{{ __('Cost Price') }}</th>
                                            <th>{{ __('Maufacturer') }}</th>
                                            <!-- <th>{{ __('Status') }}</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>

                                            @foreach ($products_by_manufacturers as  $manufacturer)

                                                <tr>
                                                    <td>{{ isset($manufacturer->product_name)?$manufacturer->product_name:''}}</td>
                                                    <td>{{ isset($manufacturer->generalInformation->internal_reference)?$manufacturer->generalInformation->internal_reference:''}}</td>
                                                    {{-- <td>{{ number_format($manufacturer->generalInformation->sales_price,2)}}</td> --}}
                                                    <td>{{ number_format($manufacturer->generalInformation->cost_price,2)}}
                                                    </td>
                                                    <td>{{ isset($manufacturer->manufacturer->manufacturer_name)?$manufacturer->manufacturer->manufacturer_name:'' }}</td>
                                                    <!-- @if($manufacturer->is_active == 1)
                                                    <td> <strong> Active</strong></td>
                                                    @else
                                                    <td> <strong>InActive</strong></td>
                                                    @endif -->
                                                </tr>
                                            @endforeach

                                    </tbody>
                                </table>
                            </div>

                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.box-body -->

                        <!-- /.box-footer -->
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
    <!-- <script>
        export_url = "{{route('admin.sales-report')}}";
        ajax_data=[];
        ajax_data['currency'] = $('select[name=currency]').val();
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
                // When the response to the AJAX request comes back render the chart with new data
                // area_chart.setData(data['sales_data']);

                // $('#guest_count').html(data['guest_count']);
                // $('#reseller_count').html(data['reseller_count']);
                // $('#customer_count').html(data['customer_count']);
                ajax_data['currency'] = ajax_data['currency'] == null ? '' : ajax_data['currency'];
                $('#salesorders_count').html(data['salesorders_count']);
                $('#quotation_count').html(data['quotation_count']);
                $('#total_taxes').html(data['total_taxes']+' <strong>'+ajax_data['currency']+'</strong>');
                $('#all_quotation_untaxed_total').html(data['all_quotation_untaxed_total']+' <strong>'+ajax_data['currency']+'</strong>');
                $('#all_quotation_total').html(data['all_quotation_total']+' <strong>'+ajax_data['currency']+'</strong>');
                $('#total_invoiced_amount').html(data['total_invoiced_amount']+' <strong>'+ajax_data['currency']+'</strong>');
                // $('#order_to_invoice').html(parseFloat(data['order_to_invoice']).toFixed(2));
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
                        table += '<td>'+value.total+' '+value.currency+'</td>';
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
    </script> -->
@endsection
