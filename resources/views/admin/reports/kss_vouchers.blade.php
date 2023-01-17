@extends('admin.layouts.app')
@section('title',  __('KSS'))
@section('styles')
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<style>
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
</style>
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header">
      <div class="row">
         <div class="col-md-6">
            <h2>
               KSS Subscriptions
            </h2>
         </div>
      </div>
   </section>
   <!-- Table content -->
    <section class="content kss-subscription-box-sections">
        <div class="box pt-1">
            <div class="row">
                <div class="col-md-12">
                    <div class="row filter_row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="control-label">Voucher</label>
                                <input type="text" id="vouchers" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="control-label">Customer Name/Email</label>
                                <input type="text" id="customer" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="control-label">License Type</label>
                                <select name="" id="type" class="form-control">
                                    <option value="">All</option>
                                    <option value="0">Existing</option>
                                    <option value="1">New</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="control-label">Coupon Code</label>
                                <input type="text" id="coupon" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="control-label">Exchanged License / Voucher</label>
                                <select name="" id="is_exchanged" class="form-control">
                                    <option value="">Show All</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="control-label">Exchanged Date Filter</label>
                                <input type="text" id="date_filter" class="form-control" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                    <table id="KSSSubscriptionsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Voucher') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Is Exchanged') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
<script>
    ajax_data = [];
    ajax_data['start_date'] = '';
    ajax_data['end_date'] = '';
    var table = $('#KSSSubscriptionsTable').DataTable({
        "dom": '<"top"fi>rt<"bottom"lp><"clear">',
        orderCellsTop: true,
        lengthChange: false,
        responsive: true,
        serverSide: true,
        "ajax": {
            "url": "{{route('admin.kss.vouchers')}}",
            "data": function(d){
                d.vouchers= $('#vouchers').val();
                d.customer= $('#customer').val();
                d.type= $('#type').val();
                d.coupon= $('#coupon').val();
                d.start_date= ajax_data['start_date'];
                d.end_date= ajax_data['end_date'];
                d.is_exchanged= $('#is_exchanged').val();
            }
        },
        columns: [
            {
                data: 'vouchers',
                name: 'vouchers'
            },
            {
                data: 'customer',
                name: 'customer'
            },
            {
                data: 'is_exchanged',
                name: 'is_exchanged'
            }
        ]
    });
    $('.filter_row input, .filter_row select').on('input',function(){
        table.ajax.reload();
    })
    $('.filter_row input, .filter_row select').on('change',function(){
        table.ajax.reload();
    })
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
        table.ajax.reload();
    });
    $('#date_filter').val('Select Date Range')
</script>
@endsection
