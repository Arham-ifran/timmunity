@extends('admin.layouts.app')
@section('title', __('Contacts'))
@section('styles')
    <link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}">
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
                        {{ __('Contact') }} / @if ($action == 'Add') {{ __('Add') }} @else {{ __('Edit') }} @endif
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="box-header">
                    <div class="row">

                        <div class="col-md-12 text-center">
                            <div class="quotation-right-side content-center">

                                @if ($action == 'Edit')
                                    <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                                        <i class="fa fa-cog" aria-hidden="true"></i>
                                        <a class="dropdown-toggle" type="button" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ __('Action') }} <span class="caret"></span>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="javascript:void(0);"
                                                data-contact="{{ @$model->id }}" id="contact-delete-btn">
                                                {{ __('Delete') }}
                                            </a>
                                            <a class="dropdown-item" id="duplicate-contact-d" href="javascript:void(0);"
                                                data-id="{{ Hashids::encode(@$model->id) }}">{{ __('Duplicate') }}</a>
                                            @if (@$model->status == 2)
                                                <a class="dropdown-item" data-status="unarchive"
                                                    data-id="{{ Hashids::encode(@$model->id) }}" id="archive-contact-d"
                                                    href="javascript:void(0);">{{ __('Unarchive') }}</a>
                                            @else
                                                <a class="dropdown-item" data-status="archive"
                                                    data-id="{{ Hashids::encode(@$model->id) }}" id="archive-contact-d"
                                                    href="javascript:void(0);">{{ __('Archive') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if (@$model->status == 2)
                                <div class="ribbon ribbon-top-right o_widget" id="archived_ribbon">
                                    <span class="bg-danger">
                                        {{ __('Archived') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="box">
                {{-- <div class="row">
                    <div class="top-button-box-bar">
                        <div class="top-button-box">
                            <a href="#">
                                <i class="fa fa-star box-icon" aria-hidden="true"></i>
                                <div class="box-content"><span class="box-value">0</span><br>
                                    <span class="box-text">Opportunities</span>
                                </div>
                            </a>
                        </div>
                        <div class="top-button-box">
                            <a href="#">
                                <i class="fa fa-calendar box-icon" aria-hidden="true"></i>
                                <div class="box-content"><span class="box-value">0</span><br>
                                    <span class="box-text">Meetings</span>
                                </div>
                            </a>
                        </div>

                        <div class="top-button-box">
                            <a href="#">
                                <i class="fa fa-usd box-icon" aria-hidden="true"></i>
                                <div class="box-content"><span class="box-value">30</span><br>
                                    <span class="box-text">Sales</span>
                                </div>
                            </a>
                        </div>

                        <div class="top-button-box">
                            <a href="#">
                                <i class="fa fa-refresh box-icon" aria-hidden="true"></i>
                                <div class="box-content"><span class="box-value">0</span><br>
                                    <span class="box-text">Subscription</span>
                                </div>
                            </a>
                        </div>

                        <div class="top-button-box">
                            <a href="#">
                                <i class="fa fa-shopping-cart box-icon" aria-hidden="true"></i>
                                <div class="box-content"><span class="box-value">0</span>
                                    <span class="box-text">Purchases</span>
                                </div>
                            </a>
                        </div>


                        <div class="top-button-box">
                            <a href="#">
                                <i class="fa fa-bars box-icon" aria-hidden="true"></i>
                                <div class="box-content"><span class="box-value">0</span><br>
                                    <span class="box-text">Due</span>
                                </div>
                            </a>
                        </div>

                        <div class="top-button-box dropdown">

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">More <span
                                        class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">

                                    <li><a href="quotations.html">Invoiced</a></li>
                                    <div class="divider"></div>
                                    <li><a href="sale-orders.html">Due</a></li>
                                    <div class="divider"></div>
                                    <li><a href="sale-teams.html">Purchases</a></li>
                                    <div class="divider"></div>
                                    <li><a href="#">Subscription</a></li>
                                </ul>
                            </li>

                        </div>
                    </div>
                </div> --}}
                <div class="box-body pt-3">
                    <form class="timmunity-custom-dashboard-form form-validate" id="contacts-form"
                                            method="POST" action="{{ route('admin.contacts.store') }}"
                                            enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="quotations-form-container">
                                    <div class="box box-success box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">
                                                @if ($action == 'Add') {{ __('Add New Contact') }}
                                                @else {{ __('Edit Contact') }} @endif
                                            </h3>
                                            <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                        class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body" style="">
                                            @csrf
                                            <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                                            <input type="hidden" name="action" value="{!! $action !!}">
                                            <input type="hidden" name="contact-addresses-id"
                                                class="contact-addresses-id">
                                            <input type="hidden" name="contact-addresses[]" class="contact-addresses">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="row">
                                                        <div class="col-md-6 pt-1 customer-radio-button pb-3">
                                                            <input type="radio" id="individual" name="company_type" value="1"
                                                                @if (isset($model->company_type) && $model->company_type == 1) checked @elseif(!isset($model->company_type)) checked @endif>
                                                            <label for="individual">{{ __('Individual') }}</label>
                                                            <input type="radio" id="company" name="company_type" value="2" @if (isset($model->company_type) && $model->company_type == 2) checked @endif>
                                                            <label for="company">{{ __('Company') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group " id="select-title">
                                                                <h4>{{ __('Title') }}</h4>
                                                                <select class="form-control" name="title_id" style='color:gray'
                                                                    oninput='style.color="black"'>
                                                                    <option value="">---{{ __('Select a title') }} ---</option>
                                                                    @if ($contact_titles->count() > 0)
                                                                        @foreach ($contact_titles as $title)
                                                                            <option value="{{ $title->id }}" @if (isset($model) && $title->id == $model->title_id) selected @endif>
                                                                                {{ $title->title }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <h4>{{ __('Name') }}</h4>
                                                                <input type="text"
                                                                    class="form-control @error('name') is-invalid @enderror"
                                                                    id="name" name="name"
                                                                    value="{{ old('name', $model->name ?? '') }}"
                                                                    maxlength="255" aria-describedby="name" required>
                                                                @error('name')
                                                                    <div id="name-error"
                                                                        class="invalid-feedback animated fadeInDown">
                                                                        {{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4" id="select-company">
                                                            <div class="form-group">
                                                                <h4>{{ __('Company') }}</h4>
                                                                <select class="form-control" name="company_id"
                                                                    style='color:gray' oninput='style.color="black"'>
                                                                    <option style="display:none" value="">---{{ __('Select a company') }}---</option>
                                                                    @if ($companies->count() > 0)
                                                                        @foreach ($companies as $company)
                                                                            <option value="{{ $company->id }}" @if (isset($model) && $company->id == $model->company_id) selected @endif>
                                                                                {{ $company->name }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <h4 class="col-md-12">{{ __('Company Address') }}</h4>
                                                        <div class="form-group col-md-3">
                                                            <input class="form-control" type="text" name="street_1"
                                                                placeholder="{{ __('Street 1...') }}"
                                                                value="{{ old('street_1', translation(@$model->id,4,app()->getLocale(),'street_1',@$model->street_1) ?? '') }}" />
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <input class="form-control" type="text" name="street_2"
                                                                placeholder="{{ __('Street 2...') }}"
                                                                value="{{ old('street_2', translation(@$model->id,4,app()->getLocale(),'street_2',@$model->street_2) ?? '') }}" />
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <input class="form-control" type="text" name="city"
                                                                placeholder="{{ __('City') }}"
                                                                value="{{ old('city', translation(@$model->id,4,app()->getLocale(),'street_2',@$model->city) ?? '') }}" />
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <select class="form-control" name="state_id" style='color:gray'
                                                                oninput='style.color="black"'>
                                                                <option value="">---{{ __('Select a state') }}---</option>
                                                                @if ($contact_fed_states->count() > 0)
                                                                    @foreach ($contact_fed_states as $state)
                                                                        <option value="{{ $state->id }}" @if (isset($model) && $state->id == $model->state_id) selected @endif>
                                                                            {{ $state->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <h4>{{ __('Country') }}</h4>
                                                            <select class="form-control" name="country_id" style='color:gray'
                                                                oninput='style.color="black"'>
                                                                <option value="">---{{ __('Select a country') }}---</option>
                                                                @if ($contact_countries->count() > 0)
                                                                    @foreach ($contact_countries as $country)
                                                                        <option value="{{ $country->id }}" @if (isset($model) && $country->id == $model->country_id) selected @endif>
                                                                            {{ $country->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <h4>{{ __('Zip Code') }}</h4>
                                                            <input class="form-control" type="number" name="zipcode"
                                                                placeholder="{{ __('Zip Code') }}"
                                                                value="{{ old('zipcode', $model->zipcode ?? '') }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <h4>{{ __('Vat') }}</h4>
                                                                <input type="text" placeholder="{{ __('e.g.') }} BE0477472701"
                                                                    class="form-control" id="vat_id" name="vat_id"
                                                                    value="{{ old('vat_id', $model->vat_id ?? '') }}"
                                                                    maxlength="255" aria-describedby="vat_id">

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-3" id="job-position">
                                                        <label>{{ __('Job Position') }}</label>
                                                        <input type="text" name="job_position" class="form-control"
                                                            placeholder="{{ __('e.g. Sale Director') }}"
                                                            value="{{ old('job_position', $model->job_position ?? '') }}">
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label>{{ __('Phone') }}</label>
                                                        <input type="number" name="phone" class="form-control"
                                                            placeholder="{{ __('Phone') }}"
                                                            value="{{ old('phone', $model->phone ?? '') }}">
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label>{{ __('Mobile') }}</label>
                                                        <input type="number" name="mobile" class="form-control"
                                                            placeholder="{{ __('Mobile') }}"
                                                            value="{{ old('mobile', $model->mobile ?? '') }}">
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label>{{ __('Email') }}</label>
                                                        <input type="email" name="email" class="form-control"
                                                            placeholder="{{ __('Email') }}"
                                                            value="{{ old('email', $model->email ?? '') }}" required>

                                                        @error('email')
                                                            <div id="email-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label>{{ __('Website Link') }}</label>
                                                        <input type="url" name="web_link" class="form-control"
                                                            placeholder="{{ __('e.g. timmunity.com') }}"
                                                            value="{{ old('web_link', $model->web_link ?? '') }}">
                                                    </div>

                                                    <div class="form-group col-md-3">

                                                        <label>{{ __('Tags') }}</label>

                                                        <select class="form-control select2" name="tag_id[]" multiple="multiple"
                                                            style='color:gray' oninput='style.color="black"'>
                                                            @foreach ($contact_tags as $tags)
                                                                <option value="{{ $tags->id }}"
                                                                    @if (@$model->contact_tags)
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
                                                    <div class="form-group col-md-12">
                                                        <div class="row">
                                                            <button class="skin-gray-light-btn btn" id="contacts-save-btn"
                                                                type="submit">{{ __('Save') }}</button>
                                                            <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2"
                                                                href="{{ route('admin.contacts.index') }}">{{ __('Discard') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 ">
                                                    <div class="avatar-upload form-group">
                                                        <div class="avatar-fileds hover-effect">
                                                            <div class="avatar-edit">
                                                                <input type='file' id="imageUpload" name="image"
                                                                    class="form-control @error('image') is-invalid @enderror"
                                                                    maxlength="255" aria-describedby="image" />
                                                                <label for="imageUpload"></label>
                                                            </div>
                                                        </div>

                                                        <div class="avatar-preview">
                                                            @if (@$model->admin_id == null)
                                                                <img id="imagePreview" src="{!! checkImage(asset('storage/uploads/contact/' . Hashids::encode(@$model->id) . '/' . @$model->image), 'avatar5.png') !!}"
                                                                    width="100%" height="100%">
                                                            @else
                                                                <img id="imagePreview" src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode(@$model->admin_users->id) . '/' . @$model->admin_users->image), 'avatar5.png') !!}"
                                                                    width="100%" height="100%">
                                                            @endif
                                                        </div>
                                                        <span class="text-danger" id="image-input-error"></span>
                                                        @error('image')
                                                            <div id="image-error"
                                                                class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                    <!-- /.box -->
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="custom-tabs mt-3">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#contract" aria-expanded="false">{{ __('Contact & Addresses') }}</a></li>
                                        <li class=""><a data-toggle="tab" href="#sales-purchase" aria-expanded="false">{{ __('Sales & Purchase') }}</a></li>
                                        <li class=""><a data-toggle="tab" href="#internal-notes"
                                                aria-expanded="false">{{ __('Internal Notes') }}</a></li>
                                    </ul>

                                    <div class="tab-content">
                                        <div id="contract" class="tab-pane fade active in pt-2">
                                            <div class="row">
                                                <div class="output-box"></div>
                                                <div class="col-md-6">
                                                    <a type="button" class="btn skin-green-light-btn" data-toggle="modal"
                                                        data-target="#contact-model" id="add-contact-address" data-actionr="add">
                                                        {{ __('Add') }}
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="row" id="contact-address-row">
                                                @if (@$model->contact_addresses)
                                                    @foreach (@$model->contact_addresses as $item)
                                                        <a href="javascript:void(0)" id="add-contact-address"
                                                            data-id-old="edit-contact-address"
                                                            data-contact-address-id="{{ $item->id }}" data-actionr="edit">
                                                            <div class="col-sm-6 col-md-4 mt-2" id="contact_address_clm">
                                                                <div class="customer-box">
                                                                    <div class="customer-img">
                                                                        <div class="avatar-preview">
                                                                            <img id="imagePreview" src="{!! checkImage(asset('storage/uploads/contact-address/' . '/' . $item->contact_image), 'avatar5.png') !!}"
                                                                                width="100%" height="100%">
                                                                        </div>
                                                                    </div>
                                                                    <div class="customer-content col-md-6">
                                                                        <h5 class="customer-heading">
                                                                            {{ $item->contact_name }}</h5>
                                                                        <a href="#"><span
                                                                                class="email">{{ $item->email }}</span></a>
                                                                        <h5 class="sub-heading">{{ __('Phone') }}:{{ $item->phone }}</h5>
                                                                        <h5 class="sub-heading">{{ __('Mobile') }}:{{ $item->mobile }}
                                                                        </h5>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                @endif
                                                <div id="contact-address-row-array"></div>
                                            </div>
                                            <!-- Contact add Model -->

                                            <!-- End  Contact Add Model -->
                                        </div>
                                        <!-- Sales & Purchase -->
                                        <div id="sales-purchase" class="tab-pane fade">
                                            <div class="tab-form" style="padding: 10px 0px; ">
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
                                                                        <option value="">---{{ __('Select sales person')}}---</option>
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
                                                                        <option value="">---{{ __('Select Payment Terms') }}---</option>
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
                                                    style="height: 73px;border: none;border-bottom: 1px solid #ddd;">{{ old('internal_notes', translation(@$model->id,4,app()->getLocale(),'street_2',@$model->internal_notes) ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <div id="contact-model-area"></div>
        <div id="contact-model-d"></div>
        <!-- Bottom- section -->
        @if(@$action == "Edit")
        <section class="bottom-section">
            <div class="row box">
            <div class="row activity-back-color">
                <div class="col-md-12">
                    <div class="custom-tabs mt-3 mb-2">
                    <div class="row">
                        <div class="col-md-8">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#send_message">{{ __('Send Message') }}</a></li>
                            <li><a data-toggle="tab" href="#log_note">{{ __('Log Note') }}</a></li>
                            <li><a data-toggle="tab" href="#schedual_activity">{{ __('Schedule Activity') }}</a></li>
                        </ul>
                        </div>
                        <div class="col-md-4 pull-right text-right follower-icons">
                        <!-- Attachments View -->
                        {!! $attachments_partial_view !!}
                        @if($is_following == 1 )
                            <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="3" id="following"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                            <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="3" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                        @else
                            <a class="followButton" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="3"id="followBtn" >{{ __('Follow') }}</a>
                                <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="3" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
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
                        <div id="send_message" class="tab-pane fade active in">
                            <div class="row tab-form pt-3">
                            <div class="row">
                                <div class="col-md-3">
                                <a class="skin-green-light-btn btn" type="button" data-toggle="modal"  data-target="#send-message-model" onclick="clearMessageForm()"><i class="fa fa-paper-plane"></i>&nbsp;{{ __('Send Message') }}</a>
                                {!! $send_messages_view !!}
                                </div>
                            </div>
                            {!! $send_message_tab_partial_view !!}
                            </div>
                        </div>
                        <!-- Log Note -->
                        <div id="log_note" class="tab-pane fade">
                            <div class="row tab-form pt-3">
                            <div class="row">
                                <div class="col-md-3">
                                <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#log-note-model" onclick="clearNoteForm()"><i class="fa fa-plus"></i>&nbsp; {{ __('Add Note') }}</a>
                                {!! $log_notes_view !!}
                                </div>
                            </div>
                            {!! $notes_tab_partial_view !!}
                            </div>
                        </div>
                        <!-- Schedule Activity -->
                        <div id="schedual_activity" class="tab-pane fade">
                            <div class="row tab-form pt-3">
                            <div class="row">
                                <div class="col-md-3">
                                <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#schedule-activity-model" onclick="ClearScheduleActivity()"><i class="fa fa-clock-o"></i>&nbsp;{{ __('Add Schedule Activity') }}</a>
                                {!! $schedual_activities_view !!}
                                </div>
                            </div>
                            {!! $schedual_activity_tab_partial_view !!}
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>
        @endif
</div>
@endsection
@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script type="text/javascript">
        var multiple_contact_addresses = [];
        var multiple_addresses = [];
        var contact_address_image = null;
        var actions = <?php echo json_encode($action); ?>;
        $(document).ready(function() {

            $('body').on('change', '#individual', function() {
                $('#select-company').hide();
                // $('#select-title').hide();
                $('#job-position').hide();
            });

            $('body').on('change', '#company', function() {
                $('#select-company').show();
                // $('#select-title').show();
                $('#job-position').show();
            });

            //=========Contact Company and Individual checked===========/

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

            //=========contact address radio button type change functions ===========//
            $('body').on('click', '#contact-address-radio', function() {
                $('#contact-address-area').hide();
                $('#select-title-address').show();
                $('#job-position-address').show();

            });
            $('body').on('click', '#invoice-address, #delivery-address,#other-address,#private-address',function() {
                $('#contact-address-area').show();
                $('#select-title-address').hide();
                $('#job-position-address').hide();
            });

            // =======contact address modal discard btn function=======
            $('body').on('click', '#discard-btn', function() {
                $('#contact-name-error').empty();
                $('#email-error').empty();
                $('.modal-form-input-val :input').val('');
                $('select').attr("selected", false);
                $('option:selected', this).remove();
                $('#notes').empty();
            });

            $(document).on('click', '#contact-delete-btn', function(e) {

                e.preventDefault();
                var contact_id = $(this).data('contact');
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: "{{ __("You won't be able to revert this!") }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('Yes, delete it!') }}"
                }).then((result) => {

                    if (result.isConfirmed) {
                        $.ajax({
                            url: ADMIN_URL + '/contacts/' + contact_id,
                            dataType: "JSON",
                            type: 'DELETE',
                            success: function(resp) {
                                if (resp['success'] == true) {
                                    window.location.href =
                                        "{{ route('admin.contacts.index') }}";
                                } else {
                                    Swal.fire("{{ __('Warning') }}", resp['error'], "warning");
                                }
                            }
                        });
                    }
                });
            });
            //=======contact address delete function from DB========
            $(document).on('click', '#edit-contact-address-remove', function(e) {
                e.preventDefault();
                var contact_delete_id = $(this).data('id');
                console.log(contact_delete_id, 'contact delete id ');
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: "{{ __("You won't be able to revert this!") }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('Yes, delete it!') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire(
                            "{{ __('Deleted!') }}",
                            "{{ __('Your file has been deleted.') }}",
                            'success'
                        )
                        $.ajax({
                            url: '/admin/contact-address-dlt/' + contact_delete_id,
                            dataType: "html",
                            type: 'DELETE',
                            success: function(data) {
                                ;
                                $('#contact-address-row').empty().append();
                                $('#contact-address-row').append(contact_addresses.map((
                                    contact, idx) => {
                                    var {
                                        id,
                                        contact_image
                                    } = contact;
                                    return '<div class="col-sm-6 col-md-4 mt-2">' +
                                        '<a href="javascript:void(0)" id="edit-contact-address" data-contact-address-id="' +
                                        id + '" >' +
                                        '<input type="hidden" class="contact-address-id" value="' +
                                        id + '">' +
                                        '<div class="customer-box">' +
                                        ' <div class="customer-img">' +
                                        ' <img id="contact-address-image" src="{!! checkImage(asset("storage/uploads/contact-address/'+contact_image+'"), 'avatar5.png') !!}" width="100%" height="100%">' +
                                        ' </div>' +
                                        '<div class="customer-content col-md-6">' +
                                        ' <h5 class="sub-heading">delt:' +
                                        contact.contact_name + '</h5>' +
                                        '<a href="#"> <span class="email">' +
                                        contact.email + '</span></a>' +
                                        ' <h5 class="customer-heading">"{{ __('Phone') }}":' +
                                        contact.phone + '</h5>' +
                                        ' <h5 class="customer-heading">"{{ __('Mobile') }}":' +
                                        contact.mobile + '</h5>' +
                                        '</div>' +
                                        '  </div>' +
                                        ' </a>' +
                                        ' </div>';
                                }))
                                $("#edit-contact-model").modal("hide");
                            },
                        });
                    }
                });
            });
        });

        //=======contact address file upload function========
        $(document).ready(function($) {
            $('[name="contact_image"]').hide();

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#contact-address-image').show();
                        $('#contact-address-image').attr('src', e.target.result);
                        contact_address_image = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('[name="contact_image"]').change(function() {
                readURL(this);
            });
        });


        //=======contact address save change function========
        $('body').on('click', '#save-change', function(e) {
            //e.preventDefault();

            if ($('#contact-name').val() == '') {
                $('#contact-model').modal('show');
            } else if ($('#contact-email').val() == '') {
                $('#contact-model').modal('show');
            } else {
                $('#contact-model').modal('hide');
                //$("#contact-address-form")[0].reset();
            }

            var contact_name = $('#contact-name').val();
            var email = $('#contact-email').val();
            $('#contact-name').on('keydown', function() {
                $('#contact-name-error').empty()
            })
            $('#contact-email').on('keydown', function() {
                $('#email-error').empty()
            });

            if (!contact_name.length > 0 && !email.length > 0) {
                $('#contact-name-error').empty().append();
                $('#contact-name-error').empty().append("{{ __('This field is required.') }}");
                $('#email-error').empty().append();
                $('#email-error').empty().append("{{ __('This field is required.') }}");

            } else {
                const contact_address_obj = {
                    contact_name: $('#contact-name').val(),
                    job_position: $('.job-position').val(),
                    email: $('#contact-email').val(),
                    type: $('input[name="type"]:checked').val(),
                    mobile: $('#mobile').val(),
                    phone: $('#phone').val(),
                    street_1: $('#street_1').val(),
                    street_2: $('#street_2').val(),
                    city: $('#city').val(),
                    zipcode: $('#zipcode').val(),
                    notes: $('#notes').val(),
                    country_id: $('#country_id').val(),
                    title_id: $('#title_id').val(),
                    state_id: $('#state_id').val(),
                    contact_image: contact_address_image ? contact_address_image : ''
                }

                if ($(this).attr('action-type') == 'add') {
                    multiple_contact_addresses.push(contact_address_obj);
                    contact_address_list();

                    if ($(this).attr("data-modal") == 'save-and-new') {
                        $('#add-contact-address[data-actionr="add"]').click();
                    }
                }
            }

            if ($(this).attr('action-type') == 'edit') {
                update_contact_address($(this));
            }

        });



        //=====contact address edit function from array=====
        function contact_address_edit(index) {
            console.log(index, 'edit');
            let data = multiple_contact_addresses;
            var {
                contact_name,
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
            var contact_name = $('#contact-name').val(contact_name);
            var job_position = $('.job-position').val(job_position);
            var email = $('#contact-email').val(email);
            if (type == 0) {
                $("#contact-address-radio").prop('checked', true);
                $('#select-title-address').show();
                $('#job-position-address').show();
                $('#contact-address-area').hide();
            } else if (type == 1) {
                $("#invoice-address").prop('checked', true);
                $('#select-title-address').hide();
                $('#job-position-address').hide();
                $('#contact-address-area').show();
            } else if (type == 2) {
                $("#delivery-address").prop('checked', true);
                $('#select-title-address').hide();
                $('#job-position-address').hide();
                $('#contact-address-area').show();
            } else if (type == 3) {
                $("#other-address").prop('checked', true);
                $('#select-title-address').hide();
                $('#job-position-address').hide();
                $('#contact-address-area').show();
            } else if (type == 4) {
                $("#private-address").prop('checked', true);
                $('#select-title-address').hide();
                $('#job-position-address').hide();
                $('#contact-address-area').show();
            } else {
                $("#contact-address-radio").prop('checked', false);
                $("#invoice-address").prop('checked', false);
                $("#delivery-address").prop('checked', false);
                $("#other-address").prop('checked', false);
                $("#private-address").prop('checked', false);
            }


            var mobile = $('#mobile').val(mobile);
            var phone = $('#phone').val(phone);
            var street_1 = $('#street_1').val(street_1);
            var street_2 = $('#street_2').val(street_2);
            var city = $('#city').val(city);
            var zipcode = $('#zipcode').val(zipcode);
            var notes = $('#notes').val(notes);
            var country_id = $('#country_id').val(country_id);
            var title_id = $('#title_id').val(title_id);
            var state_id = $('#state_id').val(state_id);
            var contact_image = $('#contact-address-image').attr('src', "" + (contact_image ? contact_image :
                "{!! checkImage(asset('storage/uploads/contact/' . Hashids::encode(@$model->id) . '/' . @$model->image), 'avatar5.png') !!}") + "");
            var idx = $('#txtId').val(index);
            console.log(type, 'second type of checked')
            $('#contact-address-radio').val(0);
            $('#invoice-address').val(1);
            $('#delivery-address').val(2);
            $('#other-address').val(3);
            $('#private-address').val(4);


            $("#save-change").show();
            $("#save-change").attr('action-type','edit');
            $("#save-and-new").hide();
            $("#save-change-update").show();
            $("#save-and-new-update").show();
            $("#remove-contact-address-btn").show();

        }

        //=======contact address save change update function========

        $('body').on('click', '#save-change-update', function() {
            // let data = JSON.parse(sessionStorage.getItem('contact_address'));
            let data = multiple_contact_addresses;
            console.log(data, 'update fun');
            var contact_name = $('#contact-name').val();
            var email = $('#contact-email').val();
            $('#contact-name').on('keydown', function() {
                $('#contact-name-error').empty()
            })
            $('#contact-email').on('keydown', function() {
                $('#email-error').empty()
            })
            if (contact_name === '' && email === '') {
                $('#contact-name-error').empty().append();
                $('#contact-name-error').empty().append("{{ __('This field is required.') }}");
                $('#email-error').empty().append();
                $('#email-error').empty().append("{{ __('This field is required.') }}");
            } else {
                let contact_name = $('#contact-name').val();
                let job_position = $('.job-position').val();
                let email = $('#contact-email').val();
                let type = $('input[name="type"]:checked').val();
                let mobile = $('#mobile').val();
                let phone = $('#phone').val();
                let street_1 = $('#street_1').val();
                let street_2 = $('#street_2').val();
                let city = $('#city').val();
                let zipcode = $('#zipcode').val();
                let notes = $('#notes').val();
                let country_id = $('#country_id').val();
                let title_id = $('#title_id').val();
                let state_id = $('#state_id').val();
                let address_index = $('#txtId').val();
                data[address_index].contact_name = contact_name;
                data[address_index].job_position = job_position;
                data[address_index].email = email;
                data[address_index].type = type;
                data[address_index].mobile = mobile;
                data[address_index].phone = phone;
                data[address_index].street_1 = street_1;
                data[address_index].street_2 = street_2;
                data[address_index].city = city;
                data[address_index].zipcode = zipcode;
                data[address_index].notes = notes;
                data[address_index].country_id = country_id;
                data[address_index].title_id = title_id;
                data[address_index].state_id = state_id;
                data[address_index].contact_image = contact_address_image ? contact_address_image : '';
            }
            contact_address_list();
            $("#contact-address-form")[0].reset();
            $('#contact-model').modal('hide');

        });

        //======contact address save and new update function=========
        $('body').on('click', '#save-and-new-update', function() {
            let data = multiple_contact_addresses;
            console.log(data, 'update fun');
            var contact_name = $('#contact-name').val();
            var email = $('#contact-email').val();
            $('#contact-name').on('keydown', function() {
                $('#contact-name-error').empty()
            })
            $('#contact-email').on('keydown', function() {
                $('#email-error').empty()
            })
            if (contact_name === '' && email === '') {
                $('#contact-name-error').empty().append();
                $('#contact-name-error').empty().append("{{ __('This field is required.') }}");
                $('#email-error').empty().append();
                $('#email-error').empty().append("{{ __('This field is required.') }}");
            } else {
                let contact_name = $('#contact-name').val();
                let job_position = $('.job-position').val();
                let email = $('#contact-email').val();
                let type = $('input[name="type"]:checked').val();
                let mobile = $('#mobile').val();
                let phone = $('#phone').val();
                let street_1 = $('#street_1').val();
                let street_2 = $('#street_2').val();
                let city = $('#city').val();
                let zipcode = $('#zipcode').val();
                let notes = $('#notes').val();
                let country_id = $('#country_id').val();
                let title_id = $('#title_id').val();
                let state_id = $('#state_id').val();
                let address_index = $('#txtId').val();
                data[address_index].contact_name = contact_name;
                data[address_index].job_position = job_position;
                data[address_index].email = email;
                data[address_index].type = type;
                data[address_index].mobile = mobile;
                data[address_index].phone = phone;
                data[address_index].street_1 = street_1;
                data[address_index].street_2 = street_2;
                data[address_index].city = city;
                data[address_index].zipcode = zipcode;
                data[address_index].notes = notes;
                data[address_index].country_id = country_id;
                data[address_index].title_id = title_id;
                data[address_index].state_id = state_id;
                data[address_index].contact_image = contact_address_image ? contact_address_image : '';
            }
            contact_address_list();
            $("#contact-address-form")[0].reset();


        });

        //=========contact address list function========
        function contact_address_list() {
            console.log(multiple_contact_addresses, 'address');
            var obj_str = JSON.stringify(multiple_contact_addresses)
            if (multiple_contact_addresses) {
                $('#contact-address-row-array').empty().append();
                $('#contact-address-row-array').append(multiple_contact_addresses.map((contact, idx) => {
                    var {
                        contact_name,
                        email,
                        phone,
                        mobile,
                        contact_image
                    } = contact;

                    return '<div class="col-sm-6 col-md-4 mt-2">' +
                        '<a href="#" data-address-id="' + idx + '"  onClick="contact_address_edit(' + idx +
                        ')" data-toggle="modal" data-target="#contact-model" >' +
                        '<div class="customer-box">' +
                        ' <div class="customer-img">' +
                        (contact_image ? ' <img id="contact-address-images" src="' + contact_image +
                            '" width="100%" height="100%">' :
                            ' <img id="contact-address-img" src="{!! checkImage(asset("storage/uploads/contact-address/''"), 'avatar5.png') !!}" width="100%" height="100%">'
                            ) +
                        ' </div>' +
                        '<div class="customer-content col-md-6">' +
                        ' <h5 class="sub-heading">' + contact_name + '</h5>' +
                        ' <span class="email">' + email + '</span>' +
                        ' <h5 class="customer-heading">"{{ __('Phone') }}":' + phone + '</h5>' +
                        ' <h5 class="customer-heading">"{{ __('Mobile') }}":' + mobile + '</h5>' +
                        '</div>' +
                        '  </div>' +
                        ' </a>' +
                        ' </div>'
                }));
                var contact_addresses = $(".contact-address-arr-val").map(function() {
                    return $(this).val();
                }).get().join(',');

                $(".contact-addresses").val(obj_str);
            }
        }


        //=========contact address remove function from array========
        $('body').on('click', '#remove-contact-address-btn', function() {
            let address_index = $('#txtId').val();
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __("You won't be able to revert this!") }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('Yes, delete it!') }}"
            }).then((result) => {

                if (result.isConfirmed) {
                    Swal.fire(
                        "{{ __('Deleted!') }}",
                        "{{ __('Your file has been deleted.') }}",
                        'success'
                    )
                    multiple_contact_addresses.splice(address_index, 1);
                    contact_address_list();
                    $("#contact-address-form")[0].reset();
                }
            });
        });


        $('body').on('click', '#add-contact-address', async function() {

            if ($('#contact-model').length > 0) {
                $('#contact-model').modal('hide');
                $('#contact-model-d').html('');
            }

            let action = $(this).attr("data-actionr");
            let data_action_id = '';

            let data_addr_id = $(this).attr('data-contact-address-id');

            if (typeof data_addr_id !== 'undefined' && data_addr_id !== false) {
                data_action_id = data_addr_id;
            }
            var data = {
                'action': action,
                'id': data_action_id
            };

            let contact_address = await prepare_ajax_request(ADMIN_URL + '/contact-address-model', data);
            $('#contact-model-d').html(contact_address.html);
            $('#contact-model').modal('show');
            $('#contact_id').val($('#contacts-form input[name="id"]').val());
            if (action == 'add') {
                $('#contact-address-radio').prop('checked', true);
            }
            let contact_address_type = $('.update-field-visibility-d:checked').val();

            if (contact_address_type == 0) {
                $('#select-title-address').show();
                $('#job-position-address').show();
                $('#contact-address-area').hide();
            } else {
                $('#select-title-address').hide();
                $('#job-position-address').hide();
                $('#contact-address-area').show();
            }

            // $('#contact-address-image').attr('src', "{!! checkImage(asset('storage/uploads/contact/' . Hashids::encode(@$model->id) . '/' . @$model->image), 'avatar5.png') !!}");

            $("#save-change").show();
            $("#save-and-new").show();
            $("#save-change-update").hide();
            $("#save-and-new-update").hide();
            $("#remove-contact-address-btn").hide();
        });


        //update edit contact address
        function update_contact_address(_this) {

            let route = $('#contact-address-form').attr('action');

            let formData = new FormData(document.getElementById("contact-address-form"));
            $.ajax({
                url: route,
                type: "post",
                //enctype: 'multipart/form-data',
                contentType: false,
                processData: false,
                cache: false,
                data: formData,
                dataType: 'JSON',
                success: (data) => {
                    var contact_addresses = data.contact_addresses;
                    console.log(contact_addresses);
                    multiple_addresses.push(data);
                    $('#contact-address-row').empty().append();
                    $('#contact-address-row').append(contact_addresses.map((contact, index) => {
                        var {
                            id,
                            contact_image
                        } = contact;
                        let profile_image = '';
                        if (contact.contact_image != '' && contact.contact_image != null) {
                            profile_image = SITE_URL + '/storage/uploads/contact-address/' + contact
                                .contact_image;
                        } else {
                            profile_image = SITE_URL + '/backend/dist/img/avatar5.png';
                        }

                        return '<div class="col-sm-6 col-md-4 mt-2">' +
                            '<a href="javascript:void(0)" id="edit-contact-address" data-id-old="edit-contact-address" data-contact-address-id="' +
                            id + '" >' +
                            '<input type="hidden" class="contact-address-id" value="' + id + '">' +
                            '<div class="customer-box">' +
                            ' <div class="customer-img">' +
                            ' <img id="contact-address-image" src="' + profile_image +
                            '"  width="100%" height="100%">' +
                            ' </div>' +
                            '<div class="customer-content col-md-6">' +
                            '<a href="javascript:void(0)" id="add-contact-address" data-id-old="edit-contact-address" data-contact-address-id="'+
                            id +'" data-actionr="edit">'+
                            ' <h5 class="sub-heading">' + contact.contact_name + '</h5>' +
                            ' </a>'+
                            '<a href="#"> <span class="email">' + contact.email + '</span></a>' +
                            ' <h5 class="customer-heading">"{{ __('Phone') }}":' + (contact.phone ? contact
                                .phone : '') + '</h5>' +
                            ' <h5 class="customer-heading">"{{ __('Mobile') }}":' + (contact.mobile ? contact
                                .mobile : '') + '</h5>' +
                            '</div>' +
                            '  </div>' +
                            ' </a>' +
                            ' </div>';
                    }))

                    if (_this.attr("data-modal") == 'save-and-new') {
                        $('#add-contact-address[data-actionr="add"]').click();
                    }
                },
                error: function(xhr, error, status) {

                    $.each(xhr.responseJSON, function(key, item) {
                        $('#edit-contact-name-error').empty().append(item.contact_name);
                        $('#email-error').empty().append(item.email);
                    });
                    $('#contact-model').modal('show');
                }
            });
        }

        $('body').on('click','#duplicate-contact-d , #archive-contact-d',async function() {

            let id = $(this).attr("id");
            let action_type = '';

            if(id == 'duplicate-contact-d') {
                action_type = 'duplicate';
            } else if(id == 'archive-contact-d') {
                action_type = $(this).attr('data-status');
            }

            const data = {
                data_id: $(this).attr('data-id'),
                action_type:action_type
            }
            let duplicate_contact = await prepare_ajax_request(ADMIN_URL+'/contact-actions',data);
            console.log(duplicate_contact);
        });

        $('.select2').select2();
        // Actions URL's
        var add_new_contact_url = '{{ route('admin.log.add-new-contact') }}';
        var do_follow_url = '{{ route('admin.log.user-following') }}';
        var do_unfollow_url = '{{ route('admin.log.user-un-follow') }}';
</script>
<script src="{{ asset('backend/dist/js/common.js') }}"></script>
@endsection
