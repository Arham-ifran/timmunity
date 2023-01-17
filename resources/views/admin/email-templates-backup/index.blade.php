@extends('admin.layouts.app')
@section('title',  __('Email Templates'))
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
                {{ __('Email Templates') }}
             </h2>
          </div>
       </div>
       <div class="row">
          <div class="box-header">
             <div class="row">
                <div class="col-md-4">
                    <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.email-templates.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                  </div>

             </div>
          </div>
       </div>
    </section>
    <!-- Table content -->
    <section class="content">
       <div class="box pt-1">
          <div class="row box-body">
             <table id="email-templates-datatable" class="table table-bordered table-striped">
                <thead>
                   <tr>
                     <th>{{ __('Title') }}</th>
                     <th>{{ __('Email Template Type') }}</th>
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

            var table = $('#email-templates-datatable').DataTable({
                "dom": '<"top"fi>rt<"bottom"lp><"clear">',
                lengthChange: false,
                responsive: true,
                serverSide: true,
                orderCellsTop: true,
                ajax: "{{ route('admin.email-templates.index') }}",
                fnDrawCallback: function(oSettings) {
                    $('[data-toggle="popover"]').popover();
                    $('[data-toggle="tooltip"]').tooltip();
                },
                columns: [

                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'email_template_type',
                        name: 'email_template_type',
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
