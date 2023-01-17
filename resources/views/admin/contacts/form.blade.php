@extends('admin.layouts.app')
@section('title', __('Contacts'))
@section('styles')
    <link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}">
    <style>
        .timmunity-custom-dashboard-form textarea.form-control {
            max-height: 100%;
        }
        #reseller_info_row{
            background: #019a713d;
            padding-bottom:10px;
        }
        .invoice_type{
            display: flex;
            justify-content: space-between;
            margin-top: 20px !important;
        }
        .invoice_type_opt{
            display: flex;
        }
        #invoice-head{
            margin: 0px;
        }
        .invoice_type_opt .form-group{ 
            display: flex;
        }
        .invoice_type_opt .form-group input{ 
            appearance: auto !important;
        }
        .invoice_type_opt .form-group label{ 
            margin-top: 2px !important;
        }
        #opt_company{
            margin-right: 20px;
        }
        #Company, #Individual{
            height:13px; 
            width:13px; 
            margin-right:10px;
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
                        {{ __('Contact') }} /
                        @if ($action == 'Add')
                            {{ __('Add') }}
                        @else
                            {{ __('Edit') }}
                            <small>
                                @if($model->type == 0)
                                    ({{ __('Contact') }})
                                @elseif($model->type == 1)
                                    ({{ __('Admin') }})
                                @elseif($model->type == 2)
                                    ({{ __('Customer') }})
                                @elseif($model->type == 3)
                                    ({{ __('Reseller') }})
                                @elseif($model->type == 4)
                                    ({{ __('Guest') }})
                                @endif
                            </small>
                        @endif
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="box-header">
                    <div class="row">

                        <div class="col-md-12 text-center">
                            <div class="quotation-right-side content-center">

                                @if ($action == 'Edit')
                                @canany(['Delete Contact','Duplicate Contact','Archive Contact'])
                                    <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                                        <i class="fa fa-cog" aria-hidden="true"></i>
                                        <a class="dropdown-toggle" type="button" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ __('Action') }} <span class="caret"></span>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            @can('Delete Contact')
                                                 <a class="dropdown-item" href="javascript:void(0);"
                                                    data-contact="{{ @$model->id }}" id="contact-delete-btn">
                                                    {{ __('Delete') }}
                                                 </a>
                                            @endcan
                                            @if (@$model->status == 2)
                                                <a class="dropdown-item" data-status="unarchive"
                                                    data-id="{{ Hashids::encode(@$model->id) }}" id="archive-contact-d"
                                                    href="javascript:void(0);">{{ __('Unarchive') }}</a>
                                            @else
                                            @can('Archive Contact')
                                                <a class="dropdown-item" data-status="archive"
                                                    data-id="{{ Hashids::encode(@$model->id) }}" id="archive-contact-d"
                                                    href="javascript:void(0);">{{ __('Archive') }}</a>
                                            @endcan
                                            @endif
                                            @if($model->type == 3 || $model->type == 2)
                                                <a class="dropdown-item" 
                                                    target="_blank" 
                                                    href="{{route('admin.contact.impersonate',Hashids::encode($model->user->id))}}">{{ __('Impersonate User') }}</a>
                                            @endif
                                            @if($model->type == 2 || $model->type == 3)
                                                <a class="dropdown-item" 
                                                    href="{{route('admin.contact.resend-verification-email',Hashids::encode($model->user->id))}}">{{ __('Resend Verification Email') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                @endcanany
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
                                                @if ($action == 'Add')
                                                    {{ __('Add New Contact') }}
                                                @else
                                                    {{ __('Edit Contact') }}
                                                @endif
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
                                            @php
                                                $contact_address_ids = [];
                                                if(isset($model)){
                                                    foreach($model->contact_addresses as $contact_address){
                                                        $contact_address_ids[] =  $contact_address->id;
                                                    }
                                                }
                                            @endphp
                                            <input type="hidden" name="contact_addresses_ids" id="contact_addresses_ids" value="{{ implode(',' ,$contact_address_ids) }}">
                                            <input type="hidden" name="contact-addresses[]" class="contact-addresses">
                                            <input type="hidden" id="redeem_page_url" name="redeem_page_url" value="">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <p> <strong>{{ __('User Type') }}</strong> </p>
                                                                <ul style="list-style:none">
                                                                    <li>
                                                                        <input @if($action == "Edit") disabled="disabled" @endif type="radio" id="admin-type" name="type" value="0"@isset($model) @if (@$model->type == 0) checked @endif @else checked @endif>
                                                                        <label for="admin-type">{{ __('Contact') }}</label>
                                                                    </li>
                                                                    <li>
                                                                        <input @if($action == "Edit") disabled="disabled" @endif type="radio" id="admin-type" name="type" value="1"@if (@$model->type == 1) checked @endif>
                                                                        <label for="admin-type">{{ __('Admin') }}</label>
                                                                    </li>
                                                                    <li>
                                                                        <input @if($action == "Edit") disabled="disabled" @endif type="radio" id="reseller-type" name="type" value="3"@if (@$model->type == 3) checked @endif>
                                                                        <label for="reseller-type">{{ __('Reseller') }}</label>
                                                                    </li>
                                                                    <li>
                                                                        <input @if($action == "Edit") disabled="disabled" @endif type="radio" id="customer-type" name="type" value="2" @if (@$model->type == 2) checked @endif>
                                                                        <label for="customer-type">{{ __('Customer') }}</label>
                                                                    </li>
                                                                    <li>
                                                                        <input @if($action == "Edit") disabled="disabled" @endif type="radio" id="guest-type" name="type" value="4" @if (@$model->type == 4) checked @endif>
                                                                        <label for="customer-type">{{ __('Guest') }}</label>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="form-group reseller_package_div" @if(!isset($model) || (isset($model) && $model->type != 3) )style="display:none" @endif>
                                                                <h4>{{ __('Reseller Package ') }}</h4>
                                                                <select class="form-control" name="reseller_package_id" style='color:gray'
                                                                oninput='style.color="black"'>
                                                                {{-- <select name="form-control" id="" name="reseller_package_id"> --}}
                                                                    <option value="">{{ __('Select Reseller Package') }}</option>
                                                                    @foreach ($reseller_packages as $reseller_package)
                                                                        <option value="{{ Hashids::encode($reseller_package->id)}}" @if(@$model->reseller_package_id == $reseller_package->id) selected="selected" @endif>{{ $reseller_package->package_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <p> <strong>{{ __('User Company') }}</strong> </p>
                                                                <ul style="list-style:none">
                                                                    <li>
                                                                        <input type="radio" id="individual" name="company_type" value="1"
                                                                            @if (isset($model->company_type) && $model->company_type == 1) checked @elseif(!isset($model->company_type)) checked @endif>
                                                                        <label for="individual">{{ __('Individual') }}</label>
                                                                    </li>
                                                                    <li>
                                                                        <input type="radio" id="company" name="company_type" value="2" @if (isset($model->company_type) && $model->company_type == 2) checked @endif>
                                                                        <label for="company">{{ __('Company') }}</label>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <p> <strong>{{ __('Active Status') }}</strong> </p>
                                                                <ul style="list-style:none">
                                                                    @isset($model->user)
                                                                    <li>
                                                                        <input type="radio" id="active-status" name="active-status" value="1" @if (@$model->user->is_active == 1  && @$model->user->is_approved == 1) checked  @endif>
                                                                        <label for="individual">{{ __('Active') }}</label>
                                                                    </li>
                                                                    <li>
                                                                        <input type="radio" id="deactive-status" name="active-status" value="0" @if (@$model->user->is_active == 0 || @$model->user->is_approved == 0) checked  @endif>
                                                                        <label for="deactive-status">{{ __('In-Active') }}</label>
                                                                    </li>
                                                                    @else
                                                                    <li>
                                                                        <input type="radio" id="active-status" name="active-status" value="1" @if(@$model->admin_users->is_active == 1)  checked  @endif>
                                                                        <label for="individual">{{ __('Active') }}</label>
                                                                    </li>
                                                                    <li>
                                                                        <input type="radio" id="deactive-status" name="active-status" value="0" @if(@$model->admin_users->is_active == 0)  checked  @endif>
                                                                        <label for="deactive-status">{{ __('In-Active') }}</label>
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
                                                            <div class="form-group contact_name">
                                                                <h4>{{ __('Name') }}<small class="asterik" style="color:red">*</small></h4>
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
                                                    
                                                    @if(!isset($model) || ( isset($model) && $model->type == 3 ) )
                                                    <div class="row" id="reseller_info_row">
                                                        <div class="form-group col-md-6">
                                                            <h4 class="col-md-12">{{ __('Company Name') }}</h4>
                                                            <input class="form-control" type="text" name="company_name"
                                                                placeholder="{{ __('Company Name') }}"
                                                                value="{{ old('company_name', @$model->company_name ?? '') }}" />
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <h4 class="col-md-12">{{ __('Company URL') }}</h4>
                                                            <input class="form-control" type="text" name="company_url"
                                                                placeholder="{{ __('Company URL') }}"
                                                                value="{{ old('company_url', $model->company_url ?? '') }}" />
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <h4 class="col-md-12">{{ __('Reseller Credit Limit') }}</h4>
                                                            <input type="number" step="0.01" name="reseller_credit_limit" value="{{@$model->reseller_credit_limit}}" id="" class="form-control">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <h4 class="col-md-12">{{ __('Commercial Extract') }}</h4>
                                                            @if(!isset($model))
                                                                <input type="file" name="commercial_registration_extract" id="" class="form-control">
                                                            @else
                                                            @if($model->commercial_registration_extract)
                                                                    <input type="file" name="commercial_registration_extract" id="" class="form-control">
                                                                    <a class="btn btn-primary" target="_blank" href="{{ asset('/storage/uploads/commercial_registration_extract/' . Hashids::encode($model->id) . '/' . $model->commercial_registration_extract)}}"> View Extract</a>
                                                                @else
                                                                    <input type="file" name="commercial_registration_extract" id="" class="form-control">
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="control-label" for="reseller_invoice_cron_days_duration">{{ __('Payment Cycle') }}</label>
                                                            <select class="form-control" name="reseller_invoice_cron_days_duration" id="reseller_invoice_cron_days_duration">
                                                                <option @if(@$model->reseller_invoice_cron_days_duration === null) selected @endif value="">Select Payment Cycle</option>
                                                                {{-- Same Day --}}
                                                                <option @if(@$model->reseller_invoice_cron_days_duration == 0) selected @endif value="0">0 Day (on same day)</option>       
                                                                {{-- Weekly --}}
                                                                <option @if(@$model->reseller_invoice_cron_days_duration == 7) selected @endif value="7">7 Days (weekly)</option>
                                                                {{-- Fortnitly --}}
                                                                <option  @if(@$model->reseller_invoice_cron_days_duration == 14) selected @endif  value="14">14 Days (Fortnitly)</option>
                                                                {{-- Monthly --}}
                                                                <option  @if(@$model->reseller_invoice_cron_days_duration == 28) selected @endif  value="28">28 Days (Monthly)</option>
                                                                {{-- 2 Monthly --}}
                                                                <option  @if(@$model->reseller_invoice_cron_days_duration == 56) selected @endif  value="56">56 Days (2 Months)</option>
                                                                {{-- Quarterly --}}
                                                                <option  @if(@$model->reseller_invoice_cron_days_duration == 84) selected @endif  value="84">108 Days (Quarterly)</option>
                                                                {{-- Yearly --}}
                                                                <option  @if(@$model->reseller_invoice_cron_days_duration == 336) selected @endif  value="84">336 Days (Yearly)</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="control-label" for="reseller_invoice_cron_day">{{ __('Invoice Day') }}</label>
                                                            <select class="form-control" name="reseller_invoice_cron_day" id="reseller_invoice_cron_day">
                                                                <option @if(@$model->reseller_invoice_cron_day == null) selected @endif value="">Select Day</option>
                                                                <option @if(@$model->reseller_invoice_cron_day == 1) selected @endif value="1">Monday</option>
                                                                <option @if(@$model->reseller_invoice_cron_day == 2) selected @endif value="2">Tuesday</option>
                                                                <option @if(@$model->reseller_invoice_cron_day == 3) selected @endif value="3">Wednesday</option>
                                                                <option @if(@$model->reseller_invoice_cron_day == 4) selected @endif value="4">Thursday</option>
                                                                <option @if(@$model->reseller_invoice_cron_day == 5) selected @endif value="5">Friday</option>
                                                                <option @if(@$model->reseller_invoice_cron_day == 6) selected @endif value="6">Saturday</option>
                                                                <option @if(@$model->reseller_invoice_cron_day == 7) selected @endif value="7">Sunday</option>
                                                            </select>
                                                        </div>
                                                            <div class="col-lg-6 col-md-12 col-sm-6 invoice_type">
                                                                <div class="form-group">
                                                                    <p id="invoice-head">Invoice As:</p>
                                                                    <label id="Invoice-error" class="error" for="invoice_as" style="display:none !important;">Company is required</label>
                                                                </div>
                                                                    <div class="invoice_type_opt">
                                                                        <div class="form-group" id="opt_company">
                                                                            <input id="Company" type="radio"
                                                                                class="invoice_as" class="form-control valid" name="invoice_as"
                                                                                {{ @$model->invoice_as == '0' ? 'checked' : '' }} value="0" required>
                                                                            <label for="Company">Company</label><br>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input id="Individual"
                                                                                type="radio" class="form-control" name="invoice_as"
                                                                                {{ @$model->invoice_as == '1' ? 'checked' : '' }} value="1"
                                                                                class="invoice_as" required>
                                                                            <label for="Individual">Individual</label><br>
                                                                        </div>
                                                                    </div>
                                                            </div>   
                                                            
                                                        
                                                    </div>
                                                    @endif
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
                                                                value="{{ old('city', translation(@$model->id,4,app()->getLocale(),'city',@$model->city) ?? '') }}" />
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <input class="form-control" type="text" name="state"
                                                                placeholder="{{ __('State') }}"
                                                                value="{{ old('state', @$model->state ?? '') }}" />
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-3">
                                                            <h4>{{ __('Country') }}<small class="asterik" style="color:red">*</small></h4>
                                                            <select class="form-control" required="required" name="country_id" style='color:gray'
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
                                                            <input class="form-control" type="text" name="zipcode"
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
                                                    <div class="row">
                                                        <div class="form-group col-md-3" id="job-position">
                                                            <label>{{ __('Designation') }}</label>
                                                            <input type="text" name="job_position" class="form-control"
                                                                placeholder="{{ __('e.g. Sale Director') }}"
                                                                value="{{ old('job_position', $model->job_position ?? '') }}">
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label>{{ __('Phone') }}</label>
                                                            <input type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxLength="15" name="phone" class="form-control"
                                                                placeholder="{{ __('Phone') }}"
                                                                value="{{ old('phone', $model->phone ?? '') }}">
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label>{{ __('Mobile') }}</label>
                                                            <input type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxLength="15" name="mobile" class="form-control"
                                                                placeholder="{{ __('Mobile') }}"
                                                                value="{{ old('mobile', $model->mobile ?? '') }}">
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label>{{ __('Email') }}<small class="asterik" style="color:red">*</small></label>
                                                            <input type="email" name="email" class="form-control"
                                                                placeholder="{{ __('Email') }}"
                                                                value="{{ old('email', $model->email ?? '') }}" required>

                                                            @error('email')
                                                                <div id="email-error" class="invalid-feedback animated fadeInDown">
                                                                    {{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="row">
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
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <div class="row">
                                                                <button class="skin-gray-light-btn btn" id="contacts-save-btn"
                                                                    type="submit">{{ __('Save') }}</button>
                                                                <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2"
                                                                    href="{{ route('admin.contacts.index') }}">{{ __('Discard') }}</a>
                                                            </div>
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
                                        <li class=""><a data-toggle="tab" href="#internal-notes"  aria-expanded="false">{{ __('Internal Notes') }}</a></li>

                                        @if($action == 'Edit')
                                            @if($model->type == 2 || $model->type == 4)
                                                <li id="voucher" data-model-id="{{$model->id}}" class=""><a data-toggle="tab"  href="#voucher_redeemed" aria-expanded="false">{{ __('Voucher Redeemed') }}</a></li>
                                                <li id="order_quotations" data-model-id="{{$model->id}}" class=""><a data-toggle="tab" href="#order_quotation" aria-expanded="false">{{ __('Order Quotations') }}</a></li>
                                            @endif
                                            @if($model->type == 3)
                                                <li  id="voucher" data-model-id = "{{$model->id}}" class=""><a data-toggle="tab" href="#voucher_redeemed" aria-expanded="false">{{ __('Voucher Redeemed') }}</a></li>
                                                <li  id="order_vouchers"  data-model-id = "{{$model->id}}" class=""><a data-toggle="tab" href="#order_voucher" aria-expanded="false">{{ __('Order Vouchers') }}</a></li>
                                            @endif
                                        @endif
                                    </ul>

                                    <div class="tab-content">
                                        <div id="contract" class="tab-pane fade active in pt-2">
                                            <div class="row">
                                                <div class="output-box"></div>
                                                <div class="col-md-6">
                                                    <a type="button" class="btn skin-green-light-btn add-address-btn" data-toggle="modal"
                                                    data-target="#contact-model">
                                                    {{ __('Add') }}
                                                </a>
                                                </div>
                                            </div>

                                            <div class="row" id="contact-address-row">
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
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <textarea name="internal_notes" rows="4" id="internal_notes" class="form-control" placeholder="{{ __('Internal Note') }}">{{ old('internal_notes', trim(translation(@$model->id,4,app()->getLocale(),'street_2',@$model->internal_notes)) ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Voucher Redeemed -->
                                        <div id="voucher_redeemed" class="tab-pane fade pt-2">
                                            <div class="row">
                                                <div class="col-md-12 table-responsive ">
                                                    <table style="width:100%" id="contacts_vouchers" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                                                        <thead>
                                                            <tr role="row">
                                                                <th>{{ __('Product Name') }}</th>
                                                                <th>{{ __('Voucher Code') }}</th>
                                                                <th>{{ __('Platform') }}</th>
                                                                <th>{{ __('Secondary Platform') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                       <!-- Order Quotation -->
                                        <div id="order_quotation" class="tab-pane fade pt-2">
                                            <div class="row">
                                                <div class="col-md-12 table-responsive ">
                                                    <table style="width:100%" id="order_quotations_table" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                                                        <thead>
                                                            <tr role="row">
                                                                <th>{{ __('ID') }}</th>
                                                                <th>{{ __('Order #') }}</th>
                                                                <th>{{ __('Customer Name') }}</th>
                                                                <th>{{ __('Order Date') }}</th>
                                                                <th>{{ __('Amount') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Order Voucher  -->
                                        <div id="order_voucher" class="tab-pane fade pt-2">
                                            <div class="row">
                                                <div class="col-md-12 table-responsive ">
                                                    <table style="width:100%" id="order_vouchers_table" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                                                        <thead>
                                                            <tr role="row">
                                                                
                                                                <th>{{ __('Product') }}</th>
                                                                <th>{{ __('Date') }}</th>
                                                                <th>{{ __('Quantity') }}</th>
                                                                <th>{{ __('Used') }}</th>
                                                                <th>{{ __('Remaining') }}</th>
                                                                <th>{{ __('Unit Price') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
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
                                    <label class="control-label">{{ __('Title') }}*</label>
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
                                    <label class="control-label">{{ __('Contact Name') }} *</label>
                                    <input class="form-control" type="text" name="contact_add_name">
                                </div>
                                <div class="form-group col-md-3 required">
                                    <label class="control-label">{{ __('Email') }} *</label>
                                    <input class="form-control" type="email" name="contact_add_email">
                                </div>
                                <div class="form-group col-md-3 required">
                                    <label class="control-label">{{ __('Designation') }}*</label>
                                    <input class="form-control" type="text" name="contact_add_job_position">
                                </div>
                                <div class="form-group col-md-3 required">
                                    <label class="control-label" class="control-label">{{ __('Phone') }}*</label>
                                    <input class="form-control" type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxLength="15" name="contact_add_phone">
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="control-label">{{ __('Mobile') }}</label>
                                    <input class="form-control" type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxLength="15" name="contact_add_mobile">
                                </div>
                                <div class="form-group col-md-3 required">
                                    <label class="control-label">{{ __('Street 1...') }}*</label>
                                    <input class="form-control" type="text" name="contact_add_street_1">
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="control-label">{{ __('Street 2...') }}</label>
                                    <input class="form-control" type="text" name="contact_add_street_2">
                                </div>
                                <div class="form-group col-md-3 required">
                                    <label class="control-label">{{ __('City') }}*</label>
                                    <input class="form-control" type="text" name="contact_add_city">
                                </div>
                                <div class="form-group col-md-3 required">
                                    <label class="control-label">{{ __('Zip Code') }}</label>
                                    <input class="form-control" type="text" name="contact_add_zipcode">
                                </div>
                                <div class="form-group col-md-3 required">
                                    <label class="control-label">{{ __('Country') }}*</label>
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
                                    <label class="control-label">{{ __('State') }}*</label>
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

        <!-- Bottom- section -->
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
                            <li  @if(!auth()->user()->can('View Send Messages') && !auth()->user()->can('Send Message') && !auth()->user()->can('View Log Note') && !auth()->user()->can('Add Note')) class="active" @endif><a data-toggle="tab" href="#schedual_activity">{{ __('Schedule Activity') }}</a></li>
                            @endcanany
                        </ul>
                        @endcanany
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
                        <div id="log_note" class="tab-pane fade @if(!auth()->user()->can('View Send Messages') && !auth()->user()->can('Send Message')) active in @endif">
                            <div class="row tab-form pt-3">
                            <div class="row">
                                <div class="col-md-3">
                                @can('Add Note')
                                <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#log-note-model" onclick="clearNoteForm()"><i class="fa fa-plus"></i>&nbsp; {{ __('Add Note') }}</a>
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
                        <div id="schedual_activity" class="tab-pane fade @if(!auth()->user()->can('View Send Messages') && !auth()->user()->can('Send Message') && !auth()->user()->can('View Log Note') && !auth()->user()->can('Add Note')) active in @endif">
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
@endsection
@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\dataTables.buttons.min.js') }}"></script>

    <script type="text/javascript">
        var multiple_contact_addresses = [];
        var multiple_addresses = [];
        var contact_address_image = null;
        var actions = <?php echo json_encode($action); ?>;
        $(document).ready(function() {
            $(".contact_name #name").on('keyup blur change', function() {
                var title = $("#name").val();
                $("#redeem_page_url").val(convertToSlug(title));
            });
            function convertToSlug(Text) {
                var text = Text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                var protocol = window.location.protocol;
                var hostname = window.location.hostname;
                var url = window.location.href;
                var id = url.substring(url.lastIndexOf('/') + 1);
                // var url_slug =  protocol+"//"+hostname+'/'+text;
                var url_slug =  "https://www.{{env('reseller_domain')}}"+'/'+text;
                return url_slug.replace('--', '-');
            }
            $('body').on('change', '#individual', function() {
                $('#select-company').hide();
                $('#job-position').hide();
            });

            $('body').on('change', '#company', function() {
                $('#select-company').show();
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
            let reseller_type = $('input[name="type"]:checked').val();
            if (reseller_type == 3) {
                $('#reseller_package_div').show();
            } else {
                $('#reseller_package_div').hide();
            }
            $('input[name="type"]').on('click',function(){
                reseller_type = $('input[name="type"]:checked').val();
                if (reseller_type == 3) {
                    $('.reseller_package_div').show();
                } else {
                    $('.reseller_package_div').hide();
                }
            })

            //=======contact address file upload function========

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


            @if($action == 'Edit')
            var contact_id = "{{ @$model->id}}";
            var contact_voucher_table = $("#contacts_vouchers").DataTable({
                "order": [],
                "aaSorting": [],
                lengthChange: false,
                responsive: true,
                orderCellsTop: true,
                serverSide: true,
                scrollCollapse: true,
                scrollX: true,
                fixedColumns: true,
                bFilter:false,
                // ajax: '{{ route("admin.voucher.orders") }}',
                "ajax": {
                    "url": "{{ route('admin.voucher.redeemed.contacts') }}",
                    "data":{
                        contact_id:contact_id,
                    }
                },
                columns: [

                 
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'voucher_code',
                        name: 'voucher_code'
                    },
                    {
                        data: 'platform',
                        name: 'platform'
                    },
                    {
                        data: 'secondary_platform',
                        name: 'secondary_platform'
                    }
                ],
                "columnDefs": [
                    { "orderable": false, "targets": 3 }
                ]
            });
            var order_vouchers_table = $("#order_vouchers_table").DataTable({
                "order": [],
                "aaSorting": [],
                lengthChange: false,
                responsive: true,
                orderCellsTop: true,
                serverSide: true,
                scrollCollapse: true,
                fixedColumns: true,
                scrollX: true,
                bFilter:false,
                // ajax: '{{ route("admin.voucher.orders") }}',
                "ajax": {
                    "url": "{{ route('admin.voucher.order.vouchers.contacts') }}",
                    "data":{
                        contact_id:contact_id,
                    }
                },
                columns: [

                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'used',
                        name: 'used'
                    },
                    {
                        data: 'remaining',
                        name: 'remaining'
                    },
                    {
                        data: 'unit_price',
                        name: 'unit_price'
                    },


                ],
                "columnDefs": [
                    { "orderable": false, "targets": 3 }
                ]
            });
            var quotations_table = $("#order_quotations_table").DataTable({
                "order": [],
                "aaSorting": [],
                lengthChange: false,
                responsive: true,
                orderCellsTop: true,
                serverSide: true,
                scrollCollapse: true,
                scrollX: true,
                fixedColumns: true,
                bFilter:false,
                // ajax: '{{ route("admin.voucher.orders") }}',
                "ajax": {
                    "url": "{{ route('admin.voucher.order.quotation.contacts') }}",
                    "data":{
                        contact_id:contact_id,
                    }
                },
                columns: [

                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'order_no',
                    name: 'order_no'
                },
                {
                    data: 'customer',
                    name: 'customer'
                },

                {
                    data: 'order_date',
                    name: 'order_date'
                },
                {
                    data: 'amount',
                    name: 'amount'
                }
                ],
                "columnDefs": [
                    { "orderable": false, "targets": 3 }
                ]
            });
            @endif

            $("#order_vouchers").on('click', function(){
                order_vouchers_table.draw();
            });
            $("#order_quotations").on('click', function(){
                quotations_table.draw();
            });

            $("#voucher").on('click', function(){
                contact_voucher_table.draw();
            });

        });
        @if(!isset($model))
                $('#reseller_info_row').hide();
        @endif
        $('body').on('change','input[name=type]',function(){
            if($(this).val() == 3){
                $('#reseller_info_row').show();
            }else{
                $('#reseller_info_row').hide();
            }
        });
        // Show Hide Contact Address Fields based on the address type
        $('body').on('change','input[name=add_type]',function(){
            switch($(this).val()){
                case "0":
                    $('#contact-model input[name=contact_add_street_1]').parents('.form-group').hide();
                    $('#contact-model input[name=contact_add_street_2]').parents('.form-group').hide();
                    $('#contact-model input[name=contact_add_city]').parents('.form-group').hide();
                    $('#contact-model input[name=contact_add_zipcode]').parents('.form-group').hide();
                    $('#contact-model select[name=contact_add_country_id]').parents('.form-group').hide();
                    $('#contact-model select[name=state_id]').parents('.form-group').hide();

                    $('#contact-model input[name=contact_add_name]').parents('.form-group').show();
                    $('#contact-model select[name=contact_add_title_id]').parents('.form-group').show();
                    $('#contact-model input[name=contact_add_job_position]').parents('.form-group').show();
                    $('#contact-model input[name=contact_add_phone]').parents('.form-group').show();
                    $('#contact-model input[name=contact_add_mobile]').parents('.form-group').show();
                    $('#contact-model textarea[name=contact_add_notes]').parents('.form-group').show();
                    $('#contact-model input[name=contact_add_email]').parents('.form-group').show();
                    break;
                default:
                    $('#contact-model select[name=contact_add_title_id]').parents('.form-group').hide();
                    $('#contact-model input[name=contact_add_job_position]').parents('.form-group').hide();

                    $('#contact-model input[name=contact_add_street_1]').parents('.form-group').show();
                    $('#contact-model input[name=contact_add_street_2]').parents('.form-group').show();
                    $('#contact-model input[name=contact_add_city]').parents('.form-group').show();
                    $('#contact-model input[name=contact_add_zipcode]').parents('.form-group').show();
                    $('#contact-model select[name=contact_add_country_id]').parents('.form-group').show();
                    $('#contact-model select[name=state_id]').parents('.form-group').show();
                    $('#contact-model input[name=contact_add_name]').parents('.form-group').show();
                    $('#contact-model input[name=contact_add_phone]').parents('.form-group').show();
                    $('#contact-model input[name=contact_add_mobile]').parents('.form-group').show();
                    $('#contact-model textarea[name=contact_add_notes]').parents('.form-group').show();
                    $('#contact-model input[name=contact_add_email]').parents('.form-group').show();
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
            if(!(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(email))){
                toastr.error('Incorrect Email format');
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
                                    ' <h5 class="customer-heading phone">{{ __("Phone") }}: ' + phone + '</h5>' +
                                    ' <h5 class="customer-heading mobile">{{ __("Mobile") }}: ' + mobile + '</h5>' +
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
                switch(type){
                    case "0":
                        if( !contact_name )
                        {
                            toastr.error('Contact Name is required.');
                        }
                        if( !email  )
                        {
                            toastr.error('Email is required.');
                        }
                        if( !job_position )
                        {
                            toastr.error('Designation is required.');
                        }
                        if( !phone )
                        {
                            toastr.error('Phone is required.');
                        }
                        if( !title_id )
                        {
                            toastr.error('Title is required.');
                        }
                        break;
                    default:
                        if( !street_1 )
                        {
                            toastr.error('Street 1 is required.');
                        }
                        if( !city )
                        {
                            toastr.error('City is required.');
                        }
                        if( !state_id )
                        {
                            toastr.error('State is required.');
                        }
                        if( !country_id )
                        {
                            toastr.error('Country is required.');
                        }
                        if( !email )
                        {
                            toastr.error('Email is required.');
                        }
                        if( !phone  )
                        {
                            toastr.error('Phone is required.');
                        }
                        break;
                }

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
        $('body').on('click', '.add-address-btn', function() {
            clear_address_model();
            $('.delete-address-btn').hide();
            $('input[name=add_type]:checked').change();
            $('#contact-model input[name=contact_add_id]').val('');
        });

        $(document).on('click', '#contact-delete-btn', function(e) {

            e.preventDefault();
            var contact_id = $(this).data('contact');
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: '{{ __("You will not be able to revert this!") }}',
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
        $.validator.addMethod("email", function (value, element) {
            return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
        }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
        $('#contacts-form').validate({
            rules: {
                "email":{
                    required:true,
                    email: true,
                    remote: {
                        url: "{{ route('admin.contact.check.email') }}",
                        type: "post",
                        data: {
                            email: function() {
                                return $("[name=email]").val();
                            },
                            id: function() {
                                return $("[name=id]").val();
                            },
                            "csrf-token": function() {
                                return $('meta[name="csrf-token"]').attr('content')
                            }

                        }
                    }
                }
            },
            messages: {
                "email":{
                    remote: "Email has already been taken"
                }
            }

        });
    </script>
    <script src="{{ asset('backend/dist/js/common.js') }}"></script>
@endsection
