@extends('admin.layouts.app')
@section('title',  __('CMS Pages'))
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
        <div class="row">
            <div class="col-md-6">
                <h2>
                {{ __('CMS Page') }} / @if($action == "Add") {{ __('Add') }} @else {{ __('Edit') }} @endif
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
                            <h3 class="box-title">@if($action == "Add") {{ __('Add New CMS Page') }} @else {{ __('Edit CMS Page') }} @endif</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <form class="mt-2 form-validate" action="{{ route('admin.cms.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                                        <input type="hidden" name="action" value="{!!$action!!}">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="title">{{ __('Title') }}<small class="asterik" style="color:red">*</small></label>
                                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', translation(@$model->id,22,app()->getLocale(),'title',@$model->title) ?? '') }}" maxlength="100" aria-describedby="title" required />
                                                @error('title')
                                                <div id="title-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="meta_title">{{ __('Meta Title') }}<small class="asterik" style="color:red">*</small></label>
                                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" value="{{ old('meta_title', translation(@$model->id,22,app()->getLocale(),'meta_title',@$model->meta_title) ?? '') }}" maxlength="100" aria-describedby="meta_title" required />
                                                @error('meta_title')
                                                <div id="meta_title-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="seo_url">{{ __('SEO URL') }}<small class="asterik" style="color:red">*</small></label>
                                                <input type="text" class="form-control @error('seo_url') is-invalid @enderror" id="seo_url" name="seo_url" value="{{ old('seo_url', $model->seo_url ?? '') }}" maxlength="100" aria-describedby="seo_url" required />
                                                @error('seo_url')
                                                <div id="seo_url-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">{{ __('Description') }}</label>
                                                <textarea class="summernote form-control @error('description') is-invalid @enderror" name="description" rows="4">{{ old('description', translation(@$model->id,22,app()->getLocale(),'description',@$model->description) ?? '') }}</textarea>
                                                @error('description')
                                                <div id="description-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="short_description">{{ __('Short Description') }} <small>{{ __('For homepage listing')}}</small></label>
                                                <textarea class="summernote form-control @error('short_description') is-invalid @enderror" name="short_description" rows="3">{{ old('short_description', translation(@$model->id,22,app()->getLocale(),'short_description',@$model->short_description) ?? '') }}</textarea>
                                                @error('short_description')
                                                <div id="short_description-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="tracking_code">{{ __('Tracking Code') }}</label>
                                                <textarea class="form-control @error('tracking_code') is-invalid @enderror" name="tracking_code" rows="4">{{ old('tracking_code', $model->tracking_code ?? '') }}</textarea>
                                                @error('tracking_code')
                                                <div id="tracking_code-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="meta_description">{{ __('Meta Description') }}</label>
                                                <textarea class="summernote form-control @error('meta_description') is-invalid @enderror" name="meta_description">{{ old('meta_description', translation(@$model->id,22,app()->getLocale(),'meta_description',@$model->meta_description) ?? '') }}</textarea>
                                                @error('meta_description')
                                                <div id="meta_description-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="meta_keywords">{{ __('Meta Keywords') }}</label>
                                                <textarea class="summernote form-control @error('meta_keywords') is-invalid @enderror" name="meta_keywords">{{ old('meta_keywords', $model->meta_keywords ?? '') }}</textarea>
                                                @error('meta_keywords')
                                                <div id="meta_keywords-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <label for="is_static">
                                                        <input type="checkbox" name="is_static" id="is_static" value="0" @if(isset($model->is_static) && $model->is_static == 1) checked @endif>
                                                        {{ __('Is Static') }}
                                                    </label>
                                                    @error('is_static')
                                                    <div id="is_static-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <label for="show_in_header">
                                                        <input type="checkbox" name="show_in_header" id="show_in_header" value="0"  @if(isset($model->show_in_header) && $model->show_in_header == 1) checked @endif>
                                                        {{ __('Show In Header') }}
                                                    </label>
                                                    @error('show_in_header')
                                                    <div id="show_in_header-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <label for="show_in_footer">
                                                        <input type="checkbox" name="show_in_footer" id="show_in_footer" value="0" @if(isset($model->show_in_footer) && $model->show_in_footer == 1) checked @endif>
                                                        {{ __('Show In Footer') }}
                                                    </label>
                                                    @error('show_in_footer')
                                                    <div id="show_in_footer-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <label for="is_homepage_listing">
                                                    <input type="checkbox" name="is_homepage_listing" id="is_homepage_listing" value="0" @if(isset($model->is_homepage_listing) && $model->is_homepage_listing == 1) checked @endif>
                                                    {{ __('Show on Homepage Listing') }}
                                                    </label>
                                                    @error('is_homepage_listing')
                                                    <div id="is_homepage_listing-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 imageDiv" @if(@$model->is_homepage_listing == 0) style="display:none" @endif>
                                            <div class="avatar-upload form-group">
                                                <div class="avatar-fileds hover-effect">
                                                    <div class="avatar-edit">
                                                    <input type="file" class="form-control" id="imageUpload" name="image" value="{{ old('image', $model->image ?? '')}}" />
                                                    <label for="imageUpload"></label>
                                                    </div>
                                                </div>
                                                <div class="avatar-preview">
                                                <img id="imagePreview"
                                                    src="{{ checkImage(asset('storage/uploads/cms/'. @$model->image),'avatar5.png') }}" width="100%" height="100%" />
                                                        @error('image')
                                                        <div id="image-error" class="invalid-feedback animated fadeInDown">
                                                        {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{ __('Status') }}<small class="asterik" style="color:red">*</small></label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="is_active" id="is_active1" value="1" @if(isset($model->is_active) && $model->is_active == 1) checked @endif required>
                                                    <label class="form-check-label" for="is_active1">
                                                        {{ __('Active') }}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="is_active" id="is_active2" value="0" @if(isset($model->is_active) && $model->is_active == 0) checked @endif>
                                                    <label class="form-check-label" for="is_active2">
                                                        {{ __('Deactive') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row clearfix">
                                            <div class="box-header">
                                                <div class="row">
                                                    <div class="col-md-4 pl-0">
                                                        <button type="submit" class="skin-green-light-btn btn ml-2">{{ __('Save')}}</button>
                                                        <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.cms.index') }}">{{ __('Discard') }}</a>
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
    </section>
</div>
<!-- /.content -->
@endsection
@section('scripts')
<script type="text/javascript">
$(document).ready(function() {
    $('#is_static, #show_in_header, #show_in_footer').on('click',function(){
        if($(this).is(':checked'))
        {
            $(this).val('1');
        }
        else
        {
            $(this).val('0');
        }
    });
    $('body').on('click', 'input[name=is_homepage_listing]', function(){
        if($(this).is(':checked'))
        {
            $('.imageDiv').show();
        }
        else
        {
            $('.imageDiv').hide();
        }
    });
    $('body').on('keyup', 'input[name=title]', function(){
        slug = slugify($(this).val());
        $.ajax({
                url: "{{ route('admin.cms.check_slug') }}",
                type: 'GET',
                data: {
                    slug: slug
                },
                success: function (seo_slug) {
                    $("#seo_url").val(seo_slug);
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
});
</script>
@endsection
