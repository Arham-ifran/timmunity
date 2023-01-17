@extends('admin.layouts.app')
@section('title', 'Voucher Payments')
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<style>
    select[name=status]{
        border: none;
    }
</style>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header top-header">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                        {{ __('Voucher Payments') }}
                    </h2>
                </div>

            </div>

        </section>
        <!-- Table content -->
         <section class="content">
            @include('frontside.layouts.partials.message')
          <div class="box pt-1">
             <div class="row box-body">
                <table id="vouchers" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                    <thead>
                        <tr role="row">
                            @canany(['Make Voucher Payment','View Payment Voucher Invoice'])
                            <th>{{ __('Action') }}</th>
                            @endcanany
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
             <!-- /.box-body -->
          </div>
       </section>
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
<script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>
        // var ajax_data = [];
        // ajax_data['status'] = '';
        // ajax_data['code'] = '';
        // tableajaxurl = '{{ $ajax_url }}';
        var table = $("#vouchers").DataTable({
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            lengthChange: false,
            responsive: true,
            orderCellsTop: true,
            serverSide: true,
            ajax: '{{ $ajax_url }}',
            columns: [
            @canany(['Make Voucher Payment','View Payment Voucher Invoice'])
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            @endcanany
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
        // // Selecting the Status Start
        // $('body').on('change', 'select[name=status]', function(){
        //     ajax_data['status'] = $(this).val();
        //     refreshDataTable();
        // });
        // $('body').on('input', 'input[name=code]', function(){
        //     ajax_data['code'] = $(this).val();
        //     refreshDataTable();
        // });
        // // Selecting the Status End
        // function refreshDataTable(){
        //     table.ajax.reload();
        // }
    </script>
@endsection
