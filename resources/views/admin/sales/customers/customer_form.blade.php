@extends('admin.layouts.app')
@section('title', __('Customers'))
@section('styles')
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}">
<style>
    .form-group.required .control-label:after {
        content:"*";
        color:red;
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
        <section class="content-header top-header">
            <div class="row">
                <div class="col-md-12">
                    <h2>
                        {{ __('Customer') }} /
                        <small> @if(@$action == "Edit") {{ __('Edit') }} @else {{ __('Add') }} @endif </small>
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="box-header">
                    <div class="row">
                        <div class="col-md-4">
                            <a class="skin-gray-light-btn btn save-customer-d" href="javascript:void(0)"> @if($action == "Edit") {{ __('Update') }} @else {{ __('Save') }} @endif</a>
                            <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.customers.index') }}">{{ __('Discard') }}</a>
                            @can('Customer Resend Invitation')
                                @if($action == "Edit")
                                    <a class="skin-green-light-btn btn active" onclick="resendEmail($(this))" data-model-id= "{{ Hashids::encode($model->id) }}" data-invitation-code= "{{ $model->invitation_code }}" href="javascript:void(0)">{{ __('Re-Send Invitation Email') }}</a>
                                @endif
                            @endcan
                        </div>
                        @can('Delete Customer')
                            @if(@$action == "Edit")
                            <div class="quotation-right-side">
                                <div class="btn-flat filter-btn dropdown custom-dropdown-buttons action-btn">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                    <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ __('Actions') }} <span class="caret"></span>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item delete-btn" href="#">{{ __('Delete') }}</a>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <form class="timmunity-custom-dashboard-form" id="customer-form" method="POST"
            action="{{ route('admin.customers.store') }}"
            enctype="multipart/form-data">
                <div class="box">
                    <div class="box-body pt-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="quotations-form-container">
                                    <div class="box box-success box-solid">
                                        <div class="box-header with-border">
                                            @if(@$action == "Edit")
                                            <h3 class="box-title">{{ __('Update Customer') }}</h3>
                                            @else
                                            <h3 class="box-title">{{ __('Create New Customer') }}</h3>
                                            @endif
                                            <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                        class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="col-md-12 pt-1 customer-radio-button pb-3">
                                                        <input type="radio" id="individual" name="company_type" value="1" @if (isset($model->company_type) && $model->company_type == 1) checked @endif />
                                                        <label for="individual">{{ __('Individual') }}</label>
                                                        <input type="radio" id="company" name="company_type" value="2" @if (isset($model->company_type) && $model->company_type == 2) checked @endif />
                                                        <label for="company">{{ __('Company') }}</label>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>{{ __('Title') }}</label>
                                                            <select class="form-control" name="title_id">
                                                                <option value="">---{{ __('Select a title') }}---</option>
                                                                @if ($contact_titles->count() > 0)
                                                                    @foreach ($contact_titles as $title)
                                                                        <option value="{{ $title->id }}" @if($title->id == @$model->title_id) selected="selected" @endif>
                                                                            {{ $title->title }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('Customer name') }}<small class="asterik" style="color:red">*</small></label>
                                                                <input type="text" name="name" class="form-control"
                                                                    placeholder="{{ __('Name') }}..." required
                                                                    value="{{ @$model->name }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 company_div">
                                                            <div class="form-group">
                                                                <label>{{ __('Company Name') }}</label>
                                                                <select class="form-control" name="company_id">
                                                                    <option value="">---{{ __('Select a company') }}---</option>
                                                                    @foreach($companies as $key => $value)
                                                                    <option value="{{ $value->id }}" @if($value->id == @$model->company_id) selected="selected" @endif>{{ $value->name }}</option>

                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <h4 class="col-md-12">{{ __('Address') }}</h4>
                                                        <div class="form-group col-md-4">
                                                            <input class="form-control" type="text" name="street_1"
                                                                placeholder="{{ __('Street 1...') }}" value="{{ translation( @$model->id,5,app()->getLocale(),'street_1', @$model->street_1) }}" />
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <input class="form-control" type="text" name="street_2"
                                                                placeholder="{{ __('Street 2...') }}" value="{{ translation( @$model->id,5,app()->getLocale(),'street_2', @$model->street_2) }}" />
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <input class="form-control" type="text" name="city"
                                                                placeholder="{{ __('City') }}" value="{{ translation( @$model->id,4,app()->getLocale(),'city', @$model->city) }}" />
                                                        </div>
                                                        <input type="hidden" class="form-control" name="state_id" />
                                                        {{-- <div class="form-group col-md-4">
                                                            <select class="form-control" name="state_id">
                                                                <option value="">{{ __('State') }}</option>
                                                                @if ($contact_fed_states->count() > 0)
                                                                    @foreach ($contact_fed_states as $state)
                                                                        <option value="{{ $state->id }}" @if (isset($model) && $state->id == $model->state_id) selected @endif>
                                                                            {{ $state->name }}
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div> --}}
                                                        <div class="form-group col-md-4">
                                                            <input class="form-control" type="number" name="zipcode"
                                                                placeholder="{{ __('Zip Code') }}" value="{{ @$model->zipcode }}" />
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <select class="form-control" name="country_id">
                                                                <option value="">---{{ __('Select a country *') }}---</option>
                                                                @if ($contact_countries->count() > 0)
                                                                    @foreach ($contact_countries as $country)
                                                                        <option value="{{ $country->id }}" @if (isset($model) && $country->id == $model->country_id) selected @endif>
                                                                            {{ $country->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                        {{-- <div class="form-group col-md-3">
                                                            <input class="form-control" type="text" name="vat_id"
                                                                placeholder="Tax id..." value="{{ @$model->vat_id }}" />
                                                        </div> --}}
                                                    </div>
                                                    <div class="form-group col-md-4 job_position_div">
                                                        <label>{{ __('Job Position') }}</label>
                                                        <input type="text" name="job_position" class="form-control"
                                                            placeholder="{{ __('e.g. Sale Director') }}"
                                                            value="{{ @$model->job_position }}" />
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>{{ __('Phone') }}</label>
                                                        <input type="text" name="phone" class="form-control" placeholder="{{ __('Phone') }}"
                                                            value="{{ @$model->phone }}" />
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>{{ __('Mobile') }}</label>
                                                        <input type="text" name="mobile" class="form-control"
                                                            placeholder="{{ __('Mobile') }}" value="{{ @$model->mobile }}" />
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>{{ __('Email') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="text" name="email" class="form-control" placeholder="{{ __('Email') }}"
                                                            value="{{ @$model->email }}" />

                                                        @error('email')
                                                            <div id="email-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror

                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>{{ __('Website Link') }}</label>
                                                        <input type="text" name="web_link" class="form-control"
                                                            placeholder="{{ __('e.g. timmunity.com') }}" value="{{ @$model->web_link }}" />
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>{{ __('Tags') }}</label>
                                                        <select class="form-control" name="tag_id" multiple="">
                                                            @foreach($contact_tags as $key => $value)
                                                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <input type="hidden" name="contact_addresses_ids" value="@isset($model){{ implode(',',$contact_address_ids) }}@endisset">
                                                    <input type="hidden" name="id" value="{{ @$model->id }}">
                                                    <input type="hidden" name="action" value="{{ @$action }}">

                                                </div>
                                                <!-- FILE UPLOAD -->
                                                <div class="col-md-4 pull-right">
                                                    <div class="avatar-upload form-group">
                                                    <div class="avatar-fileds hover-effect">
                                                        <div class="avatar-edit">
                                                        <input type="file" class="form-control" id="imageUpload" name="image" value="{{ old('image', $model->image ?? '')}}" />
                                                        <label for="imageUpload"></label>
                                                        </div>
                                                    </div>
                                                    <div class="avatar-preview">
                                                    <img id="imagePreview"
                                                        src="{!!checkImage(asset('storage/uploads/contact/' . Hashids::encode(@$model->id) . '/' . @$model->image),'avatar5.png')!!}" width="100%" height="100%" />
                                                            @error('image')
                                                            <div id="image-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @csrf

                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                    <!-- /.box -->
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="custom-tabs mt-3">
                                    <ul class="nav nav-tabs">
                                        @can('Contact & Addresses')
                                        <li class="active"><a data-toggle="tab" href="#contract" aria-expanded="false">{{ __('Contact & Addresses') }}</a></li>
                                        @endcan
                                        <li @can('Contact & Addresses') class="" @else class="active" @endcan><a data-toggle="tab" href="#sales-purchase" aria-expanded="false">{{ __('Sales & Purchase') }}</a></li>
                                        <li class=""><a data-toggle="tab" href="#internal-notes" aria-expanded="false">{{ __('Internal Notes') }}</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        @can('Contact & Addresses')
                                        <div id="contract" class="tab-pane fade active in pt-2">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <a type="button" class="btn skin-green-light-btn add-address-btn" data-toggle="modal"
                                                        data-target="#contact-model">
                                                        {{ __('Add') }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div id="contact-address-row-array">
                                                @isset($model->contact_addresses)
                                                    @foreach($model->contact_addresses as $c_add)
                                                        <div class="col-sm-6 col-md-4 mt-2" data-address-id="{{ $c_add->id }}">
                                                            <a href="#" data-address-id="{{ $c_add->id }}"  onClick="contact_address_edit({{ $c_add->id }})"
                                                                data-toggle="modal" data-target="#contact-model" >
                                                                <div class="customer-box">
                                                                    <div class="customer-img">
                                                                        <img id="contact-address-img" src="{!! checkImage(asset("storage/uploads/contact-address/''"), 'avatar5.png') !!}" width="100%" height="100%">
                                                                    </div>
                                                                    <div class="customer-content col-md-6">
                                                                        <h5 class="sub-heading">{{ $c_add->contact_name }}</h5>
                                                                        <span class="email">{{ $c_add->email }}</span>
                                                                        <h5 class="customer-heading phone">{{ __('Phone') }}: {{ $c_add->phone }}</h5>
                                                                        <h5 class="customer-heading mobile">{{ __('Mobile') }}: {{ $c_add->mobile }}</h5>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                @endisset
                                            </div>
                                            <!-- Contact Model -->
                                            <div class="modal fade in" id="contact-model" tabindex="-1" role="dialog"
                                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="contact-big-model modal-dialog modal-dialog-centered"
                                                    role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Contact') }}</h3>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true"><i class="fa fa-times"
                                                                        aria-hidden="true"></i></span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body ">
                                                            <!-- Form Start Here  -->
                                                            <div class="col-md-12">
                                                                <div class="clearfix mt-2">
                                                                    <div class="col-md-12 pl-0">
                                                                        <div class="col-md-12 pt-1 customer-radio-button pb-3">

                                                                            <label for="add_type">
                                                                                <input type="radio"  name="add_type" value="0">
                                                                                {{ __('Contact') }}
                                                                            </label>
                                                                            <label for="add_type">
                                                                                <input type="radio"  name="add_type" value="1">
                                                                                {{ __('Invoice Address') }}
                                                                            </label>
                                                                            <label for="add_type">
                                                                                <input type="radio"  name="add_type" value="2">
                                                                                {{ __('Delivery Address') }}
                                                                            </label>
                                                                            <label for="add_type">
                                                                                <input type="radio"  name="add_type" value="3">
                                                                                {{ __('Other Address') }}
                                                                            </label>
                                                                            <label for="add_type">
                                                                                <input type="radio"  name="add_type" value="4">
                                                                                {{ __('Private Address') }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group col-md-3 required">
                                                                        <label class="control-label">{{ __('Title') }}</label>
                                                                        <select class="form-control " name="contact_add_title_id">
                                                                            <option value="">{{ __('Select Title') }}</option>
                                                                            @if ($contact_titles->count() > 0)
                                                                                @foreach ($contact_titles as $title)
                                                                                    <option value="{{ $title->id }}">
                                                                                        {{ $title->title }}</option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group required col-md-3">
                                                                        <label class="control-label">{{ __('Contact Name') }}</label>
                                                                        <input class="form-control" type="text" name="contact_add_name">
                                                                    </div>
                                                                    <div class="form-group col-md-3 required">
                                                                        <label class="control-label">{{ __('Email') }}</label>
                                                                        <input class="form-control" type="email" name="contact_add_email">
                                                                    </div>
                                                                    <div class="form-group col-md-3 required">
                                                                        <label class="control-label">{{ __('Job Position') }}</label>
                                                                        <input class="form-control" type="text" name="contact_add_job_position">
                                                                    </div>
                                                                    <div class="form-group col-md-3 required">
                                                                        <label class="control-label" class="control-label">{{ __('Phone') }}</label>
                                                                        <input class="form-control" type="tel" name="contact_add_phone">
                                                                    </div>
                                                                    <div class="form-group col-md-3">
                                                                        <label class="control-label">{{ __('Mobile') }}</label>
                                                                        <input class="form-control" type="tel" name="contact_add_mobile">
                                                                    </div>
                                                                    <div class="form-group col-md-3 required">
                                                                        <label class="control-label">{{ __('Street 1...') }}</label>
                                                                        <input class="form-control" type="text" name="contact_add_street_1">
                                                                    </div>
                                                                    <div class="form-group col-md-3">
                                                                        <label class="control-label">{{ __('Street 2...') }}</label>
                                                                        <input class="form-control" type="text" name="contact_add_street_2">
                                                                    </div>
                                                                    <div class="form-group col-md-3 required">
                                                                        <label class="control-label">{{ __('City') }}</label>
                                                                        <input class="form-control" type="text" name="contact_add_city">
                                                                    </div>
                                                                    <div class="form-group col-md-3 required">
                                                                        <label class="control-label">{{ __('Zip Code') }}</label>
                                                                        <input class="form-control" type="text" name="contact_add_zipcode">
                                                                    </div>
                                                                    <div class="form-group col-md-3 required">
                                                                        <label class="control-label">{{ __('Country') }}</label>
                                                                        <select class="form-control" name="contact_add_country_id">
                                                                            <option value="">---{{ __('Select a country') }}---</option>
                                                                            @if ($contact_countries->count() > 0)
                                                                                @foreach ($contact_countries as $country)
                                                                                    <option value="{{ $country->id }}" >
                                                                                        {{ $country->name }}</option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group col-md-3 required">
                                                                        <label class="control-label">{{ __('State') }}</label>
                                                                        <select class="form-control" name="contact_add_state_id">
                                                                            <option value="">---{{ __('Select a state') }}---</option>
                                                                            @if ($contact_fed_states->count() > 0)
                                                                                @foreach ($contact_fed_states as $state)
                                                                                    <option value="{{ $state->id }}" >
                                                                                        {{ $state->name }}</option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                        <input type="hidden" name="contact_add_id">
                                                                    </div>
                                                                    <div class="form-group col-md-12">
                                                                        <label class="control-label">{{ __('Notes') }}</label>
                                                                        <textarea class="form-control" name="contact_add_notes"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- End Here -->
                                                        </div>
                                                        <!-- Footer model popupp -->
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">{{ __('Close') }}</button>
                                                            <button type="button" class="btn btn-danger delete-address-btn" data-id="">{{ __('Remove') }}</button>
                                                            <button type="button" class="btn btn-success">{{ __('Save changes') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endcan
                                        <!-- Sales & Purchase -->
                                        <div id="sales-purchase" @can('Contact & Addresses') class="tab-pane fade" @else class="tab-pane fade active in pt-2" @endcan>
                                            <div class="tab-form clearfix" style="padding: 10px 0px; ">
                                                <div class="row">
                                                    <!-- Gernal Tab Col No 01 -->
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <h3 class="col-sm-12">{{ __('Sales') }}</h3>
                                                            <div class="col-sm-4">
                                                                <h4>{{ __('Sales Person') }}</h4>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <div class="form-group">
                                                                    <select class="form-control" name="sales[sales_person_id]">
                                                                        <option value="">---{{ __('Select a sales person') }}---</option>
                                                                        @foreach( $salespersons as $salesperson )
                                                                            <option value="{{ $salesperson->id }}"
                                                                                    @if($action == 'Add')
                                                                                        @if(Auth::user()->id == $salesperson->id )
                                                                                            selected="selected"
                                                                                        @endif
                                                                                    @elseif($action == 'Edit')
                                                                                        @if(@$model->sales_purchase->sales_person_id == $salesperson->id )
                                                                                            selected="selected"
                                                                                        @endif
                                                                                    @endif
                                                                                >
                                                                                {{ $salesperson->firstname.' '.$salesperson->lastname }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <h4>{{ __('Price Lists') }}</h4>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <div class="form-group">
                                                                    <select class="form-control" name="sales[pricelist_id]">
                                                                        @foreach($price_lists as $ind => $price_list)
                                                                            <option value="{{ $price_list->id }}" @if( @$model->sales_purchase->pricelist->id == $price_list->id ) selected="selected" @elseif($ind == 0) selected="selected" @endif>{{ $price_list->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Gernal Tab Col No 02 -->
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <h3 class="col-md-12">{{ __('Purchase') }}</h3>
                                                            <div class="col-sm-4">
                                                                <h4>{{ __('Payment Terms') }}</h4>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <div class="form-group">
                                                                    <select class="form-control" name="sales[payment_terms]">
                                                                        <option value="">---{{ __('Select a payment term') }}---</option>
                                                                        @foreach ($payment_term as $ind =>$p_term)
                                                                            <option value="{{ $p_term->id }}" @if (isset($model) && $p_term->id == @$model->sales_purchase->payment_terms) selected @elseif($ind == 0) selected="selected" @endif>
                                                                                {{ $p_term->term_value }}
                                                                                @switch($p_term->term_type)
                                                                                    @case(1)
                                                                                        {{ __('Days') }}
                                                                                        @break
                                                                                    @case(2)
                                                                                        {{ __('Months') }}
                                                                                        @break
                                                                                    @case(2)
                                                                                        {{ __('Years') }}
                                                                                        @break
                                                                                    @default
                                                                                @endswitch
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Internal Note -->
                                        <div id="internal-notes" class="tab-pane fade clearfix">
                                            <div class="col-md-12">
                                                <textarea name="internal_notes" id="internal_notes" class="form-control"
                                                    placeholder="{{ __('Internal Note') }}"
                                                    style="height: 73px;border: none;border-bottom: 1px solid #ddd;">{{ old('internal_notes', $model->internal_notes ?? '') }}</textarea>
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
        @if(@$action == "Edit")
      @canany(['Add Note','View Log Note','Add Schedule Activity','View Schedule Activity','Send Message','View Send Messages'])
      <section class="bottom-section">
        <div class="row box">
          <div class="row activity-back-color">
            <div class="col-md-12">
                <div class="custom-tabs mt-3 mb-2">
                  <div class="row">
                        <div class="col-md-8">
                        @canany(['View Send Messages','Send Message','View Log Note','Add Note','View Schedule Activity','Add Schedule Activity'])
                          <ul class="nav nav-tabs">
                            @canany(['View Send Messages','Send Message'])
                            <li class="active"><a data-toggle="tab" href="#send_message">{{ __('Send Message') }}</a></li>
                            @endcanany
                            @canany(['View Log Note','Add Note'])
                            <li @if(!auth()->user()->can('View Send Messages') && !auth()->user()->can('Send Message')) class="active" @endif><a data-toggle="tab" href="#log_note">{{ __('Log Note') }}</a></li>
                            @endcanany
                            @canany(['View Schedule Activity','Add Schedule Activity'])
                            <li @if(!auth()->user()->can('View Send Messages') && !auth()->user()->can('Send Message') && !auth()->user()->can('View Log Note') && !auth()->user()->can('Add Note')) class="active" @endif><a data-toggle="tab" href="#schedual_activity">{{ __('Schedule Activity') }}</a></li>
                            @endcanany
                          </ul>
                          @endcanany
                        </div>
                        <div class="col-md-4 pull-right text-right follower-icons">
                           <!-- Attachments View -->
                           {!! $attachments_partial_view !!}
                           @if($is_following == 1 )
                             <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="4" id="following"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                             <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="4" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                          @else
                              <a class="followButton" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="4"id="followBtn" >{{ __('Follow') }}</a>
                                <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="4" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                          @endif
                          <a class="dropdown-toggle" href="javascript:void(0)" title="Show Followers"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;<span id="follower_counter">{{ $followers->count() }} </span></a>
                          <!-- Follower List -->
                          <ul class="follower_list" id="f_list">
                            @forelse ($followers as $follower)
                              <li><a href="{{ route('admin.contacts.edit',['contact'=> Hashids::encode($follower->contacts->id)]) }}" target="_blank">{{ $follower->contacts->name }}</a></li>
                             @empty
                             <li><div class="text-center">{{ __("Currently there's no follower") }}</div></li>
                            @endforelse
                          </ul>
                        </div>
                    </div>
                    <div class="tab-content custom-tabs-style custom-tabs-pd-set">
                      <!--  Send Messages -->
                      @canany(['Send Message','View Send Messages'])
                      <div id="send_message" class="tab-pane fade active in">
                        <div class="row tab-form pt-3">
                          <div class="row">
                            <div class="col-md-3">
                              @can('Send Message')
                              <a class="skin-green-light-btn btn" type="button" data-toggle="modal"  data-target="#send-message-model" onclick="clearMessageForm()"><i class="fa fa-paper-plane"></i>&nbsp;{{ __('Send Message') }}</a>
                              {!! $send_messages_view !!}
                              @endcan
                            </div>
                          </div>
                          @can('View Send Messages')
                          {!! $send_message_tab_partial_view !!}
                          @endcan
                        </div>
                      </div>
                      @endcanany
                      <!-- Log Note -->
                      @canany(['Add Note','View Log Note'])
                      <div id="log_note" class="tab-pane fade">
                        <div class="row tab-form pt-3">
                          <div class="row">
                            <div class="col-md-3">
                              @can('Add Note')
                              <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#log-note-model" onclick="clearNoteForm()"><i class="fa fa-plus"></i>&nbsp;{{ __('Add Note') }}</a>
                              {!! $log_notes_view !!}
                              @endcan
                            </div>
                          </div>
                          @can('View Log Note')
                          {!! $notes_tab_partial_view !!}
                          @endcan
                        </div>
                      </div>
                      @endcanany
                      <!-- Schedule Activity -->
                      @canany(['Add Schedule Activity','View Schedule Activity'])
                     <div id="schedual_activity" class="tab-pane fade">
                        <div class="row tab-form pt-3">
                          <div class="row">
                            <div class="col-md-3">
                              @can('Add Schedule Activity')
                              <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#schedule-activity-model" onclick="ClearScheduleActivity()"><i class="fa fa-clock-o"></i>&nbsp;{{ __('Add Schedule Activity') }}</a>
                              {!! $schedual_activities_view !!}
                              @endcan
                            </div>
                          </div>
                          @can('View Schedule Activity')
                          {!! $schedual_activity_tab_partial_view !!}
                          @endcan
                        </div>
                      </div>
                      @endcanany
                    </div>
                </div>
             </div>
          </div>
        </div>
      </section>
      @endcanany
    @endif
    </div>
    <form id="actionForm" action="" method="POST">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>
        function resendEmail(_context) {
            var uid = $(_context).attr('data-model-id');
            var invitation_code = $(_context).attr('data-invitation-code');
            var is_reset_password = $(_context).attr('data-reset-password');
            var fd = new FormData();
            fd.append('_token', $('input[name="_token"]').val());
            fd.append('id', uid);
            fd.append('user', 1);
            fd.append('invitation_code', invitation_code);
            fd.append('is_reset_password', is_reset_password);
            $.ajax({
                url: '{{ route('invitation.resend-email') }}',
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
                    Swal.fire("{{ __('Sent') }}", "{{ __('Password reset insruction has been sent successfully!') }}", "success");
                    }

            }
            });
        }
        @isset($model)
        deleteurl = "{{ route('admin.customers.destroy',Hashids::encode($model->id)) }}";
        @endisset
        $('body').on('click','.delete-btn', function(){
            $("#actionForm").attr('action', deleteurl);
            $("#actionForm").submit();
        });
        $('body').on('click', '.save-customer-d', function() {
            $('#customer-form').submit();
        });
        $('select[name=tag_id]').select2();

        $('body').on('click', '.add-address-btn', function() {
            clear_address_model();
            $('.delete-address-btn').hide();
            $('input[name=add_type]:checked').change();
        });
        $('body').on('change', '#company', function() {
            $('#select-company').hide();
            $('#select-title').hide();
            $('#job-position').hide();
        });
        $('input[name=add_type]:first').click()
        $('input[name=add_type]:checked').change();


        // Show/Hide field based on individual / company type
        $('body').on('click', 'input[name=company_type]',function(){
            switch($(this).val()){
                case '1':

                    $('.company_div').hide();
                    $('.job_position_div').hide();

                    break;
                case '2':
                    $('.company_div').show();
                    $('.job_position_div').show();
                    break;

            }
        })
        // Show Hide Contact Address Fields based on the address type
        $('body').on('change','input[name=add_type]',function(){
            switch($(this).val()){
                case "0":
                    $('input[name=contact_add_street_1]').parents('.form-group').hide();
                    $('input[name=contact_add_street_2]').parents('.form-group').hide();
                    $('input[name=contact_add_city]').parents('.form-group').hide();
                    $('input[name=contact_add_zipcode]').parents('.form-group').hide();
                    $('select[name=contact_add_country_id]').parents('.form-group').hide();
                    $('select[name=state_id]').parents('.form-group').hide();

                    $('input[name=contact_add_name]').parents('.form-group').show();
                    $('select[name=contact_add_title_id]').parents('.form-group').show();
                    $('input[name=contact_add_job_position]').parents('.form-group').show();
                    $('input[name=contact_add_phone]').parents('.form-group').show();
                    $('input[name=contact_add_mobile]').parents('.form-group').show();
                    $('textarea[name=contact_add_notes]').parents('.form-group').show();
                    $('input[name=contact_add_email]').parents('.form-group').show();
                    break;
                default:
                    $('select[name=contact_add_title_id]').parents('.form-group').hide();
                    $('input[name=contact_add_job_position]').parents('.form-group').hide();

                    $('input[name=contact_add_street_1]').parents('.form-group').show();
                    $('input[name=contact_add_street_2]').parents('.form-group').show();
                    $('input[name=contact_add_city]').parents('.form-group').show();
                    $('input[name=contact_add_zipcode]').parents('.form-group').show();
                    $('select[name=contact_add_country_id]').parents('.form-group').show();
                    $('select[name=state_id]').parents('.form-group').show();
                    $('input[name=contact_add_name]').parents('.form-group').show();
                    $('input[name=contact_add_phone]').parents('.form-group').show();
                    $('input[name=contact_add_mobile]').parents('.form-group').show();
                    $('textarea[name=contact_add_notes]').parents('.form-group').show();
                    $('input[name=contact_add_email]').parents('.form-group').show();
                    break;

            }

        });

        // Save / Edit Contact Address
        $('body').on('click','#contact-model .btn-success',function(){
            type = $('#contact-model input[name=add_type]:checked').val();
            contact_name = $('#contact-model input[name=contact_add_name]').val();
            email = $('#contact-model input[name=contact_add_email]').val();
            job_position = $('#contact-model input[name=contact_add_job_position]').val();
            phone = $('#contact-model input[name=contact_add_phone]').val();
            mobile = $('#contact-model input[name=contact_add_mobile]').val();
            street_1 = $('#contact-model input[name=contact_add_street_1]').val();
            street_2 = $('#contact-model input[name=contact_add_street_2]').val();
            notes = $('#contact-model textarea[name=contact_add_notes]').val();
            city = $('#contact-model input[name=contact_add_city]').val();
            zipcode = $('#contact-model input[name=contact_add_zipcode]').val();
            country_id = $('#contact-model select[name=contact_add_country_id]').val();
            title_id = $('#contact-model select[name=contact_add_title_id]').val(),
            state_id = $('#contact-model select[name=contact_add_state_id]').val();
            contact_add_id = $('#contact-model input[name=contact_add_id]').val();

            error = 0;
            switch(type){
                case "0":
                    if( !contact_name || !email || !job_position || !phone || !title_id )
                    {
                        error = 1;
                    }
                    break;
                default:
                    if( !street_1 || !city || !state_id || !country_id || !email || !phone  )
                    {
                        error = 1;
                    }
                    break;
            }
            if(error == 0){
                $.ajax({
                    url: "{{ route('admin.customer.address.save') }}",
                    data: {
                        type : $('#contact-model input[name=add_type]:checked').val(),
                        contact_name : $('#contact-model input[name=contact_add_name]').val(),
                        email : $('#contact-model input[name=contact_add_email]').val(),
                        job_position : $('#contact-model input[name=contact_add_job_position]').val(),
                        phone : $('#contact-model input[name=contact_add_phone]').val(),
                        mobile : $('#contact-model input[name=contact_add_mobile]').val(),
                        street_1 : $('#contact-model input[name=contact_add_street_1]').val(),
                        street_2 : $('#contact-model input[name=contact_add_street_2]').val(),
                        notes : $('#contact-model textarea[name=contact_add_notes]').val(),
                        city : $('#contact-model input[name=contact_add_city]').val(),
                        zipcode : $('#contact-model input[name=contact_add_zipcode]').val(),
                        country_id : $('#contact-model select[name=contact_add_country_id]').val(),
                        title_id : $('#contact-model select[name=contact_add_title_id]').val(),
                        state_id : $('#contact-model select[name=contact_add_state_id]').val(),
                        contact_add_id : $('#contact-model input[name=contact_add_id]').val(),
                        _token : $('input[name=_token]').val(),
                    },
                    type: 'POST',
                    success: function (data) {

                        if(contact_add_id == null || contact_add_id == '' || contact_add_id == 0){

                            html_card = '<div class="col-sm-6 col-md-4 mt-2" data-address-id="'+data+'">' +
                                    '<a href="#" data-address-id="' + data + '"  onClick="contact_address_edit(' + data +
                                    ')" data-toggle="modal" data-target="#contact-model" >' +
                                    '<div class="customer-box">' +
                                    ' <div class="customer-img">' +
                                    (' <img id="contact-address-img" src="{!! checkImage(asset("storage/uploads/contact-address/''"), 'avatar5.png') !!}" width="100%" height="100%">')+
                                    ' </div>' +
                                    '<div class="customer-content col-md-6">' +
                                    ' <h5 class="sub-heading">' + contact_name + '</h5>' +
                                    ' <span class="email">' + email + '</span>' +
                                    ' <h5 class="customer-heading phone">Phone:' + phone + '</h5>' +
                                    ' <h5 class="customer-heading mobile">Mobile:' + mobile + '</h5>' +
                                    '</div>' +
                                    '  </div>' +
                                    ' </a>' +
                                    ' </div>';
                            $("#contact-address-row-array").append(html_card);
                            if( $('input[name=contact_addresses_ids]').val() == null || $('input[name=contact_addresses_ids]').val() == '' ){
                                $('input[name=contact_addresses_ids]').val(data);
                            }else{
                                $('input[name=contact_addresses_ids]').val($('input[name=contact_addresses_ids]').val()+','+data);
                            }

                        }else{
                            $('div[data-address-id='+data+'] .customer-content .sub-heading').html(contact_name);
                            $('div[data-address-id='+data+'] .customer-content .email').html(email);
                            $('div[data-address-id='+data+'] .customer-content .phone').html(phone);
                            $('div[data-address-id='+data+'] .customer-content .mobile').html(mobile);
                        }

                        $("#contact-model").modal('hide');
                    },
                    complete:function(data){
                        // Hide loader container
                    }
                });
            }else{
                toastr.error('Some fields are required.');
            }
        });

        // Delete the Contact Address
        $('body').on('click','.delete-address-btn',function(){
            id = $(this).data('id');
            $.ajax({
                    url: "{{ route('admin.customer.address.delete') }}",
                    data: {
                        id : id,
                        _token : $('input[name=_token]').val(),
                    },
                    type: 'POST',
                    success: function (data) {
                        $('input[name=contact_addresses_ids]').val(data['address_ids']);
                        $('div[data-address-id='+data['id']+']').remove();
                        $("#contact-model").modal('hide');
                    },
                    complete:function(data){
                        // Hide loader container
                    }
                });
        });
        // Form Validation
        $.validator.addMethod("email", function (value, element) {
            return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
        }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
        $('#customer-form').validate({
            ignore: [],
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            rules: {
                "name":{
                    required:true
                },
                "email":{
                    required:true,
                    email:true
                },
                "country_id":{
                    required:true
                }
            },
            messages: {
                "name":{
                    required:"{{ __('Name is required') }}"
                },
                "email":{
                    required:"{{ __('Email is required') }}"
                },
                "country_id":{
                    required:"{{ __('Select Country') }}"
                }
            },
            errorPlacement: function(error, element) {
                name = $(element).attr('name');

                error.insertAfter(element);

                if($('.form-main-error:visible').length == 0)
                {
                    $('.save-btn-div').append('<small class="form-main-error">"{{ __('Some of the form fields are required') }}"</small>');
                    setTimeout(function(){
                        $('.form-main-error:visible').css('display','none');
                    },4000);
                }

                toastr.error(error);
            }
        });

        function contact_address_edit(id){
            $.ajax({
                url: "{{ route('admin.customer.address.detail') }}",
                data: {
                    id : id,
                    _token : $('input[name=_token]').val(),
                },
                type: 'POST',
                success: function (data) {
                    $('#contact-model input[name=contact_add_id]').val(data.id);
                    $('#contact-model input[name=add_type][value='+data.type+']').click();
                    $('#contact-model input[name=contact_add_name]').val(data.contact_name);
                    $('#contact-model input[name=contact_add_email]').val(data.email);
                    $('#contact-model input[name=contact_add_job_position]').val(data.job_position);
                    $('#contact-model input[name=contact_add_phone]').val(data.phone);
                    $('#contact-model input[name=contact_add_mobile]').val(data.mobile);
                    $('#contact-model input[name=contact_add_street_1]').val(data.street_1);
                    $('#contact-model input[name=contact_add_street_2]').val(data.street_2);
                    $('#contact-model textarea[name=contact_add_notes]').val(data.notes);
                    $('#contact-model input[name=contact_add_city]').val(data.city);
                    $('#contact-model input[name=contact_add_zipcode]').val(data.zipcode);
                    $('#contact-model select[name=contact_add_country_id]').val(data.country_id);
                    $('#contact-model select[name=contact_add_title_id]').val(data.title_id);
                    $('#contact-model select[name=contact_add_state_id]').val(data.state_id);

                    $('.delete-address-btn').attr('data-id',data.id);
                    $('.delete-address-btn').show();
                    $('input[name=add_type]:checked').change();
                },
                complete:function(data){
                    // Hide loader container
                }
            });
        }

        function clear_address_model(){
            $('#contact-model input[name=add_type][value="0"]').click();
            $('#contact-model input[name=contact_add_name]').val('');
            $('#contact-model input[name=contact_add_email]').val('');
            $('#contact-model input[name=contact_add_job_position]').val('');
            $('#contact-model input[name=contact_add_phone]').val('');
            $('#contact-model input[name=contact_add_mobile]').val('');
            $('#contact-model input[name=contact_add_street_1]').val('');
            $('#contact-model input[name=contact_add_street_2]').val('');
            $('#contact-model textarea[name=contact_add_notes]').val('');
            $('#contact-model input[name=contact_add_city]').val('');
            $('#contact-model input[name=contact_add_zipcode]').val('');
            $('#contact-model select[name=contact_add_country_id]').val('');
            $('#contact-model select[name=contact_add_title_id]').val('');
            $('#contact-model select[name=contact_add_state_id]').val('');
        }
    </script>
<script type="text/javascript">
// Actions URL's
var add_new_contact_url = '{{ route('admin.log.add-new-contact') }}';
var do_follow_url = '{{ route('admin.log.user-following') }}';
var do_unfollow_url = '{{ route('admin.log.user-un-follow') }}';
</script>
<script src="{{ asset('backend/dist/js/common.js') }}"></script>
@endsection
