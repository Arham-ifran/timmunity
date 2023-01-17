<html >
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Email Template</title>
</head>
<body style="padding:0; margin:0px; background: #f6fcfe;font-family: 'Nunito Sans', sans-serif !important;">
  <div style="max-width:600px; margin:auto; margin-top:50px; margin-bottom: 50px;padding: 0 20px; color:#192737; font-size:17px;">
    <table width="100%" style="display: block;">
      <tbody>
        <tr>
          <td>
            <a href="#" style="display: block;margin-bottom: 20px; text-align: center;">
              <img src="{{asset('frontside/dist/img/logo.png')}}" alt="Logo">
            </a>
          </td>
        </tr>
        <tr>
          <td>
            <h1 style="color: #009a71;font-size: 25px;margin-top: 40px;margin-bottom: 30px;font-weight: 700;">{{ $site_settings['site_name'] ?? '' }}</h1>
          </td>
        </tr>
        <tr>
          <td>
            <p style="color: #009a71;font-size: 16px; font-weight: 700;margin-bottom:20px;">Hey {{ $request['name'] ?? '' }},</p>
          </td>
        </tr>
        <tr>
          <td>
            <p style="color: #009a71;font-size: 16px; font-weight: 400;margin-bottom:10px;"><a style="color: #fff;" href="mailto:{{ $request['email'] ?? '' }}">{{ $request['email'] ?? '' }}</a></p>
          </td>
        </tr>
        <tr>
          <td>
            <p style="color: #009a71;font-size: 16px; font-weight: 400; margin-bottom: 40px"><strong>Contact Phone:&nbsp;</strong>{{ $request['phone'] ?? '' }}
            </p>
          </td>
        </tr>
        <tr>
          <td>
            <p style="color: #009a71;font-size: 16px; font-weight: 400; margin-bottom: 40px"><strong>Contact Subject:&nbsp;</strong>{{ $request['subject'] ?? '' }}
            </p>
          </td>
        </tr>
        <tr>
          <td>
            <p style="color: #333333d4;font-size: 16px; font-weight: 400; margin-bottom: 40px"><strong style="color: #009a71">Contact Message:</strong><br/></br/>{{ $request['message'] ?? '' }}
            </p>
          </td>
        </tr>
        <tr>
          <td>
            <p style="font-size: 14px;color: #009a71;">Thank you so much,</p>
          </td>
        </tr>
        <tr>
          <td>
            <p style="font-size: 14px;color: #009a71;font-style: italic;">The {{ $site_settings['site_name'] ?? '' }} Team</p>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</body>
</html>
