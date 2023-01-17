@extends('admin.layouts.app')
@section('title', __('Channel Pilot Analytics'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<style>
    td.details-control {
        background: url("{{ asset('backend/dist/img/details_open.png') }}") no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url("{{ asset('backend/dist/img/details_close.png') }}") no-repeat center center;
    }
    tr.shown td {
        color: white;
    }

    tr.shown {
        background: #009a71 !important;
        /* color: white !important; */
    }

</style>
@endsection
@section('content')

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header" style="padding-left: 15px;">
                    <div class="row">
                        <div class="col-md-6">
                            <h2>Channel Pilot Analytics</h2>
                        </div>
                        <div class="col-md-3"></div>

                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body row">
                    <div class="col-md-3">
                        <label for="date" class="control-label">Select Date:</label>
                        <input type="date" name="date" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="channel" class="control-label">Channel</label>
                        <input type="text" name="channel" class="form-control" value="">
                    </div>
                    <div class="col-md-3">
                        <label for="article" class="control-label">Article</label>
                        <input type="text" name="article" class="form-control" value="">
                    </div>
                    {{-- <div class="col-md-4">
                        <label for="" class="control-label"></label>
                        <button id="search_date_submit" onclick="newSearch()" class="btn btn-primary mt-3">Search</button>
                    </div> --}}
                </div>
            </div>
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body row">
                    <table id="analyticsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr role="row">
                                <th></th>
                                <th>{{ __('Channel') }}</th>
                                <th>{{ __('Sku') }}</th>
                                <th>{{ __('Article') }}</th>
                                <th>{{ __('Category') }}</th>
                                {{-- <th>{{ __('Metrics') }}</th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
<script>
    var  tableajaxurl ="{{ route('admin.channel-pilot-sales-analytics') }}";
    function format ( d ) {
            return '<table  class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable" style="width:100%">'+
                '<tr>'+
                    '<th>Clicks</th>'+
                    '<td>'+d.clicks+'</td>'+
                    '<th>Articles Sold</th>'+
                    '<td>'+d.articlesSold+'</td>'+
                '</tr>'+
                '<tr>'+
                    '<th>Orders</th>'+
                    '<td>'+d.orders+'</td>'+
                    '<th>Revenue</th>'+
                    '<td>'+d.revenue+'</td>'+
                '</tr>'+
                '<tr>'+
                    '<th>Margin</th>'+
                    '<td>'+d.margin+'</td>'+
                    '<th>Costs</th>'+
                    '<td>'+d.costs+'</td>'+
                '</tr>'+
            '</table>';
        }
    var table = $('#analyticsTable').DataTable({
            "order": [],
        "dom": '<"top"fi>rt<"bottom"lp><"clear">',
        orderCellsTop: true,
        lengthChange: false,
        responsive: true,
        serverSide: true,
        "ajax": {
            "url": tableajaxurl,
            "data": function(d){
                d.date = $('[name=date]').val();
                d.channel = $('[name=channel]').val();
                d.article = $('[name=article]').val();
            }
        },
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            {
                data: 'channel',
                name: 'channel'
            },
            {
                data: 'sku',
                name: 'sku'
            },
            {
                data: 'article',
                name: 'article'
            },
            {
                data: 'category',
                name: 'category'
            },
            // {
            //     data: 'metrics',
            //     name: 'metrics'
            // }
        ]
    });
    $('#analyticsTable tbody').on('click', 'td', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
    table.columns.adjust().draw();
    $('body').on('input','[name=date],[name=channel],[name=article]',function(){
        if (table && table.hasOwnProperty('settings')) {
            table.settings()[0].jqXHR.abort();
        }
        table.ajax.reload();
    });
</script>
@endsection
