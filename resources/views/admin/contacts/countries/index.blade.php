@extends('admin.layouts.app')
@section('title',  __('Countries'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
<div class="content-wrapper">
    <section class="content-header top-header">
       <div class="row">
          <div class="col-md-6">
             <h2>
                {{ __('Contact Countries') }}
             </h2>
          </div>
       </div>
       <div class="row">
          <div class="box-header">
             <div class="row">
                @canany('Add New Contact Countries')
                <div class="col-md-4">
                    <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.contacts-countries.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                 </div>
                 @endcanany
             </div>
          </div>
       </div>
    </section>
    <!-- Table content -->
    <section class="content">
       <div class="box pt-1">
          <div class="row box-body">
             <table id="countries-datatable" class="table table-bordered table-striped">
                <thead>
                   <tr>
                     <th>{{ __('Name') }}</th>
                     <th>{{ __('Country Code') }}</th>
                     <th>{{ __('Country Calling Code') }}</th>
                     <th>{{ __('VAT')  }} %</th>
                     {{-- <th>{{ __('Currency') }}</th> --}}
                     @canany(['Edit Contact Countries','Delete Contact Countries'])
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
        $(document).ready(function() {

            var count = 0;
            $('#countries-datatable thead tr').clone(true).appendTo( '#countries-datatable thead' );
            $('#countries-datatable thead tr:eq(1) th').each( function (i) {
                if(count < 5) {
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
                else if(count == 5) {
                    $(this).html('');
                }
            } );
            var table = $('#countries-datatable').DataTable({
                lengthChange: false,
                responsive: true,
                serverSide: true,
                orderCellsTop:true,
                stateSave: true,
            "order": [[ 1, "desc" ]],
                ajax: "{{ route('admin.contacts-countries.index') }}",
                fnDrawCallback: function(oSettings) {
                    $('[data-toggle="popover"]').popover();
                    $('[data-toggle="tooltip"]').tooltip();
                },
                columns: [

                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'country_code',
                        name: 'country_code',

                    },
                    {
                        data: 'country_calling_code',
                        name: 'country_calling_code',

                    },
                    {
                        data: 'vat_in_percentage',
                        name: 'vat_in_percentage',

                    },
                    // {
                    //     data: 'currency',
                    //     name: 'currency',

                    // },
                    @canany(['Edit Contact Countries','Delete Contact Countries'])
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



        //

    </script>
@endsection
