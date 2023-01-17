@extends('admin.layouts.app')
@section('title', __('Companies'))
@section('content')
@section('styles')
    <style>
        .add-company-member ul {
            list-style: none;
            padding-left: 0px;
            display: flex;
            gap: 30px
        }

        .modal-dialog.add-company-member {
            width: 1000px;
        }

        span.select2.select2-container.select2-container--default.select2-container {
            width: 100% !important;
        }

    </style>
@endsection
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
        <div class="row">
            <div class="col-md-6">
                <h2>
                    {{ __('Company') }} / @if ($action == 'Add') {{ __('Add') }} @else {{ __('Edit') }} @endif
                </h2>
            </div>
        </div>
    </section>
    <!-- Table content -->
    <section class="content">
        <form class="timmunity-custom-dashboard-form mt-2 form-validate" id="company-form"
            action="{{ route('admin.companies.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="company_id" name="id" value="{!! Hashids::encode(@$model->id) !!}">
            <input type="hidden" name="action" value="{!! $action !!}">
            <input type="hidden" name="contact_member_id" id="contact_member_id" value="">
            <input type="hidden" name="remove_contacts_ids" value="">
            <input type="hidden" name="contacts_array[]" id="contacts-array" />
            <div class="main-box box">
                <div class="row mt-3">
                    <div class="col-xs-12">
                        <div class="box box-success box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title">@if ($action == 'Add') {{ __('Add New Company') }} @else {{ __('Edit Company') }} @endif</h3>
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
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">{{ __('Company Name') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input type="text"
                                                        class="form-control @error('name') is-invalid @enderror company_name"
                                                        id="name" name="name"
                                                        value="{{ old('name', $model->name ?? '') }}" maxlength="100"
                                                        aria-describedby="name" required />
                                                    @error('name')
                                                        <div id="name-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 pt-5">
                                            <div class="row pull-right">
                                                <button type="submit" class="skin-green-light-btn btn ml-2"
                                                    id="save-company">{{ __('Save') }}</button>
                                                <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                                    href="{{ route('admin.companies.index') }}">{{ __('Discard') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- FILE UPLOAD -->
                                    <div class="col-md-3 pull-right">
                                        <div class="avatar-upload form-group">
                                            <div class="avatar-fileds hover-effect">
                                                <div class="avatar-edit">
                                                    <input type="file" class="form-control" id="imageUpload"
                                                        name="image"
                                                        value="{{ old('image', $model->image ?? '') }}" />
                                                    <label for="imageUpload"></label>
                                                </div>
                                            </div>
                                            <div class="avatar-preview">
                                                <img id="imagePreview" src="{!! checkImage(asset('storage/uploads/companies/' . Hashids::encode(@$model->id) . '/' . @$model->image), 'company.png') !!}" width="100%"
                                                    height="100%" />
                                                {{-- @error('image')
                                    <div id="image-error" class="invalid-feedback animated fadeInDown">
                                    {{ $message }}
                                    </div>
                                  @enderror --}}
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
                                <li class="active"><a data-toggle="tab"
                                        href="#general-information">{{ __('General Information') }}</a></li>
                                {{-- @canany(['Add Contacts Member', 'Contacts Member Listing']) --}}
                                <li><a data-toggle="tab" href="#add-contact">{{ __('Add Contact') }}</a></li>
                                {{-- @endcanany --}}
                            </ul>
                            <div class="tab-content custom-tabs-style custom-tabs-pd-set">
                                <!-- General Setting -->
                                <div id="general-information" class="tab-pane fade active in">
                                    <div class="row tab-form pt-3">
                                        <!-- Gernal Tab Col No 01 -->
                                        <div class="col-md-6">
                                            <div class="row">
                                                <h3 class="col-md-12">{{ __('Address') }}</h3>
                                                <div class="col-sm-6">
                                                    <label for="phone">{{ __('Street...') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input
                                                        class="form-control @error('street_address') is-invalid @enderror"
                                                        type="text" name="street_address"
                                                        value="{{ old('name', $model->street_address ?? '') }}"
                                                        placeholder="{{ __('Street...') }}" required>
                                                    @error('street_address')
                                                        <div id="street_address-error"
                                                            class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label for="phone">{{ __('Country') }}<small class="asterik" style="color:red">*</small></label>
                                                    <select
                                                        class="form-control @error('country_id') is-invalid @enderror"
                                                        name="country_id" required>
                                                        <option value="">---{{ __('Select a country') }}---</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}" @if (old('country_id', isset($model) && $country->id == $model->country_id))
                                                                selected
                                                        @endif>{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('country_id')
                                                        <div id="country_id-error"
                                                            class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-4 form-group">
                                                    <label for="phone">{{ __('City') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input class="form-control @error('city') is-invalid @enderror"
                                                        type="text" name="city"
                                                        value="{{ old('city', $model->city ?? '') }}"
                                                        placeholder="{{ __('City') }}..." required>
                                                    @error('city')
                                                        <div id="city-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <input type="hidden" class="form-control" name="state_id" />
                                                {{-- <div class="col-sm-4 form-group">
                                                    <label for="phone">{{ __('State') }}</label>
                                                    <select class="form-control @error('state_id') is-invalid @enderror"
                                                        name="state_id">
                                                        <option value="">---{{ __('Select a state') }}---</option>
                                                        @foreach ($states as $state)
                                                            <option value="{{ $state->id }}" @if (old('state_id', isset($model) && $state->id == $model->state_id))
                                                                selected
                                                        @endif>{{ $state->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('state_id')
                                                        <div id="state_id-error"
                                                            class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div> --}}
                                                <div class="col-sm-4 form-group">
                                                    <label for="phone">{{ __('Zip Code') }}</label>
                                                    <input class="form-control @error('zipcode') is-invalid @enderror"
                                                        type="number" name="zipcode"
                                                        value="{{ old('zipcode', $model->zipcode ?? '') }}"
                                                        placeholder="{{ __('Zip Code') }}..." >
                                                    @error('zipcode')
                                                        <div id="zipcode-error"
                                                            class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                {{-- <h3 class="col-md-12">{{ __('Chief Executive Officer') }}</h3> --}}
                                                <div class="col-sm-12 form-group">
                                                    <label for="phone">{{ __('Phone') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input type="tel"
                                                        class="form-control @error('phone') is-invalid @enderror"
                                                        id="phone" name="phone"
                                                        value="{{ old('phone', $model->phone ?? '') }}"
                                                        maxlength="20" aria-describedby="phone"
                                                         required
                                                        onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxLength="15"/>
                                                    @error('phone')
                                                        <div id="phone-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label for="email">{{ __('Email') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        id="email" name="email"
                                                        value="{{ old('email', $model->email ?? '') }}"
                                                        maxlength="255" aria-describedby="phone" required />
                                                    @error('email')
                                                        <div id="email-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('Website') }}</label>
                                                    <input class="form-control" type="text" name="website"
                                                        value="{{ old('website', $model->website ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Gernal Tab Col No 02 -->
                                        <div class="col-md-6">
                                            <div class="row">
                                                <h3 class="col-md-12"></h3>
                                                <div class="col-sm-12 form-group">
                                                    <label>{{ __('VAT ID') }}</label>
                                                    <input class="form-control" type="text" name="vat_id"
                                                        value="{{ old('vat_id', $model->vat_id ?? '') }}">
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('Company Registration No') }}</label>
                                                    <input class="form-control" type="text" name="registration_no"
                                                        value="{{ old('registration_no', $model->registration_no ?? '') }}">
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('Consultant No') }}</label>
                                                    <input class="form-control" type="text" name="consultant_no"
                                                        value="{{ old('consultant_no', $model->consultant_no ?? '') }}">
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('Customer No') }}</label>
                                                    <input class="form-control" type="text" name="customer_no"
                                                        value="{{ old('customer_no', $model->customer_no ?? '') }}">
                                                </div>
                                                {{-- <div class="col-sm-6 form-group">
                                                    <label for="currency_id">{{ __('Currency') }}<small class="asterik" style="color:red">*</small></label>
                                                    <select
                                                        class="form-control @error('currency_id') is-invalid @enderror"
                                                        name="currency_id" required>
                                                        <option value="">---{{ __('Select a currency') }}---</option>
                                                        @foreach ($currences as $currency)
                                                            @if($currency->is_active == 1)
                                                                <option value="{{ $currency->id }}" @if (old('currency_id', isset($model) && $currency->id == $model->currency_id))
                                                                            selected
                                                                @endif>{{ $currency->symbol .' - '. $currency->code }}
                                                            @endif
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('currency_id')
                                                        <div id="currency_id-error"
                                                            class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Kss -->
                                <div id="add-contact" class="tab-pane fade">
                                    {{-- @canany(['Add Contacts Member', 'Contacts Member Listing']) --}}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="custom-tabs mt-3">
                                                {{-- <ul class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#team-members">{{ __('Add Members') }}</a></li>
                                    </ul> --}}
                                                <div>
                                                    <div id="team-members" class="tab-pane fade in active">
                                                        {{-- @can('Add Contacts Member') --}}
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <a class="skin-green-light-btn btn" type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#contact-modalbox-d"
                                                                    onclick="resetForm()"
                                                                    id="user_list">{{ __('Add') }}</a>
                                                            </div>
                                                        </div>
                                                        {{-- @endcan
                                            @can('Contacts Member Listing') --}}

                                                        <div class="row" id="list-contact-member-d">

                                                            @if (isset($contact_members) && $contact_members->count() > 0)
                                                                @foreach ($contact_members as $key => $c_member)
                                                                    <div
                                                                        id="update-record_{{ Hashids::encode($c_member->id) }}">
                                                                        <div class="col-sm-6 col-md-4 member-parent"
                                                                            data-member-id="{{ Hashids::encode($c_member->id) }}">
                                                                            <a href="javascript:void(0)"
                                                                                class="c_id"
                                                                                onclick="UpdateContactMember(this)"
                                                                                data-id="{{ Hashids::encode($c_member->id) }}">
                                                                                <div class="customer-box">
                                                                                    <div class="customer-img">

                                                                                        <img src="{{ checkImage(asset('storage/uploads/contact/' . Hashids::encode($c_member->id) . '/' . $c_member->image), 'avatar5.png') }}"
                                                                                            alt="Contact Image"
                                                                                            width="100%" height="100%">

                                                                                    </div>
                                                                                    <div
                                                                                        class="customer-content col-md-6">
                                                                                        <h3 class="customer-heading">
                                                                                            {{ $c_member->name }}
                                                                                        </h3>
                                                                                        <span
                                                                                            class="customer-heading">{{ $c_member->email }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                            <div id="list-contact-member-array"></div>
                                                        </div>

                                                        {{-- @endcan --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- @endcanany --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <!-- Modal For Add New Member user -->
    <div class="modal fade" id="contact-modalbox-d" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog large-modal add-company-member" role="document">
            <div class="modal-content">
                <div class="sales-team-modal-generate-d">
                    <div class="modal-header">
                        <h3 class="modal-title col-sm-9" id="exampleModalLabel">
                            <span>{{ __('Add') }}:</span> {{ __('Member') }}
                        </h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-content">
                        <div class="modal-body">

                            <div class="row box-body">
                                <form id="add-member-form" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="req_action" id="req_action" value="add" />
                                    <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                                    <input type="hidden" name="id" id="contact_id" />


                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <p> <strong>{{ __('User Type') }}</strong> </p>
                                                        <ul style="list-style:none">
                                                            <li>
                                                                <input type="radio" id="admin-type" name="type"
                                                                    value="0" @isset($model) @if (@$model->type == 0) checked @endif @else checked @endif>
                                                                    <label for="admin-type">{{ __('Contact') }}</label>
                                                                </li>
                                                                <li>
                                                                    <input type="radio" id="reseller-type" name="type"
                                                                        value="3" @if (@$model->type == 3) checked @endif>
                                                                    <label
                                                                        for="reseller-type">{{ __('Reseller') }}</label>
                                                                </li>
                                                                <li>
                                                                    <input type="radio" id="customer-type" name="type"
                                                                        value="2" @if (@$model->type == 2) checked @endif>
                                                                    <label
                                                                        for="customer-type">{{ __('Customer') }}</label>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <p> <strong>{{ __('User Company') }}</strong> </p>
                                                            <ul style="list-style:none">
                                                                {{-- <li>
                                                                    <input type="radio" id="individual" name="company_type"
                                                                        value="1" @if (isset($model->company_type) && $model->company_type == 1) checked @elseif(!isset($model->company_type)) checked @endif>
                                                                    <label
                                                                        for="individual">{{ __('Individual') }}</label>
                                                                </li> --}}
                                                                <li>
                                                                    <input type="radio" id="company" name="company_type" value="2" checked>
                                                                    <label for="company">{{ __('Company') }}</label>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <p> <strong>{{ __('Active Status') }}</strong> </p>
                                                            <ul style="list-style:none">
                                                                @isset($model->user)
                                                                    <li>
                                                                        <input type="radio" id="active-status"
                                                                            name="active-status" value="1"
                                                                            @if (@$model->user->is_active == 1) checked  @endif>
                                                                        <label for="individual">{{ __('Active') }}</label>
                                                                    </li>
                                                                    <li>
                                                                        <input type="radio" id="deactive-status"
                                                                            name="active-status" value="0"
                                                                            @if (@$model->user->is_active == 0) checked  @endif>
                                                                        <label
                                                                            for="deactive-status">{{ __('In-Active') }}</label>
                                                                    </li>
                                                                @else
                                                                    <li>
                                                                        <input type="radio" id="active-status"
                                                                            name="active-status" value="1"
                                                                            @if (@$model->admin_users->is_active == 1)  checked  @endif>
                                                                        <label for="individual">{{ __('Active') }}</label>
                                                                    </li>
                                                                    <li>
                                                                        <input type="radio" id="deactive-status"
                                                                            name="active-status" value="0"
                                                                            @if (@$model->admin_users->is_active == 0)  checked  @endif>
                                                                        <label
                                                                            for="deactive-status">{{ __('In-Active') }}</label>
                                                                    </li>
                                                                @endisset
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-6 pt-1 customer-radio-button pb-3">

                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group " id="select-title">
                                                            <h4>{{ __('Title') }}</h4>
                                                            <select class="form-control" id="title_id" name="title_id"
                                                                style='color:gray' oninput='style.color="black"'>
                                                                <option value="0">---{{ __('Select a title') }} ---
                                                                </option>
                                                                @if ($contact_titles->count() > 0)
                                                                    @foreach ($contact_titles as $title)
                                                                        <option value="{{ $title->id }}"
                                                                            @if (isset($model) && $title->id == $model->title_id) selected @endif>
                                                                            {{ $title->title }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <h4>{{ __('Name') }}<small class="asterik" style="color:red">*</small></h4>
                                                            <input type="text" class="form-control" id="name" name="name"
                                                                maxlength="255" aria-describedby="name" required>
                                                            <label id="name-error" class="error"
                                                                for="name"></label>
                                                            <div style="" id="contact-name-error"
                                                                class="invalid-feedback animated  add">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4" id="job-position">
                                                        <div class="form-group">
                                                            <h4>{{ __('Designation') }}</h4>
                                                            <input type="text" class="form-control" id="job_position" name="job_position"
                                                                maxlength="255" aria-describedby="job_position" required>
                                                            <label id="job_position-error" class="error"
                                                                for="job_position"></label>
                                                            <div style="" id="contact-job_position-error"
                                                                class="invalid-feedback animated  add">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <h4 class="col-md-12">{{ __('Company Address') }}</h4>
                                                    <div class="form-group col-md-6">
                                                        <input class="form-control" type="text" id="street_1"
                                                            name="street_1" placeholder="{{ __('Street 1...') }}"
                                                            value="{{ old('street_1', translation(@$model->id, 4, app()->getLocale(), 'street_1', @$model->street_1) ?? '') }}" />
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <input class="form-control" type="text" id="street_2"
                                                            name="street_2" placeholder="{{ __('Street 2...') }}"
                                                            value="{{ old('street_2', translation(@$model->id, 4, app()->getLocale(), 'street_2', @$model->street_2) ?? '') }}" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <input class="form-control" type="text" id="city" name="city"
                                                            placeholder="{{ __('City') }}"
                                                            value="{{ old('city', translation(@$model->id, 4, app()->getLocale(), 'city', @$model->city) ?? '') }}" />
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <select class="form-control" name="state_id" id="state_id"
                                                            style='color:gray' oninput='style.color="black"'>
                                                            <option value="0">---{{ __('Select a state') }}---</option>
                                                            @if ($contact_fed_states->count() > 0)
                                                                @foreach ($contact_fed_states as $state)
                                                                    <option value="{{ $state->id }}"
                                                                        @if (isset($model) && $state->id == $model->state_id) selected @endif>
                                                                        {{ $state->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <h4>{{ __('Country') }}<small class="asterik" style="color:red">*</small></h4>
                                                        <select class="form-control" name="country_id" id="country_id" required
                                                            style='color:gray' oninput='style.color="black"'>
                                                            <option value="0">---{{ __('Select a country') }}---</option>
                                                            @if ($contact_countries->count() > 0)
                                                                @foreach ($contact_countries as $country)
                                                                    <option value="{{ $country->id }}"
                                                                        @if (isset($model) && $country->id == $model->country_id) selected @endif>
                                                                        {{ $country->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div style="" id="country_id-error"
                                                            class="invalid-feedback animated  add">
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <h4>{{ __('Zip Code') }}</h4>
                                                        <input class="form-control" type="number" name="zipcode"
                                                            id="zipcode" placeholder="{{ __('Zip Code') }}"
                                                            value="{{ old('zipcode', $model->zipcode ?? '') }}">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <h4>{{ __('Vat') }}</h4>
                                                            <input type="text"
                                                                placeholder="{{ __('e.g.') }} BE0477472701"
                                                                class="form-control" id="vat_id" name="vat_id"
                                                                value="{{ old('vat_id', $model->vat_id ?? '') }}"
                                                                maxlength="255" aria-describedby="vat_id">

                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label>{{ __('Phone') }}</label>
                                                        <input type="text" name="phone" id="phone" class="form-control"  onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxLength="15"
                                                            placeholder="{{ __('Phone') }}"
                                                            value="{{ old('phone', $model->phone ?? '') }}">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label>{{ __('Mobile') }}</label>
                                                        <input type="number" name="mobile" id="mobile" class="form-control"
                                                            placeholder="{{ __('Mobile') }}"
                                                            value="{{ old('mobile', $model->mobile ?? '') }}">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label>{{ __('Email') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="email" name="email" class="form-control" id="email"
                                                            placeholder="{{ __('Email') }}"
                                                            value="{{ old('email', $model->email ?? '') }}" required>
                                                        <label id="email-error" class="error" for="email"></label>
                                                        <div style="" id="contact-email-error"
                                                            class="invalid-feedback animated  add">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label>{{ __('Website Link') }}</label>
                                                        <input type="url" name="web_link" class="form-control" id="web_link"
                                                            placeholder="{{ __('e.g. timmunity.com') }}"
                                                            value="{{ old('web_link', $model->web_link ?? '') }}">
                                                        <div style="" id="contact-website-error"
                                                            class="invalid-feedback animated  add">
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-6">

                                                        <label>{{ __('Tags') }}</label>

                                                        <select class="form-control select2" id="tag_id" name="tag_id[]"
                                                            multiple="multiple" style='color:gray'
                                                            oninput='style.color="black"'>
                                                            @foreach ($contact_tags as $tags)
                                                                <option value="{{ $tags->id }}" @if (@$model->contact_tags)
                                                                    @foreach ($model->contact_tags as $contact_tag)
                                                                        @if ($contact_tag->id == $tags->id)
                                                                            {{ 'selected="selected"' }}
                                                                        @endif
                                                                    @endforeach
                                                            @endif>
                                                            {{ $tags->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <div class="row">

                                                            <button class="skin-gray-light-btn btn" id="contacts-save-db"
                                                                type="submit" form-action="save">{{ __('Save') }}</button>
                                                            <button type="button" class="btn btn-danger"
                                                                id="contacts-delete-db"
                                                                onclick="removeContactMember()">{{ __('Remove') }}</button>
                                                            {{-- array btns --}}
                                                            <button class="skin-gray-light-btn btn" id="contacts-save-btn"
                                                                type="button" form-action="save">{{ __('Save') }}</button>
                                                            <button class="skin-gray-light-btn btn"
                                                                id="contacts-save-update-btn" type="button"
                                                                form-action="save">{{ __('Save') }}</button>
                                                            <button type="button" class="btn btn-danger"
                                                                id="contacts-delete-btn">{{ __('Remove') }}</button>
                                                            <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2"
                                                                data-dismiss="modal">{{ __('Discard') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 ">
                                                <div class="avatar-upload form-group" id="avater-upload-db">
                                                    <div class="avatar-fileds hover-effect">
                                                        <div class="avatar-edit">
                                                            <input type='file' id="ContactmemberImageUploadDB"
                                                                name="contact_image"
                                                                class="form-control @error('image') is-invalid @enderror"
                                                                maxlength="255" aria-describedby="image" accept="image/*" />
                                                            <label for="ContactmemberImageUploadDB"></label>
                                                        </div>
                                                    </div>

                                                    <div class="avatar-preview" id="img_appendDB">
                                                        @if (@$model->admin_id == null)
                                                            <img id="ContactmemberImagePreviewDB"
                                                                src="{!! checkImage(asset('storage/uploads/contact/' . Hashids::encode(@$model->id) . '/' . @$model->image), 'avatar5.png') !!}" width="100%" height="100%"
                                                                alt='{{ asset('backend/dist/img/avatar5.png') }}'>
                                                        @else
                                                            <img id="ContactmemberImagePreviews"
                                                                src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode(@$model->admin_users->id) . '/' . @$model->admin_users->image), 'avatar5.png') !!}" width="100%" height="100%"
                                                                alt='{{ asset('backend/dist/img/avatar5.png') }}'>
                                                        @endif
                                                    </div>
                                                    <span class="text-danger" id="image-input-error"></span>
                                                    @error('image')
                                                        <div id="image-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="avatar-upload form-group" id="avater-upload-array">
                                                    <div class="avatar-fileds hover-effect">
                                                        <div class="avatar-edit">
                                                            <input type='file' id="ContactmemberImageUpload"
                                                                name="contact_image"
                                                                class="form-control @error('image') is-invalid @enderror"
                                                                maxlength="255" aria-describedby="image" accept="image/*" />
                                                            <label for="ContactmemberImageUpload"></label>
                                                        </div>
                                                    </div>

                                                    <div class="avatar-preview" id="img_append">
                                                        @if (@$model->admin_id == null)
                                                            <img id="ContactmemberImagePreview"
                                                                src="{!! checkImage(asset('storage/uploads/contact/' . Hashids::encode(@$model->id) . '/' . @$model->image), 'avatar5.png') !!}" width="100%" height="100%"
                                                                alt='{{ asset('backend/dist/img/avatar5.png') }}'>
                                                        @else
                                                            <img id="ContactmemberImagePreviews"
                                                                src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode(@$model->admin_users->id) . '/' . @$model->admin_users->image), 'avatar5.png') !!}" width="100%" height="100%"
                                                                alt='{{ asset('backend/dist/img/avatar5.png') }}'>
                                                        @endif
                                                    </div>
                                                    <span class="text-danger" id="image-input-error"></span>
                                                    @error('image')
                                                        <div id="image-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="txtId">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content -->
    </div>
@endsection
@section('scripts')
    <script>
        var actions = {!! json_encode($action) !!};
    </script>
    <script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        function ContactreadURL(input) {

            var file = document.querySelector("#ContactmemberImageUploadDB");
            if (/\.(jpe?g|png)$/i.test(file.files[0].name) === true) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#ContactmemberImagePreviewDB').attr('src', e.target.result);
                    $('#ContactmemberImagePreviewDB').hide();
                    $('#ContactmemberImagePreviewDB').fadeIn(650);
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                // console.log(file, 'file');
            }

        }
        $("#ContactmemberImageUploadDB").change(function() {
            ContactreadURL(this);
        });
        var remove_member_ids = [];
        var contact_ids = [];
        $('#add-member-form').validate({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            ignore: [],
            rules: {
                "email":{
                    required:true,
                    email:true
                },
                "name":{
                    required:true
                },
                "web_link":{
                    website:true
                },
                "country_id":{
                    required:true
                },
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
                //toastr.error(error);
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                var file_data = $('#add-member-form #ContactmemberImageUploadDB').prop('files')[0];

                var fd = new FormData();
                fd.append('action', $("#req_action").val());
                fd.append('type', $('#add-member-form input[name=type]:checked').val());
                fd.append('company_type', $('#add-member-form input[name=company_type]:checked').val());
                fd.append('status', $('#add-member-form input[name=active-status]:checked').val());
                fd.append('title_id', $('#title_id').val());
                fd.append('name', $('#add-member-form  #name').val());
                fd.append('street_1', $('#add-member-form  #street_1').val());
                fd.append('street_2', $('#add-member-form  #street_2').val());
                fd.append('city', $('#add-member-form  #city').val());
                fd.append('country_id', $('#add-member-form  #country_id').val());
                fd.append('zipcode', $('#add-member-form  #zipcode').val());
                fd.append('vat_id', $('#add-member-form  #vat_id').val());
                fd.append('phone', $('#add-member-form  #phone').val());
                fd.append('mobile', $('#add-member-form  #mobile').val());
                fd.append('email', $('#add-member-form  #email').val());
                fd.append('web_link', $('#add-member-form  #web_link').val());
                fd.append('tag_id', $('#add-member-form  #tag_id').val());
                fd.append('company_id', $('#add-member-form  #company_id').val()),
                    fd.append('state_id', $('#add-member-form  #state_id').val());
                fd.append('job_position', $('#add-member-form  #job_position').val());
                fd.append('image', file_data);
                fd.append('id', $('#contact_id').val());
                $.ajax({
                    url: '{{ route('admin.companies.contact.member') }}',
                    data: fd,
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    success: function(resp) {

                        if (resp) {

                            let _html = '';
                            // if($("#req_action").val() == "add") {
                            _html +=
                                '<div class="col-sm-6 col-md-4 member-parent" data-member-id=' +
                                resp.data.id + '>';
                            _html +=
                                '<a href="javascript:void(0)" onclick = "UpdateContactMember(this)" data-id =' +
                                resp.data.id + '>';
                            _html += '<div class="customer-box">';
                            _html += '<div class="customer-img">';
                            _html += resp.data.image;
                            _html += '</div>';
                            _html += '<div class="customer-content col-md-6">';
                            _html += '<h3 class="customer-heading">' + resp.data.name + '</h3>';
                            _html += '<span class="customer-heading">' + resp.data.email +
                            '</span>';
                            _html += '</div>';
                            _html += '</div>';
                            _html += '</a>';
                            _html += '</div>';

                            $('#update-record_' + resp.data.id).empty();
                            $('#update-record_' + resp.data.id).append(_html);
                            // $('#list-contact-member-d').append(_html);
                            contact_ids.push(resp.data.id);
                            $("input[name=contact_member_id]").val(contact_ids.join(","));
                            // }
                            // else {

                            $('div[data-member-id=' + resp.data.id +
                                '] .customer-box .customer-img').html(resp.data.image);
                            $('div[data-member-id=' + resp.data.id +
                                '] .customer-content .customer-heading-name').html(resp.data
                                .name);
                            $('div[data-member-id=' + resp.data.id +
                                '] .customer-content .customer-heading-email').html(resp.data
                                .email);
                            //    }

                            $('#contact-modalbox-d').modal('hide');
                            $("#add-member-form")[0].reset()
                        } else {
                            $('#req_action').val()
                            //  $("#firstname-error").css('display','block');
                            //  $("#lastname-error").css('display','block');
                            //  $("#firstname-error").text(resp.data.firstname);
                            //  $("#lastname-error").text(resp.data.lastname);
                            $("#email-error").text(resp.data.email);
                        }
                    },
                });
            }
        });
        $.validator.addMethod("isUnique",
            function(value, element, params) {
                var isUnique = false;
                if(value == '')
                        return isUnique;
                $.ajax({
                    url: "{{route('admin.companies.check.email')}}",
                    type : 'POST',
                    async: false,
                    data: {email : value, id:$('#company_id').val()},
                    dataType: 'json',
                    success: function(data){
                        // isUnique = data == 'true' ? true : false;
                        isUnique = data ;
                    }
                });

                return isUnique;

            },
            jQuery.validator.format("Value already in use")
    );
        $('#company-form').validate({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            ignore: [],
            rules: {
                "street_address":{
                    required:true
                },
                "country_id":{
                    required:true
                },
                "city":{
                    required:true
                },
                "phone":{
                    required:true,
                },
                "currency_id":{
                    required:true,
                },
                "email":{
                    required:true,
                    email:true,
                    isUnique:true
                },
                "name":{
                    required:true
                },
                "website":{
                    website:true
                },
            },
            // errorPlacement: function(error, element) {
            //     error.insertAfter(element);
            //     //toastr.error(error);
            // }
        });
        function UpdateContactMember(_context) {
            var id = $(_context).attr('data-id');
            $("#contact-modalbox-d").modal('show');
            $("#contacts-save-db").show();
            $("#contacts-delete-db").show();
            $("#avater-upload-db").show();

            $("#avater-upload-array").hide();
            $("#contacts-save-btn").hide();
            $("#contacts-save-update-btn").hide();
            $("#contacts-delete-btn").hide();
            $("#contacts-delete-btn").hide();
            var action = "Edit";
            var contact_update_url = "{{ route('admin.companies.contact.member.update') }}"
            var fd = new FormData();
            $.ajax({
                url: contact_update_url,
                data: {
                    id: id,
                    _token: $('input[name=_token]').val(),
                    action: action
                },
                type: 'POST',
                success: function(data) {
                    const {
                        type,
                        name,
                        email,
                        job_position,
                        phone,
                        mobile,
                        street_1,
                        street_2,
                        notes,
                        zipcode,
                        city,
                        company_id,
                        country_id,
                        title_id,
                        state_id,
                        status,
                        company_type
                    } = data.model;
                    $('#add-member-form input[name=type][value=' + type + ']').click();
                    $('#add-member-form input[name=company_type][value=' + company_type + ']').click();
                    $('#add-member-form input[name=active-status][value=' + status + ']').click();
                    $('#add-member-form input[name=name]').val(name);
                    $('#add-member-form input[name=email]').val(email);
                    $('#add-member-form input[name=job_position]').val(job_position);
                    $('#add-member-form input[name=phone]').val(phone);
                    $('#add-member-form input[name=mobile]').val(mobile);
                    $('#add-member-form input[name=street_1]').val(street_1);
                    $('#add-member-form input[name=street_2]').val(street_2);
                    $('#add-member-form textarea[name=notes]').val(notes);
                    $('#add-member-form input[name=city]').val(city);
                    $('#add-member-form input[name=zipcode]').val(zipcode);
                    $('#add-member-form select[name=country_id]').val(country_id);
                    $('#add-member-form select[name=company_id]').val(company_id);
                    $('#add-member-form select[name=title_id]').val(title_id);
                    $('#add-member-form select[name=state_id]').val(state_id);

                    $("#img_appendDB").html(data.image);
                    $("#req_action").val(data.action);
                    $("#contact_id").val(id);
                    $('.delete-contact-btn').attr('data-id', id);
                    $('.delete-contact-btn').show();
                    $('input[name=type]:checked').change();
                },
                complete: function(data) {
                    // Hide loader container
                }
            });
        }
        function removeContactMember() {
            var member_id = $("#contact_id").val();

            id = $(this).data('id');
            remove_member_ids.push(member_id);
            $("input[name=remove_contacts_ids]").val(remove_member_ids.join(", "));
            $('div[data-member-id=' + member_id + ']').remove();
            $("#contact-modalbox-d").modal('hide');
            // $.ajax({
            //     url: "{{ route('admin.companies.contact.member.delete') }}",
            //     data: {
            //         id : member_id,
            //         _token : $('input[name=_token]').val(),
            //     },
            //     type: 'POST',
            //     success: function (data) {

            //     },
            //     complete:function(data){
            //         // Hide loader container
            //     }
            // });

        }
        $.validator.addMethod("email", function (value, element) {
            return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
        }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
        $.validator.addMethod("website", function (value, element) {
            return this.optional(element) || /[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/i.test(value);
        }, "Website is invalid: Please enter a valid website address(eg: google.com).");
        $('#save-company').on('click', function(e) {
            $('#company-form').valid();
            let company_name = $('.company_name').val();
            if (actions == "Add") {
                if ($('#list-contact-member-array').find('.member-parent').length == 0 && company_name != "") {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Create at least one contact member!',
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {

                        } else if (result.isDenied) {
                            // Swal.fire('Changes are not saved', '', 'info')
                        }
                    });
                } else {

                    $('#company-form').submit();
                    // console.log('2 add');
                }

            } else if (actions == "Edit") {
                if ($('#list-contact-member-d').find('.member-parent').length == 0 && company_name != "") {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Create at least one contact member!',
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {

                        } else if (result.isDenied) {
                            // Swal.fire('Changes are not saved', '', 'info')
                        }
                    });
                } else {
                    $('#company-form').submit();
                }
            }

        })
        var multiple_contact_addresses = [];
        var contact_image = null;
        $('body').on('change', '#individual', function() {
            $('#select-company').hide();
            // $('#job-position').hide();
        });
        $('body').on('change', '#company', function() {
            $('#select-company').show();
            $('#job-position').show();
        });
        //Contact Company and Individual checked
        if (actions === 'Add') {
            $("#company:radio[value='1']").prop('checked', true);
        }
        let status = $('input[name="company_type"]:checked').val();
        if (status == 1) {
            $('#select-company').hide();
            // $('#select-title').hide();
            $('#job-position').hide();
        } else {
            $('#select-company').show();
            // $('#select-title').show();
            $('#job-position').show();
        }
        //contact file upload function
        $(document).ready(function($) {
            $('[name="contact_image"]').hide();

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#ContactmemberImagePreview').show();
                        $('#ContactmemberImagePreview').attr('src', e.target.result);
                        contact_image = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $('[name="contact_image"]').change(function() {
                readURL(this);
            });
        });
        //contact save change functio
        $('#contacts-save-btn').on('click', function() {

            var re = /^([\w\.\+]{1,})([^\W])(@)([\w]{1,})(\.[\w]{1,})+$/;
            var contact_name = $('#add-member-form #name').val();
            var country = $('#add-member-form select[name=country_id]').val();
            var email = $('#add-member-form #email').val();
            var emailFormat = re.test(email); // This return result in Boolean type
            var reweb = /[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/;
            var website = $('#add-member-form #web_link').val();
            var websiteFormat = website != '' ? reweb.test(website) : true; // This return result in Boolean type
            $('#add-member-form #name').on('keydown', function() {
                $('#contact-name-error').empty()
            });
            $('#add-member-form #email').on('keydown', function() {

                $('#contact-email-error').empty()

            });
            if (contact_name && email && emailFormat && websiteFormat && country != 0) {

                $.ajax({
                    url: '{{ route('admin.customer.check.email') }}',
                    data: {email : email},
                    type: 'POST',
                    success: function(resp) {
                        if(resp.trim() == 'false'){
                            const contact_address_obj = {
                                name: $('#add-member-form #name').val(),
                                job_position: $('#add-member-form #job_position').val(),
                                email: $('#add-member-form #email').val(),
                                type: $('#add-member-form input[name="type"]:checked').val(),
                                company_type: $('#add-member-form input[name=company_type]:checked').val(),
                                status: $('#add-member-form input[name=active-status]:checked').val(),
                                mobile: $('#add-member-form #mobile').val(),
                                phone: $('#add-member-form #phone').val(),
                                street_1: $('#add-member-form #street_1').val(),
                                street_2: $('#add-member-form #street_2').val(),
                                city: $('#add-member-form #city').val(),
                                zipcode: $('#add-member-form #zipcode').val(),
                                country_id: $('#add-member-form #country_id').val(),
                                title_id: $('#add-member-form #title_id').val(),
                                state_id: $('#add-member-form #state_id').val(),
                                tag_id: $('#add-member-form #tag_id').val(),
                                web_link: $('#add-member-form #web_link').val(),
                                vat_id: $('#add-member-form #vat_id').val(),
                                status: $('#add-member-form input[name="active-status"]').val(),
                                contact_image: contact_image ? contact_image : ''
                            }
                            multiple_contact_addresses.push(contact_address_obj);
                            contact_address_list();
                            $('#contact-modalbox-d').modal('hide');
                        }else{
                            $('#add-member-form #contact-email-error').text('Email already taken.');
                        }
                    },
                });
            } else {
                // $('#add-member-form #name-error').empty();
                if (!contact_name) {
                    $('#add-member-form #contact-name-error').empty().append('This field is required.');
                }
                if (!email) {
                    $('#add-member-form #contact-email-error').empty().append('This field is required.');
                } else if (!emailFormat) {
                    $('#add-member-form #contact-email-error').text('Please enter valid email.');
                }
                if (!websiteFormat) {
                    $('#add-member-form #contact-website-error').text('Please enter valid websites.');
                }
                if (country == 0) {
                    $('#add-member-form #country_id-error').text('Please select country.');
                }
                setTimeout(() => {
                    $('.invalid-feedback').text('');
                }, 5000);
            }

        });
        //contact edit function
        function contact_address_edit(index) {
            $('#contact-email-error').empty()
            $('#contact-name-error').empty()
            let data = multiple_contact_addresses;
            var {
                name,
                job_position,
                type,
                email,
                phone,
                mobile,
                city,
                notes,
                country_id,
                title_id,
                state_id,
                street_1,
                street_2,
                zipcode,
                contact_image
            } = data[index];
            var contact_name = $('#add-member-form #name').val(name);
            var job_position = $('#add-member-form #job_position').val(job_position);
            var email = $('#add-member-form #email').val(email);
            var mobile = $('#add-member-form #mobile').val(mobile);
            var phone = $('#add-member-form #phone').val(phone);
            var street_1 = $('#add-member-form #street_1').val(street_1);
            var street_2 = $('#add-member-form #street_2').val(street_2);
            var city = $('#add-member-form #city').val(city);
            var zipcode = $('#add-member-form #zipcode').val(zipcode);
            var notes = $('#add-member-form #notes').val(notes);
            var country_id = $('#add-member-form #country_id').val(country_id);
            var title_id = $('#add-member-form #title_id').val(title_id);
            var state_id = $('#add-member-form #state_id').val(state_id);
            var contact_image = $('#add-member-form #ContactmemberImagePreview').attr('src', "" + (contact_image ?
                contact_image : "{!! checkImage(asset('storage/uploads/contact/' . @$model->image), 'avatar5.png') !!}") + "");
            var idx = $('#txtId').val(index);
            $("#contacts-save-db").hide();
            $("#contacts-delete-db").hide();
            $("#contacts-delete-db").hide();
            $("#avater-upload-db").hide();
            $("#contacts-save-btn").hide();

            $("#contacts-save-update-btn").show();
            $("#contacts-delete-btn").show();
            $("#avater-upload-array").show();
        }
        //contact save change update function
        $('#contacts-save-update-btn').on('click', function() {


            $("#contacts-save-db").hide();
            $("#contacts-delete-db").hide();

            $("#contacts-save-btn").hide();
            $("#contacts-save-update-btn").show();
            $("#contacts-delete-btn").show();
            // let data = JSON.parse(sessionStorage.getItem('contact_address'));
            var re = /^([\w\.\+]{1,})([^\W])(@)([\w]{1,})(\.[\w]{1,})+$/;
            var contact_name = $('#add-member-form #name').val();
            var email = $('#add-member-form #email').val();
            var emailFormat = re.test(email); // This return result in Boolean type
            var reweb = /[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/;
            var website = $('#add-member-form #web_link').val();
            var websiteFormat = website != '' ? reweb.test(website) : true; // This return result in Boolean type
            $('#add-member-form #name').on('keydown', function() {
                $('#contact-name-error').empty()
            })
            $('#add-member-form #email').on('keydown', function() {

                $('#contact-email-error').empty()

            })

            if (!contact_name == "" && !email == "" && emailFormat && websiteFormat) {
                let data = multiple_contact_addresses;
                var contact_image_src = $('#ContactmemberImagePreview').attr('src');
                let name = $('#add-member-form #name').val();
                let job_position = $('#add-member-form #job_position').val();
                let email = $('#add-member-form #email').val();
                let type = $('input[name="type"]:checked').val();
                let mobile = $('#add-member-form #mobile').val();
                let phone = $('#add-member-form #phone').val();
                let street_1 = $('#add-member-form #street_1').val();
                let street_2 = $('#add-member-form #street_2').val();
                let city = $('#add-member-form #city').val();
                let zipcode = $('#add-member-form #zipcode').val();
                let notes = $('#add-member-form #notes').val();
                let country_id = $('#add-member-form #country_id').val();
                let title_id = $('#add-member-form #title_id').val();
                let state_id = $('#add-member-form #state_id').val();
                let contact_index = $('#txtId').val();
                data[contact_index].name = name;
                data[contact_index].job_position = job_position;
                data[contact_index].email = email;
                data[contact_index].type = type;
                data[contact_index].mobile = mobile;
                data[contact_index].phone = phone;
                data[contact_index].street_1 = street_1;
                data[contact_index].street_2 = street_2;
                data[contact_index].city = city;
                data[contact_index].zipcode = zipcode;
                data[contact_index].notes = notes;
                data[contact_index].country_id = country_id;
                data[contact_index].title_id = title_id;
                data[contact_index].state_id = state_id;
                data[contact_index].contact_image = contact_image_src;
                contact_address_list();
                // $("#contact-address-form")[0].reset();
                $('#contact-modalbox-d').modal('hide');
            } else {
                // $('#add-member-form #name-error').empty();
                if (!contact_name) {
                    $('#add-member-form #contact-name-error').empty().append('This field is required.');
                }
                if (!email) {
                    $('#add-member-form #contact-email-error').empty().append('This field is required.');

                } else if (!emailFormat) {
                    $('#add-member-form #contact-email-error').text('Please enter valid email.');
                }
                if (!websiteFormat) {
                    $('#add-member-form #contact-website-error').text('Please enter valid websites.');
                }
                setTimeout(() => {
                    $('.invalid-feedback').hide();
                }, 5000);
                // $('#add-member-form #email-error').empty().append();
            }

        });
        function resetForm() {
            contact_image = null;
            document.getElementById("add-member-form").reset();
            var validator = $("#add-member-form").validate();
            $('#contact-email-error').empty()
            $('#contact-name-error').empty()
            validator.resetForm();
            $("#req_action").val('add');
            $("#avater-upload-db").hide();
            $("#contacts-save-db").hide();
            $("#contacts-delete-db").hide();
            $("#contacts-delete-btn").hide();
            $("#contacts-save-update-btn").hide();
            $("#contacts-save-btn").show();
            $("#avater-upload-array").show();
            var attr_soruce = $('#ContactmemberImagePreview').attr('alt');
            $('#ContactmemberImagePreview').attr('src', attr_soruce);
            $('#add-member-form input[name=type][value="0"]').click();
            $('#add-member-form input[name=company_type][value="0"]').click();
            $('#add-member-form input[name=active-status][value="0"]').click();
            $('#add-member-form input[name=name]').val('');
            $('#add-member-form input[name=email]').val('');
            $('#add-member-form input[name=job_position]').val('');
            $('#add-member-form input[name=phone]').val('');
            $('#add-member-form input[name=mobile]').val('');
            $('#add-member-form input[name=street_1]').val('');
            $('#add-member-form input[name=street_2]').val('');
            $('#add-member-form textarea[name=notes]').val('');
            $('#add-member-form input[name=city]').val('');
            $('#add-member-form input[name=zipcode]').val('');
            $('#add-member-form select[name=country_id]').val('0');
            $('#add-member-form select[name=title_id]').val('0');
            $('#add-member-form select[name=state_id]').val('0');
            $('#add-member-form input[name=vat_id]').val('');
            $('#add-member-form input[name=tag_id]').val('');
        }
        //contact list function
        function contact_address_list() {
            var obj_str = JSON.stringify(multiple_contact_addresses)
            if (multiple_contact_addresses) {
                $('#list-contact-member-array').empty().append();
                $('#list-contact-member-array').append(multiple_contact_addresses.map((contact, idx) => {
                    var {
                        name,
                        email,
                        phone,
                        mobile,
                        contact_image
                    } = contact;
                    return '<div class="col-sm-6 col-md-4 member-parent">' +
                        '<a href="#" data-address-id="' + idx + '"  onClick="contact_address_edit(' + idx +
                        ')" data-toggle="modal" data-target="#contact-modalbox-d" >' +
                        '<div class="customer-box">' +
                        ' <div class="customer-img">' +
                        (contact_image ? ' <img id="contact-address-images" src="' + contact_image +
                            '" width="100%" height="100%">' :
                            ' <img id="contact-address-img" src="{!! checkImage(asset("storage/uploads/contact-address/''"), 'avatar5.png') !!}" width="100%" height="100%">'
                            ) +
                        ' </div>' +
                        '<div class="customer-content col-md-6">' +
                        ' <h3 class="customer-heading">' + name + '</h3>' +
                        ' <span class="customer-heading">' + email + '</span>' +
                        '</div>' +
                        '  </div>' +
                        ' </a>' +
                        ' </div>'
                }));
                $("#contacts-array").val(obj_str);
            }
        }
        //contact remove function
        $('#contacts-delete-btn').on('click', function() {
            let contact_index = $('#txtId').val();
            Swal.fire({
                title: 'Are you sure?',
                text: "You will not be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {

                if (result.isConfirmed) {
                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                    multiple_contact_addresses.splice(contact_index, 1);
                    $('#contact-modalbox-d').hide();
                    contact_address_list();

                }
            });

        });
        $('.select2').select2();
        $('#cert_url').bind('change', function() {
            var filename = $("#cert_url").val();
            if (/^\s*$/.test(filename)) {
                $(".file-upload").removeClass('active');
                $("#noFile").text("No file chosen...");
            } else {
                $(".file-upload").addClass('active');
                $("#noFile").text(filename.replace("C:\\fakepath\\", ""));
            }
        });
    </script>

    <script src="{{ asset('backend/dist/js/common.js') }}"></script>
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
@endsection
