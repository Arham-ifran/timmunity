<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{env('APP_NAME')}}</title>
</head>

<body style="padding:0; margin:0px; background:#eee;">

    <div style="max-width:600px; margin:auto;  background:#fff; color:#222; font-size:17px; position: relative;">
        <div style="width:100%; padding:15px 0px 35px; text-align:center; margin-bottom: 15px;background-size: cover;">
            <br>
            <img src="{{ asset('frontside/dist/img/logo.png') }}" width="200" alt="Logo">
        </div>
        {!! $content !!}
        <div style="margin-top: 40px; text-align: center; background: #009a7124;padding:10px 20px 10px;  ">
            <p style="font-size:12px; color: #000000; margin-bottom: 0; font-family: sans-serif;">{{ __('Please check out FAQs section in our website for further assistance.') }}</p>
            <a style="color: #000000; font-size:12px;font-family: sans-serif; " href="https://www.timmunity.com">https://www.timmunity.com</a>
        </div>
        <!--footer area-->
        <div style=" background: #009a71; padding:10px 20px 10px; font-size: 12px;text-align: center; font-family: sans-serif;  color: #fff;">
            <!-- copyright area-->
            <p style="margin-bottom: 0; margin-top: 0">{{ __('Copyright') }} &copy; {{ env('APP_NAME') }} {{ __('GmbH') }}. {{ __('All Rights Reserved.') }}</p>
        </div>
    </div>
</body>

</html>
