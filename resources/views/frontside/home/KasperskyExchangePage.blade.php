@extends('frontside.layouts.app')
@section('title')  Kaspersky Exchange Program @endsection
@section('style')
<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{ asset('backend/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
<style> .swal2-title {
    font-size: 2.3rem;
    }
    .swal2-popup {
    font-size: 1.5rem;
    border-radius: 8px;
    }
    .swal2-actions button{
        font-size: 1.5rem !important;
    }
    .swal2-confirm.swal2-styled {
  padding: 10px 25px;
  background: #009b72;
  border: 2px solid #009b72;
}
.swal2-confirm.swal2-styled:hover {
    padding: 10px 25px;
    color:#009b72;
    background: #fff;
    border: 2px solid #009b72;
}</style>
@endsection
@section('content')
     <!-- Main -->
     <section class="main mb-2">
        <div class="container">
            <div class="main-content text-center heading-width">
                <h1 >{{__('Kaspersky Exchange Program')}} - TIMmunity GmbH</h1>
                <p class="banner-p">{{__("If you're using a Kaspersky license and want to be a part of exchange program and get the discounted products, please fill in the form below.")}}</p>
            </div>
        </div>
    </section>
    <!-- Product -->
    <section class="product section-padding">
        <div class="container">
            <form action="{{route('frontside.page.KasperskyExchangePage.post')}}" method="POST">
                @csrf
                <div class="row custom-row custom-row-section">
                    <div class="col-md-6">
                        <div class="form-group" >
                            <label for="access_type">{{__('I have')}}</label><br>
                            <label for="" class="control-label">
                                <input type="radio" name="access_type" value="0" checked>
                                {{__('License Key')}}
                            </label>
                            <label for="" class="control-label ml-2">
                                <input type="radio" name="access_type" value="1">
                                {{__('Voucher Code')}}
                            </label>
                            <br>
                            <label for="" class="control-label">
                                <input type="radio" name="access_type" value="2">
                                {{__('I am not a TIMmunity customer, but would like to participate in the program')}}
                            </label>
                            <br>
                        </div>
                    </div>
                </div>
                <div class="row product_associated">
                    <div class="col-md-6">
                        <div class="form-group" id="license_div" >
                            <label for="" class="control-label">{{__('License Key')}}</label>
                            <input type="text" name="license_key" class="form-control" placeholder="{{__('Enter License Key')}}">
                        </div>
                        <div class="form-group" id="voucher_div" style="display:none">
                            <label for="" class="control-label">{{__('Voucher Code')}}</label>
                            <input type="text" name="voucher_key" class="form-control" placeholder="{{__('Enter Voucher Code')}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" >
                            <label for="associated_product">{{__('Product associated')}}</label><br>
                            <label for="" class="control-label">
                                <input type="radio" name="associated_product" value="0" required checked>
                                {{__('Kaspersky Total Security')}}
                            </label>
                            <label for="" class="control-label ml-2">
                                <input type="radio" name="associated_product" value="1" required>
                                {{__('Kaspersky Internet Security')}}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_name" class="control-label">{{__('Your Name')}}<small style="color:red">*</small></label>
                            <input type="text" required="required" name="customer_name" class="form-control" placeholder="{{__('Enter Your Name')}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_email" class="control-label">{{__('Your Email Address')}}<small style="color:red">*</small></label>
                            <input type="email" required="required" name="customer_email" class="form-control" placeholder="{{__('Enter Your Email Address')}}">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 text-center mb-2">
                        <input type="submit" class="btn btn-primary" value="{{__('Apply For Exchange Program')}}">
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12">
                        <p>
                            <strong>
                                {{ __('In the wake of the urgent warning by the German Federal Office for Information Security (BSI) to refrain from using')}} <a href="https://www.bsi.bund.de/DE/Service-Navi/Presse/Pressemitteilungen/Presse2022/220315_Kaspersky-Warnung.html" target="_blank">{{__('security software from the Russian provider Kaspersky')}}</a> 
                            </strong>,
                            {{__('TIMmunity GmbH is supporting all Kaspersky users in their migration to alternative solutions with an attractive switch offer.')}}
                        </p> 

                        <p>
                           {{__('The primary goal is to ensure the most uninterrupted protection possible for all devices of end users, companies and municipalities.')}} 
                        </p>  

                        <p>
                           {{__('For this reason, TIMmunity GmbH terminated all Kaspersky licences (which were acquired and managed via TIMmunity) in the night from 15 to 16 March 2022 and had them switched off. The Kaspersky applications used by customers are thus in test mode and can still be used in full for 30 days. This gives users the time to look for alternative solutions.')}}
                        </p> 

                        <p>
                            {{__('To cushion the additional costs incurred by switching to solutions from other manufacturers such as Acronis, Avast, Bullguard, F-Secure, Norton or WatchGuard/Panda, TIMmunity GmbH grants shop visitors a 20 percent discount on the security solutions currently available online.')}}
                        </p> 

                        <p>
                            {{__('It is only fair that we support you in choosing an alternative security solution due to the current recommendation of the BSI.')}}
                        </p> 

                        <p>
                            {{__('We only offer solutions from European and Western providers and support users during migration. In addition, in the course of the migration, we are granting all shop visitors a 20 per cent discount on the security solutions currently available online.')}}
                        </p>

                        <p>
                            <strong>
                                {{__('Transparency for private and business customers as well as municipalities')}}
                            </strong>
                            <br>
                            {{__("As part of the campaign, private customers can switch from 2.52 euros for three devices and one year - manufacturer's list price 59.99 euros , business customers from 4.52 euros gross - manufacturer's list price 49.97 euros - 3.82 euros net per device. Municipalities can obtain proof of purchase price - without TIMmunity being present - from our tax office. These documents are available digitally or on site.")}}
                        </p>

                        <p>
                            <strong>
                                {{__('Basis for the decision of')}} TIMmunity GmbH
                            </strong><br>
                            {{__("In view of the financial risks for TIMmunity and the security gap for our customers that exist with the continued use of the Kaspersky software and a possible misuse of this software (by third parties), we have decided to have the customers licences or subscription contracts deleted or terminated in order to prevent them from being accessed by Kaspersky. With the warning of the BSI, the integrity of the Kaspersky company as well as the protectability of the products is highly doubtful.")}}
                        </p>

                        <p>
                            {{__('If even the football club Eintracht Frankfurt can terminate its sponsorship with Kaspersky "with immediate effect" due to the BSI warning')}} (<a href="https://www.faz.net/aktuell/sport/fussball/bundesliga/eintracht-frankfurt-trennt-sich-von-russi-schem-sponsor-kaspersky-17879050.html" target="_blank">https://www.faz.net/aktuell/sport/fussball/bundesliga/eintracht-frankfurt-trennt-sich-von-russi-schem-sponsor-kaspersky-17879050.html</a>), {{__('it is all the more unreasonable for TIMmunity to continue to make Kaspersky\'s products available to its customers if highly sensitive (business) data are affected.')}}</p>

                        <p>
                            {{__('Our customers are very important to us, which is why we would like to make the gradual changeover possible for you. After cancelling your Kaspersky subscription, your licence will switch to a 30-day maintenance mode. During this period, you can choose one of the replacement products from our shop mentioned above.')}}
                        </p>
                         
                       
                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection
@section('script')

<script src="{{ _asset('backend/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $('[name=access_type]').on('click',function(){
            if($(this).val() == 0){
                $('#license_div').show();
                $('#voucher_div').hide();
                $('.product_associated').show();
            }else if($(this).val() == 1){
                $('#license_div').hide();
                $('#voucher_div').show();
                $('.product_associated').show();
            }else{
                $('#license_div').hide();
                $('#voucher_div').hide();
                $('.product_associated').hide();
            }
        });
        


        //     ({
        //         icon: 'warning',
        //         title: 'Warning',
        //         text: 'sss',
        //     });
    </script>
   <!-- Script Goes Here -->
@endsection
