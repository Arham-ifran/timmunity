@extends('distributor.layouts.app')
@section('title',  __('Distributor Profile'))
@section('styles')
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
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
                     {{ __('Distributor Profile') }}
                  </h2>
               </div>

               <div class="col-md-4 text-center mt-3">

               </div>

               <div class="col-md-4">
                    <!-- <div class="ribbon ribbon-top-right o_widget" id="archived_ribbon" @if(@$model->is_archive == 0) style="display: none" @endif>
                        <span class="bg-danger">
                        {{ __('Archived') }}
                        </span>
                    </div> -->
                </div>
            </div>
         </div>
      </div>
</section>
<!-- Table content -->
<section class="content">
   <form class="timmunity-custom-dashboard-form mt-2 form-validate" id="distributor-form" action="{{route('distributor.profile')}}" method="post" enctype="multipart/form-data">
      @csrf

      <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
      <div class="row mt-3">
         <div class="col-xs-12">
            <div class="box box-success box-solid">
               <div class="box-header with-border">
                  <h3 class="box-title">{{ __('Distributor Profile') }}</h3>
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
                                 <label for="firstname">{{ __('Distributor Name') }}<small class="asterik" style="color:red">*</small></label>
                                 <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter Name" value="{{ isset($model) ? old('name', $model->name ?? '').' '.(isset($keyword) ? $keyword : '' ) : '' }}" maxlength="30" aria-describedby="firstname" required />
                                 @error('name')
                                 <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="email">{{ __('Email') }}<small class="asterik" style="color:red">*</small></label>
                                 <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter Email" value="{{ old('email', $model->email ?? '') }}" maxlength="255" aria-describedby="email" required />
                                 @error('email')
                                 <div id="email-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                 @enderror
                              </div>
                           </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                              <div class="form-group">
                                 <label for="lastname">{{ __('Company') }}<small class="asterik" style="color:red">*</small></label>
                                 <input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company" value="{{ old('company', $model->company ?? '') }}" placeholder="Enter Company Name" maxlength="30" aria-describedby="lastname" required />
                                 @error('company')
                                 <div id="company-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                 @enderror
                              </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                 <label for="lastname">{{ __('Password') }}</label>
                                 <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" value=""/>
                                 @error('password')
                                 <div id="password-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                 @enderror
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                 <label for="c_pass">{{ __('Confirm Password') }}</label>
                                 <input type="password" class="form-control @error('company') is-invalid @enderror" id="c_pass" name="c_pass" placeholder="Confirm Password" value="" />
                                 @error('c_pass')
                                 <div id="c_pass-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                 @enderror
                              </div>
                            </div>

                        </div>

                        <div class="row">
                           <div class="col-md-6 pt-5">

                              <button type="submit"
                              class="skin-green-light-btn btn ml-2">{{ __('Save') }}</button>
                              <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                              href="{{ route('distributor.dashboard') }}">{{ __('Discard') }}</a>

                           </div>
                        </div>
                     </div>
                     <!-- FILE UPLOAD -->
                     <!-- <div class="col-md-3 pull-right">
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
                     </div> -->
                  </div>
               </div>
               <!-- /.box-body -->
            </div>
         </div>
         <!-- /.box -->
      </div>
      <!-- /.box -->

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
    $.validator.addMethod("passwords", function (value, element) {
        return this.optional(element) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }, "*Should contain at least 8 from the mentioned characters, *Password should contain at least one digit, *Should contain at least one upper & lower case letter, *Should contain special character  & numbers.");
    $.validator.addMethod("email", function (value, element) {
        return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
    }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
    $('#distributor-form').validate({
        ignore: [],
        rules: {
            "name":{
                required:true
            },
            "email":{
                required:true,
                email:true
            },
            "company":{
                required:true
            },
            "password":{

                passwords:true
            },
            "c_pass":{

                equalTo: "#password"
            }

        },
    });
</script>
@endsection
