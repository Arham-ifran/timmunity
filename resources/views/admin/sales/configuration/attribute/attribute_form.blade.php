@extends('admin.layouts.app')
@section('title', __('Attributes'))
@section('styles')
<style>
    .fa.fa-trash{
        cursor:pointer;
    }
</style>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header top-header">
            <div class="row">
                <div class="col-md-12">
                    <h2>
                        {{ __('Attribute') }} /
                        <small>@if($action == "Add") {{ __('Add') }} @else {{ __('Edit') }} @endif</small>
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="box-header">
                    <div class="row">
                        <div class="col-md-4">
                            <a class="skin-gray-light-btn btn save-attribute-d" href="javascript:void(0)">@if($action == "Add") {{ __('Save') }} @else {{ __('Update') }} @endif</a>
                            <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2"
                                href="{{ route('admin.attribute.index') }}">{{ __('Discard') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 attribute-box">
                            <form class="form-validate" id="attribute-form" method="POST"
                                action="{{ route('admin.attribute.store') }}" enctype="multipart/form-data">
                                @csrf
                                @if (isset($model->id))
                                    <input type="hidden" name="id" value="{{ Hashids::encode($model->id) }}" />
                                    <input type="hidden" name="action" value="Edit" />
                                @endif
                                <div class="row">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6" style="padding-left: 0px;">
                                                <div class="form-group">
                                                    <h3>{{ __('Attribute Name') }}<small class="asterik" style="color:red">*</small></h3>
                                                    <input type="text" class="form-control" placeholder=""
                                                        name="attribute_name" value="{{ translation(@$model->id,13,app()->getLocale(),'attribute_name',@$model->attribute_name) ?? '' }}" required="required" />
                                                </div>
                                                <div class="col-md-12 pl-0">
                                                    <h3 class="col-md-12 pl-0">{{ __('Display Type') }}</h3>
                                                    <div class="col-md-12  pl-0 attribute-radio-buttons">
                                                        <input type="radio" name="display_type" value="1" @if (@$model->display_type == 1) checked @endif />
                                                        <span class="color-black">{{ __('Radio') }}</span>
                                                        <input type="radio" name="display_type" value="2" @if (@$model->display_type == 2) checked @endif />
                                                        <span class="color-black">{{ __('Select') }}</span>
                                                        <input type="radio" name="display_type" value="3" @if (@$model->display_type == 3) checked @endif />
                                                        <span class="color-black">{{ __('Color') }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pl-0 pt-1">
                                                    <h3 class="col-md-12 pl-0">{{ __('Variants Creation mode') }}</h3>
                                                    <div class="col-md-12  pl-0 attribute-radio-buttons">
                                                        <input type="radio" name="variants_creation_mode" value="1" @if (@$model->variants_creation_mode == 1) checked @endif />
                                                        <span class="color-black">{{ __('Instantly') }}</span>
                                                        <input type="radio" name="variants_creation_mode" value="2" @if (@$model->variants_creation_mode == 2) checked @endif />
                                                        <span class="color-black">{{ __('Dynamically') }}</span>
                                                        <input type="radio" name="variants_creation_mode" value="3" @if (@$model->variants_creation_mode == 3) checked @endif />
                                                        <span class="color-black">{{ __('Never') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="custom-tabs mt-3">
                                            <ul class="nav nav-tabs">
                                                <li class="active">
                                                    <a data-toggle="tab" href="#team-members">{{ __('Attribute Values') }}</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div id="team-members" class="tab-pane fade in active">
                                                    <div class="row">
                                                        {{-- <div class="col-md-4">
                                                            <a href="#" type="button" class="btn btn-primary"
                                                                data-toggle="modal" data-target="#add-member">
                                                                Add Attribute
                                                            </a>
                                                        </div>
                                                        <div class="modal fade" id="add-member" tabindex="-1" role="dialog"
                                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h3 class="modal-title col-sm-9"
                                                                            id="exampleModalLabel">Add Attribute</h3>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Close</button>
                                                                        <button type="button" class="btn btn-primary">Save
                                                                            changes</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> --}}
                                                        <table id="example1" class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('Attribute Value') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="attributeValuesTableBody">
                                                                @if( !isset($model_values) )
                                                                    <tr>
                                                                        <td>
                                                                            <input name="attribute_value[]" type="text"
                                                                                required>
                                                                        </td>
                                                                        <td>
                                                                            <i class="fa fa-trash"></i>
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    @foreach($model_values as $att_val)
                                                                        <tr>
                                                                            <td>
                                                                                <input name="attribute_value[]" type="text"
                                                                                    required value="{{ $att_val->attribute_value }}"><small class="asterik" style="color:red">*</small>
                                                                            </td>
                                                                            <td>
                                                                                <i class="fa fa-trash"></i>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                        <a href="#." type="button" class="btn btn-primary add-attr-value">
                                                            {{ __('Add Attribute Value') }}
                                                        </a>
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
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>
        $('body').on('click', '.save-attribute-d', function() {
            $('#attribute-form').submit();
        });
        $('body').on('click', '.add-attr-value', function() {
            html = '<tr><td><input name="attribute_value[]" type="text" required></td> <td><i class="fa fa-trash"></i></td></tr>';
            $('#attributeValuesTableBody').append(html);
            $('#attributeValuesTableBody input:last-child').focus();
        })
        $('body').on('click', '#attributeValuesTableBody .fa.fa-trash', function() {
            $(this).parents('tr').remove();
        });
    </script>
@endsection
