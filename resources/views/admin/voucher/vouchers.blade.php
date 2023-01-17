@extends('admin.layouts.app')
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
        @can('Voucher Listing Filters')
        <section class="pt-2">
            <div class="row">
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('Select a status') }}</option>
                                    <option value="0">{{ __('Redeemed') }}</option>
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="2">{{ __('Disabled') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control" value="{{$code}}" name="code" placeholder="{{ __('Enter Voucher Code Here') }}" id="">
                            </div>
                        </div>
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </section>
        @endcan
        <!-- Table content -->
        <section class="content">
            @include('frontside.layouts.partials.message')
          <div class="box pt-1">
             <div class="row box-body">
                @canany(['Bulk Disable Vouchers','Bulk Activate Vouchers','Bulk Redeemed Vouchers'])
                <div class="row mb-2">
                    <form method="POST" id="bulActionForm" style="display:none" action="{{ route('admin.change.bulk.voucher.order.vouchers.status') }}">
                        @csrf
                        <input type="hidden" name="ids">
                        <div class="col-md-4">
                            <select class="form-control" name="statuss" id="" required>
                                <option value="">---{{ __('Select Bulk Action ') }}---</option>
                                @can('Bulk Disable Vouchers')
                                <option value="2">{{ __('Disabled') }}</option>
                                @endcan
                                @can('Bulk Activate Vouchers')
                                <option value="1">{{ __('Active') }}</option>
                                @endcan
                                @can('Bulk Redeemed Vouchers')
                                <option value="0">{{ __('Redeemed') }}</option>
                                @endcan
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary">{{ __('Apply Bulk Action') }}</button>
                        </div>
                    </form>
                </div>
                @endcanany
                <table id="vouchers" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                    <thead>
                        <tr role="row">
                            @canany(['Bulk Disable Vouchers','Bulk Activate Vouchers','Bulk Redeemed Vouchers'])
                            <th>{{ __('#') }}</th>
                            @endcanany
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('End Customer') }}</th>
                            <th>{{ __('Redeemed At') }}</th>
                            @canany(['Voucher Disable','Voucher Redeemed','Voucher Approved'])
                            <th>{{ __('Action') }}</th>
                            @endcanany
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
        var ajax_data = [];
        ajax_data['status'] = '';
        ajax_data['code'] = $('input[name=code]').val();
        tableajaxurl = '{{ $ajax_url }}';
        all_selected = 0;
        var table = $("#vouchers").DataTable({
            dom: 'Blfrtip',
            stateSave: true,
            responsive: true,
            orderCellsTop: true,
            lengthMenu: [[10, 50, 100, 250, -1], [10, 50, 100, 250, 'All']],
            serverSide: true,
            buttons: [
                {
                    text: 'Select All',
                    action: function ( e, dt, node, config ) {
                        selected_values = '';
                        if(all_selected == 0){
                            $('#bulActionForm').show();
                            $('.selectedids').prop('checked',true);
                            $('.selectedids:first').data('all-ids').forEach(function(value,index){
                                if(index==0){
                                    selected_values = value;
                                }else{
                                    selected_values += ';'+value;
    
                                }
                            });
                            $('[name=ids]').val(selected_values);
                            all_selected = 1;
                        }else{
                            $('#bulActionForm').hide();
                            $('.selectedids').prop('checked',false);
                            $('[name=ids]').val(selected_values);
                            all_selected = 0;

                        }
                    },
                    className: 'select-all-button-dtjs'
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: ':visible:not(.notexport)',
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':visible:not(.notexport)',
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: ':visible:not(.notexport)',
                    }
                },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: ':visible:not(.notexport)',
                    }
                },
            ],
            "ajax": {
                "url": tableajaxurl,
                "data": function(d){
                    d.status = ajax_data['status'];
                    d.code = ajax_data['code'];
                },
                "beforeSend": function() {
                    if (table && table.hasOwnProperty('settings')) {
                        table.settings()[0].jqXHR.abort();
                    }
                }
            },
            columns: [
             @canany(['Bulk Disable Vouchers','Bulk Activate Vouchers','Bulk Redeemed Vouchers'])
                {
                    data: 'input',
                    name: 'input'
                },
            @endcanany
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
                @canany(['Voucher Disable','Voucher Redeemed','Voucher Approved'])
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
                @endcanany
            ]
        });
        // Selecting the Status Start
        $('body').on('change', 'select[name=status]', function(){
            ajax_data['status'] = $(this).val();
            refreshDataTable();
        });
        $('body').on('input', 'input[name=code]', function(){
            
            ajax_data['code'] = $(this).val();
            
            refreshDataTable();
        });
       
        $('body').on('click','.selectedids',function(){
            selected_values = "";
            if($('.selectedids:checked').length>0){
                $('#bulActionForm').show();
            }else{
                $('#bulActionForm').hide();
                all_selected = 0;
            }
            $('.selectedids:checked').each(function(index,val){
                value = $(val).val();
                if(index==0){
                    selected_values = value;
                }else{
                    selected_values += ';'+value;

                }
            });
            $('[name=ids]').val(selected_values);
        });
        // Selecting the Status End
        function refreshDataTable(){
            table.ajax.reload();
        }
    </script>
@endsection
