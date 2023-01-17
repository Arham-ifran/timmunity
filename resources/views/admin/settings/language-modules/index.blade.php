@extends('admin.layouts.app')
@section('title',  __('Language Modules'))
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
               {{ __('Settings') }} / {{ __('Language Modules') }}
            </h2>
         </div>
      </div>
   </section>
   <!-- Table content -->
   <section class="content">
        <div class="box pt-1">
            @can('Language Modules Listing')
            <div class="row box-body">
                <table id="languages-datatable" class="table table-bordered table-striped">
                   <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Columns') }}</th>
                        </tr>
                   </thead>
                   <tbody>
                   </tbody>
                </table>
            </div>
            @endcan
        <!-- /.box-body -->
        </div>
   </section>
   <!-- /.content -->
</div>
@endsection
@section('scripts')
@can('Language Modules Listing')
<script type="text/javascript">
    $(document).ready(function() {
    var count = 0;
    $('#languages-datatable thead tr').clone(true).appendTo( '#languages-datatable thead' );
      $('#languages-datatable thead tr:eq(1) th').each( function (i) {
        if(count < 2) {
            var title = $(this).text();
            if(count >= 0) {
            $(this).html( '<input class ="form-control" type="text" placeholder="{{__('Search')}} '+{{__('title') }}+'" />' );
            }
            $('input', this ).on( 'keyup change', function () {
                if ( table.column(i).search() !== this.value ) {
                    table
                        .column(i)
                        .search( this.value )
                        .draw();
                }
            });
            count++;
          }
        });

        var table = $('#languages-datatable').DataTable({
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            orderCellsTop: true,
            lengthChange: false,
            responsive: true,
            serverSide: true,
            stateSave: true,
            ajax: {
              url: "{{ route('admin.language-modules.index') }}"
            },
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
                    data: 'columns',
                    name: 'columns'
                },
            ]
        });
        $('#lang_filter').change(function(){
            $('#languages-datatable').DataTable().draw();
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
@endcan
@endsection
