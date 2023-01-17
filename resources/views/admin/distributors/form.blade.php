@extends('admin.layouts.app')
@section('title', __('Distributors'))
@section('styles')
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
<style>
    .auth_row{
        background: #009a71;
        padding: 10px 0px;
    }
    .auth_row label, .auth_row small{
        color:white;
    }
    .auth_row h3{
        color:white;
    }
</style>
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
    <!-- Content Header (Page header) -->
    <section class="content-header top-header">
        <div class="row">
            <div class="col-md-12">
                <h2>
                    {{ __('Distributor') }} /
                    <small>@if(@$action == "Edit") {{ __('Edit') }} @else {{ __('Add') }} @endif</small>
                </h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-6 save-btn-div">
                        <a class="skin-gray-light-btn btn save-man-d" href="javascript:void(0)">@if(@$action == "Edit") {{ __('Update') }} @else {{ __('Save') }} @endif</a>
                        <a style="margin-left: 5px;" class="skin-green-light-btn btn ml-2" href="{{route('admin.manufacturer.index')}}">{{ __('Discard') }}</a>
                        {{-- @if(@$action == "Edit")
                            <a style="margin-left: 5px;" class="skin-green-light-btn btn ml-2" href="{{route('admin.manufacturer.reset.password.link', Hashids::encode(@$model->id))}}">{{ __('Send Password Reset Link') }}</a>
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <form class="form-validate" id="distributor-form" method="POST" action="{{ route('admin.distributor.store') }}" enctype="multipart/form-data">
        <section class="content">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="action" value="{{ $action}}">
                            <input type="hidden" name="distributor_id" value="{{ @$model->id }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Distributor Name') }}<small class="asterik" style="color:red">*</small></label>
                                        <input type="text" required  name="name" class="form-control" placeholder="{{ __('Distributor Name') }}" value="{{ @$model->name }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group prefix_div">
                                        <label class="control-label">{{ __('Distributor Email') }} <small class="asterik" style="color:red">*</small></label>
                                        <input type="text" required name="email" class="form-control" placeholder="{{ __('Distributor Email') }}" value="{{ @$model->email }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Company') }}<small class="asterik" style="color:red">*</small></label>
                                        <input type="text" required  name="company" class="form-control" placeholder="{{ __('Company') }}" value="{{ @$model->company }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="control-label"> {{__('Shop URL')}}</label>
                                        <input type="url" class="form-control" name="shop_url" id="shop_url" value="{{ @$model->shop_url }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="control-label"> {{__('Password')}} @if($action == "Add")<small class="asterik" style="color:red">*</small> @endif</label>
                                        <input type="password" class="form-control" name="password" id="password">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="control-label"> {{__('Confirm Password')}} @if($action == "Add")<small class="asterik" style="color:red">*</small> @endif</label>
                                        <input type="password" class="form-control" name="confirm_password">
                                    </div>
                                </div>
                            </div>
                            <div class="row auth_row">
                                <div class="col-md-6">
                                    {{-- <label for="auth_key" class="control-label">Authentication Token</label> --}}
                                    <h3>Authentication Token</h3>
                                    <small>Add this token in the header of API call auth_key=xxxxxxxxxxxxxxx</small>
                                </div>
                                <div class="col-md-3 pt-2">
                                    <input type="text" readonly name="auth_key" value="{{ @$model->auth_key }}" class="form-control">
                                </div>
                                <div class="col-md-3 pt-2">
                                    <a href="#." class="btn btn-default" id="generate_token_btn">Generate New Token</a>
                                </div>
                            </div>
                        </div>
                        <!-- Tabs section -->
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="box-header">
                    <h3>{{ __('Distributor Products') }}</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>{{__('Product Name')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Extra Price')}}</th>
                            </tr>
                            @if(isset($model))
                                @foreach ($model->details as $product_detail)
                                <tr>
                                    <td>{{$product_detail->product->product_name}}</td>
                                    <td>
                                        <select name="product_status[{{$product_detail->product->id}}]"  class="product_status form-control">
                                            <option value="1" @if($product_detail->is_active == 1) selected="selected" @endif>Active</option>
                                            <option value="0" @if($product_detail->is_active == 0) selected="selected" @endif>In-Active</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="extra_price[{{$product_detail->product->id}}]" value="{{$product_detail->extra_price}}" step="0.01" min='0' >
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                @foreach ($products as $product)
                                <tr>
                                    <td>{{$product->product_name}}</td>
                                    <td>
                                        <select name="product_status[{{$product->id}}]"  class="product_status form-control">
                                            <option value="1" >Active</option>
                                            <option value="0" >In-Active</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="extra_price[{{$product->id}}]" step="0.01" min='0' >
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <!-- Bottom- section -->
        </section>
    </form>
    <!-- /.content -->
</div>

@endsection

@section('scripts')
<script src="{{ asset('backend/dist/js/custom.js') }}"></script>
<script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script>
      $.validator.addMethod("email", function (value, element) {
        return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
    }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
      // Mix Password Method
    $.validator.addMethod("passwords", function (value, element) {
        return this.optional(element) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }, "*Should contain at least 8 from the mentioned characters, *Password should contain at least one digit, *Should contain at least one upper & lower case letter, *Should contain special character  & numbers.");

    $('body').on('click','.save-man-d',function(){
        $('#distributor-form').submit();
    });
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
                @if($action == "Add")
                required:true,
                @endif
                passwords:true
            },
            "confirm_password":{
                @if($action == "Add")
                required:true,
                @endif
                equalTo: "#password"
            }
        },
    });
    $('body').on('click','#generate_token_btn',function(){
        $('input[name=auth_key').val(randomAuthKey(15));
    });
    function randomAuthKey(length) {
        let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789#.!';
        let str = '';
        for (let i = 0; i < length; i++) {
            str += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return str;
    };
</script>

<script src="{{ asset('backend/dist/js/common.js') }}"></script>
@endsection
