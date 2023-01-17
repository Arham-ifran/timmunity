<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <title>Upload Licenses</title>
</head>

<body style="padding:0; margin:0px; background: #f6fcfe;font-family: 'Nunito Sans', sans-serif !important;">
    <div
        style="max-width:600px; margin:auto; margin-top:50px; margin-bottom: 50px;padding: 0 20px; color:#192737; font-size:17px;">
        <table width="100%" style="display: block;">
            <tbody>
                <tr>
                    <td>
                        <a href="#" style="display: block;margin-bottom: 20px; text-align: center;">
                            <img src="{{ asset('frontside/dist/img/logo.png') }}" alt="Logo">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="color: #009a71;font-size: 16px; font-weight: 700;margin-bottom:20px;">Hey Admin,</p>
                    </td>
                </tr>
                <tr>
                    <td>
                     <p style="color: #009a71;font-size: 16px;">
                        {!! $data['body'] !!}
                     </p>
                  </td>
                </tr>
                <tr>
                    <td>
                        <p style="font-size: 14px;color: #009a71;"><a style="background: #009a71; margin-top:20px; margin-bottom:20px; font-size: 13px; color: #fff; padding: 5px 10px; text-decoration: none;" href="{{ route('admin.license.index') }}">Upload Licenses</a></p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
