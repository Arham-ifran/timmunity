<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <title>Order Successfull. License Available</title>
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
                        <h1
                            style="color: #009a71;font-size: 25px;margin-top: 40px;margin-bottom: 30px;font-weight: 700;">
                            Order Successfull. License Available {{ $data['quotationnumber'] }}</h1>
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
                            Your Quotation is approved. Following are the assigned licenses <br>
                            @foreach ($data['licenses'] as $product => $licences)
                                <h3>{{ $product }}</h3>
                                <ul>
                                    @foreach ($licences[0] as $license)
                                        <li>{{ $license->license_key }}</li>
                                    @endforeach
                                </ul>
                            @endforeach

                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="font-size: 14px;color: #009a71;font-style: italic;">The
                            {{ $site_settings['site_name'] ?? '' }} Team</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
