@extends('admin.layouts.app')
@section('title', __('Countries'))
@section('styles')
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                    {{ __('Contact Countries') }} / @if ($action == 'Add') {{ __('Add') }} @else {{ __('Edit') }}
                        @endif
                    </h2>
                </div>
                {{-- <div class="col-md-6">
                    <div class="search-input-das">
                        <form>
                            <input type="text" name="search" placeholder="{{ __('Search') }}...">
                        </form>
                    </div>
                </div> --}}
            </div>
        </section>
        <!-- Table content -->
        <section class="content">
            <div class="main-box box">
                <div class="row mt-3">
                    <div class="col-xs-12">
                        <div class="box box-success box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                @if ($action == 'Add') {{ __('Add New Contact Country') }} @else
                                        {{ __('Edit Contact Country') }} @endif
                                </h3>
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
                                        action="{{ route('admin.contacts-countries.store') }}" method="post"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                                        <input type="hidden" name="action" value="{!! $action !!}">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name">{{ __('Name') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                            id="name" name="name" value="{{ old('country_code',  $model->name ?? '') }}" maxlength="255"
                                                            aria-describedby="name" required />
                                                        @error('name')
                                                            <div id="name-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="country_code">{{ __('Country Code') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="text"
                                                            class="form-control @error('country_code') is-invalid @enderror"
                                                            id="country_code" name="country_code"
                                                            value="{{ old('country_code', $model->country_code ?? '') }}" maxlength="255"
                                                            aria-describedby="country_code" required />
                                                        @error('country_code')
                                                            <div id="country_code-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vat_label">{{ __('Vat Label') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="text"
                                                            class="form-control @error('vat_label') is-invalid @enderror"
                                                            id="vat_label" name="vat_label" value="{{old('vat_label',  $model->vat_label ?? '') }}"
                                                            maxlength="255" aria-describedby="vat_label" required />
                                                        @error('vat_label')
                                                            <div id="vat_label-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="country_calling_code">{{ __('Country Calling Code') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="text"
                                                            class="form-control @error('country_calling_code') is-invalid @enderror"
                                                            id="country_calling_code" name="country_calling_code"
                                                            value="{{ old('country_calling_code', $model->country_calling_code ?? '') }}" maxlength="255"
                                                            aria-describedby="country_calling_code" required />
                                                        @error('country_calling_code')
                                                            <div id="country_calling_code-error"
                                                                class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="currency">{{ __('Currency') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="text"
                                                            class="form-control @error('currency') is-invalid @enderror"
                                                            id="currency" name="currency" value="{{ old('currency', $model->currency ?? '') }}"
                                                            maxlength="255" aria-describedby="currency" required />
                                                        @error('currency')
                                                            <div id="currency-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="latitude">{{ __('Latitude') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="text"
                                                            class="form-control @error('latitude') is-invalid @enderror"
                                                            id="latitude" name="latitude" value="{{ old('latitude', $model->latitude ?? '') }}"
                                                            maxlength="255" aria-describedby="latitude" required />
                                                        @error('latitude')
                                                            <div id="latitude-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="longitude">{{ __('Longitude') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="text"
                                                            class="form-control @error('longitude') is-invalid @enderror"
                                                            id="longitude" name="longitude" value="{{ old('longitude', $model->longitude ?? '') }}"
                                                            maxlength="255" aria-describedby="longitude" required />
                                                        @error('longitude')
                                                            <div id="longitude-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vat_in_percentage">{{ __('VAT (%)') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="number"
                                                            class="form-control @error('vat_in_percentage') is-invalid @enderror"
                                                            id="vat_in_percentage" min="0" max="100" name="vat_in_percentage" step=".01" value="{{ old('vat_in_percentage', $model->vat_in_percentage ?? '') }}" aria-describedby="vat_in_percentage" required />
                                                         @error('vat_in_percentage')
                                                            <div id="vat_in_percentage-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                @if($action == "Add") @php $checked = "checked"; @endphp @endif
                                                <div class="col-md-12">
                                                   <div class="form-group">
                                                      <label>{{ __('Apply Default VAT') }}<small class="asterik" style="color:red">*</small></label>
                                                      <div class="form-check">
                                                         <input class="form-check-input" type="radio" name="is_default_vat" id="is_default_vat1" value="1" @if(isset($model->is_default_vat) && $model->is_default_vat == 1) checked @endif>
                                                         <label class="form-check-label" for="is_default_vat1">
                                                         {{ __('Yes') }}
                                                         <small class="asterik" style="color:red">*</small></label>
                                                      </div>
                                                      <div class="form-check">
                                                         <input class="form-check-input" type="radio" name="is_default_vat" id="is_default_vat2" value="0" @if(isset($model->is_default_vat) && $model->is_default_vat == 0) checked @endif {{@$checked}}>
                                                         <label class="form-check-label" for="is_default_vat2">
                                                         {{ __('No') }}
                                                         <small class="asterik" style="color:red">*</small></label>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="col-md-12 ">
                                                    <div class="form-group">
                                                        <button type="submit" class="skin-green-light-btn btn ">{{ __('Save') }}</button>
                                                        <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                                            href="{{ route('admin.contacts-countries.index') }}">{{ __('Discard') }}</a>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="col-md-12">
                                                    <div class="avatar-upload form-group" >
                                                        <div class="avatar-fileds hover-effect">
                                                            <div class="avatar-edit">
                                                                <input type='file' id="imageUpload" name="image" value="{{  old('image', $model->image ?? '')}}"
                                                                    class="form-control @error('image') is-invalid @enderror"
                                                                    maxlength="255" aria-describedby="image" accept="image/*" />
                                                                <label for="imageUpload"><small class="asterik" style="color:red">*</small></label>
                                                            </div>
                                                        </div>

                                                        <div class="avatar-preview">
                                                            <img id="imagePreview" src="{!! checkImage(asset('storage/uploads/countries/' . Hashids::encode(@$model->id) . '/' . @$model->image), 'avatar5.png') !!}" width="100%" height="100%">
                                                        </div>
                                                        @error('image')
                                                            <div id="image-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                <!-- FILE UPLOAD -->

                                {{-- end div image --}}

                            </div>
                        </div>
                    </div>
                </div>

                </form>
            </div>
    </div>
    </div>
    </div>
    <!-- /.box-body -->
    </div>
    </div>
    </div>
    <!-- /.box -->
    </div>
    </div>
    </section>
    <!-- /.content -->
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}">

    </script>
@endsection
