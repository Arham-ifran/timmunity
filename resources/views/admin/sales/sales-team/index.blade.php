@extends('admin.layouts.app')
@section('title', __('Sales Team'))
@section('content')
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\dist\css\loader.css') }}" rel="stylesheet" type="text/css">
@endsection
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
                <h2>{{ __('Sales Teams') }}</h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
                <div class="row">
                    @can('Add Sales Team')
                    <div class="col-md-4">
                      <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.sales-team.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    </div>
                    @endcan
                    @can('Sales Team Filter Record')
                    <div class="col-md-4 pull-right">
                      <div class="form-group">
                          <label><strong>{{ __('Filter Record') }}</strong></label>
                          <select class="form-control" id="sale_team_filter">
                              <option value="">{{ __('All Sale Teams') }}</option>
                              <option value="0" selected>{{ __('Un-Archived Sale Teams') }}</option>
                              <option value="1">{{ __('Archived Sale Teams') }}</option>
                          </select>
                      </div>
                    </div>
                    @endcan
                </div>
                <div class="row">
                  <div class="col-md-12">
                     <button style="margin-bottom: 10px;display: none;" class="btn btn-danger" id="sale_team_blk_dlt" data-url="{{ route('admin.sale-team.bulk.delete') }}">{{ __('Delete Selected Records') }}</button>
                   </div>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="box pt-1">
            <div class="row box-body">
                <table id="sales_team_table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50px"><input type="checkbox" class="checkbox-input" name="teamDeleteCheck[]" id="chk_all_sale_teams"></th>
                            <th>{{ __('Sales Team') }}</th>
                            <th>{{ __('Team Leader') }}</th>
                            @canany(['Edit Sales Team','Delete Sales Team','View Sales Team'])
                            <th>{{ __('Actions') }}</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        // Show / Hide Delete Button
        $(".checkbox-input").click(function (e) {
          if ($('[name="teamDeleteCheck[]"]:checked').length > 0) {
              $("#sale_team_blk_dlt").show();
          } else {
              $("#sale_team_blk_dlt").hide();
          }
        })
      // Setup - add a text input to each footer cell
        var count = 0;
        $('#sales_team_table thead tr').clone(true).appendTo('#sales_team_table thead' );
          $('#sales_team_table thead tr:eq(1) th').each( function (i) {
            if(count < 3) {
                var title = $(this).text();
                if(count != 0) {
                $(this).html( '<input class ="form-control" type="text" placeholder="{{__('Search')}} '+{{__('title') }}+'" />' );
               }
               else if(count == 0) {
                 $(this).html('')
               }
                $('input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
                count++;
              }
              else if(count == 3) {
                $(this).text('');
              }
          } );
        var table = $('#sales_team_table').DataTable({
            "order": [],
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            orderCellsTop: true,
            lengthChange: false,
            responsive: true,
            serverSide: true,
            "aaSorting": [],
            ajax: {
              url: "{{ route('admin.sales-team.index') }}",
              type: 'GET',
              data: function (d) {

                d.is_archive = $("#sale_team_filter").val();

              }
             },
            fnDrawCallback: function( oSettings ) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [
            {
                  data: 'delete_check',
                  name: 'delete_check',
                  orderable:false,
                  searchable:false
            },
            {
                data: 'sales_team',
                name: 'sales_team'
            },
            {
                data: 'team_leader',
                name: 'team_leader'
            },
            @canany(['Edit Sales Team','Delete Sales Team','View Sales Team'])
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
            @endcanany
            ]
        });
        $('#sale_team_filter').change(function(){
          $('#sales_team_table').DataTable().draw();
        });
    });
</script>
<script type="text/javascript">
function checkBoxActions(context) {
  // Checked Parent Checkbox
 if ($('[name="teamDeleteCheck[]"]:checked').length > 0) {
      $("#sale_team_blk_dlt").show();
  } else {
      $("#sale_team_blk_dlt").hide();
  }
  // Show / Hide Delete Button
  if($('.teamCountChecks:checked').length == $('.teamCountChecks').length){
    $('#chk_all_sale_teams').prop('checked',true);
  }
  else{
    $('#chk_all_sale_teams').prop('checked',false);
  }
}
</script>
<script type="text/javascript">
  $(document).ready(function () {

        $('#chk_all_sale_teams').on('click', function() {
         if($(this).is(':checked',true))
         {
            $("#sale_team_blk_dlt").show()
            $(".sale_team_sub_chk").prop('checked', true);
         } else {
            $("#sale_team_blk_dlt").hide()
            $(".sale_team_sub_chk").prop('checked',false);
         }
        });
        // Delete Selected Records
        $('#sale_team_blk_dlt').on('click', function(e) {


          var allVals = [];
          $(".sale_team_sub_chk:checked").each(function() {
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
                        $('#sales_team_table').DataTable().ajax.reload();
                        $("#sale_team_blk_dlt").hide();
                        Swal.fire("{{ __('Deleted') }}",data['success'], "success");
                        $('#chk_all_sale_teams').prop('checked',false);
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
