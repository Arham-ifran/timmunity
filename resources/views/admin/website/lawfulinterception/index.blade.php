@extends('admin.layouts.app')
@section('title', __('Lawful Interception'))
@section('styles')
    <link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
    <link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">

    <style>
        .loading-text{
            width: 100%;
            height: 67px;
            position: absolute;
            left: 53%;
            top: 65%;
            transform: translate(-50%,-50%);
            text-align: center;
        }
    </style>
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
            <div class="loading-text"><h3>Your Data is being compressed. Please wait...</h3></div>
        </div>
        <section class="content-header top-header">
            <div class="row">
                <div class="col-md-4">
                    <h2>{{ __('Lawful Interception') }} </h2>
                </div>
                <div class="col-md-4">
                </div>
                 <div class="col-md-4 pull-right">
                  <div class="form-group">
                      <label><strong>{{ __('Filter By Type') }}</strong></label>
                        <select name="" id="contact_type_filter" class="form-control">
                            <option value="0">{{ __('All') }}</option>
                            <option value="2">{{ __('Customers') }}</option>
                            <option value="3">{{ __('Resellers') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="box product-box" id="contats-append">
                <div class="box-body">
                    <div class="box-body table-responsive ">
                        <table id="resellers"
                            class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                            <thead>
                                <tr role="row">
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    @canany(['Reseller Details PDF','Reseller Orders PDF','Reseller Vouchers PDF','Reseller Vouchers Payment PDF','Download Reseller All Data','Customer Details PDF','Customer Orders PDF','Customer Order Invoices PDF','Download Customer All Data'])
                                    <th>{{ __('Actions') }}</th>
                                    @endcanany
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
    <div class="modal fade" id="expotDataModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Add a Section') }}
                    </h3>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"
                                aria-hidden="true"></i></span>
                    </button>
                </div>
                <div class="modal-body ">
                    <!-- Form Start Here  -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="clearfix mt-2">
                                <div class="col-md-12">
                                    <div class="form-group col-md-12">
                                        <label>{{ __('Export Data') }}</label>
                                    </div>
                                    <div class="col-md-12">
                                        <a href="" class="btn btn-success" id="exportBtn">{{ __('Download Data') }}</a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ __('Close') }}</button>
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
            lengthChange: false,
            responsive: true,
            orderCellsTop: true,
            serverSide: true,
            scrollCollapse: true,
            fixedColumns: true,
            "aaSorting": [],
             ajax: {
              url: "{{ route('admin.website.lawfulinterception') }}",
              type: 'GET',
              data: function (d) {

                d.contact_type = $("#contact_type_filter").val();

              }
             },
            columns: [

                {
                    data: 'name',
                    name: 'name'
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
                    data: 'type',
                    name: 'type'
                },
                @canany(['Reseller Details PDF','Reseller Orders PDF','Reseller Vouchers PDF','Reseller Vouchers Payment PDF','Download Reseller All Data','Customer Details PDF','Customer Orders PDF','Customer Order Invoices PDF','Download Customer All Data'])
                {
                    data: 'actions',
                    name: 'actions'
                }
                @endcanany
            ],
            "columnDefs": [
                { "orderable": false, "targets": 4 }
            ]
        });
        $('#contact_type_filter').change(function(){
          $('#resellers').DataTable().draw();
        });
    });
        $('body').on('click', '.export-all', function(){
            id = $(this).data('id');
            url = '{{ route("admin.website.lawfulinterception.export.all.zip", [":id",1]) }}';
            url = url.replace(':id', id);
            $('#ajax_loader').show()
            $.ajax({
                url: url,
                success: function (data) {
                    $('#expotDataModal').modal('show');
                    $('#exportBtn').attr('href',data);
                    $('#exportBtn').attr('target','_blank');

                },
                complete:function(data){
                    $('#ajax_loader').hide();
                }
            });
        });
        // Ajax Call Export All Customer Details Zip
        $('body').on('click', '.export-all-customer', function(){
            id = $(this).data('id');
            url = '{{ route("admin.website.lawfulinterception.customer.export.all.zip", [":id",1]) }}';
            url = url.replace(':id', id);
            $('#ajax_loader').show()
            $.ajax({
                url: url,
                success: function (data) {
                    $('#expotDataModal').modal('show');
                    $('#exportBtn').attr('href',data);
                    $('#exportBtn').attr('target','_blank');

                },
                complete:function(data){
                    $('#ajax_loader').hide();
                }
            });
        });
    </script>
@endsection
