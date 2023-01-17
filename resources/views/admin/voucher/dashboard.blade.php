@extends('admin.layouts.app')
@section('title', __('Voucher Dashboard'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components//morris.js/morris.css') }}">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header top-header">
            <div class="row" style="padding-bottom: 15px;">
                <div class="col-md-3">
                    <h2>
                        {{ __('Voucher Dashboard') }}
                    </h2>
                </div>
                <div class="col-md-3">

                </div>

                <div class="col-md-3 pt-3">
                    <div class="form-group pt-2">
                        <!-- {{route('admin.voucher.generate.report')}} -->
                        <a href="{{route('admin.voucher.generate.report')}}?currency={{@$currencies[0]}}" id="download" value="" class="btn btn-primary mt-2">Download Report</a>
                    </div>
                </div>
                <div class="col-md-3 pull-right">

                    <div class="form-group text-right">
                        <h2 for="" class="control-label">Select Currency</h2>
                        <select class="form-control" name="currency" id="currency">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency }}">{{ $currency }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



            </div>
        </section>

        <section class="content kks-subscription-box-sections">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-default">
                        <div class="box-body" style="">
                            <div class="row">
                                <div class="col-md-4">
                                    <h3 class="text-center">{{ __('Voucher Orders') }}</h3>
                                    <div id="voucher-order-stats"></div>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-center">{{ __('Vouchers') }}</h3>
                                    <div id="vouchers-stats"></div>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-center">{{ __('Voucher Payments') }}</h3>
                                    <div id="voucher-payments-stats"></div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                @can('Voucher Order Listing')
                <div class="col-md-12">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"> <strong>{{ __('Recently Ordered Vouchers') }}</strong></h3>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="">
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="vouchers" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                                        <thead>
                                            <tr role="row">
                                                <th>{{ __('Order ID')}}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Product') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                {{-- <th>{{ __('Total Paid Amount') }}</th> --}}
                                                {{-- <th>{{ __('Total Remaing Amount') }}</th> --}}
                                                <th>{{ __('Total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                @endcan
            </div>
        </section>

        <!-- /.content -->
    </div>

@endsection
@section('scripts')
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
    <script src="{{ asset('backend/bower_components/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('backend/bower_components/morris.js/morris.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>

        export_url = "{{route('admin.voucher.generate.report')}}";
        voucher_order_data = @json($voucher_order_data);
        voucher_data = @json($voucher_data);
        voucher_payment_data = @json($voucher_payment_data);
        ajax_data = [];
        ajax_data['currency'] = $('[name=currency]').val();
        $("#download").attr('href',export_url+"?currency="+ajax_data['currency']);
        var voucherorderstats = new Morris.Donut({
            element   : 'voucher-order-stats',
            resize   : true,
            colors   : ['#3c8dbc','#f39c12' , '#009a71','#f56954'],
            data     : voucher_order_data   ,
            hideHover: 'auto',
            parseTime: false
        });
        var voucherstats = new Morris.Donut({
            element   : 'vouchers-stats',
            resize   : true,
            colors   : ['#3c8dbc','#f39c12' , '#009a71','#f56954'],
            data     : voucher_data   ,
            hideHover: 'auto',
            parseTime: false
        });
        var voucherpaymentsstats = new Morris.Donut({
            element   : 'voucher-payments-stats',
            resize   : true,
            colors   : ['#3c8dbc', '#f56954', '#009a71'],
            data     : voucher_payment_data   ,
            hideHover: 'auto',
            parseTime: false
        });
        var table = $("#vouchers").DataTable({
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            lengthChange: false,
            responsive: true,
            orderCellsTop: true,
            serverSide: true,
            "ajax": {
                "url": '{{ route("admin.voucher.orders") }}',
                "data": function(d){
                    d.currency = ajax_data['currency'];
                }
            },
            columns: [
                {
                    data:'order_id',
                    name:'order_id',
                },
                {
                    data: 'statuss',
                    name: 'statuss'
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                // {
                //     data: 'paid_amount',
                //     name: 'paid_amount'
                // },
                // {
                //     data: 'pending_payment',
                //     name: 'pending_payment'
                // },
                {
                    data: 'total_payable_amount',
                    name: 'total_payable_amount'
                }
            ]
        });

        $("body").on('change','select[name=currency]',function(){
            ajax_data['currency'] = $(this).val();
            $("#download").attr('href',export_url+"?currency="+ajax_data['currency']);
            refresh_graph_ajax();
        });
        function refresh_graph_ajax(){
            $("#ajax_loader").show();

            $.ajax({
                type: "GET",
                url: "{{route('admin.voucher.dashboard')}}", // This is the URL to the API
                data: {
                    currency: ajax_data['currency']
                }
            })
            .done(function( data ) {
                $('#order_lines_no').html(data['no_of_lines']);
                table.ajax.reload();

                voucherorderstats.setData(data['voucher_order_data']);
                voucherstats.setData(data['voucher_data']);
                voucherpaymentsstats.setData(data['voucher_payment_data']);

                $("#ajax_loader").hide();
            })
            .fail(function() {
                // If there is no communication between the server, show an error
                alert( "error occured" );
                $("#ajax_loader").hide();
            });
        }
        refresh_graph_ajax();
    </script>
@endsection
