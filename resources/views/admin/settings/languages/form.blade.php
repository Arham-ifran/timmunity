@extends('admin.layouts.app')
@section('title',  __('Languages'))
@section('styles')
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
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
   <section class="content-header top-header mb-1">
      <div class="row">
        <div class="box-header">
           <div class="row">
              <div class="col-md-4">
                <h2>
                   {{ __('Language') }} / @if($action == "Add") {{ __('Add') }}  @else {{ __('Edit') }} @endif
                </h2>
              </div>
              @if($action == "Edit")
              <div class="col-md-4 text-center mt-3">
                 <div class="quotation-right-side content-center">
                    <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                       <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                       {{ __('Action') }} <span class="caret"></span>
                       </a>
                       <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                          <a class="dropdown-item" href="javascript:void(0)" onclick="archiveLang($(this))" data-model-id= "{{ Hashids::encode($model->id) }}" data-archive ="1" data-login-id= "{{ Hashids::encode(Auth::user()->id) }}" id="archive_lang" @if($model->is_archive == 1) style="display: none" @endif>{{ __('Archive') }}</a>
                          <a class="dropdown-item" href="javascript:void(0)" onclick="archiveLang($(this))" data-model-id= "{{ Hashids::encode($model->id) }}" data-archive ="0"  @if($model->is_archive == 0) style="display: none" @endif  id="unarchive_lang">{{ __('Unarchive') }}</a>
                       </div>
                    </div>
                 </div>
              </div>
              @endif
              <div class="col-md-4">
                <div class="ribbon ribbon-top-right o_widget" id="archived_lang_ribbon" @if(@$model->is_archive == 0) style="display: none" @endif>
                    <span class="bg-danger">
                        {{ __('Archived') }}
                    </span>
                </div>
              </div>
           </div>
        </div>
     </div>
   </section>
   <!-- Table content -->
   <section class="content">
      <div class="main-box box">
         <div class="row mt-3">
            <div class="col-xs-12">
               <div class="box box-success box-solid">
                  <div class="box-header with-border">
                     <h3 class="box-title">@if($action == "Add") {{ __('Add New Languages') }} @else {{ __('Edit Languages') }} @endif</h3>
                     <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                     </div>
                     <!-- /.box-tools -->
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                     <div class="row">
                        <div class="col-md-9">
                           <form class="mt-2 form-validate" action="{{ route('admin.languages.store') }}" method="post" enctype="multipart/form-data">
                              @csrf
                              <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                              <input type="hidden" name="action" value="{!!$action!!}">
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="name">{{ __('Name') }}<small class="asterik" style="color:red">*</small></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $model->name ?? '') }}" maxlength="100" aria-describedby="name" required />
                                    @error('name')
                                    <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label for="iso_code">{{ __('ISO Code') }}<small class="asterik" style="color:red">*</small></label>
                                    <input type="text" class="form-control @error('iso_code') is-invalid @enderror" id="iso_code" name="iso_code" value="{{ old('iso_code', $model->iso_code ?? '') }}" maxlength="5" aria-describedby="iso_code" required />
                                    @error('iso_code')
                                    <div id="iso_code-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label for="local_code">{{ __('Local Code') }}<small class="asterik" style="color:red">*</small></label>
                                    <input type="text" class="form-control @error('local_code') is-invalid @enderror" id="local_code" name="local_code" value="{{ old('local_code', $model->local_code ?? '') }}" maxlength="10" aria-describedby="local_code" required />
                                    @error('local_code')
                                    <div id="local_code-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Status') }}</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_active" id="is_active1" value="1" @if(isset($model->is_active) && $model->is_active == 1) checked @endif>
                                        <label class="form-check-label" for="is_active1">
                                            {{ __('Active') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_active" id="is_active2" value="0" @if(isset($model->is_active) && $model->is_active == 0) checked @endif>
                                        <label class="form-check-label" for="is_active2">
                                            {{ __('Deactive') }}
                                        </label>
                                    </div>
                                </div>
                              </div>
                              <br>
                              <div class="row clearfix">
                                   <div class="box-header">
                                      <div class="row">
                                         <div class="col-md-4 pl-0">
                                            <button type="submit" class="skin-green-light-btn btn ml-2">{{ __('Save') }}</button>
                                            <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.languages.index') }}">{{ __('Discard') }}</a>
                                         </div>
                                      </div>
                                   </div>
                                </div>
                           </form>
                        </div>
                     </div>
                  </div>
                  <!-- /.box-body -->
               </div>
            </div>
         </div>
         <!-- /.box -->
      </div>
    </section>
</div>
<!-- /.content -->
@endsection
@section('scripts')
<script type="text/javascript">
function archiveLang(_context) {
  var lang_id = $(_context).attr('data-model-id');
  var archive = $(_context).attr('data-archive');
  var login_id = $(_context).attr('data-login-id');
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', lang_id);
  fd.append('is_archive', archive);
  fd.append('login_id', login_id);
    Swal.fire({
    title: "{{ __('Are you sure?') }}",
    text: "{{ __('Are you sure that you want to archive this record?') }}",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: "{{ __('Yes, delete it!') }}"
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
          url: '{{ route('admin.archive.language') }}',
          data: fd,
          type: 'POST',
          processData: false,
          contentType: false,
          beforeSend: function(){

              // Show loader container
              $("#ajax_loader").show();
          },
          success: function (resp) {
            if (resp['success']) {

                Swal.fire("Archived",resp['success'], "success");
                if(archive == 1) {
                   $("#archived_lang_ribbon").show();
                   $("#archive_lang").hide();
                   $("#unarchive_lang").show();
                }
                else {
                   $("#archived_lang_ribbon").hide();
                   $("#archive_lang").show();
                   $("#unarchive_lang").hide();
                }
            }
            else {
              Swal.fire("Warning",resp['warning'], "warning");
            }
          },
          complete:function(data){
          // Hide loader container
              $("#ajax_loader").hide();
         }
      });
    }
  })
}
</script>
@endsection
