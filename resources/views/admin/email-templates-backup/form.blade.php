@extends('admin.layouts.app')
@section('title', __('Email Templates'))
@section('styles')
<style>
    h3.box-title.preview-template-btn {
        float: right;
        background: white;
        color: #009a71 !important;
        margin-right: 10px ;
        /* padding: 5px; */
    }
    h3.box-title.cheatsheet-btn {
        float: right;
        background: white;
        color: #009a71 !important;
        padding-right: 5px;
    }
    h3.box-title.preview-template-btn:hover, h3.box-title.cheatsheet-btn:hover {
        color: #fff !important;
        /* padding: 5px; */
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
                        {{ __('Email Template') }} / @if ($action == 'Add') {{ __('Add') }} @else {{ __('Edit') }} @endif
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="box-title">
                                        @if ($action == 'Add')
                                            {{ __('Add New Email Template') }}
                                        @else
                                            {{ __('Edit Email Template') }}
                                        @endif
                                        </h3>
                                    </div>
                                    <div class="col-md-6">
                                        <h3 class="box-title cheatsheet-btn btn">{{ __('Cheat Sheet') }}</h3>
                                        <h3 class="box-title preview-template-btn btn ml-2">{{ __('Preview Template') }}</h3>
                                    </div>
                                </div>
                                <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="row">
                                <div class="col-md-12">
                                    <form class="timmunity-custom-dashboard-form mt-2 form-validate"
                                        action="{{ route('admin.email-templates.store') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                                        <input type="hidden" name="action" value="{!! $action !!}">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="title">{{ __('Title') }}</label>
                                                    <input type="text"
                                                        class="form-control @error('title') is-invalid @enderror"
                                                        id="title" name="title"
                                                        value="{{ old('title', @$model->title ?? '') }}" maxlength="100"
                                                        aria-describedby="name" required />
                                                    @error('type')
                                                        <div id="title-error" class="invalid-feedback animated fadeInDown">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="email_template_type">{{ __('Email Template Type') }}</label>
                                                    <select id="email_template_type" name="email_template_type"
                                                        class="form-control @error('email_template_type') is-invalid @enderror"
                                                        aria-describedby="type" required>
                                                        <option selected value="">---{{ __('Select Email Template Type') }}---</option>
                                                        <option value="1" @if (isset($model) && 1 == $model->email_template_type) selected @endif>{{ __('Welcome Email') }}</option>
                                                        <option value="2" @if (isset($model) && 2 == $model->email_template_type) selected @endif>{{ __('Signup Email') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            @if(@$action == 'Edit')
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="status">{{ __('Status') }}</label>
                                                        <select id="status" name="status"
                                                            class="form-control @error('status') is-invalid @enderror"
                                                            aria-describedby="type" required>
                                                            <option value="">---{{ __('Select a status')}}---</option>
                                                            <option value="1" @if (isset($model) && 1 == $model->status) selected @endif>{{ __('Active') }}</option>
                                                            <option value="0" @if (isset($model) && 0 == $model->status) selected @endif>{{ __('In Active') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="header">{{ __('Header Content') }}</label>
                                                <textarea class=" form-control" name="header" id="header" value="{{ @$model->header }}">{{ @$model->header }}</textarea>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="content">{{ __('Main Content') }}</label>
                                                <textarea class=" form-control" name="content" id="content" value="{{ @$model->content }}">{{ @$model->content }}</textarea>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="footer">{{ __('Footer Content') }}</label>
                                                <textarea class=" form-control" name="footer" id="footer" value="{{ @$model->footer }}">{{ @$model->footer }}</textarea>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="welcome_content">{{ __('Welcome Content') }}</label>
                                                <textarea class=" form-control" name="welcome_content" id="welcome_content" value="{{ @$model->welcome_content }}">{{ @$model->welcome_content }}</textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mt-3">
                                                <div class="form-group">
                                                    <button type="submit" class="skin-green-light-btn btn ">{{ __('Save') }}</button>
                                                    <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                                        href="{{ route('admin.taxes.index') }}">{{ __('Discard') }}</a>
                                                </div>
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
    <div class="modal fade in" id="preview-template-model" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="contact-big-model modal-dialog modal-dialog-centered"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Preview Template') }}</h3>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"
                                aria-hidden="true"></i></span>
                    </button>
                </div>
                <div class="modal-body ">
                    <!-- Form Start Here  -->
                    <div class="row">
                        <div class="col-md-12" id="preview-template-body" ></div>
                    </div>
                    <!-- End Here -->
                </div>
                <!-- Footer model popupp -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade in" id="cheat-sheet-model" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="contact-big-model modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Cheat Sheet') }}</h3>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"
                                aria-hidden="true"></i></span>
                    </button>
                </div>
                <div class="modal-body ">
                    <!-- Form Start Here  -->
                    <div class="row">
                        <ul>
                            <li>
                                :quotationnumber for Quotation Number (S00000)
                            </li>
                            <li>
                                :quotationtotal for Quotation Total Amount
                            </li>
                        </ul>
                    </div>
                    <!-- End Here -->
                </div>
                <!-- Footer model popupp -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
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
        $('body').on('click', '.preview-template-btn', function(){
            html = $('#header').val();
            html += $('#content').val();
            html += $('#footer').val();
            $("#preview-template-body").html(html);
            $('#preview-template-model').modal('show');
        });
        $('body').on('click', '.cheatsheet-btn', function(){
            $('#cheat-sheet-model').modal('show');
        });
        header_summernote = $("#header").summernote();
        content_summernote = $("#content").summernote();
        footer_summernote = $("#footer").summernote();
        welcome_summernote = $("#welcome_content").summernote();
    </script>
@endsection
