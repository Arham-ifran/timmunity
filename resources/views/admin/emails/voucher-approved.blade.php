<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

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
                        <h1 style="color: #009a71;font-size: 25px;margin-top: 40px;margin-bottom: 30px;font-weight: 700;">
                            Voucher Approved | TIMmunity</h1>
                    </td>
                </tr>

                <tr>
                    <td>
                        <p style="color: #009a71;font-size: 16px;">
                            Your voucher order has been approved. The list of Approved Vouchers are listed in the documnet attached
                        {{-- <ul>
                            @foreach ($email_data['vouchers'] as $voucher)
                                <li>{{ $voucher->code }}</li>
                            @endforeach
                        </ul> --}}
                        You must share the following url with your customers to redeem any of the above voucher
                        {{ $email_data['resller_page'] }}

                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="font-size: 14px;color: #009a71;font-style: italic;">The Timunity Team</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
