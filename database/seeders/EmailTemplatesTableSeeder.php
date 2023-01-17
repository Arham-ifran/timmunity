<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmailTemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->delete();

        \DB::table('email_templates')->insert(array (
            0 =>
            array (
              'id' => 1,
              'type' => 'reset_password',
              'subject' => 'Reset Password',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style=" font-size: 22px; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #000000;">{{label_1.0}}</h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.1}} {{name}},
                    </h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.2}}
                    </h3>
                    <div style="margin: 40px 0; text-align: center;">
                    <a href="{{link}}" target="_blank" style="display: inline-block;padding: 12px 15px;font-family: \'Source Sans Pro\', Helvetica, Arial, sans-serif;font-size: 16px;color: #ffffff;text-decoration: none;border-radius: 6px;width: 130px;background-color:#009a71;text-align: center;">{{label_1.3}}</a>
                    </div>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.4}}: <a href="{{link}}" target="_blank">{{link}}</a></p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.5}},</p>
                    {{app_name}}
                    <p><a href="https://www.productimmunity.com" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","link":"Link for reset password","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2019-11-14T17:38:27.000000Z',
              'updated_at' => '2021-08-13T03:34:35.000000Z',
            ),
            1 =>
            array (
              'id' => 2,
              'type' => 'sign_up_confirmation',
              'subject' => 'Sign up Confirmation',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style=" font-size: 22px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;">{{label_1.0}} {{app_name}}</h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.1}}  <strong>{{name}},</strong>
                    </h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.2}} {{app_name}} {{label_1.3}}.
                    </h3>
                    <div style="margin: 40px 0; text-align:center;">
                    <a href="{{link}}" target="_blank" style="display: inline-block;padding: 12px 15px;font-family: \'Source Sans Pro\', Helvetica, Arial, sans-serif;font-size: 16px;color: #ffffff;text-decoration: none;border-radius: 6px;width: 150px;background-color:#009a71;text-align: center;">{{label_1.4}}</a>
                    </div>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.5}}: <a href="{{link}}" target="_blank">{{link}}</a></p>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;color: #555;">{{label_1.6}}.</p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="margin-top: 30px;  font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.7}},</p>
                    {{app_name}}
                    <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","link":"Link for Verify Email Address","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2019-12-04T18:28:21.000000Z',
              'updated_at' => '2021-08-13T03:35:22.000000Z',
            ),
            2 =>
            array (
              'id' => 3,
              'type' => 'payment_success',
              'subject' => 'Payment Success',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style=" font-size: 22px; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #000000;">{{label_1.0}}</h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">{{label_1.1}}&nbsp;<strong>{{name}},</strong></h3><h3 style="line-height: 25px; color: rgb(0, 0, 0); font-size: 18px;"><span style="font-weight: 700;">{{label_1.2}}</span>&nbsp;: ({{order_number}})</h3><p style="font-size: 18px; line-height: 25px; font-weight: normal;"><span style="color: rgb(85, 85, 85); font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; font-size: 17px;">{{label_1.3}}</span></p><p style="font-size: 18px; line-height: 25px;"><span style="color: rgb(85, 85, 85); font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; font-size: 17px;"><b>{{label_1.4}}</b> : ({{transaction_id}})</span><span style="font-weight: normal; color: rgb(85, 85, 85); font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; font-size: 17px;"><br></span><br></p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.5}},</p>
                    {{app_name}}
                    <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","order_number":"Quotation order number","transaction_id":"Payment transaction ID","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2020-01-14T13:22:23.000000Z',
              'updated_at' => '2021-08-21T09:03:55.000000Z',
            ),
            3 =>
            array (
              'id' => 4,
              'type' => 'send_password',
              'subject' => 'Account Password',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style=" font-size: 22px; text-transform: capitalize; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #000000;">Account Password</h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    Hi {{name}},
                    </h3>
                    <p style="font-size: 17px; line-height: 25px; margin-top: 40px; margin-bottom: 40px; color: rgb(85, 85, 85);"><span style="font-weight: normal;">To login your account, please use the following password: </span><b>{{password}}</b></p>
                    <p style="font-size: 17px; line-height: 25px; margin-top: 40px; margin-bottom: 40px; color: rgb(85, 85, 85);"><span style="font-weight: normal;">Do not share this password with anyone. {{app_name}} takes your account security very seriously. {{app_name}} will never ask you to disclose your password.</span></p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">Cheers,</p><span style="font-family: Segoe, &quot;Segoe UI&quot;, sans-serif;">{{app_name}}</span><p style="font-family: Segoe, &quot;Segoe UI&quot;, sans-serif;"><a href="https://www.productimmunity.com/" target="_blank">www.productimmunity.com</a></p></div>
                    </div>',
              'info' => '{"name":"User full name","app_name":"Website name","password":"Account Password"}',
              'status' => 1,
              'created_at' => '2020-02-29T07:34:55.000000Z',
              'updated_at' => '2021-01-15T05:51:38.000000Z',
            ),
            4 =>
            array (
              'id' => 5,
              'type' => 'contact_us_inquiry_received',
              'subject' => 'Contact Us Inquiry Received',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style=" font-size: 22px; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #000000;">{{label_1.0}}</h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.1}} <strong>{{name}},</strong></h3>
                    <p style="font-size:16px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.2}}</p>
                    <p style="font-size:16px;line-height: 25px;font-weight: normal;color: #555;">{{label_1.3}}</p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="margin-top: 30px;  font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.4}},</p>
                    {{app_name}}
                    <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2020-05-14T06:59:59.000000Z',
              'updated_at' => '2021-08-13T03:24:56.000000Z',
            ),
            5 =>
            array (
              'id' => 6,
              'type' => 'contact_us_inquiry_submitted',
              'subject' => 'Contact Us Inquiry Submitted',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    Hi Admin,
                    </h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;margin-bottom: 0;">
                    An inquiry has been submitted by <strong>{{name}}</strong> with the following details:
                    </h3>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="font-size:18px;   line-height: 25px;">
                    <table style="border: 1px solid #ddd;width: 100%;">
                    <tbody>
                    <tr>
                    <th style="padding: 10px 15px; border-bottom: 1px solid #ddd; text-align: left;">Date :</th>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #ddd;">{{date}}</td>
                    </tr>
                    <tr>
                    <th style="padding: 10px 15px; border-bottom: 1px solid #ddd; text-align: left;">Name :</th>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #ddd;">{{fullname}}</td>
                    </tr>
                    <tr>
                    <th style="padding: 10px 15px; border-bottom: 1px solid #ddd; text-align: left;">Email :</th>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #ddd;">{{email}}</td>
                    </tr>
                    <tr>
                    <th style="padding: 10px 15px; border-bottom: 1px solid #ddd; text-align: left;">Phone :</th>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #ddd;">{{phone}}</td>
                    </tr>
                    <tr>
                    <th style="padding: 10px 15px; border-bottom: 1px solid #ddd; text-align: left;">Subject :</th>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #ddd;">{{subject}}</td>
                    </tr>
                    <tr>
                    <th colspan="2" style="padding: 10px 15px; border-bottom: 1px solid #ddd; text-align: left;">Message :</th>
                    </tr>
                    <tr>
                    <td colspan="2" style="padding: 10px 15px; border-bottom: 1px solid #ddd;">{{message}}</td>
                    </tr>
                    </tbody></table>

                    </div>
                    <div style="margin-top: 30px;  font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.9}},</p>
                    {{app_name}}
                    <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","app_name":"Website name","date":"Submission Date","fullname":"FullName","email":"Email Address","phone":"Contact Number","subject":"Subject","message":"Message"}',
              'status' => 1,
              'created_at' => '2020-05-14T07:07:17.000000Z',
              'updated_at' => '2021-08-13T03:34:04.000000Z',
            ),
            6 =>
            array (
              'id' => 7,
              'type' => 'voucher_redeemed_email',
              'subject' => 'Voucher Redeemed Email',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    </h3><h3 style="font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; color: rgb(0, 0, 0); font-size: 22px;">{{label_1.0}} </h3><h3 style="font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; color: rgb(0, 0, 0); font-size: 22px;">{{label_1.1}} <strong style="color: inherit; font-family: &quot;Source Sans Pro&quot;, sans-serif; font-size: 18px;">{{name}}</strong><span style="color: inherit; font-family: &quot;Source Sans Pro&quot;, sans-serif; font-size: 18px;">,</span></h3>
                    <h3 style="font-size: 18px; line-height: 25px; margin-bottom: 0px;"><span style="font-weight: normal;">
                    {{label_1.2}}&nbsp;</span></h3>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="font-size:18px;   line-height: 25px;">
                    <table style="border: 1px solid #ddd;width: 100%;">
                    <tbody>
                    <tr>
                    <th style="padding: 10px 15px; border-bottom: 1px solid #ddd; text-align: left;">{{label_1.3}} :</th>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #ddd;">{{voucher_code}}</td>
                    </tr>
                    <tr>
                    <th style="padding: 10px 15px; border-bottom: 1px solid #ddd; text-align: left;">{{label_1.4}} :</th>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #ddd;">{{product_name}}</td>
                    </tr>
                    <tr>
                    <th style="padding: 10px 15px; border-bottom: 1px solid #ddd; text-align: left;">{{label_1.5}} :</th>
                    <td style="padding: 10px 15px; border-bottom: 1px solid #ddd;">{{license_code}}</td>
                    </tr>
                    </tbody></table>

                    </div>
                    <div style="margin-top: 30px;  font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.6}},</p>
                    {{app_name}}
                    <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","app_name":"Website name","email":"User email","secret_key":"Google Authenticator Secret Key For Reset Two Factor Authentication"}',
              'status' => 1,
              'created_at' => '2020-10-06T06:47:13.000000Z',
              'updated_at' => '2021-09-07T09:25:08.000000Z',
            ),
            7 =>
            array (
              'id' => 8,
              'type' => 'order_vouchers_created',
              'subject' => 'Vouchers Order Received',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.0}} {{name}},
                    </h3>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.1}}</p>
                    <p style="font-size: 17px; line-height: 25px; margin-top: 40px; margin-bottom: 40px; color: rgb(85, 85, 85);"><span style="font-weight: normal;">{{label_1.2}} </span><b>{{order_id}}</b><span style="font-weight: normal;"> {{label_1.3}} </span><b>{{product}}</b> {{label_1.4}} {{label_1.5}}</p>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.6}}</p>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.7}}</p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.8}}</p>
                    {{app_name}}
                    <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2020-12-15T10:36:25.000000Z',
              'updated_at' => '2021-09-07T05:03:26.000000Z',
            ),
            8 =>
            array (
              'id' => 9,
              'type' => 'order_vouchers_submitted',
              'subject' => 'Voucher Approved',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style=" font-size: 22px; text-transform: capitalize; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #000000;">Order Vouchers</h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    Hi Admin,
                    </h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">A new order has been submitted by {{name}}. Tap the button below to view the order details.</h3>
                    <div style="margin: 40px 0; text-align: center;">
                    <a href="{{link}}" target="_blank" style="display: inline-block;padding: 12px 15px;font-family: \'Source Sans Pro\', Helvetica, Arial, sans-serif;font-size: 16px;color: #ffffff;text-decoration: none;border-radius: 6px;width: 130px;background-color:#009a71;text-align: center;">View Order</a>
                    </div>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">If that doesn\'t work, copy and paste the following link in your browser:{{link}}</p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">Cheers,</p>
                    {{app_name}}
                    <p><a href="https://www.productimmunity.com" target="_blank">www.productimmunity.com</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","link":"Link for view order details","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2020-12-16T10:32:40.000000Z',
              'updated_at' => '2021-08-14T02:27:48.000000Z',
            ),
            9 =>
            array (
              'id' => 10,
              'type' => 'vouchers_payment_generated',
              'subject' => 'Vouchers Payment',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.0}} {{name}},
                    </h3>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.1}}</p>
                    <p style="font-size: 17px; line-height: 25px; margin-top: 40px; margin-bottom: 40px; color: rgb(85, 85, 85);"><span style="font-weight: normal;">{{label_1.2}} </span><b>{{order_id}}</b> {{label_1.3}} {{label_1.4}}</p>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.5}}</p>

                    <div style="text-align: center;">
                    <a href="{{link}}" target="_blank" style="display: inline-block;padding: 12px 15px;font-family: \'Source Sans Pro\', Helvetica, Arial, sans-serif;font-size: 16px;color: #ffffff;text-decoration: none;border-radius: 6px;width: 130px;background-color:#009a71;text-align: center;">{{label_1.6}}</a>
                    </div>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.7}}</p>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.8}} {{link}}</p>
                    <p style="font-size:17px;font-style: italic;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.9}}</p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.10}}</p>
                    {{app_name}}
                    <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    <p style="font-size: 17px; line-height: 25px; margin-top: 40px; margin-bottom: 40px; color: rgb(85, 85, 85);"><span style="font-weight: normal;">{{label_1.11}} </span><b>{{no_of_days}}</b> {{label_1.12}} {{label_1.13}}</p>
                    </div>',
              'info' => '{"name":"User full name","link":"Payment Link","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2020-12-17T13:02:13.000000Z',
              'updated_at' => '2021-09-07T09:09:23.000000Z',
            ),
            10 =>
            array (
              'id' => 11,
              'type' => 'vouchers_payment_success',
              'subject' => 'Vouchers Payment Received',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.0}} {{name}},
                    </h3>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.1}}</p>
                    <p style="font-size: 17px; line-height: 25px; margin-top: 40px; margin-bottom: 40px; color: rgb(85, 85, 85);"><span style="font-weight: normal;">{{label_1.2}} </span><b>{{order_id}} </b>{{label_1.3}}</p>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.4}}</p>
                    <p style="font-size:17px;font-style: italic;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.5}}</p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.6}}</p>
                    {{app_name}}
                    <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2020-12-17T13:46:57.000000Z',
              'updated_at' => '2021-09-07T08:02:22.000000Z',
            ),
            11 =>
            array (
              'id' => 12,
              'type' => 'order_vouchers_approved',
              'subject' => 'Order Approved',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.0}} {{name}},</h3>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.1}}</p>
                    <p style="font-size: 17px; line-height: 25px; margin-top: 40px; margin-bottom: 40px; color: rgb(85, 85, 85);"><span style="font-weight: normal;">{{label_1.2}} </span><b>{{order_id}}</b> {{label_1.3}} <b>{{product}}</b> {{label_1.4}} {{label_1.5}}</p>

                    <div style="margin: 40px 0; text-align: center;">
                    <a href="{{link}}" target="_blank" style="display: inline-block;padding: 12px 15px;font-family: \'Source Sans Pro\', Helvetica, Arial, sans-serif;font-size: 16px;color: #ffffff;text-decoration: none;border-radius: 6px;width: 130px;background-color:#009a71;text-align: center;">{{label_1.6}}</a>
                    </div>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.7}}</p>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.8}} {{link}}</p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.9}}</p>
                    {{app_name}}
                    <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","link":"Link for view orders","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2021-01-01T06:05:28.000000Z',
              'updated_at' => '2021-09-07T06:34:54.000000Z',
            ),
            12 =>
            array (
              'id' => 13,
              'type' => 'quotation_licenses_email',
              'subject' => 'Quotation Licenses Email',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style=" font-size: 22px; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #000000;">{{label_1.0}}</h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">{{label_1.1}}&nbsp;<strong>{{name}},</strong></h3><h3 style="font-size: 18px; line-height: 25px;"><b>{{label_1.2}}</b><span style="font-weight: normal;"> : ({{order_number}})</span></h3><p style="font-size: 18px; line-height: 25px; font-weight: normal;"><span style="color: rgb(85, 85, 85); font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; font-size: 17px;">{{label_1.3}}</span></p><p style="font-size: 18px; line-height: 25px;"><span style="color: rgb(85, 85, 85); font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; font-size: 17px;">{{licenses_list}}</span><span style="color: rgb(85, 85, 85); font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; font-size: 17px;"><br></span><span style="font-weight: normal; color: rgb(85, 85, 85); font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; font-size: 17px;"></span></p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.4}},</p>
                    {{app_name}}
                    <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","order_number":"Quotation order number","licenses_list":"List of assigned liceses","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2021-01-01T12:52:44.000000Z',
              'updated_at' => '2021-08-21T09:18:35.000000Z',
            ),
            13 =>
            array (
              'id' => 14,
              'type' => 'quotation_order_placed',
              'subject' => 'Quotation Order Placed',
              'content' => '<div style="padding: 10px 30px; font-family: Segoe, &quot;Segoe UI&quot;, sans-serif;"><h3 style="font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; color: rgb(0, 0, 0); font-size: 22px;">{{label_1.0}}</h3><h3 style="line-height: 25px; color: rgb(0, 0, 0); font-size: 18px;">{{label_1.1}} <span style="font-weight: 700;">{{name}},</span></h3><h3 style="line-height: 25px; font-size: 18px;">{{label_1.2}} : <span style="font-weight: 700;">({{order_number}})</span></h3><p style="margin-top: 40px; margin-bottom: 40px; font-size: 17px; line-height: 25px; color: rgb(85, 85, 85);">{{label_1.3}}</p></div><div style="padding: 30px 30px 10px; font-family: Segoe, &quot;Segoe UI&quot;, sans-serif;"><div style="font-size: 15px; color: rgb(85, 85, 85);"><p style="margin-bottom: 0px; font-style: italic; font-weight: 600;">{{label_1.4}},</p>{{app_name}}<p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p></div></div>',
              'info' => '{"name":"Customer full name","order_number":"Quotation order number","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2021-08-18T09:58:32.000000Z',
              'updated_at' => '2021-08-21T08:46:50.000000Z',
            ),
            14 =>
            array (
              'id' => 15,
              'type' => 'send_quotation_order',
              'subject' => 'Order Pro-forma Quotation Ref( S00001  )',
              'content' => '<div style="padding: 10px 30px; font-family: Segoe, &quot;Segoe UI&quot;, sans-serif;"><h3 style="font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; color: rgb(0, 0, 0); font-size: 18px;">{{label_1.0}}</h3><h3 style="line-height: 25px; font-size: 18px;"><span style="font-weight: 400;margin-top: 20px;margin-bottom: 10px;font-size: 17px;line-height: 25px;color: rgb(85, 85, 85);">{{email_body_content}}</span></h3></div><div style="padding: 30px 30px 10px; font-family: Segoe, &quot;Segoe UI&quot;, sans-serif;"><div style="font-size: 15px; color: rgb(85, 85, 85);"><p style="margin-bottom: 0px; font-style: italic; font-weight: 600;">{{label_1.2}},</p>{{app_name}}<p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p></div></div>',
              'info' => '{"email_body_content" : "Admin defined email body content","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2021-08-21T10:50:46.000000Z',
              'updated_at' => '2021-10-08T22:17:28.000000Z',
            ),
            15 =>
            array (
              'id' => 16,
              'type' => 'customer_sign_up_confirmation',
              'subject' => 'Customer Sign Up Confirmation',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style=" font-size: 22px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;">{{label_1.0}} {{app_name}}</h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.1}}  <strong>{{name}},</strong>
                    </h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.2}} {{app_name}} {{label_1.3}}
                    </h3>
                    <div style="margin: 40px 0; text-align:center;">
                    <a href="{{link}}" target="_blank" style="display: inline-block;padding: 12px 15px;font-family: \'Source Sans Pro\', Helvetica, Arial, sans-serif;font-size: 16px;color: #ffffff;text-decoration: none;border-radius: 6px;width: 150px;background-color:#009a71;text-align: center;">{{label_1.4}}</a>
                    </div>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.5}}: <a href="{{link}}" target="_blank">{{link}}</a></p>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;color: #555;">{{label_1.6}}.</p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="margin-top: 30px;  font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.7}},</p>
                    {{app_name}}
                    <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","link":"Link for Verify Email Address","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2021-08-28T10:18:04.000000Z',
              'updated_at' => '2021-08-28T10:39:38.000000Z',
            ),
            16 =>
            array (
              'id' => 19,
              'type' => 'account_approval_confirmation',
              'subject' => 'Account  Approval Confirmation',
              'content' => '<div style="padding: 10px 30px; font-family: Segoe, &quot;Segoe UI&quot;, sans-serif;"><h3 style="line-height: 25px; font-size: 18px;">{{label_1.0}} {{name}},</h3><p style="margin-top: 40px; margin-bottom: 40px; font-size: 17px; line-height: 25px; color: rgb(85, 85, 85);"><b>{{label_1.1}}</b></p><p style="margin-top: 40px; margin-bottom: 40px; font-size: 17px; line-height: 25px; color: rgb(85, 85, 85);">{{label_1.2}}</p><div style="margin: 40px 0px; text-align: center;"><a href="{{link}}" target="_blank" style="background-color: rgb(0, 154, 113); color: rgb(255, 255, 255); display: inline-block; padding: 12px 15px; font-family: &quot;Source Sans Pro&quot;, Helvetica, Arial, sans-serif; font-size: 16px; border-radius: 6px; width: 130px;text-decoration: none;">{{label_1.3}}</a></div><p style="margin-top: 40px; margin-bottom: 40px; font-size: 17px; line-height: 25px; color: rgb(85, 85, 85);">{{label_1.4}}</p><p style="margin-top: 40px; margin-bottom: 40px; font-size: 17px; line-height: 25px; color: rgb(85, 85, 85);">{{label_1.5}} {{link}}</p></div><div style="padding: 30px 30px 10px; font-family: Segoe, &quot;Segoe UI&quot;, sans-serif;"><div style="font-size: 15px; color: rgb(85, 85, 85);"><p style="margin-bottom: 0px; font-style: italic; font-weight: 600;">{{label_1.6}}</p>{{app_name}}<p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p></div></div>',
              'info' => '{"name":"User full name","link":"Link for Login Page Address","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2021-09-17T22:34:02.000000Z',
              'updated_at' => '2021-09-17T22:51:31.000000Z',
            ),
            17 =>
            array (
              'id' => 21,
              'type' => 'account_disabled_after_inactivity',
              'subject' => 'Account Disabled After Inactivity ',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, "Segoe UI", "sans-serif";">
                        <h3 style=" font-size: 22px; font-family: Segoe, "Segoe UI", "sans-serif"; margin-top: 20px;color: #000000;"><span style="color: inherit; font-family: inherit; font-size: 18px;">Dear {{ name }},</span><br></h3>
                        <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                        Due to inactivity on your account for {{ no_of_days }} days, your account has been temporarily disabled.
                        </h3>
                        <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                        Please contact the Administrator in order to get your account activated again.
                        </h3>
                        <div style="margin: 40px 0; text-align: center;">
                        <a href="{{ contact_link }}" target="_blank" style="display: inline-block;padding: 12px 10px;font-family: "Source Sans Pro", Helvetica, Arial, sans-serif;font-size: 16px;color: #ffffff;text-decoration: none;border-radius: 6px;width: 130px;background-color:#009a71;text-align: center;">Contact Admin</a>
                        </div>
                        </div>

                        <div style=" padding:30px 30px 10px;  font-family: Segoe, "Segoe UI", "sans-serif";">
                        <div style="font-size: 15px; color: #555;">
                        <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">Regards,</p>
                        {{ app_name }}
                        </div>
                        </div>',
              'info' => '{"name":"User full name","contact_link":"Link for Contact Page","no_of_days":"account inactivity time limit days"}',
              'status' => 1,
              'created_at' => '2021-09-17T22:34:02.000000Z',
              'updated_at' => '2021-09-17T22:51:31.000000Z',
            ),
            18 =>
            array (
              'id' => 20,
              'type' => 'low_license_key_count_notification',
              'subject' => 'Low License Key Count Notification',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <h3 style=" font-size: 22px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;">{{label_1.0}}</h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.1}}  <strong>{{name}},</strong>
                    </h3>
                    <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                    {{label_1.2}}</h3>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;">{{label_1.3}}</p>
                    <p style="font-size:17px;line-height: 25px;font-weight: normal;color: #555;">{{label_1.4}}.</p>
                    </div>

                    <div style=" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';">
                    <div style="margin-top: 30px;  font-size: 15px; color: #555;">
                    <p style="font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;">{{label_1.5}},</p>
                    {{app_name}}
                    <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                    </div>
                    </div>',
              'info' => '{"name":"User full name","app_name":"Website name"}',
              'status' => 1,
              'created_at' => '2021-09-30T20:29:48.000000Z',
              'updated_at' => '2021-09-30T20:29:48.000000Z',
            ),
            19 =>
            array (
              'id' => 22,
              'type' => 'schedule_activity_email',
              'subject' => 'Schedule Activity Email',
              'content' => '<div style=" padding:10px 30px 10px;  font-family: Segoe, "Segoe UI", "sans-serif";">
                            <h3 style=" font-size: 22px; font-family: Segoe, "Segoe UI", "sans-serif"; margin-top: 20px;color: #000000;">
                                {{ label_1.0 }}</h3>
                            <h3 style="font-size:18px;line-height: 25px;font-weight: normal;">
                                {{ label_1.1 }} <strong>{{ name }},</strong></h3>
                            <p style="font-size:16px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 10px;color: #555;">
                                {{ content }}</p>
                                <a href="{{link}}" target="_blank" style="display: inline-block;padding: 12px 15px;font-family: "Source Sans Pro", Helvetica, Arial, sans-serif;font-size: 16px;color: #ffffff;text-decoration: none;border-radius: 6px;width: 130px;background-color:#009a71;text-align: center;">{{label_1.3}}</a>
                        </div>

                        <div style=" padding:30px 30px 10px;  font-family: Segoe, "Segoe UI", "sans-serif";">
                            <div style="margin-top: 30px;  font-size: 15px; color: #555;">{{ label_1.2 }},
                                {{ app_name }}
                                <p><a href="https://odoo.arhamsoft.org/" target="_blank">odoo.arhamsoft.org</a></p>
                            </div>
                        </div>',
              'info' => '{"name":"User full name","content":"Scedule Activity Content"}',
              'status' => 1,
              'created_at' => '2021-09-30T20:29:48.000000Z',
              'updated_at' => '2021-09-30T20:29:48.000000Z',
            ),
          ));
    }
}
