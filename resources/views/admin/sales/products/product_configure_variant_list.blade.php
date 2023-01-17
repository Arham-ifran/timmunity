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
table#example1 tr:hover {
    background: #009a7129;
    cursor: pointer;
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
                    <small>{{ __('Configure Variants') }}</small>
                </h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-4">
                        {{-- <a class="skin-gray-light-btn btn save-product-d" href="javascript:void(0)">{{ __('Save') }}</a> --}}
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
                            <div class="col-md-12">
                                <!-- Variants -->
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Attribute') }}</th>
                                            <th>{{ __('Value') }}</th>
                                            <th>{{ __('Display Type') }}</th>
                                            <th>{{ __('Value Price Extra') }}</th>
                                            @can('Configure Variant')
                                            <th>{{ __('Action') }}</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody id="attributesTableBody">
                                        @foreach ($product_attached_attributes as $paa )
                                            @foreach ( $paa->attributeValue as $paav )
                                                <tr data-href="{{ route('admin.products.configure.variants.edit', Hashids::encode($paav->id)) }}">
                                                    <td>{{ translation( $paa->attributeDetail->id,13,app()->getLocale(),'attribute_name',$paa->attributeDetail->attribute_name) }}</td>
                                                    <td>{{ $paav->value }}</td>
                                                    <td>
                                                        @switch($paa->attributeDetail->display_type)
                                                            @case(1)
                                                                {{ __('Radio') }}
                                                                @break
                                                            @case(2)
                                                                {{ __('Select') }}
                                                                @break
                                                            @case(3)
                                                                {{ __('Color') }}
                                                                @break
                                                            @default

                                                        @endswitch
                                                    </td>
                                                    <td>{{ $paav->extra_price }} </td>
                                                    @can('Configure Variant')
                                                    <td>
                                                        <a href="{{ route('admin.products.configure.variants.edit', Hashids::encode($paav->id)) }}" class="btn btn-primary">{{ __('Configure') }}</a>
                                                        @if($paav->is_active == 1)
                                                        <a href="{{ route('admin.products.configure.variants.status', Hashids::encode($paav->id)) }}?status=0" class="btn btn-danger">{{ __('De-Activate Variant') }}</a>
                                                        @else
                                                        <a href="{{ route('admin.products.configure.variants.status', Hashids::encode($paav->id)) }}?status=1" class="btn btn-success">{{ __('Activate Variant') }}</a>
                                                        @endif
                                                    </td>
                                                    @endcan
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
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

    $('body').on('click','table#example1 tbody tr', function(){
        window.location = $(this).attr("data-href");
    });
</script>
@endsection
