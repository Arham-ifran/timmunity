@extends('frontside.layouts.app')
@section('title')   @endsection
@section('style')
<style>
    /* body */
    body {
    margin: 0px;
    overflow-x: hidden;
    font-size: 16px;
    color: #000;
    }
    html {
    scroll-behavior: smooth;
    }

    h1 {
    font-size: 66px;
    margin-top: 0px;
    }
    h2 {
    font-size: 54px;
    line-height: 54px;
    margin-bottom: 20px;
    margin-top: 0px;
    }
    h3 {
    font-size: 22px;
    }
    h4 {
    font-size: 22px;
    margin-top: 22px;
    }
    p {
    margin-top: 0px;
    }
    .banner-p{
    font-size: 24px;
    }
    .chrac-p{
    font-size: 21px;
    }
    .add-on-content p, .add-on-content li{
    font-size: 20px;
    }
    .custom-row-section{
    display: flex;
    flex-wrap: wrap;
    }
    .h2{
    font-size: 54px;
    }

    /* Header */

    /* Main */
    .main {
    background-image: url("{{asset('frontside/dist/img/device/main.png')}}");
    background-repeat: no-repeat;
    background-size: 100% 100%;
    }
    .main-content {
    /* max-width: 860px; */
    margin: 0px auto;
    padding: 170px 0px;
    }
    .heading-width h3{
    max-width: 860px;
    margin: 0px auto;
    }
    .product-image {
    display: flex;
    justify-content: flex-end;
    }
    /* Product */
    .section-padding {
    padding-top: 110px;
    }
    .related-solution.section-padding {
    padding: 100px 0;
    }
    .product-content p {
    margin-bottom: 25px;
    }
    .product-btn {
    font-size: 17px;
    color: white;
    background-color: #009a71;
    border: 2px solid #009a71;
    padding: 14px 20px;
    font-weight: 500;
    }
    .product-btn:hover {
    color: #009a71;
    background-color: white;
    }
    .product-content,.add-on-content {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
    }
    /* Add on */
    .add-on-list {
    padding: 0px;
    }
    .add-on-list li {
    list-style: none;
    line-height: 32px;
    }
    .add-on-circle {
    color: #009a71;
    font-size: 12px;
    margin-right: 12px;
    }
    .add-on-btn-img .add-images {
    cursor: pointer;
    margin-top: 30px;
    }
    .add-on-btn-img .add-images:hover {
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    }
    .add-on-btn-img .add-images:hover.border-image {
    border: 2px solid #009b72;
    }
    .donate-and-support.weblogo-section .payment-address {
    height: unset !important;
    border: 2px solid #fff;
    display: flex;
    text-align: center;
    justify-content: center;
    }
    .payment-address {
    background: #fff;
    padding: 30px 20px;
    box-shadow: 0px 0px 20px 0px #e4e4e4;
    border-radius: 6px;
    display: flex;
    text-align: center;
    justify-content: center;
    border: 2px solid #bebebe;
    -webkit-transition: all 0.35s;
    -moz-transition: all 0.35s;
    -ms-transition: all 0.35s;
    -o-transition: all 0.35s;
    transition: all 0.35s;
    }
    .payment-address:hover {
    border: 2px solid #009b72;
    transform: scale(1.05);
    }
    .payment-address img {
    max-width: 90%;
    height: auto;
    }
    button {
    -webkit-transition: all 0.35s;
    -moz-transition: all 0.35s;
    -ms-transition: all 0.35s;
    -o-transition: all 0.35s;
    transition: all 0.35s;
    }
    /* Add in */
    .border-image {
    border: 2px solid #80808069;
    padding: 11px 25px;
    border-radius: 4px;
    }
    /* Sending Features */
    .sending-features {
    padding-bottom: 70px;
    }
    .app-images {
    display: flex;
    gap: 9px;
    }
    /* Competative Features */
    .competative-features {
    background-color: #009a71;
    padding-top: 100px;
    padding-bottom: 130px;
    }
    .competative-features-heading {
    margin-bottom: 50px;
    }
    .competative-features-heading h2,
    .competative-features-heading p,
    .get-started-content h2,
    .get-started-content p {
    color: white;
    }
    .competative-features-content {
    background-color: white;
    border-radius: 3px;
    padding: 20px 20px;
    height: 100%;
    /* margin-bottom: 27px; */
    -webkit-transition: all 0.35s;
    -moz-transition: all 0.35s;
    -ms-transition: all 0.35s;
    -o-transition: all 0.35s;
    transition: all 0.35s;
    }
    .competative-features-content p{
        min-height: 120px;
    }
    .img-shadow {
    box-shadow: 0px 2px 4px 0px #88888891;
    }
    .competative-features-content:hover {
    transform: scale(1.05);
    }
    /* Get Started */
    .get-started {
    margin-top: -75px;
    }
    .get-started-area {
    background-color: #edab0b;
    transform: skew(-10deg);
    max-width: 800px;
    margin: 0px auto;
    }
    .get-started-content {
    transform: skew(10deg);
    padding: 30px 0;
    }
    .get-started-content h2 {
    margin-bottom: 10px;
    }
    .get-started-buttons {
    display: flex;
    gap: 5px;
    justify-content: center;
    margin-top: 18px;
    }
    .try-now-btn {
    font-size: 16px;
    color: black;
    background-color: white;
    border: 2px solid white;
    border-radius: 30px;
    padding: 10px 30px;
    font-weight: 500;
    }
    .try-now-btn:hover,.try-now-btn:focus {
    color: white;
    background-color: transparent;
    cursor: pointer;
    text-decoration: unset;
    outline: unset;
    }
    /* Related Solution */
    .related-solution {
    padding-bottom: 30px;
    }
    .related-solution-links {
    display: flex;
    gap: 20px;
    margin-top: 33px;
    }
    .question-button {
    display: flex;
    justify-content: center;
    margin-top: 55px;
    }
    .any-question {
    font-size: 22px;
    color: white;
    background-color: #edab0b;
    border-radius: 30px;
    border: 2px solid #edab0b;
    padding: 12px 20px;
    font-weight: 500;
    text-decoration: none;
    }
    .any-question:hover,.any-question:focus {
    color: #edab0b;
    background-color: white;
    text-decoration: none;
    outline: unset;
    }

    @media screen and (max-width: 1199px) {
    body {
        font-size: 15px;
    }
    h1 {
        font-size: 34px;
    }
    h2 {
        font-size: 28px;
        line-height: 36px;
        margin-bottom: 17px;
    }
    h3 {
        font-size: 18px;
        margin-top: 17px;
    }
    /* Main */
    .section-padding {
        padding-top: 80px;
    }
    .main-content {
        max-width: 700px;
        padding: 75px 0px;
    }
    /* Product */
    .product-content p {
        margin-bottom: 22px;
    }
    .product-btn {
        font-size: 16px;
    }
    /* .product-image img {
        width: 80%;
    } */
    /* Add on */
    .add-on-btn-img .add-images {
        margin-top: 27px;
    }
    .add-on-circle {
        font-size: 10px;
        margin-right: 10px;
    }
    .add-on-image img {
        width: 80%;
    }
    /* Sending Features */
    .sending-features {
        padding-bottom: 60px;
    }
    /* Comptetive Features */
    h4 {
        font-size: 19px;
        margin-top: 21px;
    }
    .competative-features {
        padding-top: 40px;
        padding-bottom: 110px;
    }
    .competative-features-content {
        padding: 7px 22px 20px;
    }
    .competative-features-content p {
        font-size: 14px;
    }
    /* Get Started */
    .get-started-area {
        max-width: 680px;
    }
    /* Related Solution */
    .related-solution-links {
        margin-top: 27px;
    }
    .related-solution-links {
        justify-content: center;
    }
    .related-solution-links-image {
        width: 27%;
    }
    .question-button {
        margin-top: 48px;
    }
    .any-question {
        font-size: 20px;
    }
    .border-image {
        padding: 10px 20px;
    }
    .payment-address img {
        max-width: 100%;
        height: 25px;
    }
    }
    @media screen and (max-width: 991px){
    body{
        font-size: 14px;
    }
    h1 {
        font-size: 28px;
    }
    h2 {
        font-size: 22px;
        line-height: 30px;
        margin-bottom: 15px;
    }
    h3 {
        font-size: 14px;
        margin-top: 12px;
        line-height: 20px;
    }
    /* Main */
    .main-content {
        max-width: 540px;
        padding: 60px 0px;
    }
    /* Product */
    .product-content p {
        margin-bottom: 16px;
    }
    .product-btn {
        font-size: 14px;
    }
    /* Add on */
    .add-on-circle {
        font-size: 8px;
        margin-right: 8px;
        margin-top: 8px;
    }
    .add-on-list li {
        line-height: 25px;
        display: flex;
        align-items: flex-start;
    }
    .add-on-btn-img .add-images {
        margin-top: 23px;
        width: 140px;
    }
    /* Get Started */
    .get-started-area {
        max-width: 500px;
    }
    .try-now-btn {
        font-size: 14px;
        padding: 4px 22px;
    }
    .get-started-content {
        padding-top: 15px;
        padding-bottom: 25px;
    }
    .section-padding {
        padding-top: 45px;
    }
    .related-solution-links {
        margin-top: 24px;
        gap: 12px;
    }
    .question-button {
        margin-top: 40px;
    }
    .any-question {
        font-size: 17px;
        padding: 9px 19px;
    }
    .payment-address {
    background: #fff;
    padding: 20px 10px;
    margin-bottom: 20px;
    }
    }
    @media screen and (max-width: 767px){
    body{
        font-size: 13px;
    }
    h1 {
        font-size: 24px;
    }
    h2 {
        font-size: 20px;
        margin-bottom: 12px;
    }
    h3 {
        font-size: 13px;
        margin-top: 10px;
    }
    h4 {
        font-size: 15px;
        margin-top: 19px;
    }
    /* Main */
    .main-content {
        max-width: 470px;
        padding: 41px 0px;
    }
    /* Product */
    .custom-row{
        display: flex;
        flex-flow: column-reverse;
    }
    .product-content,.add-on-content {
        display: flex;
        flex-flow: column;
        align-items: center;
        max-width: 100%;
        margin: 0px auto;
        text-align: center;
        margin-top: 30px;
    }
    .product-image img,.add-on-image img{
        width: 55%;
        margin: 0px auto;
    }
    /* Competative Features */
    .competative-features {
        padding-top: 30px;
        padding-bottom: 90px;
    }
    .competative-features-content {
        width: 400px;
        margin: 0px auto;
        margin-bottom: 20px;
    }
    .img-shadow {
        width: 100%;
    }
    .competative-features-heading {
        margin-bottom: 30px;
    }
    /* Get Started */
    .get-started-area {
        max-width: 430px;
    }
    .get-started-buttons {
        margin-top: 14px;
    }
    .try-now-btn {
        font-size: 12px;
        padding: 4px 22px;
    }
    .section-padding {
        padding-top: 32px;
    }
    /* Related Solution */
    .any-question {
        font-size: 14px;
    }
    .question-button {
        margin-top: 32px;
    }
    .payment-address {
        padding: 20px 10px;
        margin-bottom: 20px;
        max-width: 300px;
        margin-left: auto;
        margin-right: auto;
    }

    }
    @media screen and (max-width: 575px){
    body {
        font-size: 11px;
    }
    h1 {
        font-size: 20px;
    }
    h2 {
        font-size: 18px;
        margin-bottom: 8px;
        line-height: 22px;
    }
    h3 {
        font-size: 12px;
        line-height: 18px;
    }
    /* Main */
    .main {
        /* background-image: url(../images/main.png); */
        /* background-repeat: no-repeat; */
        background-size: cover;
    }
    /* Product */
    .section-padding {
        padding-top: 25px;
    }
    .product-image img, .add-on-image img {
        width: 60%;
    }
    .product-content p {
        margin-bottom: 13px;
    }
    .product-btn {
        font-size: 12px;
    }
    /* Add on */
    .add-on-list li {
        line-height: 19px;
        text-align: left;
    }
    .add-on-circle {
        font-size: 6px;
        margin-top: 7px;
    }
    .add-on-btn-img .add-images {
        margin-top: 5px;
        width: 115px;
    }
    /* Competative Features */
    .competative-features {
        padding-top: 20px;
        padding-bottom: 90px;
    }
    .competative-features-content {
        width: 280px;
        margin-bottom: 18px;
    }
    .competative-features-heading {
        margin-bottom: 20px;
    }
    /* Get Started */
    .get-started-area {
        max-width: 260px;
    }
    .try-now-btn {
        font-size: 10px;
        padding: 4px 18px;
    }
    .get-started-content {
        padding-left: 10px;
        padding-right: 10px;
    }
    /* Related Solution */
    .related-solution-links {
        margin-top: 20px;
        gap: 7px;
    }
    .question-button {
        margin-top: 25px;
    }
    .any-question {
        font-size: 12px;
        padding: 7px 16px;
    }
    .product-btn {
        padding: 10px 15px;
    }
    }
</style>
@endsection
@section('content')
     <!-- Main -->
     <section class="main">
        <div class="container">
            <div class="main-content text-center heading-width">
                <h1 >{{__('Secure Transfer of Files of Any Type, Size & Complexity')}}</h1>
                <p class="banner-p">{{__('transfer immunity helps users quickly send large files through emails as embedded links with improved agility using mobile apps.')}}</p>
            </div>
        </div>
    </section>
    <!-- Product -->
    <section class="product section-padding">
        <div class="container">
            <div class="row custom-row custom-row-section">
                <div class="col-md-6 col-sm-6">
                     <div class="product-content add-on-content">
                        <h2 >{{__('The Product')}}</h2>
                        <p >{{__('transfer immunity is a fast and secure solution for sharing and transferring files. This high-fidelity, SaaS-based platform allows users to send large files to different destinations.')}}</p>
                        <p >{{__('transfer immunity comes with a set of mobile apps (iOS, Android) and plugins for email applications (Outlook/Thunderbird) to help corporate professionals and businesses transfer files from any source, mainly through email accounts, to any target destination.')}}</p>
                        {{-- <button class="product-btn">{{__('Start Uploading')}}</button> --}}
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="product-image"data-aos="zoom-in" data-aos-duration="1000" data-aos-easing="ease-out-cubic" class="aos-init aos-animate">
                        <img src="{{asset('frontside/dist/img/device/product.png')}}" class="img-responsive">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Add on -->
    <section class="add-on section-padding">
        <div class="container">
            <div class="row custom-row-section">
                <div class="col-md-6 col-sm-6">
                    <div class="add-on-image"data-aos="zoom-in" data-aos-duration="1000" data-aos-easing="ease-out-cubic" class="aos-init aos-animate">
                        <img src="{{asset('frontside/dist/img/device/add-on-img.png')}}" class="img-responsive">
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="add-on-content">
                        <h2 >{{__('Thunderbird Add-On')}}</h2>
                        <p >{{__('Install transfer immunity Add-On for your Thunderbird email app and start moving your files to multiple destinations without errors, considering file size, migration scale, and time constraints.')}}</p>
                        <ul class="list add-on-list">
                            <li class="list-item p"><i class="fa fa-circle add-on-circle"></i>{{__('Transfer your files in groups or all at once')}}</li>
                            <li class="list-item p"><i class="fa fa-circle add-on-circle"></i>{{__('Ensures time-saving and cost reduction')}}</li>
                            <li class="list-item p"><i class="fa fa-circle add-on-circle"></i>{{__('Quickly move files with data security')}}</li>
                          </ul>
                          <div class="add-on-btn-img">
                            <a href="https://addons.thunderbird.net/en-US/thunderbird/addon/transfer-immunity/?src=ss"><img src="{{asset('frontside/dist/img/device/add-on-btn-img.png')}}" class="img-responsive add-images border-image"></a>
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Add in -->
    <section class="add-in section-padding">
        <div class="container">
            <div class="row custom-row custom-row-section">
                <div class="col-md-6 col-sm-6">
                    <div class="add-on-content">
                        <h2 >{{__('Thunderbird Add-On')}}</h2>
                        <p >{{__('Install transfer immunity Add-On for your Thunderbird email app and start moving your files to multiple destinations without errors, considering file size, migration scale, and time constraints.')}}</p>
                        <ul class="list add-on-list">
                            <li class="list-item "><i class="fa fa-circle add-on-circle"></i>{{__('Ensures smooth file transfer and data fidelity')}}</li>
                            <li class="list-item "><i class="fa fa-circle add-on-circle"></i>{{__('Removes complexity, saves money and resources')}}</li>
                            <li class="list-item "><i class="fa fa-circle add-on-circle"></i>{{__('Password-protected links transmissions for uploaded files')}}</li>
                        </ul>
                          <div class="add-on-btn-img add-in-btn-img">
                            <a href="https://appsource.microsoft.com/en-us/product/office/WA200002373?tab=Overview"><img src="{{asset('frontside/dist/img/device/add-in-btn-img.png')}}" class="img-responsive add-images border-image"></a>
                          </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="add-on-image product-image"data-aos="zoom-in" data-aos-duration="1000" data-aos-easing="ease-out-cubic" class="aos-init aos-animate">
                        <img src="{{asset('frontside/dist/img/device/add-in-img.png')}}" class="img-responsive">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Sending Features -->
    <section class="sending-features section-padding">
        <div class="container">
            <div class="row custom-row-section">
                <div class="col-md-6 col-sm-6">
                    <div class="add-on-image"data-aos="zoom-in" data-aos-duration="1000" data-aos-easing="ease-out-cubic" class="aos-init aos-animate">
                        <img src="{{asset('frontside/dist/img/device/sending-features-img.png')}}" class="img-responsive">
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="add-on-content">
                        <h2 >{{__('High-End Mobile Apps with Email Sending Feature')}}</h2>
                        <p >{{__('Simplify and accelerate the migration of large files through emails with transfer immunity multilingual mobile apps.')}}</p>
                        <ul class="list add-on-list">
                            <li class="list-item "><i class="fa fa-circle add-on-circle"></i>{{__('Send large files (up to 20GB) via emails as embedded links')}}</li>
                            <li class="list-item "><i class="fa fa-circle add-on-circle"></i>{{__('Handy account and subscriptions management')}}</li>
                            <li class="list-item "><i class="fa fa-circle add-on-circle"></i>{{__('Track your emails history with a single tap')}}</li>
                        </ul>
                        <div class="app-images">
                            <div class="add-on-btn-img">
                                <a href="https://play.google.com/store/apps/details?id=com.timmunity.transferimmunity"><img src="{{asset('frontside/dist/img/device/app-store-img.png')}}" class="img-responsive add-images images-width border-image"></a>
                            </div>
                            <div class="add-on-btn-img">
                                <a href="https://apps.apple.com/us/app/transfer-immunity/id1585166326"><img src="{{asset('frontside/dist/img/device/google-play-img.png')}}" class="img-responsive add-images images-width border-image"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Competative Features -->
    <section class="competative-features">
        <div class="container">
            <div class="competative-features-heading text-center">
                <h2 >{{__('Competitive Features')}}</h2>
                <p class="chrac-p">{{__('Share Large Files and Use Short Links to Access Transferred Files from Anywhere')}}</p>
            </div>
            <div class="row custom-row-section">
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/cf-1.png')}}" class="img-responsive img-shadow">
                        </div>
                        <h4 >{{__('Zero Local Deployment')}}</h4>
                        <p>{{__('No need to install any additional application, but this easy-to-integrate plugin to your email apps - outlook/thunderbird will help you transfer big-size files.')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/cf-2.png')}}" class="img-responsive img-shadow">
                        </div>
                        <h4 >{{__('Password Protected Transfer')}}</h4>
                        <p>{{__('Send files with password protection to prevent security breaches. You can set up a password for its secure transfer to the final destination as you forward any big file.')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/cf-3.png')}}" class="img-responsive img-shadow">
                        </div>
                        <h4>{{__('Set Custom Expiry Date')}}</h4>
                        <p>{{__('Select an expiry time for automatically created short links to expire and also for transferred files to be deleted. It depends on the subscription package you select.')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/cf-4.png')}}" class="img-responsive img-shadow">
                        </div>
                        <h4 >{{__('Auto-Generated Short Links')}}</h4>
                        <p >{{__('Short links will be auto-generated for your transferred files to make their access far more effortless. Share a short URL as many times as you want without disrupting the links.')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/cf-5.png')}}" class="img-responsive img-shadow">
                        </div>
                        <h4 >{{__('Dedicated Plugins')}}</h4>
                        <p >{{__('Make your files transfer process quick & easy with transfer immunity dedicated plugins. Add these plugins to Thunderbird & Outlook email applications & move files seamlessly.')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/cf-6.png')}}" class="img-responsive img-shadow">
                        </div>
                        <h4>{{__('Download Statistics')}}</h4>
                        <p>{{__('Your data matters to us. Stop worrying about it. You can track and download the statistics from the transfer immunity dashboard and save them for later analysis.')}}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Get Started -->
    <section class="get-started">
        <div class="container">
            <div class="get-started-area">
                <div class="get-started-content text-center">
                    <h2 >{{__('Ready to Get Started?')}}</h2>
                    <p >{{__('Take Control of Files Transfer for More Manageable Workflows')}}</p>
                    <div class="get-started-buttons">
                        <a href="https://www.transferimmunity.com" target="_blank" class="try-now-btn">{{__('Try Now')}}</a>
                        <a href="{{ route('reseller.signup') }}" class="try-now-btn">{{__('Start Selling')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Related Solution -->
    <section class="related-solution section-padding">
        <div class="container">
            <div class="row justify-content-center align-items-center related-solution">
                <div class="col-12">
                    <div class="related-solution-heading text-center">
                        <h2 class="h2">{{__('Related to This Solution')}}</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-4 col-md-4 col-sm-6">
                    {{-- <a href="https://overmail.vm37.qdns1.com" target="_blank"> --}}
                    <a href="{{ route('frontside.comingsoon.index') }}" target="_blank">
                        <div class="payment-address "><img src="{{asset('frontside/dist/img/device/link-img-04.svg')}}" ></div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <a href="https://www.moveimmunity.com" target="_blank">
                        <div class="payment-address  ned-image"><img src="{{asset('frontside/dist/img/device/link-img-02.svg')}}"></div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <a href="https://www.ned.link" target="_blank">
                        <div class="payment-address"><img src="{{asset('frontside/dist/img/device/link-img-03.svg')}}"></div>
                    </a>
                </div>
            </div>
            <div class="question-button">
              <a href="{{route('frontside.contact.index')}}" class="any-question">{{__('Any Questions? Let???s Talk!')}}</a>
            </div>
        </div>
    </section>

@endsection
@section('script')
   <!-- Script Goes Here -->
@endsection
