@extends('admin.layouts.app')
@section('title',  __('Ecommerce Categories'))
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
                {{ __('Ecommerce Categories') }}
             </h2>
          </div>
       </div>
       <div class="row">
        @can('Add Ecommerce Categories')
          <div class="box-header">
             <div class="row">
                <div class="col-md-4">
                    <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.eccomerce-categories.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                  </div>

             </div>
          </div>
        @endcan
       </div>
    </section>
    <!-- Table content -->
    <section class="content">
       <div class="box pt-1">
          <div class="row box-body">
             <table id="eccomerce-categories-datatable" class="table table-bordered table-striped">
                <thead>
                   <tr>
                     <th>{{ __('Category Name') }}</th>
                     <th>{{ __('Parent Category') }}</th>
                     @canany(['Edit Ecommerce Categories','Delete Ecommerce Categories'])
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
    <script>
        $(document).ready(function() {

            var table = $('#eccomerce-categories-datatable').DataTable({
                "dom": '<"top"fi>rt<"bottom"lp><"clear">',
                lengthChange: false,
                responsive: true,
                serverSide: true,
                orderCellsTop: true,
                "aaSorting": [],
                ajax: "{{ route('admin.eccomerce-categories.index') }}",
                fnDrawCallback: function(oSettings) {
                    $('[data-toggle="popover"]').popover();
                    $('[data-toggle="tooltip"]').tooltip();
                },
                columns: [

                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'parent_category',
                        name: 'parent_category',
                    },
                    @canany(['Edit Ecommerce Categories','Delete Ecommerce Categories'])
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
    <script src="{{ asset('backend\plugins\datatables\jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.js') }}"></script>
@endsection
