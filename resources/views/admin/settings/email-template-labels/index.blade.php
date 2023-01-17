@extends('admin.layouts.app')
@section('title',  __('Email Template Labels'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header">
      <div class="row">
         <div class="col-md-6">
            <h2>
               {{ __('Settings') }} / {{ __('Email Template Labels') }}
            </h2>
         </div>
      </div>
      <div class="row">
        <div class="col-md-4 pt-2">
            @can('Add New Email Template Labels')
            <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.email-template-labels.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
            @endcan
        </div>
        <div class="col-md-4 pull-right">
          <div class="form-group">
              @can('Filter Record Email Template')
              <label><strong>{{ __('Filter Record') }}</strong></label>
              <select class="form-control" id="filter">
                  <option value="">---{{ __('Select a email template') }}---</option>
                  @foreach($email_templates as $email_template)
                  <option value="{{ Hashids::encode($email_template->id) }}">
                    {{ $email_template->subject }}
                </option>
                @endforeach
            </select>
            @endcan
          </div>
        </div>
    </div>
   </section>
   <!-- Table content -->
   <section class="content">
      <div class="box pt-1">
         <div class="row box-body">
            <table id="email-template-labels-datatable" class="table table-bordered table-striped">
               <thead>
                  <tr>
                    <th>{{ __('Label') }}</th>
                    <th>{{ __('Value') }}</th>
                    @canany(['Edit Email Template Labels','Delete Email Template Labels '])
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
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#email-template-labels-datatable').DataTable({
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            orderCellsTop: true,
            lengthChange: false,
            responsive: true,
            serverSide: true,
            stateSave: true,
            ajax: {
              url: "{{ route('admin.email-template-labels.index') }}",
              type: 'GET',
              data: function (d) {
                d.email_template_id = $("#filter").val();
              }
             },
            fnDrawCallback: function( oSettings ) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [
              {
                  data: 'label',
                  name: 'label'
              },
              {
                  data: 'value',
                  name: 'value'
              },
              @canany(['Edit Email Template Labels','Delete Email Template Labels '])
              {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
              },
              @endcanany
            ]
        });

        $('#filter').change(function(){
          $('#email-template-labels-datatable').DataTable().draw();
        });
    });
</script>
@endsection
