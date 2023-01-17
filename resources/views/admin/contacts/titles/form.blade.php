@extends('admin.layouts.app')
@section('title', __('Contact Tags'))
@section('styles')
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                        {{ __('Contact Title') }} / @if ($action == 'Add') {{ __('Add') }} @else {{ __('Edit') }} @endif
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
                                <h3 class="box-title">@if ($action == 'Add') {{ __('Add New Contact Title') }} @else {{ __('Edit Contact Title') }} @endif</h3>
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
                                    {{-- <div class="col-md-6"> --}}
                                    <form class="timmunity-custom-dashboard-form mt-2 form-validate"
                                        action="{{ route('admin.contacts-titles.store') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                                        <input type="hidden" name="action" value="{!! $action !!}">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="title">{{ __('Title') }}<small class="asterik" style="color:red">*</small></label>
                                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                                    id="title" name="title"
                                                    value="{{ old('title', $model->title ?? '') }}" maxlength="255"
                                                    aria-describedby="title" required />
                                                @error('title')
                                                    <div id="title-error" class="invalid-feedback animated fadeInDown">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="abbreviation">{{ __('Abbreviation') }}<small class="asterik" style="color:red">*</small></label>
                                                <input type="text"
                                                    class="form-control @error('abbreviation') is-invalid @enderror"
                                                    id="abbreviation" name="abbreviation"
                                                    value="{{ old('abbreviation', $model->abbreviation ?? '') }}"
                                                    maxlength="255" aria-describedby="abbreviation" required />
                                                @error('name')
                                                    <div id="name-error" class="invalid-feedback animated fadeInDown">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="salution">{{ __('Salutation') }}<small class="asterik" style="color:red">*</small></label>
                                                <input type="text"
                                                    class="form-control @error('salutation') is-invalid @enderror"
                                                    id="salutation" name="salutation"
                                                    value="{{ old('salutation', $model->salutation ?? '') }}"
                                                    maxlength="255" aria-describedby="salutation" required />
                                                @error('salutation')
                                                    <div id="salutation-error" class="invalid-feedback animated fadeInDown">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit"
                                                class="skin-green-light-btn btn ml-2">{{ __('Save') }}</button>
                                            <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                                href="{{ route('admin.contacts-titles.index') }}">{{ __('Discard') }}</a>
                                        </div>
                                </div>
                                {{-- </div> --}}
                                <!-- FILE UPLOAD -->
                            </div>
                        </div>

                        </form>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
@endsection
