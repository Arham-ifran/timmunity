@extends('frontside.layouts.app')
@section('title') {{ __('Voucher Payments') }} @endsection
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

        #vouchers{
            width:100% !important;
            margin-bottom:5px !important;
        }

    </style>
@endsection
@section('content')
    <div class="container">

        <div class="row cloud-row">
            <div class="col-md-12">
                <h3 class="voucher_heading">{{ __('Voucher Payments') }}</h3>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="vouchers"
                        class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                        <thead>
                            <tr role="row">
                                <th>{{ __('Action') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Voucher') }}</th>
                                <th>{{ __('Price Per Voucher') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Taxes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
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
                    data: 'product',
                    name: 'product'
                },
                {
                    data: 'vouchers',
                    name: 'vouchers'
                },
                {
                    data: 'price_per_voucher',
                    name: 'price_per_voucher'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'taxes',
                    name: 'taxes'
                }
            ]
        });
    </script>
@endsection
