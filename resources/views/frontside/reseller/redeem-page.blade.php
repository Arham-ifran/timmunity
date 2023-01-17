@extends('frontside.layouts.redeem-page-app')
@section('title') {{ $reseller->name }} {{ __('Voucher Redeem Page') }} @endsection
@section('body_class') cart-page @endsection
@section('style')
    <link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <style>

        .navbar-toggle{
            color: {{$reseller->color}};
        }
        .language-bar .dropdown .btn-primary:hover {
            background: {{$reseller->color}} !important;
        }
        .language-bar .dropdown .btn-primary{
            color: {{$reseller->color}} !important;
        }
        .row.cloud-row {
            margin-top: 20px;
        }
        #voucher-div{
            padding: 10px;
            color: white;
            background: {{$reseller->color}};
        }
        ul.nav.navbar-nav.header-nav>li>a:hover {
            background-color: {{$reseller->color}} !important;
        }
        .nav>li>a:hover{
            color: white !important;
            background-color: {{$reseller->color}} !important;
        }
        ul.nav.navbar-nav.custom-margin.header-nav>li>a:active,
        ul.nav.navbar-nav.custom-margin.header-nav .active{
            background-color: {{$reseller->color}} !important;
            color:#fff;
        }

        .btn{
            font-weight: 500 !important;
            border: 2px solid {{$reseller->color}} !important;
            color: white !important;
            background-color: {{$reseller->color}} !important;
            /* background-color: {{$reseller->color}} !important; */
        }
        .main-header{
            border-top: 1px solid {{$reseller->color}} !important;
        }
        .btn:hover{
            color: {{$reseller->color}} !important;
            background-color: transparent !important;
        }
        #voucher-div{
            background-color: {{$reseller->color}};
        }
        .alert-success{
            background-color: {{$reseller->color}} !important;
        }
        .green-line{
            background-color: {{$reseller->color}} !important;
        }
        .copy-right {
            background-color: {{$reseller->color}};
        }
        .footer-links ul li a, footer .contact-info ul li a{
            color: {{$reseller->color}};
        }
        .footer-logo .image {
           max-width: 250px;
            height: auto;
        }
        label.error {
            color: #ff0000cf;
            font-style: inherit;
            font-size: 12px !important;
            font-weight: 600;
        }
        
        label.error {
            font-style: normal;
            background: white;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row cloud-row">
            <div class="col-md-12">
                <h3 class="voucher_heading">{{ucwords($reseller->title)}} - {{__('Voucher Redeem Page')}}</h3>
            </div>
            @if(Auth::user())
                @if(Auth::user()->contact->type == 3)
                    <div class="col-md-4 mt-3">
                        <p><a class="btn btn-info btn-icon" href={{ route('voucher.edit.redeemed', Hashids::encode($reseller->reseller_id))}} title={{__('Edit')}}>{{ __('Edit') }}</a></p>
                    </div>
                @endif
            @endif
            <div class="col-md-12">
                {{-- {!! translation( $reseller->id,31,app()->getLocale(),'description',$content) !!} --}}
                {!! $content !!}
            </div>
        </div>
    </div>

@endsection
@section('script')
<script>
    $('body').on('change','[name=new_account]',function(){
        if($(this).is(':checked'))
        {
            $('.password_group').show();
        }else{
            $('.password_group').hide();
        }
    });

    $.validator.addMethod("email", function (value, element) {
        return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
    }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
      // Mix Password Method
    $.validator.addMethod("passwords", function (value, element) {
        return this.optional(element) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }, "{{ __('*Password should contain at least one digit, *Should contain at least one upper & lower case letter,*Should contain at least 8 from the mentioned characters, *Should contain special character  & numbers.')}}");

    $('#redeem_form').validate({
        rules: {
            "accept": {
                required:true
            },
            "voucher_code":{
                required:true
            },
            "name":{
                required:true
            },
            "email":{
                required:true,
                email:true
            },
            "country_id":{
                required:true
            },
            "password":{
                required:true,
                passwords:true,
            },
            "password_confirm":{
                required:true,
                equalTo : "#password"
            }
        },
        messages: {
            "accept": {
                required: "{{__('Kindly accept the terms to redeem the voucher')}}"
            },
            "password_confirm":{
                equalTo: "{{__('The password must match')}}"
            }
        },
        errorPlacement: function(error, element) {
            name = $(element).attr('name');
            // $(element).css('border', '1px solid red')
            if(name == "accept"){
                $(element).parent().parent().append(error)
            }else{
                // $(element).append(error)
                error.insertAfter(element);
            }
        },
    });

</script>
@endsection
