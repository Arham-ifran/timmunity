@extends('admin.layouts.app')
@section('title',  __('Email Templates'))
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
               {{ __('Settings') }} / {{ __('Email Templates') }}
            </h2>
         </div>
      </div>
      <div class="row">
         <div class="box-header">
            <div class="row">
               <div class="col-md-4">
                @can('Add New Templates')
                <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.email-templates.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                @endcan
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- Table content -->
   <section class="content">
      <div class="box pt-1">
         <div class="row box-body">
            <table id="email-templates-datatable" class="table table-bordered table-striped">
               <thead>
                  <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Subject') }}</th>
                    @can('Edit Email Templates')
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
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#email-templates-datatable').DataTable({
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            orderCellsTop: true,
            lengthChange: false,
            responsive: true,
            serverSide: true,
            stateSave: true,
            ajax: "{{ route('admin.email-templates.index') }}",
            fnDrawCallback: function( oSettings ) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [
              {
                  data: 'name',
                  name: 'name'
              },
              {
                  data: 'subject',
                  name: 'subject'
              },
              @can('Edit Email Templates')
              {
                  data: 'action',
                  name: 'action',
                  orderable:false,
              },
              @endcan
            ]
        });
    });
</script>
@endsection
