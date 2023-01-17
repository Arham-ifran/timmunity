@extends('distributor.layouts.app')
@section('title', __('Vouchers'))
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
                        {{ __('Vouchers') }}
                    </h2>
                </div>

            </div>
        </section>
        <!-- Table content -->
        <section class="content">
          <div class="box pt-1">
             <div class="row box-body">
                <table id="vouchers" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
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
             <!-- /.box-body -->
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
<script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>
        tableajaxurl = '{{ $ajax_url }}';
        var table = $("#vouchers").DataTable({
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            lengthChange: false,
            responsive: true,
            orderCellsTop: true,
            serverSide: true,
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
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'statuss',
                    name: 'statuss'
                },
                {
                    data: 'customer',
                    name: 'customer'
                },
                {
                    data: 'redeemed_time',
                    name: 'redeemed_time'
                },

            ]
        });
    </script>
@endsection
