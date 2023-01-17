<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <style>
        header{
            display:none;
        }
        .content,body,html{
            background: white;
        }
        table thead{
            background: #009A71;
        }
        table thead th{
            color: white;
            background-color: #009A71;
        }

        table>thead>tr>th, table>tbody>tr>th, table>tfoot>tr>th, table>thead>tr>td, table>tbody>tr>td, table>tfoot>tr>td {
            border: 1px solid #0000004f;
        }
        .copy-right {
            position: fixed; 
            bottom: 0px; 
            left: 0px; 
            right: 0px;
            color: white;
            font-size: 16px;
            background-color: #009A71;
            padding: 0;
        }
        .text-center{
            text-align: center;
        }
        table{
            width: 100%;
        }
    </style>

   @yield('styles')
</head>
<body>
       <div>
            <section>
                <div>
                    <div>
                        <div >
                            <img src="{{ checkImage(asset('storage/uploads/' . $site_settings[0]->site_logo),'logo.png') }}" alt="">
                        </div>
                    </div>
                    <div >
                        <div ></div>
                        <div >
                            <div >
                                <div >
                                    <h3>Customer Information</h3>
                                    <p><strong>Date:</strong> {{ date("F j, Y") }}</p>
                                </div>
                            </div>
                            <div >
                                <div >
                                    <table class="table  table-bordered  no-footer dataTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Attribute
                                                </th>
                                                <th>
                                                    Value
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p><strong>Name</strong></p>
                                                </td>
                                                <td>
                                                    <p>{{ $customer_detail->name }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><strong>Email</strong></p>
                                                </td>
                                                <td>
                                                    <p>{{ $customer_detail->email }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><strong>Country</strong></p>
                                                </td>
                                                <td>
                                                    <p>{{ $customer_detail->contact_countries->name }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><strong>Status</strong></p>
                                                </td>
                                                <td>
                                                    <p>{{ $customer_detail->status == 1 ? 'Active' : 'Inactive' }}</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div >

                        </div>
                    </div>
                </div>
            </section>
        </div>
        <footer class="copy-right row main-footer text-center" >
                <div class="col-md-12 text-center pt-1">
                    <span class="footer-logo-text">Copyright © TIMmunity GmbH. All Rights Reserved.</span>
                </div>
        </footer>
    </body>
</html>
