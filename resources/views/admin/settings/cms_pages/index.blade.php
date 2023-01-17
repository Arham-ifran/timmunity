@extends('admin.layouts.app')
@section('title',  __('CMS Pages'))
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
               {{ __('Settings') }} / {{ __('CMS Pages') }}
            </h2>
         </div>
      </div>
      <div class="row">
         <div class="box-header">
            <div class="row">
               <div class="col-md-4">
                @can('Add New CMS Page')
                <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.cms.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
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
            <table id="cms-datatable" class="table table-bordered table-striped">
               <thead>
                  <tr>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Meta Title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Status') }}</th>
                    @canany(['Edit CMS Page','Delete CMS Page','Duplicate CMS Page'])
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
       var count = 0;
        $('#cms-datatable thead tr').clone(true).appendTo( '#cms-datatable thead' );
          $('#cms-datatable thead tr:eq(1) th').each( function (i) {
            if(count < 4) {
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
              else if(count == 4) {
                $(this).html('');
              }
          } );
        var table = $('#cms-datatable').DataTable({
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            orderCellsTop: true,
            lengthChange: false,
            responsive: true,
            serverSide: true,
            ajax: "{{ route('admin.cms.index') }}",
            fnDrawCallback: function( oSettings ) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [
            {
                data: 'title',
                name: 'title'
            },
            {
                data: 'meta_title',
                name: 'meta_title'
            },
            {
                data: 'description',
                name: 'description',
            },
            {
                data: 'is_active',
                name: 'is_active',
            },
            @canany(['Edit CMS Page','Delete CMS Page','Duplicate CMS Page'])
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
@endsection
