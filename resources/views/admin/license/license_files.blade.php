@extends('admin.layouts.app')
@section('title', __('License Files'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
<style>
    td.details-control {
        background: url("{{ asset('backend/dist/img/details_open.png') }}") no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url("{{ asset('backend/dist/img/details_close.png') }}") no-repeat center center;
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
        <div class="loader-parent" id="ajax_loader" style="z-index: 100000;">
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
                        {{ __('License Files') }}
                    </h2>
                </div>
                @can('Import License Keys')
                <div class="col-md-6 text-right" >
                    <h2>
                        <a type="button" class="btn skin-green-light-btn" data-toggle="modal" data-target="#import-license-modal">{{ __('Import License Keys') }}</a>
                    </h2>
                </div>
                @endcan
            </div>
        </section>
        <!-- Table content -->
        <section class="content">
          <div class="box pt-1">
             @include('frontside.layouts.partials.message')
             <div class="row box-body ">
                 <div class="table-responsive">
                    <table id="license" class="table table-striped table-bordered  nowrap no-footer " style="width:100%">
                        <thead>
                            <tr role="row">
                                <th>{{ __('File') }}</th>
                                <th class="notexport">{{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
             </div>
             <!-- /.box-body -->
          </div>
       </section>
        <!-- /.content -->
    </div>
    <div class="modal fade" id="import-license-modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Import License Keys') }}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
                    </button>
                </div>
                <div class="modal-body ">
                    <div class="row">
                        <form id="importform" action="{{ route('admin.license.import') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <h2 class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="file" accept=".csv, text/csv" multiple="" name="file[]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <input type="submit" class="btn btn-success" value="{{ __('Import License') }}"/>
                                    <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                   href="{{ asset('backend\download-license-sample\TIMmunity License Sample.csv') }}" download>{{ __('Download Sample') }}</a>
                                </div>
                            </h2>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
     <div class="modal fade" id="view_file_modal" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('File Contents') }}</h3>
                </div>
                <div class="modal-body ">
                    <div class="row">
                        <div class="col-md-12" id="csv-column">
                            <table id="license_keys_table" class="table table-striped table-bordered  nowrap no-footer " style="width:100%">
                                <thead>
                                    <tr role="row">
                                        <th>{{ __('License Key') }}</th>
                                        <th>{{ __('SKU') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Is Duplicate') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
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
<script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>
        $('input[type=submit]').on('click',function(){
            if($('.error').length < 1){
                $("#ajax_loader").show();
            }
        });
        tableajaxurl = '{{ route("admin.license.files") }}';
        var table = $("#license").DataTable({
            dom: 'Bfrtip',
            buttons: [
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
            "order": [],
            lengthChange: false,
            scrollX: true,
            orderCellsTop: true,
            serverSide: true,
            "aaSorting": [],
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
                    data: 'file_name',
                    name: 'file_name'
                },
                {
                    data: 'actions',
                    name: 'actions'
                },
            ]
        });

        initialLoad = true;
        url = "{{ route('admin.license.file.view', ':id') }}",
        url = url.replace(':id',$(this).data('id'));
        var license_keys_table = $("#license_keys_table").DataTable({
            dom: 'Bfrtip',
            buttons: [
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
            "order": [],
            "lengthChange": false,
            "pageLength": 10,
            scrollX: true,
            orderCellsTop: true,
            serverSide: true,
            "aaSorting": [],
            ajax: function (data, callback, settings) {
                if (initialLoad) {
                    initialLoad = false;
                    callback({data: []}); // don't fire ajax, just return empty set
                    return;
                }

                // Ajax will now only fire programmatically, via ajax.reload()
                $.getJSON(url, function (data) {
                    callback(data);
                });
            },
            columns: [
                {
                    data: 'license_key',
                    name: 'license_key'
                },
                {
                    data: 'sku',
                    name: 'sku'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'is_duplicate',
                    name: 'is_duplicate'
                },
            ]
        });

        $('body').on('click','.view-btn',function(){
            url = "{{ route('admin.license.file.view', ':id') }}",
            url = url.replace(':id',$(this).data('id'));
            license_keys_table.ajax.reload();
            // $.ajax({
            //     url: url,
            //     type: 'GET',
            //     success: function (data) {
            //         $('#csv-column').html(data);
            //     },
            //     complete:function(data){
            //         // Hide loader container
            //     }
            // })
        })

    </script>
@endsection
