@extends('admin.layouts.app')
@section('title',  __('Bank Accounts'))
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
                {{ __('Contact Bank Accounts') }}
             </h2>
          </div>
       </div>
       <div class="row">
          <div class="box-header">
             <div class="row">
                @canany('Add New Bank Accounts')
                <div class="col-md-4">
                   <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.contacts-bank-accounts.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
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
             <table id="bank-accounts-datatable" class="table table-bordered table-striped">
                <thead>
                   <tr>
                     <th>{{ __('Account Number') }}</th>
                     <th>{{ __('Account Holder') }}</th>
                     <th>{{ __('Bank Name') }}</th>
                     @canany(['Edit Contact Bank Accounts','Delete Contact Bank Accounts'])
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
        $('#bank-accounts-datatable thead tr').clone(true).appendTo( '#bank-accounts-datatable thead' );
          $('#bank-accounts-datatable thead tr:eq(1) th').each( function (i) {
            if(count < 3) {
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
              else if(count == 3) {
                $(this).html('');
              }
          } );
          var table = $('#bank-accounts-datatable').DataTable({
                lengthChange: false,
                responsive: true,
                serverSide: true,
                orderCellsTop: true,
                ajax: "{{ route('admin.contacts-bank-accounts.index') }}",
                fnDrawCallback: function(oSettings) {
                    $('[data-toggle="popover"]').popover();
                    $('[data-toggle="tooltip"]').tooltip();
                },
                columns: [

                        {
                            data: 'account_number',
                            name: 'account_number'
                        },
                        {
                            data: 'account_holder_name',
                            name: 'account_holder_name',

                        },
                        {
                            data: 'contact_banks',
                            name: 'contact_banks',

                        },
                        @canany(['Edit Contact Bank Accounts','Delete Contact Bank Accounts'])
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
