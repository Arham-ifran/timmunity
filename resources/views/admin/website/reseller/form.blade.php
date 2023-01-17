@extends('admin.layouts.app')
@section('title',  __('Reselller Redeemed Page'))
<style>

    .font-size{
        font-size: 25px !important;
    }
    #color.form-control{
            display: block;
            width: 100%;
            height: 34px;
            padding: 0px 0;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
            box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
            -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
            -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        }

    .note-editor.note-frame .note-editing-area {
        height: 220px !important;
    }
    .img{

        float: left;
        height: 50px;
        font-size: 18px;
        line-height: 20px;
    }
    .description-message{
        display: flex;
        justify-content: flex-end;
        padding-right: 25px;
        padding-top: 15px;
        font-size:15px !important;
    }

</style>
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
        <div class="row">
            <div class="col-md-6">
                <h2>
                {{ __('Reseller Redeemed Page') }}
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
                            <h3 class="box-title">{{ __('Reseller Redeemed Page') }}</h3>
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
                        @csrf
                                    <form class="mt-2 form-validate" id="redeem_form" action="{{ route('admin.website.reseller.redeemed.page.add') }}" method="post" enctype="multipart/form-data" >
                                        @csrf
                                        <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                                        <input type="hidden" name="reseller_id" value="{!! Hashids::encode(@$reseller_id) !!}">

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="title">{{ __('Title') }}</label>
                                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $model->title ?? '') }}" maxlength="100" aria-describedby="title" required />
                                                @error('title')
                                                <div id="title-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="seo_url">{{ __('URL') }}</label>
                                                <input type="text" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $model->url ?? '') }}" maxlength="100" aria-describedby="url" readonly/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="seo_url">{{ __('Email') }}</label>
                                                <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" @if($model->user) value="{{$model->user->email}}" @else value="{{ old('email', $model->email ?? '') }}"@endif value="{{ old('url', $model->email ?? '') }}" maxlength="100" aria-describedby="url" required/>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="seo_url">{{ __('Phone') }}</label>
                                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('url', $model->phone ?? '') }}" maxlength="15" aria-describedby="url"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="logo">{{ __('Logo') }}</label>
                                                <input type="file" name="image" id="image" class="form-control">

                                            </div>

                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="logo">{{ __('Color') }}</label>
                                                <input type="color" value="{{ old('url', $model->color ?? '') }}" name="color" id="color" class="form-control">

                                            </div>

                                        </div>
                                        @if($model->is_domain_verified == 1)
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="domain">{{__('Sub Domain')}}</label>
                                                    <div class="input-group">
                                                        <div class="input-group">
                                                            <input pattern="[a-zA-Z0-9 ]+" onkeyup="this.value=this.value.replace(/[^a-zA-Z0-9 ]+/g,'');" type="text" class="form-control" value="{{ old('domain', str_replace('https://','',str_replace( '.'.env('reseller_domain'),'',$model->domain)) ?? '') }}"  name="domain" id="domain" placeholder="Enter Sub Domain">
                                                            <span class="input-group-addon">.{{env('reseller_domain')}}</span>
                                                            <span class="input-group-addon"><i class="fa fa-check"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="domain">{{__('Domain')}}</label>
                                                    <div class="input-group">
                                                        <input pattern="[a-zA-Z0-9 ]+" onkeyup="this.value=this.value.replace(/[^a-zA-Z0-9 ]+/g,'');" type="text" class="form-control" value="{{ old('domain', str_replace('https://','',str_replace( '.'.env('reseller_domain'),'',$model->domain)) ?? '') }}"  name="domain" id="domain" placeholder="Enter Sub Domain">
                                                        <span class="input-group-addon">.{{env('reseller_domain')}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-6">
                                            <div id="uploadPreview">
                                                @if($model->logo)
                                                    <img class="img" src="{{ asset('storage/uploads/redeem-page/'.$model->logo) }}" alt="">
                                                @else
                                                    <img src="{{ asset('frontside/dist/img/site_logo.png') }}" />
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="custom-tabs mt-3">
                                                    <ul class="nav nav-tabs" id="tabs">
                                                        <li class="active"><a data-toggle="tab" id="GeneralTabBtn" href="#description_tab">{{ __('Description') }}</a></li>
                                                        <li><a data-toggle="tab" id="variation_tab_btn" href="#terms-of-use">{{ __('Terms Of Use') }}</a></li>
                                                        <li id="SalesTabBtn"><a data-toggle="tab" href="#privacy-policy">{{ __('Privacy Policy') }}</a></li>
                                                        <li><a data-toggle="tab" href="#imprint">{{ __('Imprint') }}</a></li>
                                                        <li><a data-toggle="tab" href="#navigation">{{ __('Navigation Menu') }}</a></li>

                                                    </ul>
                                                    <div class="tab-content">
                                                        <!-- Gernal information -->
                                                        <div id="description_tab" class="tab-pane fade in active">
                                                            <?php
                                                                $string = '*Required "{{voucher_form}}" in description';
                                                            ?>
                                                            <div class="row">
                                                                <p class="text-danger float-left p-4 description-message">{{$string}}</p>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 mt-3">
                                                                    <div class="form-group">
                                                                        <textarea class="form-control summernote @error('description') is-invalid @enderror" name="description" id="description" rows="2" required="required">{!! old('description', $model->description ?? '') !!}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="terms-of-use" class="tab-pane fade">
                                                            <div class="row mt-3">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="seo_url">{{ __('Terms of Use URL') }}</label>
                                                                        <input type="text" class="form-control @error('terms_of_use_url') is-invalid @enderror" id="terms_of_use_url" name="terms_of_use_url" value="{{ old('url', $model->url ?? '') }}/terms-of-use" maxlength="100" aria-describedby="url" readonly/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 mt-3">
                                                                    <div class="form-group">
                                                                        <textarea class="form-control summernote" name="terms_of_use" rows="4">{!! old('terms_of_use', $model->terms_of_use ?? '') !!}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="privacy-policy" class="tab-pane fade">
                                                            <div class="row mt-3">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="seo_url">{{ __('Privacy Policy') }}</label>
                                                                        <input type="text" class="form-control @error('privacy_policy_url') is-invalid @enderror" id="privacy_policy_url" name="privacy_policy_url" value="{{ old('url', $model->url ?? '') }}/privacy-policy" maxlength="100" aria-describedby="url" readonly/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 mt-3">
                                                                    <div class="form-group">
                                                                        <textarea class="form-control summernote" name="privacy_policy" rows="4">{!! old('privacy_policy', $model->privacy_policy ?? '') !!}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="imprint" class="tab-pane fade">
                                                            <div class="row mt-3">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="seo_url">{{ __('Imprint') }}</label>
                                                                        <input type="text" class="form-control @error('imprint_url') is-invalid @enderror" id="imprint_url" name="imprint_url" value="{{ old('url', $model->url ?? '') }}/imprint" maxlength="100" aria-describedby="url" readonly/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 mt-3">
                                                                    <div class="form-group">
                                                                        <textarea class="form-control summernote" name="imprint" rows="4">{!! old('imprint', $model->imprint ?? '') !!}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="navigation" class="tab-pane fade">
                                                            @foreach($model->reseller_redeemed_page_navigations as $navigation_item)
                                                                <div class="row nav-row">
                                                                    <div class="col-md-4 mt-2">
                                                                        <div class="form-group">
                                                                        <label for="title">{{ __('Title') }}</label>
                                                                        <input type="text" class="form-control" id="nav_title" name="nav_title[]" value="{{ $navigation_item->title }}" maxlength="100" aria-describedby="title" required />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 mt-2">
                                                                        <div class="form-group">
                                                                            <label for="seo_url">{{ __('URL') }}</label>
                                                                            <input type="url" class="form-control @error('url') is-invalid @enderror"  name="nav_url[]" value="{{ $navigation_item->url }}" maxlength="100" aria-describedby="url" required/>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 mt-3">
                                                                        <div class="form-group mt-3">
                                                                        <i class="fa fa-times-circle fa-4x delete font-size"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            <div class="row mt-3" id="row_title">
                                                                {{-- <div class="col-md-4 mt-2">
                                                                    <div class="form-group">

                                                                    <label for="title">{{ __('Title') }}</label>
                                                                    <input type="text" class="form-control" id="nav_title" name="nav_title[]" value="{!! old('nav_title', $title->title ?? '') !!}" required maxlength="100" aria-describedby="title"  />


                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4 mt-2">
                                                                    <div class="form-group">
                                                                        <label for="seo_url">{{ __('URL') }}</label>
                                                                        <input type="text" class="form-control @error('url') is-invalid @enderror" id="nav_url" name="nav_url[]" required value="{!! old('nav_url', $title->url ?? '') !!}" maxlength="100" aria-describedby="url"/>

                                                                    </div>
                                                                </div>

                                                                <div class="col-md-2 mt-3">
                                                                    <div class="form-group mt-3">
                                                                    <i class="fa fa-times-circle fa-4x delete font-size"></i>

                                                                    </div>
                                                                </div> --}}

                                                            </div>

                                                            <div class="row" id="add_more_row">
                                                                <div class="col-md-3">
                                                                <button type="button" id="add_more" class="btn btn-primary btn float">{{ __('Add More')}}</i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row clearfix">
                                            <div class="box-header">
                                                <div class="row">
                                                    <div class="col-md-4 pl-0">
                                                        <button type="submit" class="skin-green-light-btn btn ml-2">{{ __('Save')}}</button>
                                                        <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.website.resellers') }}">{{ __('Discard') }}</a>
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
<script src="{{ asset('frontside/bower_components/summernote/summernote.min.js') }}"></script>

<script type="text/javascript">
    var id = "{{ $id }}";
    $(document).ready(function() {
        $("#title").on('keyup blur change', function() {
            var title = $("#title").val();
            $("#url").val(convertToSlug(title));
        });

        $("input[name=title]").on('keyup blur change', function() {
            var title = $("input[name=title]").val();

            $("input[name=terms_of_use_url]").val(convertToSlug(title)+'/terms-of-use');
        });
        $("input[name=title]").on('keyup blur change', function() {
            var title = $("input[name=title]").val();

            $("input[name=privacy_policy_url]").val(convertToSlug(title)+'/privacy-policy');
        });
        $("input[name=title]").on('keyup blur change', function() {
            var title = $("input[name=title]").val();

            $("input[name=imprint_url]").val(convertToSlug(title)+'/imprint');
        });
    });

    $(document).on('click', '#add_more', function(){
        var html = '';
        html = '<div class="row" id="row_title">';
            html += '<div class="col-md-4">';
                html += '<div class="form-group">';
                    html += '<label for="title">Title</label>';
                    html += '<input id="nav_title" type="text"  class="form-control" name="nav_title[]" required>';
                html += '</div>';
            html += '</div>';
            html += '<div class="col-md-4">';
                html += '<div class="form-group">';
                    html += '<label for="title">{{ __('URL') }}</label>';
                    html += '<input type="url" id="nav_url"  class="form-control" name="nav_url[]" required>';
                html += '</div>';
            html += '</div>';
            html += ' <div class="col-md-2 mt-3">'
                html += '<div class="form-group mt-2">'
                    html += '<i class="fa fa-times-circle delete fa-4x font-size"></i>'

                html += '</div>'
            html += '</div>'
        html += '</div>';


        $(html).insertBefore("#add_more_row");
    });
    $(document).on('click','.delete', function(){

        $("#row_title").remove();
    })

    function convertToSlug(Text) {
        var text = Text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
        var protocol = window.location.protocol;
        var hostname = window.location.hostname;
        var url = window.location.href;
        var id = url.substring(url.lastIndexOf('/') + 1);
        @if($model->domain != '' && $model->is_domain_verified == 1)
        var url_slug =  "{{$model->domain}}";
        @else
        var url_slug =  "{{env('reseller_domain')}}"+'/'+text+'/'+id;
        @endif
        return url_slug.replace('--', '-');
    }

    $('#redeem_form').validate({
        rules: {
            "description":{
                required:true
            },
            "email":{
                required:true
            },
            "nav_title":{
                required:true
            },
            "nav_url":{

                required:true
            },

            "domain":{
                remote : {
                        url: "{{route('redeem.domain.exists')}}",
                        type: "get",
                        data: {
                            domain: function()
                            {
                                return $("[name=domain]").val();
                            },
                            id: function()
                            {
                                return $("[name=reseller_id]").val();
                            },
                        }
                    }
            },
        },
        messages: {
            "domain":{
                    remote : "Domain already in use"
            },
            "description":{
                required:"Description is required"
            },

            "message":{
                required:"Kindly add some message."
            }
        },


        submitHandler:function (form, e) {
            if($('#description').summernote('isEmpty')) {
                e.preventDefault();
                toastr.error('Description is required!');
                // cancel submit
            }else if( $('[name="nav_title[]"]').filter(function() { return this.value == ""; }).length > 0 ){
                e.preventDefault();
                toastr.error('Navigation Menu Title is required!');
            }else if($('[name="nav_url[]"]').filter(function() { return this.value == ""; }).length > 0){
                e.preventDefault();
                toastr.error('Navigation Menu Url is required!');
            }else{

                form.submit();
            }
        },

        invalidHandler: function(e, validator){
                if(validator.errorList.length)
                $('#tabs a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show')
                },

    });


    //  Show Image preview in a Div
    $("input[name=image]").change(function(e) {
        var image, file;
        if ((file = this.files[0])) {
            image = new Image();
            image.onload = function() {
                    src = this.src;
            $('#uploadPreview').html('<img width=100 height=100pwe src="'+ src +'"></div>');
                    e.preventDefault();
                }
            };
            image.src = URL.createObjectURL(file);
    });
</script>
@endsection
