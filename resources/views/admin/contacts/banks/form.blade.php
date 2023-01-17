@extends('admin.layouts.app')
@section('title', __('Banks'))
@section('styles')
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                        {{ __('Contact Bank') }} / @if ($action == 'Add') {{ __('Add') }} @else {{ __('Edit') }} @endif
                    </h2>
                </div>
                <div class="col-md-6">
                    <div class="search-input-das">
                        <form>
                            <input type="text" name="search" placeholder="{{ __('Search') }}...">
                        </form>
                    </div>
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
                                <h3 class="box-title">@if ($action == 'Add') {{ __('Add New Contact Bank') }} @else {{ __('Edit Contact Bank') }} @endif</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                    </button>
                                </div>
                                <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="row">
                                <div class="col-md-12">
                                    <form class="timmunity-custom-dashboard-form mt-2 form-validate"
                                        action="{{ route('admin.contacts-banks.store') }}" method="post" id="bankform">
                                        @csrf
                                        <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                                        <input type="hidden" name="action" value="{!! $action !!}">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="name">{{ __('Name') }}<small class="asterik" style="color:red">*</small></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                    id="name" name="name" value="{{ old('name', $model->name ?? '') }}"
                                                    maxlength="100" aria-describedby="name" required />
                                                @error('name')
                                                    <div id="name-error" class="invalid-feedback animated fadeInDown">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label
                                                    for="bank_identifier_code">{{ __('Bank Identifier Code') }}<small class="asterik" style="color:red">*</small></label>
                                                <input type="text"
                                                    class="form-control @error('bank_identifier_code') is-invalid @enderror"
                                                    id="bank_identifier_code" name="bank_identifier_code"
                                                    value="{{ old('bank_identifier_code', $model->bank_identifier_code ?? '') }}"
                                                    maxlength="100" aria-describedby="bank_identifier_code" required />
                                                @error('bank_identifier_code')
                                                    <div id="bank_identifier_code-error"
                                                        class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="phone">{{ __('Phone') }}<small class="asterik" style="color:red">*</small></label>
                                                <input type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57'
                                                    class="form-control @error('phone') is-invalid @enderror" id="phone"
                                                    name="phone" value="{{ old('phone', $model->phone ?? '') }}" min="1"
                                                    maxlength="100" aria-describedby="phone" required />
                                                @error('phone')
                                                    <div id="phone-error" class="invalid-feedback animated fadeInDown">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="email">{{ __('Email') }}<small class="asterik" style="color:red">*</small></label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                                    name="email" value="{{ old('email', $model->email ?? '') }}"
                                                    maxlength="100" aria-describedby="email" required />
                                                @error('email')
                                                    <div id="email-error" class="invalid-feedback animated fadeInDown">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <h2 class="green-title col-sm-12">{{ __('Bank Address') }}</h2>
                                        <div class="row">
                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <label for="street_1">{{ __('Address 1') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input type="text"
                                                        class="form-control @error('street_1') is-invalid @enderror"
                                                        id="street_1" name="street_1"
                                                        value="{{ old('street_1', translation(@$model->id, 7, app()->getLocale(), 'street_1', @$model->street_1) ?? '') }}"
                                                        maxlength="255" aria-describedby="street_1" required />
                                                    @error('street_1')
                                                        <div id="street_1-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <label for="street_2">{{ __('Address 2') }}</label>
                                                    <input type="text"
                                                        class="form-control @error('street_2') is-invalid @enderror"
                                                        id="street_2" name="street_2"
                                                        value="{{ old('street_2', translation(@$model->id, 7, app()->getLocale(), 'street_2', @$model->street_2) ?? '') }}"
                                                        maxlength="255" aria-describedby="street_2" />
                                                    @error('street_2')
                                                        <div id="street_2-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">

                                                <div class="form-group">
                                                    <label for="city">{{ __('City') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                                        id="city" name="city"
                                                        value="{{ old('city', translation(@$model->id, 7, app()->getLocale(), 'city', @$model->city) ?? '') }}"
                                                        maxlength="255" aria-describedby="city" required />
                                                    @error('city')
                                                        <div id="city-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">

                                                <div class="form-group">
                                                    <label for="zipcode">{{ __('Zip code') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input type="text"
                                                        class="form-control @error('zipcode') is-invalid @enderror" id="zipcode"
                                                        name="zipcode" value="{{ old('zipcode', $model->zipcode ?? '') }}"
                                                        maxlength="255" aria-describedby="zipcode" required />
                                                    @error('zipcode')
                                                        <div id="zipcode-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">

                                                <div class="form-group">
                                                    <label for="state">{{ __('State') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input type="text" class="form-control @error('state') is-invalid @enderror"
                                                        id="state" name="state"
                                                        value="{{ old('state', $model->state ?? '') }}" maxlength="255"
                                                        aria-describedby="state" required />
                                                    @error('state')
                                                        <div id="state-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">

                                                <div class="form-group">
                                                    <label for="country-id">{{ __('Country') }}<small class="asterik" style="color:red">*</small></label>
                                                    <select id="country-id" name="country_id"
                                                        class="form-control @error('country_id') is-invalid @enderror"
                                                        aria-describedby="country_id" required>

                                                        <option value="">---{{ __('Select a country') }}---</option>
                                                        @if ($contact_countries->count() > 0)
                                                            @foreach ($contact_countries as $country)
                                                                <option value="{{ $country->id }}" @if (isset($model) && $country->id == $model->country_id) selected @endif>
                                                                    {{ $country->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('country-id')
                                                        <div id="country-id-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <button type="submit"
                                                        class="skin-green-light-btn btn ">{{ __('Save') }}</button>
                                                    <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                                        href="{{ route('admin.contacts-banks.index') }}">{{ __('Discard') }}</a>

                                                </div>
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

@endsection
@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>
         $.validator.addMethod("email", function (value, element) {
        return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
    }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
        $('#bankform').validate({
            rules: {
                "name":{
                    required:true
                },
                "bank_identifier_code":{
                    required:true
                },
                "phone":{
                    required:true,
                    // regex:"[0-9]{3}-[0-9]{2}-[0-9]{3}"
                },
                "email":{
                    required:true,
                    email: true
                },
                "street_1":{
                    required:true
                },
                "city":{
                    required:true
                },
                "zipcode":{
                    required:true
                },
                "state":{
                    required:true
                },
                "country_id":{
                    required:true
                }
            },

        });
    </script>
@endsection
