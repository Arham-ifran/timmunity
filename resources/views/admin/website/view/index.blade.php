@extends('admin.layouts.app')
@section('title', __('Views'))
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
                        {{ __('Views') }}
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
                        <div class="box-body ">
                            <div class="table-responsive">
                                <table id="visits_table" style="width:100%" class="table table-striped ">
                                    <thead>
                                        <tr role="row">
                                            <th>{{ __('Visitor') }}</th>
                                            <th>{{ __('Page') }}</th>
                                            <th>{{ __('Url') }}</th>
                                            <th>{{ __('Product') }}</th>
                                            <th>{{ __('Visit Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- <tr>
                                            <th>  Website User 1</th>
                                            <th> Home</th>
                                            <th> http://localhost</th>
                                            <th> </th>
                                            <th> 21-12-2021</th>

                                        </tr> --}}
                                    </tbody>
                                </table>
                            </div>
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

        var table = $("#visits_table").DataTable({

            // "responsive": true,
            "scrollX": true,
            // "orderCellsTop": true,
            // "scrollCollapse": true,
            // "oLanguage": {
            //     "sProcessing": $("#ajax_loader").show(),
            // },
            "processing":true,
            "serverSide": true,
            "searching": false,
            stateSave: true,
            // "bjQueryUI": true,
            // "aaSorting": [],
            // ajax: '{{ route("admin.voucher.orders") }}',
            "ajax": {
                "url": "{{ route('admin.website.views') }}",
                "beforeSend": function() {
                    if (table && table.hasOwnProperty('settings')) {
                        table.settings()[0].jqXHR.abort();
                    }
                }
            },
            columns: [
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'page',
                    name: 'page'
                },
                {
                    data: 'url',
                    name: 'url'
                },
                {
                    data: 'product',
                    name: 'product'
                },
                {
                    data: 'visit_date_time',
                    name: 'visit_date_time'
                }
            ]
        });
        /***  Filters JQuery Start ***/


    </script>
@endsection
