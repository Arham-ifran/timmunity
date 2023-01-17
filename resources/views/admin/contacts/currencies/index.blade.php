@extends('admin.layouts.app')
@section('title',  __('Contact Currency'))
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
                {{ __('Contact Currencies') }}
             </h2>
          </div>
       </div>
       <div class="row">
          <div class="box-header">
             {{-- <div class="row">
                @canany('Add New Contact Currencies')
                <div class="col-md-4">
                    <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.currencies.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                </div>
                @endcanany
             </div> --}}
          </div>
       </div>
    </section>
    <!-- Table content -->
    <section class="content">
       <div class="box pt-1">
          <div class="row box-body">
             <table id="currencies-datatable" class="table table-bordered table-striped">
                <thead>
                   <tr>
                     <th>{{ __('Currency') }}</th>
                     <th>{{ __('Code') }}</th>
                     <th>{{ __('Symbol') }}</th>
                     <th>{{ __('Exchange Rate') }}</th>
                     <th>{{ __('Status') }}</th>
                     {{-- <th>{{ __('Default') }}</th> --}}
                     {{-- <th>{{ __('Country') }}</th> --}}
                     @canany(['Edit Contact Currencies','Delete Contact Currencies'])
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
            // $('#currencies-datatable thead tr').clone(true).appendTo( '#currencies-datatable thead' );
            $('#currencies-datatable thead tr:eq(1) th').each( function (i) {
                if(count < 7) {
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
            var table = $('#currencies-datatable').DataTable({
                lengthChange: false,
                responsive: true,
                serverSide: true,
                orderCellsTop: true,
                stateSave: true,
                ajax: "{{ route('admin.currencies.index') }}",
                fnDrawCallback: function(oSettings) {
                    $('[data-toggle="popover"]').popover();
                    $('[data-toggle="tooltip"]').tooltip();
                },
                columns: [

                    {
                        data: 'currency',
                        name: 'currency'
                    },
                    {
                        data: 'code',
                        name: 'code',

                    },
                    {
                        data: 'symbol',
                        name: 'symbol',

                    },
                    {
                        data: 'exchange_rate',
                        name: 'exchange_rate',

                    },
                    {
                        data: 'is_active',
                        name: 'is_active',

                    },
                    @canany(['Edit Contact Currencies','Delete Contact Currencies'])
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    @endcanany
                ],
            });
            var filteredData = table
                .column( 4 )
                .data()
                .filter( function ( value, index ) {
                    return value == 'Active';
                } );
            });

    </script>
@endsection
