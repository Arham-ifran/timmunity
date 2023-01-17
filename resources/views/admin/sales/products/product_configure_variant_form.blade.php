@extends('admin.layouts.app')
@section('title', __('Configure Variants'))
@section('styles')
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<style>
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #499a72;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
    color:white;
}
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header top-header">
        <div class="row">
            <div class="col-md-12">
                <h2>
                    {{ __('Product') }} /
                    <small>{{ __('Configure Variant') }} {{ translation( $product_attached_attribute->attachedAttribute->attributeDetail->id,13,app()->getLocale(),'attribute_name',$product_attached_attribute->attachedAttribute->attributeDetail->attribute_name) }} : {{ $product_attached_attribute->value }} </small>
                </h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-4">
                        <a class="skin-gray-light-btn btn save-product-d" href="javascript:void(0)">{{ __('Save') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-8">
                                <form action="{{ route('admin.products.configure.variants.edit.post') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group">
                                            <h3 class="col-md-12">{{ __('Value') }}</h3>
                                            <div class="col-sm-12 form-group">
                                                <p class=""> {{ translation( $product_attached_attribute->attachedAttribute->attributeDetail->id,13,app()->getLocale(),'attribute_name',$product_attached_attribute->attachedAttribute->attributeDetail->attribute_name) }} : {{ $product_attached_attribute->value }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h4>{{ __('Variant Extra Price') }}</h4>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <input type="text" required="required" name="extra_price" value="{{ $product_attached_attribute->extra_price }}" class="form-control" />
                                            <input type="hidden" name="id" value="{{ Hashids::encode($product_attached_attribute->id) }}"  class="form-control" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection

@section('scripts')
<script src="{{ asset('backend/dist/js/custom.js') }}"></script>
<script>
    $('body').on('click','.save-product-d',function(){
        $('form').submit();
    })
</script>
@endsection
