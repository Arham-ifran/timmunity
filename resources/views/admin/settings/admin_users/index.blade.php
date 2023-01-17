@extends('admin.layouts.app')
@section('title',  __('Admin User'))
@section('styles')
<link href="{{ asset('backend/plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend/plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\dist\css\loader.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
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
         <div class="col-md-4">
            <h2>
               {{ __('Settings') }} / {{ __('Admin User') }}
            </h2>
         </div>
      </div>
      <div class="row">
         <div class="box-header">
            <div class="row">
                @can('Add New User')
                <div class="col-md-4">
                    <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.admin-user.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                </div>
                @endcan
                @can('Filter Record User')
                 <div class="col-md-4 pull-right">
                  <div class="form-group">
                      <label><strong>{{ __('Filter Record') }}</strong></label>
                      <select class="form-control" id="filter">
                          <option value="">{{ __('All Users') }}</option>
                          <option value="0" selected>{{ __('Internal Users') }}</option>
                          <option value="1">{{ __('Inactive Users') }}</option>
                      </select>
                  </div>
                </div>
                @endcan

            </div>
            <div class="row">
              <div class="col-md-12">
                 <button style="margin-bottom: 10px;display: none;" class="btn btn-danger" id="bulk_delete" data-url="{{ route('admin.bulk.delete') }}">{{ __('Delete Selected Records') }}</button>
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- Table content -->
   <section class="content">
      <div class="box pt-1">
         <div class="row box-body">
            <table id="admin-user-datatable" class="table table-bordered table-striped">
               <thead>
                  <tr>
                    @can('Delete User')
                    <th width="50px"><input type="checkbox" class="checkbox-input" name="deleteCheck[]" id="chk_all"></th>
                    @endcan
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Login') }}</th>
                    <th>{{ __('Language') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Latest Authentication') }}</th>
                    <th>{{ __('Status') }}</th>
                    @canany(['Delete User','Edit User'])
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
<script type="text/javascript">
    $(document).ready(function() {
      // Show / Hide Delete Button
        $(".checkbox-input").click(function (e) {
          if ($('[name="deleteCheck[]"]:checked').length > 0) {
              $("#bulk_delete").show();
          } else {
              $("#bulk_delete").hide();
          }
        })
       var count = 0;
        $('#admin-user-datatable thead tr').clone(true).appendTo( '#admin-user-datatable thead' );
          $('#admin-user-datatable thead tr:eq(1) th').each( function (i) {
            if(count < 6) {
                var title = $(this).text();
                if(count != 0) {
                $(this).html( '<input class ="form-control" type="text" placeholder="{{__('Search')}} '+{{__('title') }}+'" />' );
               }
               else if(count == 0) {
                 $(this).html('')
               }
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
              else if(count == 6) {
                $(this).text('');
              }
          } );
        var table = $('#admin-user-datatable').DataTable({
            "order": [],
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            orderCellsTop: true,
            lengthChange: false,
            responsive: true,
            serverSide: true,
            ajax: {
              url: "{{ route('admin.admin-user.index') }}",
              type: 'GET',
              data: function (d) {

                d.is_archive = $("#filter").val();

              }
             },
            fnDrawCallback: function( oSettings ) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columnDefs:[
               @canany(['Delete User','Edit User'])
                {width:'12%', targets:7}
                @endcanany
            ],
            columns: [
              @can('Delete User')
              {
                  data: 'delete_check',
                  name: 'delete_check',
                  orderable:false,
                  searchable:false
              },
              @endcan
              {
                  data: 'name',
                  name: 'name'
              },
              {
                  data: 'login',
                  name: 'login'
              },
              {
                  data: 'language',
                  name: 'language'
              },
              {
                  data: 'role',
                  name: 'role'
              },
              {
                  data: 'latest_authentication',
                  name: 'latest_authentication'
              },
              {
                  data: 'is_active',
                  name: 'is_active',
              },
              @canany(['Delete User','Edit User'])
              {
                  data: 'action',
                  name: 'action',
                  orderable:false,
                  searchable:false
              },
              @endcanany
            ]
        });

        $('#filter').change(function(){
          $('#admin-user-datatable').DataTable().draw();
        });
    });
</script>
<script type="text/javascript">
function checkBoxActions(context) {
  // Checked Parent Checkbox
 if ($('[name="deleteCheck[]"]:checked').length > 0) {
      $("#bulk_delete").show();
  } else {
      $("#bulk_delete").hide();
  }
  // Show / Hide Delete Button
  if($('.countChecks:checked').length == $('.countChecks').length){
    $('#chk_all').prop('checked',true);
  }
  else{
    $('#chk_all').prop('checked',false);
  }
}
</script>
<script type="text/javascript">
  $(document).ready(function () {

        $('#chk_all').on('click', function() {
         if($(this).is(':checked',true))
         {
            $("#bulk_delete").show()
            $(".sub_chk").prop('checked', true);
         } else {
            $("#bulk_delete").hide()
            $(".sub_chk").prop('checked',false);
         }
        });
        // Delete Selected Records
        $('#bulk_delete').on('click', function(e) {


          var allVals = [];
          $(".sub_chk:checked").each(function() {
              allVals.push($(this).attr('data-id'));
          });


          if(allVals.length <=0)
          {
              // alert("Please select row.");
              Swal.fire("", "{{ __('Please select row') }}", "info");
          }  else {

            var joinSelectedIds = allVals.join(",");

            Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('Are you sure that you want to delete these records?') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Yes, delete it!') }}"
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                  url: $(this).data('url'),
                  type: 'DELETE',
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                  data: 'ids='+joinSelectedIds,
                  beforeSend: function(){
                      // Show loader container
                      $("#ajax_loader").show();
                  },
                  success: function (data) {
                      if (data['success']) {
                        $('#admin-user-datatable').DataTable().ajax.reload();
                        $("#bulk_delete").hide();
                        Swal.fire("{{ __('Deleted') }}",data['success'], "success");
                        $('#chk_all').prop('checked',false);
                      }
                      else if(data['error']) {
                        Swal.fire("{{ __('The operation cannot be completed') }}",data['error'], "warning");
                      }

                  },
                  complete:function(data){
                      // Hide loader container
                      $("#ajax_loader").hide();
                 },
                 error: function (data) {
                    Swal.fire("{{ __('Error') }}",data.responseText,"warning");
                }
              });
            }
          })
        }
    });
});
</script>
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
@endsection
