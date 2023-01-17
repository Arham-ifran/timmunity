@extends('admin.layouts.app')
@section('title', __('Abandoned Carts'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<style>
    input#date_filter,
    select[name=customer_id],
    select[name=salesperson_id],
    select[name=currency],
    select[name=salesteam_id]{
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
                        {{ __('Abandoned Carts') }}
                    </h2>
                </div>
            </div>

        </section>
        <section class="pt-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" id="date_filter" class="form-control" name="date_range" autocomplete="off">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control" name="customer_name_email" placeholder="{{ __('Enter Customer Name/Email') }}" autocomplete="off">
                                {{-- <select name="customer_id" id="customer_id"class="form-control">
                                    <option value="">---{{ __('Select a customer') }}---</option>
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->id }}">{{ $cust->name }}
                                        </option>
                                    @endforeach
                                </select> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Table content -->
        <section class="content kks-subscription-box-sections">
            <div class="row">
                <div class="col-xs-12">
                    @include('frontside.layouts.partials.message')
                    <div class="box">
                        <div class="box-body table-responsive ">
                            <table id="abandoned_carts" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                                <thead>
                                    <tr role="row">
                                        <th>{{ __('User') }}</th>
                                        <th>{{ __('Cart Items') }}</th>
                                        <th>{{ __('Currency') }}</th>
                                        <th>{{ __('Abandoned At') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
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
        var ajax_data = [];
        ajax_data['start_date'] = '';
        ajax_data['end_date'] = '';
        ajax_data['customer_id'] = '';

        var table = $("#abandoned_carts").DataTable({
            lengthChange: false,
            responsive: true,
            orderCellsTop: true,
            serverSide: true,
            scrollCollapse: true,
            "aaSorting": [],
            fixedColumns: true,
            // ajax: '{{ route("admin.voucher.orders") }}',
            "ajax": {

                "url": "{{ route('admin.website.abandoned.carts') }}",
                "data": function(d){
                    d.start_date = ajax_data['start_date'];
                    d.end_date = ajax_data['end_date'];
                    d.customer_name_email = ajax_data['customer_name_email'];
                },
                "beforeSend": function() {
                    if (table && table.hasOwnProperty('settings')) {
                        table.settings()[0].jqXHR.abort();
                    }
                }
            },
            columns: [

                {
                    data: 'user',
                    name: 'user'
                },
                {
                    data: 'cart_items',
                    name: 'cart_items'
                },
                {
                    data: 'currency',
                    name: 'currency'
                },
                {
                    data: 'abandoned_at',
                    name: 'abandoned_at'
                }
            ]
        });
        /***  Filters JQuery Start ***/
        /***  Filters JQuery Start ***/
        $('#date_filter').daterangepicker({
            "showDropdowns": true,
            "autoApply": true,
            ranges: {
                '{{ __("Today") }}': [moment(), moment()],
                '{{ __("Yesterday") }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '{{ __("Last 7 Days") }}': [moment().subtract(6, 'days'), moment()],
                '{{ __("Last 30 Days") }}': [moment().subtract(29, 'days'), moment()],
                '{{ __("This Month") }}': [moment().startOf('month'), moment().endOf('month')],
                '{{ __("Last Month") }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "alwaysShowCalendars": true,
            locale: {
                cancelLabel: '{{ __("Clear") }}',
                "customRangeLabel": "{{ __('Custom') }}",
            }
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
            ajax_data['start_date'] = start.format('YYYY-MM-DD');
            ajax_data['end_date'] = end.format('YYYY-MM-DD');
            refreshDataTable();
        });
        $("#date_filter").val('Select Date Range');

        $("body").on('input','[name=customer_name_email]',function(){
            ajax_data['customer_name_email'] = $(this).val();

            refreshDataTable();

        });

        function refreshDataTable(){
            table.ajax.reload()
        }

    </script>
@endsection
