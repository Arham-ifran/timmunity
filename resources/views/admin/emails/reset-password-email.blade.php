<!DOCTYPE html>
<html>
   <head>
      <title>Invitaion | TIMmunity</title>
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
   </head>
   <body style="background: #f9f9f9;">
      <section class="container">
         <div class="content-wrapper" style=" max-width: 686px;background: #fff; margin: auto; margin-top: 30px; padding: 20px;">
            <table style="width: 100%; text-align: left; font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
               <tr>
                  <td valign="middle">Welcome to TIMmunity</td>
                  <td valign="middle" align="right"><img style="width: 36px;" src="{{ asset('backend/dist/img/favicon.png') }}" alt=""></td>
               </tr>
               <tr>
                  <td valign="top" style="border-bottom: 1px solid #ccc;padding-bottom: 10px;"><strong style="font-size: 22px; color: #444;">{{ ucfirst($email_data['name'])}}</strong></td>
               </tr>
               <tr>
                  <td valign="top" style="font-size:13px;">
                     <p>Dear {{ ucfirst($email_data['name'])}},</p>
                  </td>
               </tr>
               <tr>
                  <td>
                     <p>
                     A password reset was requested for the TIMmunity account linked to this email. You may change your password by following this link which will remain valid during 24 hours:
                     </p>
                  </td>
               </tr>
               <tr>
                  <td>
                     <p><a href="{{ route('admin.verify.admin', ['code' => $email_data['invitation_code']]) }}" type="button" style="background: #009a71; font-size: 13px; color: #fff; padding: 5px 10px; text-decoration: none;">Change Password</a></p>
                  </td>
               </tr>
               <tr>
                  <td>
                     <p>
                        If you do not expect this, you can safely ignore this email.
                     </p>
                  </td>
               </tr>
               <tr>
                  <td>
                     <p>
                        Thanks!
                     </p>
                  </td>
               </tr>
               <tr>
                  <td style="font-size:13px;border-bottom: 1px solid #ccc;padding-bottom: 10px;">
                     {!! ucfirst(Auth::user()->email_signature) !!}
                  </td>
               </tr>
               <tr>
                  <td style="font-size:13px;">
                     <p>
                        TIMmunity <br />
                        <span style="color:#aeadad;font-size: 11px;font-weight: 700">| {{ Auth::user()->email }}</span>
                     </p>
                  </td>
               </tr>
            </table>
         </div>
      </section>
   </body>
</html>