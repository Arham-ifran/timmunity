@extends('admin.layouts.app')
@section('title',  __('Admin User'))
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
   <section class="content-header top-header">
      <div class="row">
         <div class="box-header">
            <div class="row">
               <div class="col-md-4">
                  <h2>
                     {{ __('Admin User') }} / @if($action == "Add") {{ __('Add') }} @elseif($action == "Duplicate") {{ __('Duplicate') }} @else {{ __('Edit') }} @endif
                  </h2>
               </div>
               @if($action == "Edit")
               <div class="col-md-4 text-center mt-3">
                  <div class="quotation-right-side content-center">
                     @canany(['Archive / Unarchive User','Duplicate User','Send Password Reset Instruction'])
                     <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                        <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('Actions') }} <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                           @can('Archive / Unarchive User')
                           <a class="dropdown-item" href="javascript:void(0)" onclick="archiveUser($(this))" data-model-id= "{{ Hashids::encode($model->id) }}" data-archive ="1" data-login-user= "{{ Hashids::encode(Auth::user()->id) }}" id="archive" @if($model->is_archive == 1) style="display: none" @endif>{{ __('Archive') }}</a>
                           @endcan
                           <a class="dropdown-item" href="javascript:void(0)" onclick="archiveUser($(this))" data-model-id= "{{ Hashids::encode($model->id) }}" data-archive ="0"  @if($model->is_archive == 0) style="display: none" @endif  id="unarchive">{{ __('Unarchive') }}</a>
                           @can('Duplicate User')
                           <a class="dropdown-item" href="{{ route('admin.admin-user.duplicate',['id'=> Hashids::encode($model->id)]) }}">{{ __('Duplicate') }}</a>
                           @endcan
                           @can('Send Password Reset Instruction')
                           <a class="dropdown-item" href="javascript:void(0)" onclick="resendEmail($(this))" data-model-id= "{{ Hashids::encode($model->id) }}" data-reset-password="1">{{ __('Send Password Reset Instruction') }}</a>
                           @endcan
                        </div>
                     </div>
                     @endcanany
                  </div>
               </div>
               @endif
               <div class="col-md-4">
                  <div class="ribbon ribbon-top-right o_widget" id="archived_ribbon" @if(@$model->is_archive == 0) style="display: none" @endif>
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
   <form class="timmunity-custom-dashboard-form mt-2 form-validate" id="admin_user_form" action="{{ route('admin.admin-user.store') }}" method="post" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
      <input type="hidden" name="action" value="{!!$action!!}">
      <div class="main-box box">
         <div class="row">
            <div class="box-header">
               <div class="row main-breadcrumb-div">
                  @if($action == "Edit")
                  <div class="col-md-6 pl-0">
                     <div class="breadcrumb">
                        @can('Send / Re-Send Invitation Email')
                        @if(isset($model->account_status) && $model->account_status == 0)
                        <a type="button" class="active" onclick="resendEmail($(this))" data-model-id= "{{ Hashids::encode($model->id) }}" data-invitation-code= "{{ $model->invitation_code }}" href="javascript:void(0)" >{{ __('Send Invitation Email') }}</a>
                        @else
                        <a type="button" class="active" onclick="resendEmail($(this))" data-model-id= "{{ Hashids::encode($model->id) }}" data-reset-password="1" href="javascript:void(0)">{{ __('Re-Send Invitation Email') }}</a>
                        @endif
                        @endcan
                     </div>
                  </div>
                  @endif
                  <div class="col-md-6 pull-right text-right">
                     <ul class="breadcrumb custom-breadcrumb">
                        <li class="{{(isset($model->account_status) && $model->account_status == 0 && $action == 'Edit') ? 'active' : '' }}">{{ __('Never Connected') }}</li>
                        <li class="{{(isset($model->account_status) && $model->account_status == 1 && $action == 'Edit') ? 'active' : '' }}">{{ __('Confirmed') }}</li>
                     </ul>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <div class="alert alert-info archive-alert alert-dismissible" id="archive-alert" @if(@$model->is_archive == 0) style="display: none" @endif>
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                     <strong>{{ __('The contact linked to this user is still active') }}</strong><br />
                     {{ __('You can archive the contact') }} <strong style="color:#009a71">{{ @$model->firstname.' '.@$model->lastname }}</strong>
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="alert alert-info archive-alert alert-dismissible" id="reset-inscruction-alert" @if(($action == "Add") || (@$model->account_status == 1 && @$model->invitation_code == null)) style="display: none" @endif>
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <strong>{{ __('A password reset has been requested for this user. An email containing the following link has been sent:') }}</strong><br /><strong><a href="{{ route('admin.verify.admin', ['code' => @$model->invitation_code]) }}" target="_blank" style="color:#009a71;font-size: 12px;text-decoration: none" id="new_reset_link">{{ route('admin.verify.admin', ['code' => @$model->invitation_code]) }}</a></strong>
               </div>
            </div>
         </div>
      </div>
      </div>
      <div class="row mt-3">
         <div class="col-xs-12">
            <div class="box box-success box-solid">
               <div class="box-header with-border">
                  <h3 class="box-title">@if($action == "Add") {{ __('Add New Admin User') }} @else {{ __('Edit Admin User') }} @endif</h3>
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
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="firstname">{{ __('First Name') }}<small class="asterik" style="color:red">*</small></label>
                                 <input type="text" class="form-control @error('firstname') is-invalid @enderror" id="firstname" name="firstname" value="{{ isset($model) ? old('firstname', $model->firstname ?? '').' '.(isset($keyword) ? $keyword : '' ) : '' }}" maxlength="30" aria-describedby="firstname" required />
                                 @error('firstname')
                                 <div id="firstname-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="lastname">{{ __('Last Name') }}<small class="asterik" style="color:red">*</small></label>
                                 <input type="text" class="form-control @error('lastname') is-invalid @enderror" id="lastname" name="lastname" value="{{ isset($model) ? old('lastname', $model->lastname ?? '').' '.(isset($keyword) ? $keyword : '' ) : '' }}" maxlength="30" aria-describedby="lastname" required />
                                 @error('lastname')
                                 <div id="lastname-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                 @enderror
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="email">{{ __('Email') }}<small class="asterik" style="color:red">*</small></label>
                                 <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $model->email ?? '') }}" maxlength="255" aria-describedby="email" required />
                                 @error('email')
                                 <div id="email-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-sm-6 form-group">
                              @if($action != "Edit" || Auth::user()->id != @$model->id)
                              <label>{{ __('Role') }}<small class="asterik" style="color:red">*</small></label>
                              <select class="form-control" name="role_id" required>
                                 <option value="">---{{ __('Select a Role') }}---</option>
                                 @foreach($roles as $role)
                                 <option value="{{ $role->id }}" @if(old('role_id', isset($assignedRoles) && in_array($role->id, $assignedRoles)))
                                 selected
                                 @endif>{{ ucfirst($role->name) }}</option>
                                 @endforeach
                              </select>
                              @endif
                           </div>
                        </div>
                        <div class="row">
                           @if($action == "Add") @php $checked = "checked"; @endphp @endif
                           <div class="col-md-3">
                              <div class="form-group">
                                 <label>{{ __('Status') }}</label>
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_active" id="is_active1" value="1" @if(isset($model->is_active) && $model->is_active == 1) checked @endif required >
                                    <label class="form-check-label" for="is_active1">
                                    {{ __('Active') }}
                                    </label>
                                 </div>
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_active" id="is_active2" value="0" @if(isset($model->is_active) && $model->is_active == 0) checked @endif {{@$checked}}>
                                    <label class="form-check-label" for="is_active2">
                                    {{ __('Inactive') }}
                                    </label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group">
                                 <label>{{ __('Is Sales Person') }}</label>
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_sales_team_member" id="is_sales_team_member1" value="1" @if(isset($model->is_sales_team_member) && $model->is_sales_team_member == 1) checked @endif required>
                                    <label class="form-check-label" for="is_sales_team_member1">
                                    {{ __('Yes') }}
                                    </label>
                                 </div>
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_sales_team_member" id="is_sales_team_member2" value="0" @if(isset($model->is_sales_team_member) && $model->is_sales_team_member == 0) checked @endif {{@$checked}}>
                                    <label class="form-check-label" for="is_sales_team_member2">
                                    {{ __('No') }}
                                    </label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6 pt-5">
                              <div class="row pull-right">
                                 <button type="submit"
                                    class="skin-green-light-btn btn ml-2">{{ __('Save') }}</button>
                                 <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                    href="{{ route('admin.admin-user.index') }}">{{ __('Discard') }}</a>
                              </div>
                           </div>
                        </div>
                     </div>
                     <!-- FILE UPLOAD -->
                     <div class="col-md-3 pull-right">
                        <div class="avatar-upload form-group">
                           <div class="avatar-fileds hover-effect">
                              <div class="avatar-edit">
                                 <input type="file" class="form-control" id="imageUpload" name="image" value="{{ old('image', $model->image ?? '')}}" />
                                 <label for="imageUpload"></label>
                              </div>
                           </div>
                           <div class="avatar-preview">
                              <img id="imagePreview"
                                 src="{!!checkImage(asset('storage/uploads/admin/' . Hashids::encode(@$model->id) . '/' . @$model->image),'avatar5.png')!!}" width="100%" height="100%" />
                              @error('image')
                              <div id="image-error" class="invalid-feedback animated fadeInDown">
                                 {{ $message }}
                              </div>
                              @enderror
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- /.box-body -->
            </div>
         </div>
         <!-- /.box -->
      </div>
      <!-- /.box -->
      <div class="row">
         <div class="col-md-12">
            <div class="custom-tabs mt-3 mb-2">
               <ul class="nav nav-tabs">
                  <li class="active"><a data-toggle="tab" href="#prefrence">{{ __('Preferences') }}</a></li>
               </ul>
               <div class="tab-content custom-tabs-style custom-tabs-pd-set">
                  <!-- General Setting -->
                  <!-- Kss -->
                  <div id="prefrence" class="tab-pane fade active in">
                     <div class="row tab-form pt-3">
                        <div class="col-md-6">
                           <div class="row">
                              <h3 class="col-md-12">{{ __('Localization') }}</h3>
                              <div class="col-sm-12 form-group">
                                 <label>{{ __('Languages') }}<small class="asterik" style="color:red">*</small></label>
                                 <div class="localization custom-select">
                                    <select
                                       class="form-control"
                                       name="lang_id" required>
                                       <option value="">---{{ __('Select a language') }}---</option>
                                       @foreach($languages as $lang)
                                       <option value="{{ $lang->id }}" @if(old('lang_id', isset($model) && $lang->id ==
                                       $model->lang_id))
                                       selected
                                       @endif>{{ $lang->name }}</option>
                                       @endforeach
                                    </select>
                                    <a type="button" href="{{ route("admin.languages.index") }}" class="btn btn-sm btn-link mb4 fa fa-globe" aria-label="More languages" title="More languages" ></a>
                                 </div>
                              </div>
                              <div class="col-sm-12 form-group">
                                 <label>{{ __('Timezone') }}<small class="asterik" style="color:red">*</small></label>
                                 <select
                                    class="form-control"
                                    name="timezone_id" required>
                                    <option value="">---{{ __('Select a timezone') }}---</option>
                                    @foreach($timezones as $timezone)
                                    <option value="{{ $timezone->id }}" @if(old('timezone_id', isset($model) && $timezone->id ==
                                    $model->timezone_id))
                                    selected
                                    @endif>{{ $timezone->name }}({{ $timezone->offset }})</option>
                                    @endforeach
                                 </select>
                              </div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label>{{ __('Notification') }}</label>
                                    <div class="form-check">
                                       <input class="form-check-input" type="radio" name="notification" id="notification1" value="1" @if(isset($model->notification) && $model->notification == 1) checked @endif>
                                       <label class="form-check-label" for="notification1">
                                       {{ __('Handle by Emails') }}
                                       </label>
                                    </div>
                                    <div class="form-check">
                                       <input class="form-check-input" type="radio" name="notification" id="notification2" value="2" @if(isset($model->notification) && $model->notification == 2) checked @endif>
                                       <label class="form-check-label" for="notification2">
                                       {{ __('Handle in TIMmunity') }}
                                       </label>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="email_signature">{{ __('Email Signature') }}</label>
                                    <textarea class="summernote form-control" name="email_signature">{{ old('email_signature', translation(@$model->id,8,app()->getLocale(),'email_signature',@$model->email_signature) ?? '') }}</textarea>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      </div>
   </form>
</section>
<!-- /.content -->
</div>
@endsection
@section('scripts')
{{-- <script src="{{ asset('backend/plugins/summernote/summernote-bs4.min.js') }}"></script> --}}
<script type="text/javascript">
    function resendEmail(_context) {
        var uid = $(_context).attr('data-model-id');
        var is_reset_password = $(_context).attr('data-reset-password');
        if(is_reset_password == 1)
        {
            var invitation_code =  "{{ \Str::random(60) }}";
        }
        else {
            var invitation_code = $(_context).attr('data-invitation-code');
        }
        var fd = new FormData();
        fd.append('_token', $('input[name="_token"]').val());
        fd.append('id', uid);
        fd.append('invitation_code', invitation_code);
        fd.append('is_reset_password', is_reset_password);
        $.ajax({
            url: '{{ route('admin.invitation.resend-email') }}',
            data: fd,
            type: 'POST',
            processData: false,
            contentType: false,
            beforeSend: function(){
                // Show loader container
                $("#ajax_loader").show();
            },
            complete:function(data){
                // Hide loader container
                $("#ajax_loader").hide();
                if(is_reset_password != 1 || is_reset_password == '') {
                  Swal.fire("{{ __('Archived') }}", "{{ __('The Invitation email has been sent successfully!') }}", "success");
                }
                else {
                  var resetLink = "{{ route('admin.verify.admin') }}"+'?'+'code'+'='+invitation_code;
                  $("#reset-inscruction-alert").show();
                  $("#new_reset_link").attr("href", resetLink);
                  $("#new_reset_link").html(resetLink);
                  Swal.fire("{{ __('Sent') }}", "{{ __('Password reset insruction has been sent successfully!') }}", "success");
                }

           }
        });
    }
</script>
<script type="text/javascript">
function archiveUser(_context) {
  var uid = $(_context).attr('data-model-id');
  var archive = $(_context).attr('data-archive');
  var logInUser = $(_context).attr('data-login-user');
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', uid);
  fd.append('is_archive', archive);
  fd.append('login_id', logInUser);
    Swal.fire({
    title: "{{ __('Are you sure?') }}",
    text: "{{ __('Are you sure that you want to archive this record?') }}",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: '{{ __('Yes, archive it!') }}'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
          url: '{{ route('admin.archive.record') }}',
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

                Swal.fire("{{ __('Archived') }}",resp['success'], "success");
                if(archive == 1) {
                   $("#archived_ribbon").show();
                   $("#archive").hide();
                   $("#unarchive").show();
                   $("#archive-alert").show();
                }
                else {
                   $("#archived_ribbon").hide();
                   $("#archive").show();
                   $("#unarchive").hide();
                   $("#archive-alert").hide();
                }
            }
            else {
              Swal.fire("{{ __('Warning') }}",resp['error'], "warning");
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
<script type="text/javascript">
$(document).ready(function() {
  $('.summernote').summernote();
});

    // jQuery.validator.addMethod("preferences_required", function(value, element) {
    //     name = $(element).attr('name');

    //     if($('input[name="'+name+'"]:checked').length == 0)
    //     {
    //         return false;
    //     }
    //     return true;
    // }, "{{ __('This field is required') }}");

    // $('#admin_user_form').validate({
    //     ignore: [],
    //     onkeyup: false,
    //     onclick: false,
    //     onfocusout: false,
    //     rules: {
    //         "firstname":{
    //             required:true
    //         },
    //         "lastname":{
    //             required:true
    //         },
    //         "email":{
    //             required:true
    //         },
    //         "lang_id":{
    //             required:true,
    //         },
    //         "timezone_id":{
    //             required:true,
    //         }
    //     },
    //     messages: {
    //         "firstname":{
    //             required:"{{ __('Firstname is required') }}"
    //         },
    //         "lastname":{
    //             required:"{{ __('Lastname is required') }}"
    //         },
    //         "email":{
    //             required:"{{ __('Email is required') }}"
    //         },
    //         "lang_id":{
    //             required:"{{ __('Language is required is preferences') }}"
    //         },
    //         "timezone_id":{
    //             required:"{{ __('Time Zone is required is preferences') }}"
    //         }
    //     },
    //     errorPlacement: function(error, element) {
    //         err = error;
    //         error.insertAfter(element);
    //         $(element).css('border-color','red');
    //         toastr.error(err);
    //     },
    // });
</script>
@endsection
