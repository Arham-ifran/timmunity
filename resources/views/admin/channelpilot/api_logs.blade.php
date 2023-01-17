@extends('admin.layouts.app')
@section('title', __('Channel Pilot API Logs'))
@section('styles')
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<!-- Daterange picker -->
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<style>
    span.tagged {
        border: 3px solid;
        border-radius: 30px;
        padding: 0 10px;
    }
    span.tagged.quote {
        border-color: #f5f91a;
        background: #f5f91a85;
    }
    span.tagged.success {
        border-color: #06f50e;
        background: #06f50e66;
    }
    span.tagged.warning {
        border-color: #f9aa1a;
        background: #f9aa1a8c;
    }
    span.tagged.danger {
        border-color: #f91a1a;
        background: #f91a1a7a;
    }
    table#example1 tr:hover {
        background: #009a7129;
        cursor: pointer;
    }
    .ranges li {
        color: #009a71;
    }
    .ranges li:hover,.ranges li.active,.daterangepicker td.active, .daterangepicker td.active:hover {
        background-color: #009a71;
        border-color: #009a71;
    }
</style>

@endsection
@section('content')

<div class="content-wrapper">
    <section class="content-header top-header">
        <div class="row">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-4">
                        <h2>
                            {{  __('Channel Pilot API Logs') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
      <div class="box pt-1">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" id="date_filter" class="form-control" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row box-body mt-1">
                <div class="table-responsive">

                    <table style="width:100%" id="invoice_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>

                                <th>{{ __('End Point') }}</th>
                                <th>{{ __('Request Type') }}</th>
                                <th>{{ __('Response Code') }}</th>
                                <th>{{ __('Parameters') }}</th>
                                <th>{{ __('Header') }}</th>
                                <th>{{ __('Response') }}</th>
                                <th>{{ __('Created At') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
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
<script src="{{ asset('backend/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script>
    ajax_data = [];
    ajax_data['start_date_update'] = null;
    ajax_data['end_date_update'] = null;
    var table = $('#invoice_table').DataTable({
        serverSide: true,
        // responsive: true,
        // ajax: '{{ route("admin.channel-pilot.api.logs") }}',
        ajax: {
            "url": '{{ route("admin.channel-pilot.api.logs") }}',
            "data": function(d){
                d.start_date = ajax_data['start_date'];
                d.end_date = ajax_data['end_date'];
            }
        },
        scrollX: true,
        searching: false,
        columns: [
            {
                data: 'end_point',
                name: 'end_point'
            },
            {
                data: 'request_type',
                name: 'request_type'
            },
            {
                data: 'response_code',
                name: 'response_code'
            },
            {
                data: 'parmas',
                name: 'parmas'
            },
            {
                data: 'header',
                name: 'header'
            },
            {
                data: 'response',
                name: 'response'
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
        ]
    });
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
        // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        ajax_data['start_date'] = start.format('YYYY-MM-DD');
        ajax_data['end_date'] = end.format('YYYY-MM-DD');
        table.ajax.reload();
    });
    $("#date_filter").val("{{ __('Select Date Range') }}");
</script>
@endsection
