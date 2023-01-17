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
                  <td valign="top"><strong style="font-size: 22px; color: #444;">{{ ucfirst($email_data['name'])}}</strong></td>
               </tr>
               <tr>
                  <td valign="top" style="font-size:13px">
                     <p>Dear {{ ucfirst($email_data['name'])}},</p>
                  </td>
               </tr>
               <tr>
                  <td>
                      @if($email_data['type'] == 'user')
                      <p>You have registered on our TIMmunity Website.</p>
                      @else
                      <p>You have been invited by {{ ucfirst(Auth::guard('admin')->user()->firstname .' '. Auth::guard('admin')->user()->lastname) ?? '' }} to connect on TIMmunity.</p>
                      @endif
                  </td>
               </tr>
               <tr>
                  <td>
                      @if(isset($email_data['type']))
                        @if($email_data['type'] == 'admin')
                        <p><a href="{{ route('admin.verify.admin', ['code' => $email_data['invitation_code']]) }}" type="button" style="background: #009a71; font-size: 13px; color: #fff; padding: 5px 10px; text-decoration: none;">Accept Invitation</a></p>
                        @else
                        <p><a href="{{ route('verify.user', ['code' => $email_data['invitation_code']]) }}" type="button" style="background: #009a71; font-size: 13px; color: #fff; padding: 5px 10px; text-decoration: none;">Accept Invitation</a></p>
                        @endif
                      @else
                      <p><a href="{{ route('admin.verify.admin', ['code' => $email_data['invitation_code']]) }}" type="button" style="background: #009a71; font-size: 13px; color: #fff; padding: 5px 10px; text-decoration: none;">Accept Invitation</a></p>
                      @endif
                  </td>
               </tr>
               <tr>
                  <td>
                     <p>
                     <div>Your TIMmunity domain is: <a href="#" style="color: #009a71; text-decoration: none;"> {{ route('login') }}</a></div>
                     <div>Your sign in email is: <a href="#" style="color: #009a71; text-decoration: none;">{{ $email_data['email'] }}</a></div>
                     </p>
                  </td>
               </tr>
               <tr>
                  <td>
                     <p>
                        Never heard of TIMmunity? It’s an all-in-one business software loved by 3+ million users. It will considerably improve your experience at work and increase your productivity.
                     </p>
                  </td>
               </tr>
               <tr>
                  <td>
                     <p>
                        Have a look at the TIMmunity Tour to discover the tool.
                     </p>
                  </td>
               </tr>
               <tr>
                  <td>
                     <p>
                        Enjoy TIMmunity!
                     </p>
                  </td>
               </tr>
            </table>
         </div>
      </section>
   </body>
</html>
