    @extends('frontside.layouts.app')
    @section('title')   Home @endsection
    @section('style')
    <style>
        .contact-info .info-holder h3{
            font-family: 'Avenir Next' !important;
            font-weight: bold !important;
            font-size: 20px;
        }
        span.circle{
            color:white;
            border:1px solid;   
            height:30px;
            border-radius:50%;
            -moz-border-radius:50%;
            -webkit-border-radius:50%;
            width:30px;
        }
        .contact-info li span.fa {
            float: left;
            margin-right: 6px;
            position: absolute;
            left: 0;
            top: 0px;
            color: white;
            padding: 7px 7px 8px 7px;
        }

        .fa-map-marker:before {
            content: "\f041";
            padding: 3px;
        }

        .fa-phone:before {
            content: "\f095";
            padding: 1px;
        }

        .contact-info .info-holder {
            margin-left: 60px !important;
        }
    </style>
    @endsection
    @section('content')
        <!-- Content Section -->
        <div class="main-content">
            <section class="contact-form-info">
                <div class="container">
                <div class="contact-title-des text-center">
                    <h2>{{ __('GET IN TOUCH')}}</h2>
                    <p>{{ __("Contact Us & You’ll Hear Back TODAY.") }}</p>
                    <p>{{ __("You are welcome to send us enquiries or comments by completing this form and clicking the 'Submit' button.") }}</p>
                </div>
                <div class="cont-box-wrap">
                    <div class="row cont-inner-wrap">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="contact-box"  >
                            <form action="{{ route('frontside.contact.submit') }}" method="post" id="contact_form">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-12 pl-0">
                                        <input type="text" class="form-control " name="name" placeholder="{{ __('Name') }}" value="{{ old('name') }}" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12 pl-0">
                                        <input type="text" class="form-control " name="email" placeholder="{{ __('Email Address') }}" value="{{ old('email') }}" required>
                                    </div>
                                    <div class="form-group col-md-12 pl-0">
                                        <input type="tel"  onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxLength="15" class="form-control " name="phone" placeholder="{{ __('Phone Number') }}" value="{{ old('phone') }}" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-12 pl-0">
                                    <input type="text" class="form-control " name="subject" placeholder="{{ __('Subject') }}" value="{{ old('subject') }}" required>
                                </div>
                                <div class="form-group col-md-12 pl-0">
                                    <textarea rows="5" class="form-control " maxlength="450"  name="message" placeholder="{{ __('Message') }} (450 characteres allowed) ">{{ old('message') }}</textarea>
                                </div>
                                @if(config('services.recaptcha.key'))
                                    <div class="form-group col-md-12 pl-0 {{config('services.recaptcha.key')}}">
                                        <div class="captcha-des">
                                            <div class="form-group has-feedback">
                                                <div class="g-recaptcha"
                                                    data-sitekey="{{config('services.recaptcha.key')}}"
                                                    data-callback="correctCaptcha">
                                                </div>
                                                @error('g-recaptcha-response')
                                                    <span class="invalid-feedback" role="alert" id="capticha_message">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-action-btn btn-line"><button class="btn btn-theme-blue">{{ __('Submit') }}</button></div>
                            </form>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                            <div class="contact-info"  >
                            <ul>
                                <li class="">
                                    
                                    <span class="fa fa-map-marker circle"></span>
                                    <div class="info-holder">
                                        <h3>{{ __('Location') }}</h3>
                                        <p>{!! @$site_settings->site_address !!}
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <span class="fa fa-phone circle"></span>
                                    <div class="info-holder">
                                        <h3>{{ __('Call Us') }}</h3>
                                        <p>{{ @$site_settings->site_phone ? @$site_settings->site_phone : @$site_settings->site_mobile }}</p>
                                    </div>
                                </li>
                                <li>
                                    <span class="fa fa-envelope circle"></span>
                                    <div class="info-holder">
                                        <h3>{{ __('Email') }}</h3>
                                        <p>{{ @$site_settings->site_email }}</p>
                                    </div>
                                </li>
                            </ul>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </section>
        </div>

    @endsection
    @section('script')
    <script src="{{ asset('backend/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('backend/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script>
        $.validator.addMethod(
            "regex",
            function(value, element, regexp) {
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "Please check your input."
        );
        // $("[name=phone]").inputmask({"mask": "(999) 999-9999"});
        $('#contact_form').validate({
            rules: {
                "name":{
                    required:true
                },
                "email":{
                    required:true,
                    email: true
                },
                "phone":{
                    required:true,
                    // regex:"[0-9]{3}-[0-9]{2}-[0-9]{3}"
                },
                "subject":{
                    required:true
                },
                "message":{
                    required:true
                }
            },
            // messages: {
            //     "name":{
            //         required:"Name is Mandatory"
            //     },
            //     "email":{
            //         required:"Email is mandatory",
            //         email: "Incorrect email format"
            //     },
            //     "phone":{
            //         required:"Phone/Mobile is mandatory"
            //     },
            //     "subject":{
            //         required:"Subject is mandatory"
            //     },
            //     "message":{
            //         required:"Kindly add some message."
            //     }
            // },
            // // errorPlacement: function(error, element) {
            // //     error.insertAfter(element);
            // //     toastr.error(error);
            // //     if($('.form-main-error:visible').length == 0)
            // //     {
            // //         $('.form-save-btn-div').append('<small class="form-main-error">"{{ __('Some of the form fields are required') }}"</small>');
            // //         setTimeout(function(){
            // //             $('.form-main-error:visible').css('display','none');
            // //         },4000);
            // //     }
            // // },

        });
    </script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/localization/messages_{{ session()->get('locale') }}.js" />
    @endsection
