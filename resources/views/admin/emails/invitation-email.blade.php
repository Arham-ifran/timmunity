<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <title>Invitaion | TIMmunity</title>
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
                            Welcome to TIMmunity</h1>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="color: #009a71;font-size: 16px; font-weight: 700;margin-bottom:20px;">Hey
                            {{ ucfirst($email_data['name'])}},</p>
                    </td>
                </tr>
                <tr>
                    <td>
                      @if($email_data['type'] == 'user')
                      <p style="color: #009a71;font-size: 16px;">You have registered on our TIMmunity Website.</p>
                      @else
                      <p style="color: #009a71;font-size: 16px;">You have been invited by {{ ucfirst(Auth::guard('admin')->user()->firstname .' '. Auth::guard('admin')->user()->lastname) ?? '' }} to connect on TIMmunity.</p>
                      @endif
                    </td>
                </tr>
                <tr>
                    <td>
                         @if(isset($email_data['type']))
                        @if($email_data['type'] == 'admin')
                        <p style="color: #009a71;font-size: 16px;margin-bottom:20px;margin-top:20px;"><a href="{{ route('admin.verify.admin', ['code' => $email_data['invitation_code']]) }}" type="button" style="background: #009a71; font-size: 13px; color: #fff; padding: 5px 10px; text-decoration: none;">Accept Invitation</a></p>
                        @else
                        <p style="color: #009a71;font-size: 16px;margin-bottom:20px;margin-top:20px;"><a href="{{ route('verify.user', ['code' => $email_data['invitation_code']]) }}" type="button" style="background: #009a71; font-size: 13px; color: #fff; padding: 5px 10px; text-decoration: none;">Accept Invitation</a></p>
                        @endif
                      @else
                      <p style="color: #009a71;font-size: 16px;margin-bottom:20px;margin-top:20px;"><a href="{{ route('admin.verify.admin', ['code' => $email_data['invitation_code']]) }}" type="button" style="background: #009a71; font-size: 13px; color: #fff; padding: 5px 10px; text-decoration: none;">Accept Invitation</a></p>
                      @endif
                    </td>
                </tr>
                <tr>
                    <td>
                     <p style="color: #009a71;font-size: 16px;">
                        Never heard of TIMmunity? It’s an all-in-one business software loved by 3+ million users. It will considerably improve your experience at work and increase your productivity.
                     </p>
                     <p style="color: #009a71;font-size: 16px;">
                        Have a look at the TIMmunity Tour to discover the tool.
                     </p>
                  </td>
                </tr>
                <tr>
                    <td>
                        <p style="font-size: 14px;color: #009a71;">Enjoy Timunity</p>
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
