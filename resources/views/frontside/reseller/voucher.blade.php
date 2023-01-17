@extends('frontside.layouts.app')
@section('title') {{ __('Reseller Dashboard') }} @endsection
@section('body_class') cart-page @endsection
@section('style')
    <link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .row.cloud-row {
            margin-top: 20px;
        }
        #vouchers thead tr {
            background: #009a71;
        }
        #vouchers thead tr th {
            color: white;
        }
        table.dataTable.dtr-inline.collapsed>tbody>tr>td:first-child:before,
        table.dataTable.dtr-inline.collapsed>tbody>tr>th:first-child:before {
            background-color: #009a71;
        }
        #vouchers_wrapper thead tr {
            background: #009a71;
        }
        #vouchers_wrapper thead tr th {
            color: #fff;
        }
        .voucher-heading h3{
            font-size: 36px;
        }
    </style>
@endsection
@section('content')
    <div class="container">

        <div class="row cloud-row voucher-heading">
            <div class="col-md-12">
                <h3 class="voucher_heading">{{ __('Vouchers') }}</h3>
            </div>
            <div class="col-md-12">
                    <table width="100%" id="vouchers"
                        class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                        <thead>
                            <tr role="row">
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('End Customer') }}</th>
                                <th>{{ __('Redeemed At') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
            </div>
        </div>
    </div>

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
    <script>
        $("#vouchers").DataTable({
            lengthChange: false,
            responsive: true,
            orderCellsTop: true,
            serverSide: true,
            scrollCollapse: true,
            fixedColumns: true,
            scrollX: true,
            ajax: '{{ $ajax_url }}',
            columns: [
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'customer',
                    name: 'customer'
                },
                {
                    data: 'redeemed_time',
                    name: 'redeemed_time'
                }
            ]
        });

    </script>
@endsection
