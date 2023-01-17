<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <title>
    @if (!$data['transaction_id']) Order Successfull. @else Payment Received
        @endif
    </title>
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
                        @if (!$data['transaction_id']) Order Successfull. @else
                                Payment Received @endif <br> {{ $data['quotationnumber'] }}
                        </h1>
                    </td>
                </tr>

                <tr>
                    <td>
                        <p style="color: #009a71;font-size: 16px;">
                            @if (!$data['transaction_id'])
                                Your order has been placed successfully as Quotation. You will recieve confirmation when
                                the payment is succesfully done and the order will be processed.
                            @else
                                The payment against order {{ $data['quotationnumber'] }} has been received against
                                Transaction ID : {{ $data['transaction_id'] }}

                            @endif

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
