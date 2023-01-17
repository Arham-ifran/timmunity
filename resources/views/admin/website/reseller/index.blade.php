@extends('admin.layouts.app')
@section('title', __('Resellers'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />

@endsection
    <!-- Top Header Section -->
@section('content')
    <div class="content-wrapper">
        <div class="loader-parent" id="ajax_loader">
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
                    <h2>{{ __('Resellers') }} </h2>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="box product-box" id="contats-append">

                <div class="box-body">
                    <div class="box-body table-responsive ">
                        <table id="resellers" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                            <thead>
                                <tr role="row">
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Package') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
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
    $(document).ready(function() {
      // Setup - add a text input to each footer cell
       var count = 0;
        $('#resellers thead tr').clone(true).appendTo( '#resellers thead' );
          $('#resellers thead tr:eq(1) th').each( function (i) {
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
          });
        var table = $("#resellers").DataTable({
            "order": [],
            lengthChange: false,
            responsive: true,
            orderCellsTop: true,
            serverSide: true,
            scrollCollapse: true,
            fixedColumns: true,
            stateSave: true,
            "aaSorting": [],
            // ajax: '{{ route("admin.voucher.orders") }}',
            "ajax": {
                "url": "{{ route('admin.website.resellers') }}",
                "beforeSend": function() {
                    if (table && table.hasOwnProperty('settings')) {
                        table.settings()[0].jqXHR.abort();
                    }
                }
            },
            columns: [

                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'package_name',
                    name: 'package_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'actions',
                    name: 'actions'
                }
            ],
            "columnDefs": [
                { "orderable": false, "targets": 3 }
            ]
        });
    });

    </script>
@endsection
