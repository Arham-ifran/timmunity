@extends('admin.layouts.app')
@section('title',  __('Reseller Packages'))
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
                {{ __('Reseller Packages') }}
             </h2>
          </div>
       </div>
       <div class="row">
          <div class="box-header">
            <div class="row">
                <div class="col-md-4">
                    <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.reseller-package.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                  </div>

            </div>
          </div>
       </div>
    </section>
    <!-- Table content -->
    <section class="content">
       <div class="box pt-1">
          <div class="row box-body">
             <table id="reseller-package-datatable" class="table table-bordered table-striped">
                <thead>
                   <tr>
                     <th>{{ __('Package Name') }}</th>
                     <th>{{ __('Percentage') }}</th>
                     <th>{{ __('Model') }}</th>
                     <th>{{ __('Status') }}</th>
                     <th>{{ __('Actions') }}</th>
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
    <script>
        $(document).ready(function() {

            var table = $('#reseller-package-datatable').DataTable({
                "dom": '<"top"fi>rt<"bottom"lp><"clear">',
                lengthChange: false,
                responsive: true,
                serverSide: true,
                orderCellsTop: true,
                "aaSorting": [],
                ajax: "{{ route('admin.reseller-package.index') }}",
                fnDrawCallback: function(oSettings) {
                    $('[data-toggle="popover"]').popover();
                    $('[data-toggle="tooltip"]').tooltip();
                },
                columns: [

                    {
                        data: 'package_name',
                        name: 'package_name'
                    },
                    {
                        data: 'percentage',
                        name: 'percentage',

                    },
                    {
                        data: 'model',
                        name: 'model',

                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });

    </script>
    <script src="{{ asset('backend\plugins\datatables\jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.js') }}"></script>
@endsection
