@extends('admin.layouts.app')
@section('title',  __('Roles'))
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
               {{ __('Settings') }} / {{ __('Roles') }}
            </h2>
         </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
           <div class="box-header">
              <div class="row">
                  @can('Add New Role')
                    <div class="col-md-4">
                      <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.roles.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    </div>
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
            <table id="roles-datatable" class="table table-bordered table-striped" style="width:100%">
               <thead>
                  <tr>
                     <th>{{ __('Role') }}</th>
                     <th>{{ __('Permissions') }}</th>
                     @canany(['Edit Role','Delete Role'])
                     <th>{{ __('Actions') }}</th>
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
<script type="text/javascript">
   $(document).ready(function() {
    var count = 0;
        $('#roles-datatable thead tr').clone(true).appendTo( '#roles-datatable thead' );
          $('#roles-datatable thead tr:eq(1) th').each( function (i) {
            if(count < 2) {
                var title = $(this).text();
                $(this).html( '<input class ="form-control" type="text" placeholder="{{__('Search')}} '+{{__('title') }}+'" />' );
                $( 'input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
                count++;
              }
              else if(count == 2) {
                $(this).html('');
              }
          } );
       var table = $('#roles-datatable').DataTable({
           "dom": '<"top"fi>rt<"bottom"lp><"clear">',
           orderCellsTop: true,
           lengthChange: false,
           responsive: true,
           serverSide: true,
           order: [[ 0, "desc" ]],
           ajax: "{{ route('admin.roles.index') }}",
           fnDrawCallback: function( oSettings ) {
               $('[data-toggle="popover"]').popover();
               $('[data-toggle="tooltip"]').tooltip();
           },
           columns: [
           {
               data: 'role',
               name: 'role'
           },
           {
               data: 'permissions',
               name: 'permissions'
           },
           @canany(['Edit Role','Delete Role'])
           {
               data: 'action',
               name: 'action',
                orderable: false,
                searchable: false
           },
           @endcanany
           ]
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
