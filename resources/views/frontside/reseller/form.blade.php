@extends('frontside.layouts.app')
@section('title') {{ $model->title }} {{ __('Update Voucher Redeemed Page') }} @endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('frontside/bower_components/summernote/summernote.min.css') }}">
    <style>
        .row.cloud-row {
            margin-top: 20px;
        }
        .float{
            /* float:left !important; */
        }
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

        .margin-top{
            margin-top:24px !important;
        }

        @media screen and (max-width: 991px) and (min-width: 320px) {

            #uploadPreview{
                display:none;
            }
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row cloud-row">
            <div class="col-md-8">
                <h3 class="voucher_heading">{{ucwords($model->title)}}</h3>
            </div>
            <div class="col-md-4">
                <h3 style="color: #009a71;">{{__('Update Redeemed Page')}}</H3>
            </div>
            <div class="col-md-12">
                <div class="checkout-des">
                    <form id="reseller_form" class="mt-2 form-validate" action="{{ route('update.redeem.page') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                        <input type="hidden" name="reseller_id" value="{!! Hashids::encode(@$reseller_id) !!}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="title">{{ __('Title') }}<small class="asterik" style="color:red">*</small></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $model->title ?? '') }}" maxlength="100" aria-describedby="title" required />
                                    @error('title')
                                    <div id="title-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="domain">{{__('Sub Domain')}}</label>
                                    <div class="input-group">
                                        @if($model->is_domain_verified == 1)
                                            <input pattern="[a-zA-Z0-9 ]+" onkeyup="this.value=this.value.replace(/[^a-zA-Z0-9 ]+/g,'');" type="text" class="form-control" value="{{ old('domain', str_replace('https://','',str_replace( '.'.env('reseller_domain'),'',$model->domain)) ?? '') }}"  name="domain" id="domain" placeholder="Enter Sub Domain">
                                            <span class="input-group-addon">.{{env('reseller_domain')}}</span>
                                            <span class="input-group-addon"><i class="fa fa-check"></i></span>
                                        @else
                                            <input pattern="[a-zA-Z0-9 ]+" onkeyup="this.value=this.value.replace(/[^a-zA-Z0-9 ]+/g,'');" type="text" class="form-control" value="{{ old('domain', str_replace('https://','',str_replace( '.'.env('reseller_domain'),'',$model->domain)) ?? '') }}"  name="domain" id="domain" placeholder="Enter Sub Domain">
                                            <span class="input-group-addon">.{{env('reseller_domain')}}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="seo_url">{{ __('URL') }} </label>
                                    <input type="text" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $model->url ?? '') }}" maxlength="100" aria-describedby="url" readonly/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="seo_url">{{ __('Email') }}</label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" @if($model->user->email) value="{{$model->user->email}}" @else value="{{ old('email', $model->email ?? '') }}"@endif value="{{$model->email}}" maxlength="100" aria-describedby="url"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="seo_url">{{ __('Phone') }}</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $model->phone ?? '') }}" maxlength="15" aria-describedby="url"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-2">
                                <div id="uploadPreview">
                                    @if($model->logo)
                                    <img style="width: 100%;" class="img" src="{{ asset('storage/uploads/redeem-page/' . $model->logo) }}" alt="">
                                    @else
                                    <img style="width: 100%;" src="{{ asset('frontside/dist/img/site_logo.png') }}" />
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-2">
                                <div class="form-group">
                                    <label for="logo">{{ __('Logo') }}</label>
                                    <input type="file" name="image" id="image" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="logo">{{ __('Color') }}</label>
                                    <input type="color" value="{{ old('color', $model->color ?? '') }}" name="color" id="color" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="seo_url">{{ __('Language Used') }}</label>
                                    <select name="language_used" id="" class="form-control">
                                        @foreach(\App\Models\Languages::all() as $language)
                                            <option value="{{ $language->iso_code }}" @if($language->iso_code == App::getLocale()) selected="selected" @endif>
                                                {{ucwords($language->name)}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-tabs mt-3">
                                    <ul class="nav nav-tabs" id="tabs">
                                        <li class="active"><a data-toggle="tab" id="GeneralTabBtn" href="#description">{{ __('Description') }}</a></li>
                                        <li><a data-toggle="tab" id="variation_tab_btn" href="#terms-of-use">{{ __('Terms Of Use') }}</a></li>
                                        <li id="SalesTabBtn"><a data-toggle="tab" href="#privacy-policy">{{ __('Privacy Policy') }}</a></li>
                                        <li><a data-toggle="tab" href="#imprint">{{ __('Imprint') }}</a></li>
                                        <li><a data-toggle="tab" href="#navigation">{{ __('Navigation Menu') }}</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="description" class="tab-pane fade in active">
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
                                    <div class="col-md-12 mb-2">
                                        <button type="submit" class="btn btn-primary btn">{{ __('Save')}}</button>
                                        <a style="border-bottom: 2px solid #009a71;" class="btn btn-default "href="{{ route('voucher.view.redeemed', Hashids::encode(@$model->reseller_id)) }}" title={{__('Cancel')}}>{{ __('Cancel') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
<!-- Summernote JS -->

<script src="{{ asset('frontside/bower_components/summernote/summernote.min.js') }}"></script>
<script src="{{ asset('backend/plugins/input-mask/jquery.inputmask.js') }}"></script>
<script src="{{ asset('backend/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.2/js/toastr.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 180, //set editable area's height
            minHeight: null,
        });

    });
</script>
<script type="text/javascript">
    // Form Validation Function
    $(function () {
        $.validator.addMethod("domain_exist",function (value, element, param) {
                var $otherElement = $(param);
                type = $('.saas_based input[type=radio]:checked').val();
                if(type == "1"){
                    if($(element).val() == ''){
                        return false;
                    }else{
                        return true;
                    }
                }
                else{
                    return true;
                }
        }, "{{ __('Project is required') }}");
        $.validator.addMethod("email", function (value, element) {
            return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
        }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
        jQuery("#reseller_form").validate({
            ignore: [],
            rules: {
                "title":{
                    required:true
                },
                "email":{
                    required:true,
                    email:true
                },
                'nav_title':{
                    required:true,
                },
                'nav_url':{
                    required:true,
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
                "description":{
                    required:true
                }
            },
            messages: {
                "domain":{
                    remote : "Domain already in use"
                },
                'nav_title':{
                    required:"{{ __('Navigation Menu Title is required') }}"
                },

                'nav_url':{
                    required:"{{ __('Navigation Menu Url is required') }}"
                },
            },

            invalidHandler: function(e, validator){
            if(validator.errorList.length)
            $('#tabs a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show')
            }

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
        $(document).on('click', '#add_more', function(){
            var html = '';
            html = '<div class="row nav-row">';
                html += '<div class="col-md-4">';
                    html += '<div class="form-group">';
                        html += '<label for="title">Title</label>';
                        html += '<input id="nav_title" type="text" class="form-control" name="nav_title[]" required>';
                    html += '</div>';
                html += '</div>';
                html += '<div class="col-md-4">';
                    html += '<div class="form-group">';
                        html += '<label for="title">{{ __('URL') }}</label>';
                        html += '<input type="url" id="nav_url" class="form-control" name="nav_url[]" required>';
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
    });
</script>
<script type="text/javascript">

    $(document).ready(function() {

        $("input[name=title]").on('keyup blur change', function() {
            var title = $("input[name=title]").val();

            $("input[name=url]").val(convertToSlug(title));
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
    })
    var id = "{{$id}}"
    function convertToSlug(Text) {
        var text = Text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
        var protocol = window.location.protocol;
        var hostname = window.location.hostname;
        var url = window.location.href;
        // var id = url.substring(url.lastIndexOf('/') + 1);
        // var url_slug =  protocol+"//"+hostname+'/'+text+'/'+id;   /*with redeemed voucher url*/
        @if($model->domain != '' && $model->is_domain_verified == 1)
        var url_slug =  "{{$model->domain}}";
        @else
        var url_slug =  "https://www.{{env('reseller_domain')}}"+'/'+text+'/'+id;
        @endif
        return url_slug.replace('--', '-');
    }
    $(document).on('click','.delete', function(){
        $(this).parents('.nav-row').remove();
    })
</script>
@endsection
