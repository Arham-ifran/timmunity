@extends('admin.layouts.app')
@section('title',  __('Language Translations'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header">
      <div class="row">
         <div class="col-md-6">
            <h2>
               {{ __('Settings') }} / {{ __('Language Translations') }}
            </h2>
         </div>
      </div>
      <div class="row">
         <div class="box-header">
            <div class="row">
                @canany(['Language Partial Translate','Language Bulk Translate'])
               <div class="col-md-4">
                    <label style="margin-left: 10px;"><strong>{{ __('Actions') }}</strong></label>
                    <br>
                  @can('Language Partial Translate')
                  <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{url('admin/settings/language-translations/partial-translate')}}">{{ __('Partial Translate') }}</a>
                  @endcan
                  @can('Language Bulk Translate')
                  <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{url('admin/settings/language-translations/create')}}">{{ __('Bulk Translate') }}</a>
                  @endcan
                </div>
                @endcanany
                @can('Filter Record Language Modules')
               <div class="col-md-4 pull-right">
                    <div class="form-group">
                        <label><strong>{{ __('Language Modules') }}</strong></label>
                        <select class="form-control" name="language_module_id" id="language_module_id">
                            <option value="">{{ __('Select Module') }}</option>
                            @foreach ($language_modules as $language_module)
                                <option value="{{$language_module->id}}">{{$language_module->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endcan
                @can('Filter Record Language Translate')
                <div class="col-md-4 pull-right">
                    <div class="form-group">
                        <label><strong>{{ __('Languages') }}</strong></label>
                        <select class="form-control" name="language_id" id="language_id">
                            <option value="">{{ __('Select Language') }}</option>
                            @foreach($languages as $lang)
                                <option value="{{$lang->id}}">{{$lang->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endcan
            </div>
            <div class="row">
                <div class="col-md-2 mt-3">
                 <button type="submit" class="btn btn-primary" data-url="{{ route('admin.bulk.delete.languages') }}" id="delete_languages" style="display:none">{{ __('Delete Selected Records') }}</button>
                </div>
                <div class="col-md-2 mt-3 ">
                    <span class="badge badge-success" id="totalLangCount"></span>
                </div>
            </div>
         </div>
      </div>
   </section>
   <!-- Table content -->
   <section class="content">
        <div class="box pt-1">
            <div class="row box-body">
                <table id="language-translations-datatable" class="table table-bordered table-striped">
                   <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Id') }}</th>
                            <th>{{ __('Item Id') }}</th>
                            <th>{{ __('Language Module') }}</th>
                            <th>{{ __('Language Name') }}</th>
                            <th>{{ __('Column') }}</th>
                            <th>{{ __('Translation') }}</th>
                            @can('Edit Language Translations')
                            <th>{{ __('Actions') }}</th>
                            @endcan
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
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#language-translations-datatable').DataTable({
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            orderCellsTop: true,
            lengthChange: false,
            responsive: true,
            serverSide: true,
            stateSave: true,
            ajax: {
              url: "{{ route('admin.language-translations.index') }}",
              type: 'GET',
              data: function (d) {
                d.language_module_id = $("#language_module_id").val();
                d.language_id = $("#language_id").val();
              }
            },
            fnDrawCallback: function( oSettings ) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'id', name: 'id'},
                {data: 'item_id', name: 'item_id'},
                {data: 'language_module', name: 'language_name'},
                {data: 'language_name', name: 'language_module'},
                {data: 'column_name', name: 'column_name'},
                {data: 'item_value', name: 'item_value'},
                @can('Edit Language Translations')
                {data: 'action', name: 'action', orderable: false, searchable: false},
                @endcan
            ]
        });
        $('#language_module_id').change(function(){
            $('#language-translations-datatable').DataTable().draw();
        });
        $('#language_id').change(function(){
            $('#language-translations-datatable').DataTable().draw();
        });
    });
</script>
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
@endsection
