@extends('admin.layouts.app')
@section('title', __('Reseller Packages'))
@section('styles')
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                    {{ __('Reseller Packages') }} / @if ($action == 'Add') {{ __('Add') }} @else {{ __('Edit') }}
                        @endif
                    </h2>
                </div>
            </div>
        </section>
        <!-- Table content -->
        <section class="content">
            <form class="timmunity-custom-dashboard-form mt-2 form-validate"  action="{{ route('admin.reseller-package.store') }}" method="post">
                <div class="main-box box">
                    <div class="row mt-3">
                        <div class="col-xs-12">
                            <div class="box box-success box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">
                                    @if ($action == 'Add') {{ __('Add New Reseller Package') }} @else
                                            {{ __('Edit Reseller Package') }} @endif
                                    </h3>

                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="row">
                                    <div class="col-md-12">
                                            @csrf
                                            <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                                            <input type="hidden" name="action" value="{!! $action !!}">
                                            <div class="row mt-2 pt-2">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="name">{{ __('Package Name') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="text"
                                                            class="form-control @error('package_name') is-invalid @enderror"
                                                            id="package_name" name="package_name"
                                                            value="{{ old('package_name', $model->package_name ?? '') }}" maxlength="150"
                                                            aria-describedby="package_name" required />
                                                        @error('package_name')
                                                            <div id="package_name-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="type">{{ __('Model') }}</label>
                                                        <select id="type" name="model"
                                                            class="form-control @error('model') is-invalid @enderror"
                                                            aria-describedby="model" required>

                                                            <option selected>---{{ __('Select a package type') }}---</option>
                                                            <option value="0" @if (isset($model) && 0 == $model->model) selected @endif>{{ __('Increase') }}</option>
                                                            <option value="1" @if (isset($model) && 1 == $model->model) selected @endif>{{ __('Discount') }}</option>
                                                        </select>
                                                        @error('model')
                                                            <div id="model-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="percentage">{{ __('Percentage') }}</label>
                                                        <input type="number"
                                                            class="form-control @error('percentage') is-invalid @enderror"
                                                            id="percentage" name="percentage"
                                                            value="{{ old('percentage', $model->percentage ?? '') }}" step='0.01'
                                                            aria-describedby="percentage" required />
                                                        @error('percentage')
                                                            <div id="percentage-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="is_active">{{ __('Status') }}</label>
                                                        <select id="is_active" name="is_active"
                                                            class="form-control @error('is_active') is-invalid @enderror"
                                                            aria-describedby="is_active" required>

                                                            <option selected>---{{ __('Select Status') }}---</option>
                                                            <option value="0" @if (isset($model) && 0 == $model->is_active) selected @endif>{{ __('In-Active') }}</option>
                                                            <option value="1" @if (isset($model) && 1 == $model->is_active) selected @endif>{{ __('Active') }}</option>
                                                        </select>
                                                        @error('model')
                                                            <div id="is_active-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mt-3">
                                                    <div class="form-group">
                                                        <button type="submit" class="skin-green-light-btn btn ">{{ __('Save') }}</button>
                                                        <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                                            href="{{ route('admin.reseller-package.index') }}">{{ __('Discard') }}</a>
                                                    </div>
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
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}">

    </script>
@endsection
