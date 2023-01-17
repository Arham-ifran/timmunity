@extends('admin.layouts.app')
@section('title',  __('FAQs'))
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
               {{ __('Website') }} / {{ __('FAQs') }}
            </h2>
         </div>
      </div>
      <div class="row">
         <div class="box-header">
            <div class="row">
               @can('Create FAQ')
                <div class="col-md-4">
                  <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.faqs.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
               </div>
               @endcan
            </div>
            <div class="row">
                <div class="col-md-2 mt-3">
                 <button type="submit" class="btn btn-primary" data-url="{{ route('admin.bulk.delete.faqs') }}" id="delete_faqs" style="display:none">{{ __('Delete Selected Records') }}</button>
                </div>
                <div class="col-md-2 mt-3 ">
                    <span class="badge badge-success" id="totalFaqCount"></span>
                </div>
            </div>
         </div>
      </div>
   </section>
   <!-- Table content -->
   <section class="content">
      <div class="box pt-1">
         <div class="row box-body">
            <table id="faqs-datatable" class="table table-bordered table-striped">
               <thead>
                  <tr>
                     @can('Delete FAQ')
                     <th width="50px"><input type="checkbox" class="checkbox-input" name="deleteFaqChecks[]" id="chk_all_faqs"></th>
                     @endcan
                    <th>{{ __('Question') }}</th>
                    <th>{{ __('Display Order') }}</th>
                    <th>{{ __('Status') }}</th>
                    @canany(['Edit FAQ','Delete FAQ'])
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
        var table = $('#faqs-datatable').DataTable({
            "order": [],
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            orderCellsTop: true,
            lengthChange: false,
            responsive: true,
            serverSide: true,
            "order": [[ 1, "desc" ]],
            ajax: "{{ route('admin.faqs.index') }}",
            fnDrawCallback: function( oSettings ) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [
              @can('Delete FAQ')
              {
                  data: 'delete_check',
                  name: 'delete_check',
                  orderable:false,
                  searchable:false
              },
              @endcan
              {
                  data: 'question',
                  name: 'question'
              },
              {
                  data: 'display_order',
                  name: 'display_order'
              },
              {
                  data: 'status',
                  name: 'status'
              },
            @canany(['Edit FAQ','Delete FAQ'])
              {
                  data: 'action',
                  name: 'action',
                  orderable:false,
              },
            @endcanany
            ]
        });
    });
</script>
<script type="text/javascript">
function checkBoxActions(context) {
  // Checked Parent Checkbox
  var checkCount = $('[name="deleteFaqChecks[]"]:checked').length;
 if ($('[name="deleteFaqChecks[]"]:checked').length > 0) {
      $('#totalFaqCount').show();
      $('#totalFaqCount').text(checkCount+' '+"selected");
      $('#delete_faqs').show();
  } else {
      $('#totalFaqCount').hide();
      $('#delete_faqs').hide();
  }
  // Show / Hide Delete Button
  if($('.countFaqChecks:checked').length == $('.countFaqChecks').length) {
    $('#totalFaqCount').text($('.countFaqChecks:checked').length+' '+"selected");
    $('#chk_all_faqs').prop('checked',true);
  }
  else{
    $('#totalFaqCount').text($('.countFaqChecks:checked').length+' '+"selected");
    $('#chk_all_faqs').prop('checked',false);
  }
}
</script>
<script type="text/javascript">
  $(document).ready(function () {
        $('#chk_all_faqs').on('click', function() {
         if($(this).is(':checked',true))
         {
            var checkCount = $('.countFaqChecks').length;
            $('#totalFaqCount').show();
            $('#totalFaqCount').text(checkCount+' '+"selected");
            $('#delete_faqs').show();
            $(".del_faq_sub_chk").prop('checked', true);
         } else {
            $('#totalFaqCount').hide();
            $('#delete_faqs').hide();
            $(".del_faq_sub_chk").prop('checked',false);
         }
        });
        // Delete Selected Records
        $('#delete_faqs').on('click', function(e) {


          var allVals = [];
          $(".del_faq_sub_chk:checked").each(function() {
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
                        $('#faqs-datatable').DataTable().ajax.reload();
                        $('#delete_faqs').hide();
                        $('#totalFaqCount').hide();
                        Swal.fire("{{ __('Deleted') }}",data['success'], "success");
                        $('#chk_all_faqs').prop('checked',false);

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
@endsection
