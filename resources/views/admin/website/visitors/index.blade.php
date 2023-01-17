@extends('admin.layouts.app')
@section('title', __('Visitors'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
@endsection
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
                    <h2>
                        {{ __('Visitors') }}
                    </h2>
                </div>
            </div>

        </section>
        <!-- Table content -->
        <section class="content kks-subscription-box-sections">
            <div class="row">
                <div class="col-xs-12">
                    @include('frontside.layouts.partials.message')
                    <div class="box">
                        <div class="box-body table-responsive ">
                            <table id="visitors_table" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                                <thead>
                                    <tr role="row">
                                        @can('Visitors Detail')
                                        <th>{{ __('User')  }}</th>
                                        @endcan
                                        <th>{{ __('Last Visited') }}</th>
                                        {{-- <th>{{ __('Visits') }}</th> --}}
                                        <th>{{ __('Last Page') }}</th>
                                        <th>{{ __('Page Views') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
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
<script src="{{ asset('backend/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>


        var table = $("#visitors_table").DataTable({
            "responsive": true,
            "orderCellsTop": true,
            "scrollCollapse": true,
            stateSave: true,
            "oLanguage": {
                "sProcessing": $("#ajax_loader").show(),                
            },
            "processing":true,
            "serverSide": true,
            "bjQueryUI": true,
            "aaSorting": [],
            "aaSorting": [],
            "ajax": {
                "url": "{{ route('admin.website.visitors') }}",
                "beforeSend": function() {
                    if (table && table.hasOwnProperty('settings')) {
                        table.settings()[0].jqXHR.abort();
                    }
                }
            },
            columns: [
                @can('Visitors Detail')
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                @endcan
                {
                    data: 'last_visit_date',
                    name: 'last_visit_date'
                },
                {
                    data: 'last_visit_page',
                    name: 'last_visit_page'
                },
                {
                    data: 'total_pages_visited',
                    name: 'total_pages_visited'
                }
            ]
        });
        /***  Filters JQuery Start ***/
    </script>
@endsection
