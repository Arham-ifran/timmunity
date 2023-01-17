@extends('admin.layouts.app')
@section('title', __('Manufacturers'))
@section('styles')
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #499a72;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
        color:white;
    }
    #imagePreview{
        margin:auto;
    }
    /* div#search-suggestion { */
    ul#search-suggestion {
        background: white;
        overflow: auto;
        max-height: 100px;
    }
    /* div#search-suggestion ul li { */
    ul#search-suggestion li {
        line-height: 25px;
        border-bottom: 1px solid #ccc;
        padding: 2px 5px;
    }
    /* div#search-suggestion ul li:hover { */
    ul#search-suggestion li:hover {
        background: #009a71;
        color: white;
    }
    .form-control.error{
        border: 1px solid red;
    }
    .eccomerce_image_div {
        position: relative;
    }
    .eccomerce_image_div img {
        object-fit: contain;
        width: 100%;
        border: 1px solid #009a71;
    }
    .eccomerce_image_div span {
        content: 'x';
        position: absolute;
        top: 5px;
        right: 20px;
        background: white;
        color: red;
        width: 15px;
        height: 15px;
        line-height: 15px;
        text-align: center;
        border-radius: 50%;
        border: 1px solid red;
        cursor: pointer;
    }
    .eccomerce_image_div span:hover {
        background: red;
        color: white;
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
                    {{ __('Manufacturer') }} /
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
                        @if(@$action == "Edit")
                            <a style="margin-left: 5px;" class="skin-green-light-btn btn ml-2" href="{{route('admin.manufacturer.reset.password.link', Hashids::encode(@$manufacturer_details->id))}}">{{ __('Send Password Reset Link') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-validate" id="manufacturer-form" method="POST" action="{{ route('admin.manufacturer.store') }}" enctype="multipart/form-data">
                            <div class="custom-tabs mt-3">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a data-toggle="tab" id="ManufacturerInformationBtn" href="#manufacturer-information">{{ __('Manufacturer Information') }}</a>
                                    </li>
                                    <li >
                                        <a data-toggle="tab" id="MembersInfoBtn" href="#members-info">{{ __('Members Information') }}</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div id="manufacturer-information" class="tab-pane fade in active">
                                        <input type="hidden" name="action" value="{{ $action}}">
                                        <input type="hidden" name="manufacturer_id" value="{{ @$manufacturer_details->id }}">
                                        @csrf
                                        <div class="row pt-3">
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">{{ __('Manufacturer Name') }}<small class="asterik" style="color:red">*</small></label>
                                                            <input type="text" required  name="manufacturer_name" class="form-control" placeholder="{{ __('Manufacturer Name') }}" value="{{ @$manufacturer_details->manufacturer_name }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group prefix_div">
                                                            <label class="control-label">{{ __('Manufacturer Email') }} </label>
                                                            <input type="text" name="manufacturer_email" class="form-control" placeholder="{{ __('Manufacturer Email') }}" value="{{ @$manufacturer_details->manufacturer_email }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">{{ __('Company') }}<small class="asterik" style="color:red">*</small></label>
                                                            <input type="text" required  name="company" class="form-control" placeholder="{{ __('Company') }}" value="{{ @$manufacturer_details->company }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">{{ __('Street Address') }}<small class="asterik" style="color:red">*</small></label>
                                                            <input type="text" required  name="street_address" class="form-control" placeholder="{{ __('Street Address') }}" value="{{ @$manufacturer_details->street_address }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">{{ __('City') }}<small class="asterik" style="color:red">*</small></label>
                                                            <input type="text" required  name="city" class="form-control" placeholder="{{ __('City') }}" value="{{ @$manufacturer_details->city }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">{{ __('State') }}</label>
                                                            <input type="text" name="state" class="form-control" placeholder="{{ __('State') }}" value="{{ @$manufacturer_details->state }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">{{ __('Zip Code') }}</label>
                                                            <input type="text" name="zipcode" class="form-control" placeholder="{{ __('Zip Code') }}" value="{{ @$manufacturer_details->zipcode }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">{{ __('Country') }}<small class="asterik" style="color:red">*</small></label>
                                                            <select class="form-control" required="required" name="country_id" style='color:gray' oninput='style.color="black"'>
                                                                <option value="">---{{ __('Select a country') }}---</option>
                                                                @if ($contact_countries->count() > 0)
                                                                    @foreach ($contact_countries as $country)
                                                                        <option value="{{ $country->id }}" @if (isset($manufacturer_details) && $country->id == $manufacturer_details->country_id) selected="selected" @endif>
                                                                            {{ $country->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">{{ __('Website') }}</label>
                                                            <input type="text" name="website" class="form-control" placeholder="{{ __('Website') }}" value="{{ @$manufacturer_details->website }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="" class="control-label"> {{__('Manufacturer Number')}}</label>
                                                            <input type="text" class="form-control" name="manufacturer_number" id="manufacturer_number" value="{{ @$manufacturer_details->manufacturer_number }}">
                                                        </div>
                                                    </div>
                                                
                                                    @if(@$manufacturer_details->associated_manufacturer_id)
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="" class="control-label"> {{__('Role')}} </label>
                                                            <input type="text" class="form-control" name="role" id="role" value="{{ @$manufacturer_details->role }}">
                                                        </div>
                                                    </div>
                                                    @endif
                                                    {{-- <div class="col-md-4">
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
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <!-- FILE UPLOAD -->
                                            <div class="col-md-4 pull-right">
                                                <div class="avatar-upload form-group">
                                                <div class="avatar-fileds hover-effect">
                                                    <div class="avatar-edit">
                                                    <input type="file" class="form-control" id="imageUpload" name="image" value="{{ old('product_image', @$model->image ?? '')}}" />
                                                    <label for="imageUpload"></label>
                                                    </div>
                                                </div>
                                                <div class="avatar-preview">
                                                <img id="imagePreview"
                                                    src="{!!checkImage(asset('/storage/uploads/manufacturer/'.Hashids::encode(@$model->id).'/'.@$model->image),'placeholder-products.jpg')!!}" width="100%" height="100%" />
                                                       
                                                  @error('image')
                                                        <div id="image-error" class="invalid-feedback animated fadeInDown">
                                                        {{-- {{ $message }} --}}
                                                        </div>
                                                    @enderror
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="members-info" class="tab-pane fade">
                                        <div class="row pt-3">
                                            <div class="col-md-12">
                                                <p id="add_member" data-toggle="modal" data-target="#memberModal"  class="btn btn-primary">{{__('Add new member')}}</p>
                                            </div>
                                        </div>
                                        <div id="members_area" class="row pt-3">
                                            <table id="members_table" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr role="row">
                                                        <th>{{ __('Member Name') }}</th>
                                                        <th>{{ __('Member Email') }}</th>
                                                        <th>{{ __('Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @isset($manufacturer_details)
                                                    @foreach($manufacturer_details->members as $member)
                                                        <tr>
                                                            <td>{{$member->manufacturer_name}}</td>
                                                            <td>{{$member->manufacturer_email}}</td>
                                                            <td>
                                                                <div style="display:inline-flex">
                                                                    <a class="btn btn-primary btn-icon" href="{{ route('admin.manufacturer.edit',Hashids::encode($member->id)) }}" title="Edit"><i class="fa fa-pencil"></i></a>
                                                                    <a class="btn btn-default btn-icon ml-2" href="{{ route('admin.manufacturer.delete',Hashids::encode($member->id)) }}"  title="Delete"><i class="fa fa-trash"></i></a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    @endisset
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="member_ids" @isset($manufacturer_details)value="{{ implode(',',$manufacturer_details->member_ids) }}"@endisset>
                        </form>
                    </div>
                    <!-- Tabs section -->
                </div>
            </div>
        </div>
         <!-- Bottom- section -->

    </section>
    <div class="modal fade" id="memberModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{ __('Add Member') }}</h4>
                </div>
                <div class="modal-body">
                    <form action="" id="member_form" >
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ __('Member Name') }}<small class="asterik" style="color:red">*</small></label>
                                    <input type="text" required  name="member_name" class="form-control" placeholder="{{ __('Member Name') }}" value="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group prefix_div">
                                    <label class="control-label">{{ __('Member Email') }} <small class="asterik" style="color:red">*</small></label>
                                    <input type="text" required name="member_email" class="form-control" placeholder="{{ __('Member Email') }}" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="control-label"> {{__('Role')}}</label>
                                    <input type="text" class="form-control" name="member_role" id="member_role">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="control-label"> {{__('Password')}} <small class="asterik" style="color:red">*</small></label>
                                    <input type="password" class="form-control" name="member_password" id="member_password">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="control-label"> {{__('Confirm Password')}} <small class="asterik" style="color:red">*</small></label>
                                    <input type="password" class="form-control" name="member_confirm_password">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <p type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</p>
                    <p id="member_add_btn" class="btn btn-primary">{{ __('Add Member') }}</p>
                </div>
            </div>
        </div>
    </div>
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
        $('#manufacturer-form').submit();
    });
    $('#manufacturer-form').validate({
        ignore: [],
        rules: {
            "manufacturer_name":{
                required:true
            },
            "company":{
                required:true
            }
        },
    });
    $('#member_form').validate({
        ignore: [],
        rules: {
            "member_name":{
                required:true
            },
            "member_email":{
                required:true,
                email:true
            },
            "member_password":{
                required:true,
                passwords:true
            },
            "member_confirm_password":{
                required:true,
                equalTo: "#member_password"
            },
        },
    });

    $("#member_add_btn").on('click',function(){
        if($('#member_form').valid()){

            member_name = $('[name=member_name]').val();
            member_email = $('[name=member_email]').val();
            member_password = $('[name=member_password]').val();
            member_role = $('[name=member_role]').val();

            $.ajax({
                url: '{{ route("admin.manufacturer.add.member") }}',
                type: 'POST',
                data: {
                    _token : "{{ csrf_token() }}",
                    member_name : member_name,
                    member_email : member_email,
                    member_password : member_password,
                    role : member_role,
                },
                success: function (data) {
                    if(data != 'false')
                    {
                        member_ids = $('[name=member_ids]').val();
                        if(member_ids == ''){
                            $('[name=member_ids]').val(data.id);
                        }else{
                            $('[name=member_ids]').val(member_ids+','+data.id);
                        }
                        edit_route  = '{{route("admin.manufacturer.edit",":id")}}';
                        edit_route = edit_route.replace(":id", data.hashid);
                        delete_route  = '{{route("admin.manufacturer.delete",":id")}}';
                        delete_route = delete_route.replace(":id", data.hashid);
                        row = '<tr>';
                            row += '<td>'+data.manufacturer_name+'</td>';
                            row += '<td>'+data.manufacturer_email+'</td>';
                            row += '<td>';
                                row += '<div style="display:inline-flex">';
                                    row += '<a class="btn btn-primary btn-icon" target="_blank" href="'+edit_route+'" title="Edit"><i class="fa fa-pencil"></i></a>';
                                    row += '<a class="btn btn-default btn-icon ml-2" target="_blank" href="'+delete_route+'"  title="Delete"><i class="fa fa-trash"></i></a>';
                                row += '</div>';
                            row += '</td>';
                        row += '</tr>';

                        $('#members_table tbody').append(row);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Member added. Kindly save the manufacturer to reflect the change',
                        });
                        $('#memberModal').modal('hide');
                        $('[name=member_name]').val('');
                        $('[name=member_email]').val('');
                        $('[name=member_password]').val('');
                        $('[name=member_confirm_password]').val('');
                    }
                    else
                    {
                        Swal.fire({
                            icon: 'error',
                            title: 'Opss',
                            text: 'Member/Manufacturer already exists',
                        });
                    }
                },
                complete:function(data){
                }
            })
        }
    });


</script>

<script src="{{ asset('backend/dist/js/common.js') }}"></script>
@endsection
