@extends('frontside.layouts.app')
@section('title') {{ __('User Profile') }} @endsection
@section('style')
<style>
    .error{
        color:red;
    }
    .form-control.error{
        border-color:red;
    }
    /*Avatar image uplaod*/
    .avatar-upload {
    position: relative;
    max-width: 130px;
    margin: 50px auto;
    }
    .member-image.avatar-upload {
    position: relative;
    max-width: 130px;
    margin: 25px auto;
    }
    .avatar-fileds.hover-effect {
        position: absolute;
        z-index: 1;
        top: 0px;
        background: #009a71cc;
        width: 100%;
        display: flex;
        column-gap: 72px;
    }
    .avatar-upload .avatar-edit input {
    display: none;
    }
    .avatar-fileds{
    opacity: 0;
    transition: 1s;
    }
    .avatar-upload:hover .avatar-fileds{
    opacity: 1;
    }
    .avatar-upload .avatar-edit input + label {
    display: inline-block;
    width: 34px;
    height: 34px;
    margin-bottom: 0;
    border-radius: 100%;
    border: 1px solid transparent;
    /*box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);*/
    cursor: pointer;
    font-weight: normal;
    transition: all 0.2s ease-in-out;
    }
    .avatar-upload .avatar-edit input + label:hover {
    /*background: #f1f1f1;*/
    /*border-color: #d6d6d6;*/
    color: #fff;
    }
    .avatar-dlt{
    position: relative;
    top: 10px;
    }
    .avatar-upload .avatar-edit input + label:after {
    content: "\f040";
    font-family: "FontAwesome";
    color: #000;
    position: absolute;
    top: 10px;
    left: 60px;
    right: 0;
    margin: auto;
    }
    .avatar-upload .avatar-preview {
    width: 130px;
    height: 130px;
    position: relative;
    border: 2px solid #f8f8f8;
    }
    .avatar-upload .avatar-preview > div {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    }
    /*----------End----------*/
</style>
@endsection
@section('content')
<div class="row dark-green div-breadcrumbs" style="background: #009a71; color: white; padding: 10px;">
    <div class="container">
        <div>
            @if(@Auth::user()->contact->type == 3)
            <a style="color:white;font-weight:500;" href="{{ route('frontside.reseller.dashboard')  }}">{{ __('Dashboard') }}</a> /
            @else
            <a style="color:white;font-weight:500;" href="{{ route('user.dashboard') }}">{{ __('Dashboard') }}</a> /
            @endif
            {{ __('User Profile') }}
        </div>
    </div>
</div>
<section class="content-section" id="account-page">
    <div class="container">
        <div class="mt-4 row bottom-space">
            <div class="container">
                <form id="profile_form" action="{{ route('user.dashboard.profile') }}" method="post" autocomplete="off" enctype="multipart/form-data">
                    <div class="col-md-2">
                        <div class="avatar-upload form-group">
                              <div class="avatar-fileds hover-effect">
                                <div class="avatar-edit">
                                  <input type="file" class="form-control" id="imageUpload" name="image" value="{{ old('image', Auth::user()->contact->image ?? '')}}" />
                                  <label for="imageUpload"></label>
                                </div>
                              </div>
                              <div class="avatar-preview">
                              <img id="imagePreview1"
                                 src="{!! checkImage(asset('storage/uploads/contact/' . Hashids::encode(Auth::user()->contact->id) . '/' . Auth::user()->contact->image), 'avatar5.png') !!}" width="100%" height="100%" />
                                    @error('image')
                                    <div id="image-error" class="invalid-feedback animated fadeInDown">
                                    {{ $message }}
                                    </div>
                                  @enderror
                              </div>
                            </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label">{{ __('Name') }}<small class="asterik" style="color:red">*</small></label>
                                            <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}">
                                            @error('name')
                                            <div id="name-error" class="invalid-feedback animated fadeInDown">
                                                {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="control-label">{{ __('Email') }}</label>
                                            <div style="position: relative;">
                                                <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                                                @if(Auth::user()->email_verified_at == null)
                                                  <span class="inside-unverified-badge">{{ __('Unverified') }}</span>
                                                @else
                                                  <span class="inside-verified-badge">{{ __('Verified') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone" class="control-label">{{ __('Phone') }}<small class="asterik" style="color:red">*</small></label>
                                            <input type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57' name="phone" class="form-control" value="{{ @$contact->phone }}">
                                            @error('phone')
                                            <div id="phone-error" class="invalid-feedback animated fadeInDown">
                                                {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="street_1" class="control-label">{{ __('Street') }}<small class="asterik" style="color:red">*</small></label>
                                            <input type="text" name="street_1" class="form-control" value="{{ @$contact->street_1 }}">
                                            @error('street_1')
                                            <div id="street_1-error" class="invalid-feedback animated fadeInDown">
                                                {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="city" class="control-label">{{ __('City') }}<small class="asterik" style="color:red">*</small></label>
                                            <input type="text" name="city" class="form-control" value="{{ @$contact->city }}">
                                            @error('city')
                                            <div id="city-error" class="invalid-feedback animated fadeInDown">
                                                {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="zipcode" class="control-label">{{ __('Zip Code') }}</label>
                                            <input type="text" class="form-control" autocomplete="none" name="zipcode" value="{{isset($contact->zipcode)?$contact->zipcode:''}}">
                                            @error('zipcode')
                                                <div id="zipcode-error" class="invalid-feedback animated fadeInDown">
                                                    {{ $message }}</div>
                                            @enderror

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="country_id" class="control-label">{{ __('Country') }}<small class="asterik" style="color:red">*</small></label>
                                            <select name="country_id" class="select2 form-control">
                                                <option value="">{{ __('Select Country') }}</option>
                                                @if ($contact_countries->count() > 0)
                                                    @foreach ($contact_countries as $country)
                                                        <option value="{{ $country->id }}" @if(@$contact->country_id == $country->id) selected="selected" @endif>{{ $country->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('country_id')
                                                <div id="country_id-error" class="invalid-feedback animated fadeInDown">
                                                    {{ $message }}</div>
                                            @enderror

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <small class="col-md-12">{{ __('Enter new password to update the password') }}</small>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="control-label">{{ __('New Password') }}</label>
                                            <input type="password" class="form-control" name="new_password" id="password" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="control-label">{{ __('Confirm Password') }}</label>
                                            <input type="password" name="confirm_password" class="form-control" id="" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="btn btn-primary" type="submit" value="{{ __('Update') }}">
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                </form>

            </div>
        </div>
    </div>
</section>
@endsection
@section('script')
<script>
    $.validator.addMethod("email", function (value, element) {
        return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
    }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
      // Mix Password Method
    $.validator.addMethod("password", function (value, element) {
        return this.optional(element) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }, "{{ __('*Password should contain at least one digit, *Should contain at least one upper & lower case letter,*Should contain at least 8 from the mentioned characters, *Should contain special character  & numbers.')}}");

    $('#profile_form').validate({
        ignore: [],
        rules: {
            "title_id": {
                required: true
            },
            "name":{
                required:true
            },
            "phone":{
                required:true
            },
            "city":{
                required:true
            },
            "street_1":{
                required:true
            },
            "country_id":{
                required:true
            },
            "new_password" : {
                minlength : 5,
                password:true
            },
            "confirm_password" : {
                minlength : 5,
                equalTo : "#password"
            }
        },
        messages: {
            "name":{
                required:"{{ __('Name is required') }}"
            },
            "phone":{
                required:"{{ __('Phone is required') }}"
            },
            "city":{
                required:"{{ __('City is required') }}"
            },
            "street_1":{
                required:"{{ __('Street is required') }}"
            },
            "country_id":{
                required:"{{ __('Country is required') }}"
            },
        },
    });
</script>
<script type="text/javascript">

      function readURL(input) {
        var file = document.querySelector("#imageUpload");
        if (/\.(jpe?g|png)$/i.test(file.files[0].name) === true) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview1').attr('src', e.target.result);
                // $('#imagePreview1').attr('src', '');
                // $('#imagePreview1').hide();
                // $('#imagePreview1').fadeIn(650);
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            console.log(file, 'file');
        }

    }
    $("#imageUpload").change(function () {
        readURL(this);
    });
</script>
@endsection
