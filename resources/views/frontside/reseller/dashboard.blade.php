@extends('frontside.layouts.app')
@section('title') {{ __('Reseller Dashboard') }} @endsection
@section('body_class') cart-page @endsection
@section('style')
    <link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .green-box {
            border: 1px solid #787759;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        .row.cloud-row {
            margin-top: 20px;
        }

        #vouchers thead tr {
            background: #009a71;
        }

        #vouchers thead tr th {
            color: white;
        }

        .get-more-voucher-btn {
            font-size: 2rem;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td:first-child:before,
        table.dataTable.dtr-inline.collapsed>tbody>tr>th:first-child:before {
            background-color: #009a71;
        }
        label.error{
            color:red;
        }
        .form-control.error{
            border:1px solid red;
            box-shadow: inset 0 1px 1px rgb(241 134 134 / 65%);
        }
        #vouchers_wrapper thead tr {
            background: #009a71;
        }
        #vouchers_wrapper thead tr th {
            color: #fff;
        }
        .green-box h1{
            color: #009a71;
        }
        .green-box{
            border-color: #009a71;
        }
        .green-box:hover{
            background: #009a71;
        }
        .green-box:hover h1, .green-box:hover h4{
            color:white;
        }
        .copy-link{
            cursor:pointer;
        }
   
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
        /* Style the links inside the sidenav */
        #mySidenav{

            position: fixed;
            top: 100px;
            left: 0;
            display: flex;
            flex-flow: column;
            z-index: 9999;
            gap: 10px;
        }
        #mySidenav a {
            /* position: fixed; */
            transition: 0.3s;
            padding: 10px;
            width: 45px;
            text-decoration: none;
            font-size: 20px;
            color: white;
            border-radius: 0 5px 5px 0;
            writing-mode: sideways-rl !important;
            writing-mode: vertical-rl !important;
            z-index: 1000;
        }

        #mySidenav a:hover {
            left: 0; /* On mouse-over, make the elements appear as they should */
            width: 50px;
        }

        /* The about link: 20px from the top with a green background */
        #voucher-float {
            top: 109px;
            background-color: #04AA6D;
        }

        #redeem-float {
            top: 266px;
            background-color: #F0AD4E; /* Blue */
        }

        #invoices-float {
            top: 428px;
            background-color: #5cb85c; /* Red */
        }
    </style>
@endsection
@section('content')
    <div id="mySidenav" class="sidenav">
        <a href="#" data-toggle="modal" data-target="#getVoucherModal" id="voucher-float">{{ __('Get Vouchers') }}</a>
        <a target="_blank" href="{{ route('voucher.view.redeemed', Hashids::encode(@$redeem_page->reseller_id)) }}" id="redeem-float">{{ __('Redeem Page') }}</a>
        <a href="{{route('frontside.reseller.invoices')}}" id="invoices-float">{{ __('Invoices') }}</a>
    </div>
    <div class="container">
        <div class="row cloud-row">
            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <div class="green-box">
                    <h4><strong>{{ __('Total Orders') }}</strong></h4>
                    <h1><strong>{{ $total_orders }}</strong></h1>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <div class="green-box">
                    <h4><strong>{{ __('Total Vouchers') }}</strong></h4>
                    <h1><strong>{{ $total_vouchers }}</strong></h1>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <div class="green-box">
                    <h4><strong>{{ __('Used Vouchers') }}</strong></h4>
                    <h1><strong>{{ $used_vouchers }}</strong></h1>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <div class="green-box">
                    <h4><strong>{{ __('Remaining Vouchers') }}</strong></h4>
                    <h1><strong>{{ $remaining_vouchers }}</strong></h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <h3 class="voucher_heading">{{ __('Voucher Orders') }}</h3>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <div class="text-center mt-3">
                        <div class="input-group">
                            <input type="text" id="redeem_url" class="form-control" readonly value="{{$redeem_page->url}}">
                            <span class="input-group-addon copy-link" title="{{__('Click to copy redeem page url')}}"><i class="fa fa-copy"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <table  id="vouchers"
                    class="table table-striped table-bordered  nowrap no-footer dataTable">
                    <thead>
                        <tr role="row">
                            {{-- <th>{{ __('#') }}</th> --}}
                            <th>{{__('Order ID')}}</th>
                            <th>{{ __('Actions') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Active') }} </th>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Used') }}</th>
                            <th>{{ __('Remaining') }}</th>
                            <th>{{ __('Unit Price') }}</th>
                            <th>{{ __('Discount (%)') }}</th>
                            <th>{{ __('Taxes') }}</th>
                            <th>{{ __('Total Payable Amount') }}</th>
                            <th>{{ __('Pending Payment') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>

                </table>
            </div>
        </div>
        
    </div>
    @include('frontside.reseller.partials.voucher_order_form')
@endsection
@section('script')
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
    <script src="{{ asset('frontside\dist\js\reseller.js') }}"></script>
    <script>
        var currency = "{{ Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol') }}";
        var currency_code = "{{ Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code') }}";
        var url = "{{ route('get-product-variations', ':id') }}";
        var variation_select_default_text = "{{__('Select Variation')}}";
        var detail_url = "{{ route('get-product-variation-detail', ':id') }}";
        
        @php
            foreach($products as $ind => $product){
                $products[$ind]->hashed_id = Hashids::encode($product->id);
                $products[$ind]->secondary_projects = $product->secondary_projects_array;
            }
        @endphp

        var products = @json($products);
        var product_label = "{{ __('Product') }}";
        var select_product_label = "{{ __('Select Product') }}";
        var secondary_project_label = "{{ __('Secondary Products') }}";
        var variation_label = "{{ __('Variation') }}";
        var price_label = "{{ __('Price') }}";
        var quantity_label = "{{ __('Quantity') }}";
        $("#vouchers").DataTable({
            "order": [],
            lengthChange: false,
            // responsive: true,
            orderCellsTop: true,
            serverSide: true,
            // scrollCollapse: true,
            fixedColumns: true,
            scrollX: true,
            ajax: '{{ route("frontside.reseller.dashboard") }}',
            columns: [
                // {
                //     data: 'ids',
                //     name: 'ids'
                // },
                {
                    data: 'order_id',
                    name: 'order_id'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'statuss',
                    name: 'statuss'
                },
                {
                    data: 'active_status',
                    name: 'active_status'
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
                    data: 'used_quantity',
                    name: 'used_quantity'
                },
                {
                    data: 'remaining_quantity',
                    name: 'remaining_quantity'
                },
                {
                    data: 'unit_price',
                    name: 'unit_price'
                },
                {
                    data: 'discount',
                    name: 'discount'
                },
                {
                    data: 'taxes',
                    name: 'taxes'
                },
                {
                    data: 'total_payable_amount',
                    name: 'total_payable_amount'
                },
                {
                    data: 'pending_payment',
                    name: 'pending_payment'
                }
            ],
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ]
        });

        
        // Voucher form validation
        $('#voucher_form').validate();
        $('.copy-link').on('click',function(){
            // Create a "hidden" input
            var aux = document.createElement("input");
            // Assign it the value of the specified element
            aux.setAttribute("value", "{{$redeem_page->url}}");
            // Append it to the body
            document.body.appendChild(aux);
            // Highlight its content
            aux.select();
            // Copy the highlighted text
            document.execCommand("copy");
            // Remove it from the body
            document.body.removeChild(aux);
            $(this).parents('.text-center').append('<p id="copied-success" style="background: #eee;padding: 1px 27px;margin-top: 5px;border-radius: 5px;">URL Copied!</p>');
            setTimeout(() => {
                $('#copied-success').fadeOut();
                setTimeout(() => {
                    $('#copied-success').remove();
                },500)
                // $('#copied-success').remove();
            }, 2000);
        });

        $(window).scroll(function(){

            var window_top      = $(window).scrollTop();
            var footer_top      = $("#footer").offset().top;
            var sidebar_height  = $("#mySidenav").height();
            var sidebar_top     = $("#mySidenav").offset().top;

            if (window_top + sidebar_height > footer_top){
                $("#mySidenav").css('position','absolute');
                $("#mySidenav a").css('word-wrap','break-all');

            }
            else if (window_top > sidebar_top) {
                $("#mySidenav").css('position','absolute');
                $("#mySidenav a").css('word-wrap','break-all');
            }
            else {
                $("#mySidenav").css('position','');
            }
        });
    </script>
@endsection
