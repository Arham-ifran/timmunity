@extends('admin.layouts.app')
@section('title', __('Channel Pilot Marketplace Orders'))
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
                    <div class="col-md-12">
                        <h2>
                            {{  __('Channel Pilot Market Place Orders') }}
                        </h2>
                    </div>
                    <div class="col-md-12">
                        <a class="btn btn-primary" href="{{ route('admin.channel-pilot.marketplace.orders.get') }}"> Import Orders </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="pt-2">
        <div class="row">
            <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" class="form-control" id="date_filter" name="date_filter" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text"  name="orderIdExternal" placeholder="{{ __('Enter orderIdExternal') }}" autocomplete="off" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text"  name="customer_search" placeholder="{{ __('Enter Customer Query') }}" autocomplete="off" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text"  name="product_search" placeholder="{{ __('Enter Product Query') }}" autocomplete="off" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text"  name="source" placeholder="{{ __('Enter Store') }}" autocomplete="off" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
    <section class="content">
      <div class="box pt-1">
            <div class="row box-body mt-1">
                <div class="table-responsive">
                    <table style="width:100%" id="marketplace_orders" class="table table-bordered table-striped no-wrap">
                        <thead>
                            <tr>
                                <th>{{ __('Order ID') }}</th>
                                <th>{{ __('Store') }}</th>
                                <th>{{ __('Customer Details') }}</th>
                                <th>{{ __('NET Prices (Without Tax)') }}</th>
                                <th>{{ __('Gross Prices') }}</th>
                                <th>{{ __('VAT (%)') }}</th>
                                <th>{{ __('Order Time') }}</th>
                                <th>{{ __('Actions') }}</th>
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
    <div class="modal fade" id="import-license-modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="col-sm-4" id="exampleModalLongTitle">{{ __('Order Details') }}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
                    </button>
                </div>
                <div class="modal-body ">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
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
<script src="{{ asset('backend/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script>
    ajax_data = [];
    ajax_data['orderIdExternal'] = '';
    ajax_data['customer_search'] = '';
    ajax_data['product_search'] = '';
    ajax_data['start_date'] = '';
    ajax_data['end_date'] = '';
    ajax_data['source'] = '';

    var table = $('#marketplace_orders').DataTable({
        serverSide: true,
        ajax: {
            "url": '{{ route("admin.channel-pilot.marketplace.orders") }}',
            "data": function(d){
                d.orderIdExternal = ajax_data['orderIdExternal'];
                d.customer_search = ajax_data['customer_search'];
                d.product_search = ajax_data['product_search'];
                d.start_date = ajax_data['start_date'];
                d.end_date = ajax_data['end_date'];
                d.source = ajax_data['source'];
            }
        },
        scrollX: true,
        searching: false,
        columns: [
            {
                data: 'orderIdExternal',
                name: 'orderIdExternal'
            },
            {
                data: 'source',
                name: 'source'
            },
            {
                data: 'customer',
                name: 'customer'
            },
            {
                data: 'net',
                name: 'net'
            },
            {
                data: 'gross',
                name: 'gross'
            },
            {
                data: 'vat_percentage',
                name: 'vat_percentage'
            },
            {
                data: 'order_time',
                name: 'order_time'
            },
            {
                data: 'actions',
                name: 'actions'
            }
        ]
    });
    $('body').on('click','.detail_btn',function(){
        order_id = $(this).data('id');
        url = '{{ route("admin.channel-pilot.marketplace.order.detail", [":id",1]) }}';
        url = url.replace(':id', order_id);
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                $('#import-license-modal .modal-body').html(data);
            },
            complete:function(data){
                // Hide loader container
            }
        });
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

    $('body').on('input', '[name=orderIdExternal]',function(){
        ajax_data['orderIdExternal'] = $(this).val();
        table.ajax.reload();
    });
    $('body').on('input', '[name=customer_search]',function(){
        ajax_data['customer_search'] = $(this).val();
        table.ajax.reload();
    });
    $('body').on('input', '[name=product_search]',function(){
        ajax_data['product_search'] = $(this).val();
        table.ajax.reload();
    });
    $('body').on('input', '[name=source]',function(){
        ajax_data['source'] = $(this).val();
        table.ajax.reload();
    });
</script>
@endsection
