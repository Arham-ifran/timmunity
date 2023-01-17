@extends('frontside.layouts.app')
@section('title') {{ __('Voucher Redeem Page') }} @endsection
@section('body_class') cart-page @endsection
@section('style')
    <link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .row.cloud-row {
            margin-top: 20px;
        }
        #voucher-div{
            background: #009b72;
            padding: 10px;
            color: white;
        }
        .container.redeem-container {
            padding: 5% 0px;
        }
        label.error {
            font-style: normal;
            background: white;
            font-weight: 600;
            font-size: 12px !important;
        }
 
    </style>
@endsection
@section('content')
    <div class="container redeem-container">
        <div class="row cloud-row pb-3">
            <div class="col-md-2"></div>
            <div class="col-md-8 text-center">
                <h3 class="voucher_heading pt-0 mt-0">{{__('Welcome to TIMmunity Voucher Redeem')}}</h3>
                <p class="">{{ __('Please redeem your voucher key here and enjoy the amazing discounts with TIMmunity.') }}</p>
            </div>
            <div class="col-md-2"></div>
        </div>
        <div class="row">
            <div class="checkout-des">
                <form id="redeem_form" action="{{ route('voucher.generic-redeem.post') }}" method="POST">
                    @csrf
                    <div class="col-md-2"></div>
                    <div class="form-group col-md-8 ">
                        <div id="voucher-div" class="text-center">
                            <label for="voucher_code">{{ __('Have a Voucher Code? Click below button to Apply Voucher Code.') }}</label>
                            <input type="text" class="form-control text-center" name="voucher_code" placeholder="Voucher Code">
                        </div>
                        <div class="checkbox">
                            <p ><strong> {{__('Notice of premature expiration of the right of revocation')}}</strong></p>
                            <label>
                                <input type="checkbox" value="one" name="accept">
                                {{__('You agree and expressly request that TIMmunity GmbH start the execution of the ordered service before the end of the revocation period. You are aware that I lose my right of revocation upon complete fulfillment of the contract by TIMmunity GmbH. Once the voucher code has been redeemed and the licence key has been sent to you, the revocation is no longer possible')}}
                            </label>
                            <label for="accept"></label>
                        </div>
                    </div>
                    @if(!Auth::user())
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <p class="text-center"><strong>{{__('You are not logged in. Kindly')}} <a href="{{ route('login') }}">{{__('login')}}</a> {{__('or fill the following information to redeem the voucher')}}</strong></p>
                                <div class="form-group row has-feedback">
                                    <div class="col-md-6">
                                        <label for="name" class="control-label">{{__('Full Name')}}<small class="asterik" style="color:red">*</small></label>
                                        <input type="text" id="name" name="name" class="form-control" placeholder="David Beckham">
                                        @error('name')
                                            <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="control-label">{{__('Email')}}<small class="asterik" style="color:red">*</small></label>
                                        <input type="email" id="email" name="email" class="form-control" placeholder="email@company.com">
                                        @error('email')
                                            <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row has-feedback">
                                    <div class="col-md-12">
                                        <label for="country_id" class="control-label">{{ __('Select Country') }}<small class="asterik" style="color:red">*</small></label>
                                        <select name="country_id" id="country_id" class="form-control @error('country_id') is-invalid @enderror">
                                            <option value="">{{ __('Select Country') }}</option>
                                            @foreach($countries as $key => $country)
                                                <option value="{{ $country->id }}" {{old ('country_id') == $country->id ? 'selected' : ''}}>{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                            <div id="country_id-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row has-feedback" >
                                    <div class="form-group row mb-0">
                                        <label for="new_account" class="control-label col-md-12">
                                            <input type="checkbox" name="new_account" id="">
                                            {{__('Create a new account')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row has-feedback password_group" style="display:none">
                                    <div class="col-md-6">
                                        <label for="password" class="control-label">{{__('Password')}}<small class="asterik" style="color:red">*</small></label>
                                        <input type="password" id="password" name="password" class="form-control" placeholder="xxxxxxx">
                                        @error('password')
                                            <div id="password-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password_confirm" class="control-label">{{__('Confirm Password')}}<small class="asterik" style="color:red">*</small></label>
                                        <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="xxxxxxx">
                                        @error('password_confirm')
                                            <div id="password_confirm-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                    <div class="col-md-2"></div>
                    <div class="form-group col-md-12 text-center">
                        <div class="form-action-btn btn-line">
                            <button class="btn btn-primary btn-lg ">{{__('Redeem Voucher')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('script')
<script>
    $('body').on('change','[name=new_account]',function(){
        if($(this).is(':checked'))
        {
            $('.password_group').show();
        }else{
            $('.password_group').hide();
        }
    });

    $.validator.addMethod("email", function (value, element) {
        return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
    }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
      // Mix Password Method
    $.validator.addMethod("passwords", function (value, element) {
        return this.optional(element) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }, "{{ __('*Password should contain at least one digit, *Should contain at least one upper & lower case letter,*Should contain at least 8 from the mentioned characters, *Should contain special character  & numbers.')}}");

    $('#redeem_form').validate({
        rules: {
            "accept": {
                required:true
            },
            "voucher_code":{
                required:true
            },
            "name":{
                required:true
            },
            "email":{
                required:true,
                email:true
            },
            "country_id":{
                required:true
            },
            "password":{
                required:true,
                passwords:true,
            },
            "password_confirm":{
                required:true,
                equalTo : "#password"
            }
        },
        messages: {
            "accept": {
                required: "{{__('Kindly accept the terms to redeem the voucher')}}"
            },
            "password_confirm":{
                equalTo: "{{__('The password must match')}}"
            }
        },
        errorPlacement: function(error, element) {
            name = $(element).attr('name');
            // $(element).css('border', '1px solid red')
            if(name == "accept"){
                $(element).parent().parent().append(error)
            }else{
                // $(element).append(error)
                error.insertAfter(element);
            }
        },
    });

</script>
@endsection
