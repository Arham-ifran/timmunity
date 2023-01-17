@extends('admin.layouts.app')
@section('title',  __('Taxes'))
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
                {{ __('Taxes') }}
             </h2>
          </div>
       </div>
       <div class="row">
          <div class="box-header">
            @can('Add Tax')
            <div class="row">
                <div class="col-md-4">
                    <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.taxes.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                  </div>

            </div>
            @endcan
          </div>
       </div>
    </section>
    <!-- Table content -->
    <section class="content">
       <div class="box pt-1">
          <div class="row box-body">
             <table id="taxes-datatable" class="table table-bordered table-striped">
                <thead>
                   <tr>
                     <th>{{ __('Name') }}</th>
                     <th>{{ __('Type') }}</th>
                     <th>{{ __('Computation') }}</th>
                     <th>{{ __('Applicable On') }}</th>
                     <th>{{ __('Amount') }}</th>
                     <th>{{ __('Active') }}</th>
                     @canany(['Edit Tax','Delete Tax'])
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

            var table = $('#taxes-datatable').DataTable({
                "dom": '<"top"fi>rt<"bottom"lp><"clear">',
                lengthChange: false,
                responsive: true,
                serverSide: true,
                orderCellsTop: true,
                "aaSorting": [],
                ajax: "{{ route('admin.taxes.index') }}",
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
                        data: 'type',
                        name: 'type',

                    },
                    {
                        data: 'computation',
                        name: 'computation',

                    },
                    {
                        data: 'applicable_on',
                        name: 'applicable_on',

                    },
                    {
                        data: 'amount',
                        name: 'amount',
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                    },
                    @canany(['Edit Tax','Delete Tax'])
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
