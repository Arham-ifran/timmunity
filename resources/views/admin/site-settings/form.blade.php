@extends('admin.layouts.app')
@section('title',  __('Site Settings'))
@section('styles')
<style>
    .avatar-upload{
        margin:auto;
    }
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
       <div class="row">
          <div class="col-md-6">
             <h2>
                {{ __('Site Settings') }}
             </h2>
          </div>
       </div>
    </section>
    <!-- Table content -->
    <section class="content">
        <div class="main-box box">
            <div class="row mt-3">
                <div class="col-xs-12">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ __('Update Site Settings') }}</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <form class="form-validate" action="{{ route('admin.site.settings.update') }}" method="post" enctype="multipart/form-data">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="custom-tabs mt-3">
                                                    <ul class="nav nav-tabs">
                                                        <li class="active">
                                                            <a data-toggle="tab" id="GeneralTabBtn" href="#general-information">{{ __('General Information') }}</a>
                                                        </li>
                                                        <li >
                                                            <a data-toggle="tab" id="SocialMediaBtn" href="#social-media">{{ __('Social Media') }}</a>
                                                        </li>
                                                        <li >
                                                            <a data-toggle="tab" id="AccountInactivityBtn" href="#account_inactivity">{{ __('Account Inactivity') }}</a>
                                                        </li>
                                                        <li >
                                                            <a data-toggle="tab" id="LowLicenseBtn" href="#low-license">{{ __('Low License') }}</a>
                                                        </li>
                                                        <li >
                                                            <a data-toggle="tab" id="EmailBtn" href="#email-tab">{{ __('Emails') }}</a>
                                                        </li>
                                                        <li >
                                                            <a data-toggle="tab" id="PaymentReliefBtn" href="#payment-relief">{{ __('Payment Relief and Grace Periods') }}</a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div id="general-information" class="tab-pane fade in active">
                                                            <div class="row pt-3">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label" for="site_title">{{ __('Site Title') }}<small class="asterik" style="color:red">*</small></label>
                                                                        <input type="text" class="form-control" id="site_title" name="site_title" value="{{ @$model->site_title ?? '' }}" maxlength="100" aria-describedby="site_title" required />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label" for="site_name">{{ __('Company Name') }}<small class="asterik" style="color:red">*</small></label>
                                                                        <input type="text" class="form-control" id="site_name" name="site_name" value="{{ @$model->site_name ?? '' }}" maxlength="100" aria-describedby="site_name" required />
                                                                    </div>
                                                                </div>
                                                                <!-- FILE UPLOAD -->
                                                                <div class="col-md-4 pull-right">
                                                                    <div class="avatar-upload form-group">
                                                                        <div class="avatar-fileds hover-effect">
                                                                            <div class="avatar-edit">
                                                                            <input type="file" class="form-control" id="imageUpload" name="site_logo" value="{{ old('image', $model->image ?? '')}}" />
                                                                            <label for="imageUpload"><small class="asterik" style="color:red">*</small></label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="avatar-preview">
                                                                        <img id="imagePreview"
                                                                            src="{!!checkImage(asset('storage/uploads/'. @$model->site_logo),'avatar5.png')!!}" width="100%" height="100%" />
                                                                                @error('image')
                                                                                <div id="image-error" class="invalid-feedback animated fadeInDown">
                                                                                {{ $message }}
                                                                                </div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="control-label" for="company_registration_number">{{ __('Company Registration Numer') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="company_registration_number" name="company_registration_number" value="{{ @$model->company_registration_number ?? '' }}" maxlength="255" aria-describedby="company_registration_number" required />
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="control-label" for="site_url">{{ __('Site URL') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="site_url" name="site_url" value="{{ @$model->site_url ?? '' }}" maxlength="255" aria-describedby="site_url" required />
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="control-label" for="vat_id">{{ __('VAT ID') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="vat_id" name="vat_id" value="{{ @$model->vat_id ?? '' }}" maxlength="100" aria-describedby="vat_id" required />
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="control-label" for="tax_id">{{ __('TAX ID') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ @$model->tax_id ?? '' }}" maxlength="100" aria-describedby="tax_id" required />
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="control-label" for="site_keywords">{{ __('Keywords') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <textarea class="form-control" name="site_keywords" required>{{ @$model->site_keywords ?? '' }}</textarea>
                                                                </div>

                                                                <div class="form-group col-md-6">
                                                                    <label class="control-label" for="site_description">{{ __('Description') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <textarea class="form-control" name="site_description" required>{{ @$model->site_description ?? '' }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="control-label" for="site_email">{{ __('Email') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="email" class="form-control" id="site_email" name="site_email" value="{{ @$model->site_email ?? '' }}" maxlength="50" aria-describedby="site_email" required />
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="control-label" for="contactus_email">{{ __('Inquiry Email') }} <small>{{ __('Receive Contact Query') }}</small><small class="asterik" style="color:red">*</small></label>
                                                                    <input type="email" class="form-control" id="contactus_email" name="inquiry_email" value="{{ @$model->inquiry_email ?? '' }}" maxlength="50" aria-describedby="contactus_email" required />
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="control-label" for="site_phone">{{ __('Phone') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="tel" class="form-control" id="site_phone" name="site_phone" value="{{ @$model->site_phone ?? '' }}" maxlength="50" aria-describedby="site_phone" required />
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="control-label" for="site_mobile">{{ __('Mobile')}}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="tel" class="form-control" id="site_mobile" name="site_mobile" value="{{ @$model->site_mobile ?? '' }}" maxlength="50" aria-describedby="site_mobile" required />
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="control-label" for="site_address">{{ __('Office Address') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <textarea class="form-control" name="site_address" required>{{ @$model->site_address ?? '' }}</textarea>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="control-label" for="commercial_register_address">{{ __('Commercial Register Address') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <textarea class="form-control" name="commercial_register_address" required>{{ @$model->commercial_register_address ?? '' }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label class="control-label" for="operating_hours">{{ __('Operating Hours') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <textarea class="form-control" name="operating_hours" required>{{ @$model->operating_hours ?? '' }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="row tab-form">
                                                                <h3 class="col-md-12">{{ __('Company Address Information') }}</h3>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-md-3">
                                                                    <label class="control-label" for="street">{{ __('Street') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="street" name="street" value="{{ @$model->street ?? '' }}" maxlength="100" aria-describedby="street" required />
                                                                </div>
                                                                <div class="form-group col-md-3">
                                                                    <label class="control-label" for="zip_code">{{ __('Zip Code') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="zip_code" name="zip_code" value="{{ @$model->zip_code ?? '' }}" maxlength="100" aria-describedby="zip_code" required />
                                                                </div>
                                                                <div class="form-group col-md-3">
                                                                    <label class="control-label" for="city">{{ __('City') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="city" name="city" value="{{ @$model->city ?? '' }}" maxlength="100" aria-describedby="city" required />
                                                                </div>
                                                                <div class="form-group col-md-3">
                                                                    <label class="control-label" for="country">{{ __('Country') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="country" name="country" value="{{ @$model->country ?? '' }}" maxlength="100" aria-describedby="country" required />
                                                                </div>
                                                            </div>
                                                            <div class="row tab-form">
                                                                <h3 class="col-md-12">{{ __('Bank Information') }}</h3>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group col-md-4">
                                                                    <label class="control-label" for="bank_name">{{ __('Bank Name') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ @$model->bank_name ?? '' }}" maxlength="100" aria-describedby="bank_name" required />
                                                                </div>
                                                                <div class="form-group col-md-4">
                                                                    <label class="control-label" for="iban">{{ __('IBAN') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="iban" name="iban" value="{{ @$model->iban ?? '' }}" maxlength="100" aria-describedby="iban" required />
                                                                </div>
                                                                <div class="form-group col-md-4">
                                                                    <label class="control-label" for="code">{{ __('Code') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="code" name="code" value="{{ @$model->code ?? '' }}" maxlength="100" aria-describedby="code" required />
                                                                </div>
                                                            </div>
                                                            <div class="row tab-form">
                                                                <div class="form-group col-md-6">
                                                                    <h3>{{ __('VAT Settings') }}</h3>
                                                                    <label class="control-label" for="defualt_vat">{{ __('VAT (%)') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="number" class="form-control" id="defualt_vat" name="defualt_vat" value="{{ @$model->defualt_vat ?? '' }}" min="0" maxlength="100" step="0.01" aria-describedby="defualt_vat" required />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="social-media" class="tab-pane fade">
                                                            <div class="row pt-3">
                                                                <div class="form-group col-md-3">
                                                                    <label class="control-label" for="pinterest">{{ __('Pinterest') }}</label>
                                                                    <input type="text" class="form-control" id="pinterest" name="pinterest" value="{{ @$model->pinterest ?? '' }}" maxlength="100" aria-describedby="pinterest"  />
                                                                </div>
                                                                <div class="form-group col-md-3">
                                                                    <label class="control-label" for="facebook">{{ __('Facebook') }}</label>
                                                                    <input type="text" class="form-control" id="facebook" name="facebook" value="{{ @$model->facebook ?? '' }}" maxlength="100" aria-describedby="facebook"  />
                                                                </div>
                                                                <div class="form-group col-md-3">
                                                                    <label class="control-label" for="twitter">{{ __('Twitter') }}</label>
                                                                    <input type="text" class="form-control" id="twitter" name="twitter" value="{{ @$model->twitter ?? '' }}" maxlength="100" aria-describedby="twitter"  />
                                                                </div>
                                                                <div class="form-group col-md-3">
                                                                    <label class="control-label" for="linkedin">{{ __('LinkedIn') }}</label>
                                                                    <input type="text" class="form-control" id="linkedin" name="linkedin" value="{{ @$model->linkedin ?? '' }}" maxlength="100" aria-describedby="linkedin"  />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="account_inactivity" class="tab-pane fade">
                                                            <div class="row tab-form pt-3">
                                                                <div class="form-group col-md-6">
                                                                    <h3>{{ __('Account Inactivity Follow-ups') }}</h3>
                                                                    <label class="control-label" for="account_inactivity_first_notification">{{ __('First Notifictation (In days)') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="number" class="form-control" id="account_inactivity_first_notification" name="account_inactivity_first_notification" value="{{ @$model->account_inactivity_first_notification ?? '' }}" min="0" maxlength="100" aria-describedby="account_inactivity_first_notification" required />

                                                                    <label class="control-label" for="account_inactivity_second_notification">{{ __('Second Notifictation (In days)') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="number" class="form-control" id="account_inactivity_second_notification" name="account_inactivity_second_notification" value="{{ @$model->account_inactivity_second_notification ?? '' }}" min="0" maxlength="100" aria-describedby="account_inactivity_second_notification" required />

                                                                    <label class="control-label" for="account_inactivity_third_notification">{{ __('Third Notifictation (In days)') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="number" class="form-control" id="account_inactivity_third_notification" name="account_inactivity_third_notification" value="{{ @$model->account_inactivity_third_notification ?? '' }}" min="0" maxlength="100" aria-describedby="account_inactivity_third_notification" required />
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <h3>{{ __('Account Inactivity (Recomended are 30 days)') }}</h3>
                                                                    <label class="control-label" for="account_inactivity_time_limit">{{ __('Account Inactive Time Limit (In days)') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="number" class="form-control" id="account_inactivity_time_limit" name="account_inactivity_time_limit" value="{{ @$model->account_inactivity_time_limit ?? '' }}" min="0" maxlength="100" aria-describedby="account_inactivity_time_limit" required />

                                                                    <label class="control-label" for="account_soft_delete_time_limit">{{ __('Account Soft Delete Time Limit (In days)') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="number" class="form-control" id="account_soft_delete_time_limit" name="account_soft_delete_time_limit" value="{{ @$model->account_soft_delete_time_limit ?? '' }}" min="0" maxlength="100" aria-describedby="account_soft_delete_time_limit" required />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="low-license" class="tab-pane fade">
                                                            <div class="row tab-form pt-3">
                                                                <div class="form-group col-md-4">
                                                                    <h3>{{ __('Low License Key Notification Count') }}</h3>
                                                                    <label class="control-label" for="low_license_notification_count">{{ __('Notification Count') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="number" class="form-control" id="low_license_notification_count" name="low_license_notification_count" value="{{ @$model->low_license_notification_count ?? '' }}" min="0" maxlength="100" aria-describedby="low_license_notification_count" required />
                                                                </div>
                                                                <div class="form-group col-md-4">
                                                                    <h3>{{ __('Low License Key Notification Duration') }}</h3>
                                                                    <label class="control-label" for="low_license_notification_duration">{{ __('Notification duration') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="number" class="form-control" id="low_license_notification_duration" name="low_license_notification_duration" value="{{ @$model->low_license_notification_duration ?? '' }}" min="0" maxlength="100" aria-describedby="low_license_notification_duration" required />
                                                                </div>
                                                                <div class="form-group col-md-4">
                                                                    <h3>{{ __('Low License Notification Threshold') }}</h3>
                                                                    <label class="control-label" for="license_count_low_notification_threshold">{{ __('License Threshold Count') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="number" class="form-control" id="license_count_low_notification_threshold" name="license_count_low_notification_threshold" value="{{ @$model->license_count_low_notification_threshold ?? '' }}" min="0" maxlength="100" aria-describedby="low_license_notification_duration" required />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="email-tab" class="tab-pane fade">
                                                            <div class="row tab-form pt-3">
                                                                <div class="form-group col-md-12">
                                                                    <h3>{{ __('Emails') }}</h3>
                                                                </div>
                                                                <div class="form-group col-md-4">

                                                                    <!-- <h3>{{ __('Low License Email Recipients') }}</h3> -->
                                                                    <label class="control-label" for="low_license_email_recipients">{{ __('Low License Email Recipients') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="low_license_email_recipients" name="low_license_email_recipients" value="{{ @$model->low_license_email_recipients ?? '' }}" required />
                                                                    <small class="text-danger">Please add email with comma separated</small>
                                                                </div>
                                                                <div class="form-group col-md-4">
                                                                    <!-- <h3>{{ __('Low License Key Notification Duration') }}</h3> -->
                                                                    <label class="control-label" for="registration_email_recipients">{{ __('Registration Email Recipients') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="registration_email_recipients" name="registration_email_recipients" value="{{ @$model->registration_email_recipients ?? '' }}"  aria-describedby="registration_email_recipients" required />
                                                                    <small class="text-danger">Please add email with comma separated</small>
                                                                </div>
                                                                <div class="form-group col-md-4">
                                                                    <!-- <h3>{{ __('Low License Notification Threshold') }}</h3> -->
                                                                    <label class="control-label" for="orders_bcc_email_recipients">{{ __('Order BCC Email Recipients') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="text" class="form-control" id="orders_bcc_email_recipients" name="orders_bcc_email_recipients" value="{{ @$model->orders_bcc_email_recipients ?? '' }}"  aria-describedby="orders_bcc_email_recipients" required />
                                                                    <small class="text-danger">Please add email with comma separated</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="payment-relief" class="tab-pane fade">
                                                            <div class="row tab-form pt-3">
                                                                <h3>{{ __('Payment Relief Settings') }}</h3>
                                                                <div class="col-md-6">
                                                                    <label class="control-label" for="payment_relief_days">{{ __('Pending payment relief number of days') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="number" class="form-control" id="payment_relief_days" name="payment_relief_days" value="{{ @$model->payment_relief_days ?? '' }}" min="0" maxlength="100" aria-describedby="payment_relief_days" required />
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="control-label" for="reseller_credit_limit">{{ __('Reseller Credit Limit') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <input type="number" class="form-control" id="reseller_credit_limit" name="reseller_credit_limit" value="{{ @$model->reseller_credit_limit ?? '' }}" min="0" maxlength="100" aria-describedby="payment_relief_days" required />
                                                                </div>
                                                            </div>
                                                            <div class="row tab-form pt-3">
                                                                <h3>{{ __('Grace Period (Cron Job) Settings') }}</h3>
                                                                <div class="col-md-6">
                                                                    <label class="control-label" for="reseller_invoice_cron_day">{{ __('Invoice Day') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <select class="form-control" name="reseller_invoice_cron_day" id="reseller_invoice_cron_day">
                                                                        <option @if(@$model->reseller_invoice_cron_day == 1) selected @endif value="1">Monday</option>
                                                                        <option @if(@$model->reseller_invoice_cron_day == 2) selected @endif value="2">Tuesday</option>
                                                                        <option @if(@$model->reseller_invoice_cron_day == 3) selected @endif value="3">Wednesday</option>
                                                                        <option @if(@$model->reseller_invoice_cron_day == 4) selected @endif value="4">Thursday</option>
                                                                        <option @if(@$model->reseller_invoice_cron_day == 5) selected @endif value="5">Friday</option>
                                                                        <option @if(@$model->reseller_invoice_cron_day == 6) selected @endif value="6">Saturday</option>
                                                                        <option @if(@$model->reseller_invoice_cron_day == 7) selected @endif value="7">Sunday</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="control-label" for="reseller_invoice_cron_days_duration">{{ __('Payment Cycle') }}<small class="asterik" style="color:red">*</small></label>
                                                                    <select class="form-control" name="reseller_invoice_cron_days_duration" id="reseller_invoice_cron_days_duration">
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
                                                                <div class="row tab-form">
                                                                    <div class="form-group col-md-12">
                                                                        <h3>{{ __('Refund Grace Period') }}</h3>
                                                                    </div>
                                                                    <div class="form-group col-md-4">
                                                                        <label class="control-label" for="refund_grace_period_days">{{ __('Number of Days') }}<small class="asterik" style="color:red">*</small></label>
                                                                        <input type="number" class="form-control" id="refund_grace_period_days" name="refund_grace_period_days" value="{{ @$model->refund_grace_period_days ?? '' }}" required />
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

                                <hr>
                                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                   <!-- /.box-body -->
            </div>
        </div>
    </section>
 </div>
@endsection
@section('scripts')

<script type="text/javascript">
    //// Hints  ////

    // Validat single email
    $.validator.addMethod("all_valid_emails", function (value, element) {
        // Code here
        // explode the string with commas and convert to array
        // loop through all indexes
            // verify the regex
            // if vrification fails return false
        // return true at the end of loop
        var items = value.split(',');
        var i;
        for (i = 0; i < items.length; ++i) {
             return this.optional(element) || /^(\s?[^\s,]+@[^\s,]+\.[^\s,]+\s?,)*(\s?[^\s,]+@[^\s,]+\.[^\s,]+)$/i.test(value);   
            
        } 
    }, "Invalid Email or badly formatted.");


    jQuery(".form-validate").validate({
        ignore: [],
        errorClass: "invalid-feedback animated fadeInDown",
        errorElement: "div",
        errorPlacement: function(e, a) {
            jQuery(a).parents(".form-group").append(e);
        },rules: {
            "low_license_email_recipients":{
                all_valid_emails:true
            },
            "registration_email_recipients":{
                all_valid_emails:true
            },
            "orders_bcc_email_recipients":{
                all_valid_emails:true
            }
        },
        highlight: function(e) {
            jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid");
            jQuery(e).closest(".form-group > .form-control").removeClass("is-invalid").addClass("is-invalid");
        },
        success: function(e) {
            jQuery(e).closest(".form-group").removeClass("is-invalid");
            jQuery(e).closest(".form-group").find('.form-control').removeClass("is-invalid");
            jQuery(e).remove();
        }
    });
</script>
@endsection
