@extends('admin.layouts.app')
@section('title', __('Roles'))
@section('styles')
<style>
    .form-check-label-module{
        color: #009a71;
        font-weight: 500 !important;
    }
    label.form-check-label-all {
        font-size: 17px;
        font-weight: 500;
        color: #009a71;
    }
</style>
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                        {{ __('Role') }} / @if ($action == 'Add') {{ __('Add') }} @else {{ __('Edit') }} @endif
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
                                <h3 class="box-title">@if ($action == 'Add') {{ __('Add New Role') }} @else {{ __('Edit Role') }} @endif</h3>
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
                                    <div class="col-md-12">
                                        <form class="timmunity-custom-dashboard-form mt-2 form-validate"
                                            action="{{ route('admin.roles.store') }}" method="post"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="id" value="{!! @$role->id !!}">
                                            <input type="hidden" name="action" value="{!! $action !!}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name">{{ __('Name') }}<small class="asterik" style="color:red">*</small></label>
                                                        <input type="text"
                                                            class="form-control @error('name') is-invalid @enderror"
                                                            id="name" name="name" value="{{ $role->name ?? '' }}"
                                                            maxlength="255" aria-describedby="name" required />
                                                        @error('name')
                                                            <div id="name-error" class="invalid-feedback animated fadeInDown">
                                                                {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row pull-right">
                                                    <button type="submit"
                                                        class="skin-green-light-btn btn ml-2">{{ __('Save') }}</button>
                                                    <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                                        href="{{ route('admin.roles.index') }}">{{ __('Discard') }}</a>
                                                </div>
                                            </div>
                                            <!-- /.box -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="custom-tabs mt-3 mb-2">
                                                        <ul class="nav nav-tabs">
                                                            <li class="active"><a data-toggle="tab"
                                                                    href="#access-rights">{{ __('Assign Permissions to Role') }}</a>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content custom-tabs-style custom-tabs-pd-set">
                                                            <!--  Access Rights -->
                                                            <div id="access-rights" class="tab-pane fade active in">
                                                                <div class="row tab-form pt-3">
                                                                    <div class="col-md-12">
                                                                        <label
                                                                            class="form-check-label-all"
                                                                            for="form-check-input-all">
                                                                            <input
                                                                                class="form-check-input"
                                                                                type="checkbox"
                                                                                name="form-check-input-all">
                                                                            Select All Modules
                                                                        </label>
                                                                    </div>
                                                                    <!-- Tab Col No 01 -->
                                                                    <div class="col-md-12">
                                                                        @php
                                                                            $numOfCols = 3;
                                                                            $rowCount = 0;
                                                                            $ColWidth = 12 / $numOfCols;
                                                                        @endphp
                                                                        <div class="row">
                                                                            @foreach ($modules as $module)
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                            <h3>{{ $module->module_name }} </h3>
                                                                                            <label
                                                                                                class="form-check-label-module"
                                                                                                for="form-check-input-module">
                                                                                                <input
                                                                                                    class="form-check-input"
                                                                                                    type="checkbox"
                                                                                                    name="form-check-input-module">
                                                                                                Select all sub modules
                                                                                            </label>
                                                                                        <div class="row">
                                                                                            @foreach ($module->permissions as $permission)
                                                                                                @if (isset($assignedPermission) && in_array($permission->id, $assignedPermission))
                                                                                                    @php
                                                                                                        $check = 'checked';
                                                                                                    @endphp
                                                                                                @else
                                                                                                    @php
                                                                                                        $check = '';
                                                                                                    @endphp
                                                                                                @endif
                                                                                                <div
                                                                                                    class="col-md-{{ $ColWidth }}">
                                                                                                    <div
                                                                                                        class="form-check">
                                                                                                        <input
                                                                                                            class="form-check-input"
                                                                                                            type="checkbox"
                                                                                                            id="permissions"
                                                                                                            value="{{ $permission->id ?? '' }}"
                                                                                                            name="permissions[]"
                                                                                                            {{ $check }}>
                                                                                                        <label
                                                                                                            class="form-check-label"
                                                                                                            for="{{ $permission->name }}">
                                                                                                            {{ ucfirst($permission->name) }}
                                                                                                        </label>
                                                                                                    </div>
                                                                                                </div>
                                                                                                @php
                                                                                                    $rowCount++;
                                                                                                @endphp
                                                                                                @if ($rowCount % $numOfCols == 3)
                                                                                                    </div>
                                                                                                    <div class="row">
                                                                                                @endif
                                                                                            @endforeach
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                             @endforeach
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
<script>
    $('body').on('change', 'input[name=form-check-input-all]', function(){
        if($(this).is(':checked') == true )
        {
            $('input[name=form-check-input-module]').prop('checked',true);
        }
        else
        {
            $('input[name=form-check-input-module]').prop('checked',false);
        }
        $('input[name=form-check-input-module]').trigger('change');
    });
    $('body').on('change', 'input[name=form-check-input-module]', function(){
        if($(this).is(':checked') == true )
        {
            $(this).parents('.form-group').find('.row input[type=checkbox]').prop('checked',true);
        }
        else
        {
            $(this).parents('.form-group').find('.row input[type=checkbox]').prop('checked',false)
        }
        if($('input[name=form-check-input-module]').is(':checked') == true)
        {
            $('input[name=form-check-input-all]').prop('checked', true);
        }
        else
        {
            $('input[name=form-check-input-all]').prop('checked', false);
        }
    });
    $('body').on('change', 'input[name="permissions[]"]', function(){
        if($(this).closest('.row').find('input[name="permissions[]"]').is(":checked") == true )
        {
            $(this).closest('.row').siblings('.form-check-label-module').find('input').prop('checked',true);
        }else{
            $(this).closest('.row').siblings('.form-check-label-module').find('input').prop('checked',false)
        }
        if($('input[name=form-check-input-module]').is(':checked') == true)
        {
            $('input[name=form-check-input-all]').prop('checked', true);
        }
        else
        {
            $('input[name=form-check-input-all]').prop('checked', false);
        }
    });
</script>
@endsection
