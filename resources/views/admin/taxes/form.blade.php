@extends('admin.layouts.app')
@section('title', __('Taxes'))
@section('styles')
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                    {{ __('Tax Configuration') }} / @if ($action == 'Add') {{ __('Add') }} @else {{ __('Edit') }}
                        @endif
                    </h2>
                </div>
            </div>
        </section>
        <!-- Table content -->
        <section class="content">
            <form class="timmunity-custom-dashboard-form mt-2 form-validate"  action="{{ route('admin.taxes.store') }}" method="post">
                <div class="main-box box">
                    <div class="row mt-3">
                        <div class="col-xs-12">
                            <div class="box box-success box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">
                                    @if ($action == 'Add') {{ __('Add New Tax Configuration') }} @else
                                            {{ __('Edit Tax Configuration') }} @endif
                                    </h3>

                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="row">
                                    <div class="col-md-12">

                                            @csrf
                                            <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                                            <input type="hidden" name="action" value="{!! $action !!}">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="name">{{ __('Tax Name') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="text"
                                                            class="form-control @error('name') is-invalid @enderror"
                                                            id="name" name="name"
                                                            value="{{ old('name', $model->name ?? '') }}" maxlength="255"
                                                            aria-describedby="name" required />
                                                        @error('name')
                                                            <div id="name-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="type">{{ __('Type') }}</label>
                                                        <select id="type" name="type"
                                                            class="form-control @error('type') is-invalid @enderror"
                                                            aria-describedby="type" required>

                                                            <option selected>---{{ __('Select a tax type') }}---</option>
                                                            <option value="0" @if (isset($model) && 0 == $model->type) selected @endif>{{ __('None') }}</option>
                                                            <option value="1" @if (isset($model) && 1 == $model->type) selected @endif>{{ __('Sales') }}</option>
                                                            <option value="2" @if (isset($model) && 2 == $model->type) selected @endif>{{ __('Purchase') }}</option>
                                                        </select>
                                                        @error('type')
                                                            <div id="type-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="computation">{{ __('Computation') }}</label>
                                                        <select id="computation" name="computation"
                                                            class="form-control @error('computation') is-invalid @enderror"
                                                            aria-describedby="type" required>

                                                            <option selected>---{{ __('Select a tax computation') }}---</option>
                                                            <option value="0" @if (isset($model) && 0 == $model->computation) selected @endif>{{ __('Fixed') }}</option>
                                                            <option value="1" @if (isset($model) && 1 == $model->computation) selected @endif>{{ __('Percentage') }}</option>
                                                        </select>
                                                        @error('computation')
                                                            <div id="computation-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="applicable_on">{{ __('Applicable On') }}</label>
                                                        <select id="applicable_on" name="applicable_on"
                                                            class="form-control @error('applicable_on') is-invalid @enderror"
                                                            aria-describedby="type" required>

                                                            <option selected>---{{ __('Select a applicable') }}---</option>
                                                            <option value="0" @if (isset($model) && 0 == $model->applicable_on) selected @endif>{{ __('Customers') }}</option>
                                                            <option value="1" @if (isset($model) && 1 == $model->applicable_on) selected @endif>{{ __('Vendors') }}</option>
                                                        </select>
                                                        @error('applicable_on')
                                                            <div id="applicable_on-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="amount">{{ __('Amount') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="text"
                                                            class="form-control @error('amount') is-invalid @enderror"
                                                            id="amount" name="amount"
                                                            value="{{ old('amount', $model->amount ?? '') }}"
                                                            aria-describedby="amount" required />
                                                        @error('amount')
                                                            <div id="amount-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mt-3">
                                                    <div class="form-group">
                                                        <button type="submit" class="skin-green-light-btn btn ">{{ __('Save') }}</button>
                                                        <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                                            href="{{ route('admin.taxes.index') }}">{{ __('Discard') }}</a>
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
