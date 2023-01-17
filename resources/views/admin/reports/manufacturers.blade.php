@extends('admin.layouts.app')
@section('title', __('Manufacturers'))
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

        .daterangepicker .calendar{
            max-width: 230px !important;
            margin: 4px;
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
                    {{ __('Manufacturer Analysis') }}
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
                            <select name="manufacturer_id" id="manufacturer_id" class="form-control">
                            <option value="" disabled hidden selected>---Select Manufacturer---</option>
                            @foreach($manufacturers as $ind => $manufacture)
                              <option data-manufacturer_id="{{ $manufacture['id'] }}">{{$manufacture['manufacturer_name']}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 product_selection">
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
                            
                            <input type="text" id="date_filter" class="form-control" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            
                            <input type="text" id="name_email" name="name_email" placeholder="Enter Reseller Name/Email" class="form-control" >
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
                    <!--
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
                    </div> -->
                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box load-hidden" style="border-top: 2px solid #f9f9f9 !important;">
            <table id="manufacturerTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('Order Number') }}</th>
                        <th>{{ __('Creation Date') }}</th>
                        <th>{{ __('Product Name') }}</th>
                        <th>{{ __('Manufacturer Name') }}</th>
                        <th>{{ __('Reseller Name') }}</th>
                        <th> {{__('Total')}}</th>
                        <th>{{ __('Status') }}</th>
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
    ajax_data['name_email']='';
    // ajax_data['customer_id'] = '';
    // ajax_data['sales_person_id'] = '';
    // ajax_data['sales_team_id'] = $('[name=salesteam_id]').val();
    // ajax_data['currency'] = $('[name=currency]').val();
    // ajax_data['country_id'] = '';
    // ajax_data['product_id'] = '';
    // ajax_data['variation_id'] = '';
    // ajax_data['invoice_status'] = '';


    tableajaxurl = "{{ route('admin.reports.manufacturers') }}"
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

    // $("body").on('change','select[name=customer_id]',function(){
    //     ajax_data['customer_id'] = $(this).val();
    //     refresh_graph_ajax();
    // });
    // $("body").on('change','select[name=salesperson_id]',function(){
    //     ajax_data['sales_person_id'] = $(this).val();
    //     refresh_graph_ajax();
    // });
    // $("body").on('change','select[name=salesteam_id]',function(){
    //     ajax_data['sales_team_id'] = $(this).val();
    //     refresh_graph_ajax();

    
    $('body').on('input', 'input[name=name_email]', function(){
        ajax_data['name_email'] = $(this).val();
        table.ajax.reload();
    });

    $('body').on('change', 'select[name=manufacturer_id]', function(){
        ajax_data['manufacturer_id'] = $('option:selected',this).attr('data-manufacturer_id');

        $.ajax({

            type:"GET",
            url:"{{route('admin.reports.manufacturer.product')}}",
            data:{

                manufacturer_id:ajax_data['manufacturer_id'],
            },

            success: function (data) {
                if(data['success'] == 'true'){
                
                    if(data.data.length > 0){
                       
                        manufacturer_products = data.data;
                        $('select[name=product_id]').find('option').remove().end()
                        $.each(manufacturer_products,function(index,value){
                            $('select[name=product_id]').append('<option data-product_id="'+value.id+'">'+data.data[index].product_name+'</option>')
                        });
                        $('.product_selection').show();

                    }else{

                        $('select[name=product_id]').html('<option data-product_id="">'+'No Product Available'+'</option>');
                        

                    }
                   
                    
                    // $('.product_selection').show();
                }
            },
            complete:function(data){

            }

        }).done( function (data){

            console.log(data);
        })
        refresh_graph_ajax();
    });

    function refresh_graph_ajax(){
        $("#ajax_loader").show();

        $.ajax({
            type: "GET",
            url: "{{route('admin.reports.manufacturers')}}", // This is the URL to the API
            data: {
                start_date: ajax_data['start_date'],
                end_date: ajax_data['end_date'],
                name_email:ajax_data['name_email'],
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
    var table = $('#manufacturerTable').DataTable({
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
                d.start_date = ajax_data['start_date'];
                d.end_date = ajax_data['end_date'];
                d.product_id = ajax_data['product_id'];
                d.manufacturer_id = ajax_data['manufacturer_id'];
                d.name_email = ajax_data['name_email'];
            }
        },

        columns: [

            {
                data: 'order_no',
                name: 'order_no'
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: 'product_name',
                name: 'product_name'
            },
            {
                data: 'manufacturer',
                name: 'manufacturer'
            },
            {
                data: 'reseller',
                name: 'reseller'
            },

            {
                data: 'total',
                name: 'total'
            },

            {
                data: 'status',
                name: 'status'
            },

        ]
    });

    $('body').on('change', 'select[name=product_id]', function(){
        ajax_data['product_id'] = $('option:selected',this).attr('data-product_id');
        ajax_data['variation_id'] = $('option:selected',this).attr('data-variation_id');
        table.ajax.reload();
    });

    


    refresh_graph_ajax();
</script>
@endsection
