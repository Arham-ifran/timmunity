@extends('admin.layouts.app')
@section('title',  __('Languages'))
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
               {{ __('Settings') }} / {{ __('Languages') }}
            </h2>
         </div>
      </div>
      <div class="row">
         <div class="box-header">
            <div class="row">
                @can('Add New Languages')
                <div class="col-md-4">
                    <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.languages.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                </div>
                @endcan
               <div class="col-md-4 pull-right">
                  <div class="form-group">
                      @can('Filter Record Languages')
                      <label><strong>{{ __('Filter Record') }}</strong></label>
                        <select class="form-control" id="lang_filter">
                          <option value="">{{ __('All Languages') }}</option>
                          <option value="0" selected>{{ __('Un-Archived Languages') }}</option>
                          <option value="1">{{ __('Archived Languages') }}</option>
                        </select>
                      @endcan
                  </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 mt-3">
                 <button type="submit" class="btn btn-primary" data-url="{{ route('admin.bulk.delete.languages') }}" id="delete_languages" style="display:none">{{ __('Delete Selected Records') }}</button>
                </div>
                <div class="col-md-2 mt-3 ">
                    <span class="badge badge-success" id="totalLangCount"></span>
                </div>
            </div>
         </div>
      </div>
   </section>
   <!-- Table content -->
   <section class="content">
      <div class="box pt-1">
         <div class="row box-body">
            <table id="languages-datatable" class="table table-bordered table-striped">
               <thead>
                  <tr>
                    <th width="50px"><input type="checkbox" class="checkbox-input" name="deleteLangChecks[]" id="chk_all_lang"></th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('ISO Code') }}</th>
                    <th>{{ __('Status') }}</th>
                    @canany(['Edit Languages','Delete Languages','Activate / Update Languages'])
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
   <!-- Add Language Model -->
    <div class="modal fade" id="language_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title lang_modal_title col-md-9 pl-0" id="exampleModalLabel"></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="lang_active_msg"></div>
                    <div class="form-group" id="lang_modal_body" style="display:none">
                        <label for="message-text" class="col-form-label">{{ __('Language') }}</label>
                        <select class="form-control" name="language" id="language">
                            @foreach($languages as $language)
                            <option value="{{ Hashids::encode($language->id) }}">{{ $language->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer" id="add_lang_section">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-success" onclick="addLanguage($(this))" data-table-flag = "1">{{ __('Add') }}</button>
                </div>
                <div class="modal-footer" id="swith_lang_footer">
                    <div class="col-md-12 text-left" id="switch_to_lang">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="archive_language_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title lang_modal_title col-md-9 pl-0" id="exampleModalLabel"></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="lang_active_msg"></div>
                    <div class="form-group" id="a_lang_modal_body" style="display:none">
                        <p> Do you want to really Archive <span id="language_name"></span>.</p>
                    </div>
                </div>
                <div class="modal-footer" id="a_lang_section">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-success" onclick="archiveLanguage($(this))" data-table-flag = "1">{{ __('Archive / De-Activate') }}</button>
                </div>
                <div class="modal-footer" id="swith_lang_footer">
                    <div class="col-md-12 text-left" id="switch_to_lang">
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    var count = 0;
    $('#languages-datatable thead tr').clone(true).appendTo( '#languages-datatable thead' );
      $('#languages-datatable thead tr:eq(1) th').each( function (i) {
        if(count < 4) {
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
          else if(count == 4) {
            $(this).text('');
          }
      } );
        var table = $('#languages-datatable').DataTable({
            "order": [],
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            orderCellsTop: true,
            lengthChange: false,
            responsive: true,
            stateSave: true,
            serverSide: true,
            ajax: {
              url: "{{ route('admin.languages.index') }}",
              type: 'GET',
              data: function (d) {

                d.is_archive = $("#lang_filter").val();

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
                  data: 'name',
                  name: 'name'
              },
              {
                  data: 'iso_code',
                  name: 'iso_code'
              },
              {
                  data: 'is_active',
                  name: 'is_active',
              },
              @canany(['Edit Languages','Delete Languages','Activate / Update Languages'])
              {
                  data: 'action',
                  name: 'action',
                  orderable:false,
                  searchable:false
              },
              @endcanany
            ]
        });
        $('#lang_filter').change(function(){
          $('#languages-datatable').DataTable().draw();
        });
    });
</script>
{{-- <script type="text/javascript">
    function UpdateStatus(_context) {
        var lang_id = $(_context).attr('data-id');
        var current_status = $(_context).attr('data-status');
        var login_id = $(_context).attr('data-login-id');
        var fd = new FormData();
        fd.append('_token', "{{ csrf_token() }}");
        fd.append('id', lang_id);
        fd.append('current_status', current_status);
        fd.append('login_id', login_id);
        $.ajax({
            url: '{{ route("admin.update.status") }}',
            data: fd,
            type: 'POST',
            processData: false,
            contentType: false,
            success: function(resp) {
                if (resp['status'] == "success") {
                   Swal.fire(resp['statusVal'] , resp['lang']+' '+"has been" +' '+resp['statusVal']+' '+"successfully !", "success");
                }
                else {
                   Swal.fire("Warning",resp['warning'], "warning");
                }
                $('#languages-datatable').DataTable().ajax.reload();
            }
        });
    }
</script> --}}
<script type="text/javascript">
function checkBoxActions(context) {
  // Checked Parent Checkbox
  var checkCount = $('[name="deleteLangChecks[]"]:checked').length;
 if ($('[name="deleteLangChecks[]"]:checked').length > 0) {
      $('#totalLangCount').show();
      $('#totalLangCount').text(checkCount+' '+"selected");
      $('#delete_languages').show();
  } else {
      $('#totalLangCount').hide();
      $('#delete_languages').hide();
  }
  // Show / Hide Delete Button
  if($('.countLangChecks:checked').length == $('.countLangChecks').length) {
    $('#totalLangCount').text($('.countLangChecks:checked').length+' '+"selected");
    $('#chk_all_lang').prop('checked',true);
  }
  else{
    $('#totalLangCount').text($('.countLangChecks:checked').length+' '+"selected");
    $('#chk_all_lang').prop('checked',false);
  }
}
</script>
<script type="text/javascript">
  $(document).ready(function () {
        $('#chk_all_lang').on('click', function() {
         if($(this).is(':checked',true))
         {
            var checkCount = $('.countLangChecks').length;
            $('#totalLangCount').show();
            $('#totalLangCount').text(checkCount+' '+"selected");
            $('#delete_languages').show();
            $(".del_lang_sub_chk").prop('checked', true);
         } else {
            $('#totalLangCount').hide();
            $('#delete_languages').hide();
            $(".del_lang_sub_chk").prop('checked',false);
         }
        });
        // Delete Selected Records
        $('#delete_languages').on('click', function(e) {


          var allVals = [];
          $(".del_lang_sub_chk:checked").each(function() {
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
                        $('#languages-datatable').DataTable().ajax.reload();
                        $('#delete_languages').hide();
                        $('#totalLangCount').hide();
                        Swal.fire("{{ __('Deleted') }}",data['success'], "success");
                        $('#chk_all_lang').prop('checked',false);
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
<script type="text/javascript">
// Language Activation URL
   var add_language_url = '{{ route("admin.add.language") }}';
   var archive_language_url = '{{ route("admin.make.archive.language") }}';
   var archive_language_msg = '{{ __("has been successfully archived.") }}';
   var modal_title = "{{ __('Add Language') }}";
   var modal_change_title = "{{ __('Language Pack') }}";
   var switch_btn_title = "{{ __('Switch to') }}";
   var close_btn_title = "{{ __('Close') }}";
</script>
@endsection
