@extends('admin.layouts.app')
@section('title', __('Ecommerce Categories'))
@section('styles')
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                        {{ __('Ecommerce Categories') }} / @if ($action == 'Add') {{ __('Add') }} @else {{ __('Edit') }} @endif
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
                                <h3 class="box-title">
                                @if ($action == 'Add')
                                    {{ __('Add New Ecommerce Category') }}
                                @else
                                    {{ __('Edit Ecommerce Category') }}
                                @endif
                                </h3>

                                <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="row">
                                <div class="col-md-12">
                                    <form class="timmunity-custom-dashboard-form mt-2 form-validate"
                                        action="{{ route('admin.eccomerce-categories.store') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                                        <input type="hidden" name="action" value="{!! $action !!}">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">{{ __('Category Name') }}<small class="asterik" style="color:red">*</small></label>
                                                <input type="text"
                                                    class="form-control @error('category_name') is-invalid @enderror"
                                                    id="category_name" name="category_name"
                                                    value="{{ old('category_name', @$model->category_name ?? '') }}" maxlength="255"
                                                    aria-describedby="name" required />
                                                    @error('category_name')
                                                    <div id="category_name-error" class="invalid-feedback animated fadeInDown">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="type">{{ __('Category Slug') }}<small class="asterik" style="color:red">*</small></label>
                                                <input type="text"
                                                    class="form-control @error('category_slug') is-invalid @enderror"
                                                    id="category_slug" name="category_slug"
                                                    value="{{ old('category_slug', @$model->category_slug ?? '') }}" maxlength="255"
                                                    aria-describedby="name" required />
                                                @error('type')
                                                    <div id="category_slug-error" class="invalid-feedback animated fadeInDown">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="type">{{ __('Parent Category') }}</label>
                                                <select id="parent_category" name="parent_category"
                                                    class="form-control @error('parent_category') is-invalid @enderror"
                                                    aria-describedby="type">
                                                    <option value="" selected>---{{ __('Select a parent category') }}---</option>
                                                    @foreach($parent_categories as $pc)
                                                        <option value="{{ $pc->id }}" @if (isset($model) && $pc->id == $model->parent_category) selected @endif>{{ $pc->category_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <div class="form-group">
                                                <button type="submit" class="skin-green-light-btn btn ">{{ __('Save') }}</button>
                                                <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                                    href="{{ route('admin.taxes.index') }}">{{ __('Discard') }}</a>
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
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>
        $('body').on('keyup', 'input[name=category_name]', function(){
            slug = slugify($(this).val());
            $.ajax({
                    url: "{{ route('admin.eccomerce-categories.check_slug') }}",
                    type: 'GET',
                    data: {
                        slug: slug,
                        id: $('input[name=id]').val()
                    },
                    success: function (seo_slug) {
                        $("input[name=category_slug]").val(seo_slug);
                    },
                    complete:function(data){
                        // Hide loader container
                    }
                });
        });
        function slugify(text)
        {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
        }
    </script>
@endsection
