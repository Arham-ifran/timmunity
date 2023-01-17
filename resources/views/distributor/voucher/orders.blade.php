@extends('distributor.layouts.app')
@section('title', __('Voucher Orders'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />

<style>
    input#date_filter,
    select[name=product],
    select[name=status],
    input[name=name-email]{
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
    .status-action-btn {
        padding: 0 15px;
    }
    .btn.btn-secondary.buttons-html5 {
        border: 1px solid #009a71;
    }
    .btn.btn-secondary.buttons-html5:hover {
        border: 1px solid #009a71;
        background: #009a71;
        color: #fff;
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
                        {{ __('Voucher Orders') }}
                    </h2>
                </div>
            </div>
        </section>
        <!-- Table content -->

        <section class="content kks-subscription-box-sections">
            <div class="box pt-1">
                <div class="row box-body">
                    <div class="col-xs-12">
                        @include('frontside.layouts.partials.message')
                            <div class="box-body">
                                <div class="table-responsive">
                                {{-- <table id="vouchers" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable" style="width:100%"> --}}
                                    <table id="vouchers" class="table table-striped table-bordered  nowrap no-footer dataTable" style="width:100%">
                                        <thead>
                                            <tr role="row">
                                                <th class="no-sort">{{__('Order ID')}}</th>
                                                <th>{{ __('Actions') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Active Status') }}</th>
                                                <th>{{ __('Product') }}</th>
                                                <th>{{ __('Quantity') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Used') }}</th>
                                                <th>{{ __('Remaining') }}</th>
                                                <th>{{ __('Discount (%)') }}</th>
                                                <th>{{ __('Unit Price') }}</th>
                                                <th>{{ __('Taxes') }}</th>
                                                <th>{{ __('Total Payable Amount') }}</th>
                                                <th>{{ __('Pending Payment') }}</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        <!-- /.box -->
                    </div>
                </div>
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
    <script src="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend/bower_components/moment/moment.js') }}"></script>
    <script src="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>

        tableajaxurl = '{{ route("distributor.voucher.orders") }}';

        var table = $("#vouchers").DataTable({
            "order": [],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                },

              'copy', 'csv', 'excel'
            ],
            serverSide: true,
            scrollX: true,
            "ajax": {
                "url": tableajaxurl,
                "beforeSend": function() {
                    if (table && table.hasOwnProperty('settings')) {
                        table.settings()[0].jqXHR.abort();
                    }
                }
            },
            columns: [
                {
                    data:'order_id',
                    name:'order_id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action'
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
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'date',
                    name: 'date'
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
                    data: 'discount',
                    name: 'discount'
                },
                {
                    data: 'unit_price',
                    name: 'unit_price'
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
                },
            ],
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ]
        });

    </script>
@endsection
