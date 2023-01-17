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
  background-image: url("{{asset('frontside/dist/img/device/main-six.png')}}");
  background-repeat: no-repeat;
  background-size: 100% 100%;
}
.main-content {
  max-width: 860px;
  margin: 0px auto;
  padding: 170px 0px;
}
.main .main-content {
    color: #000;
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
.competative-features-content-image img{
    border-radius: 4px;
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
@media screen and (max-width: 1399px) {
  .main{
    background-size: 100%;
  }
  .main .main-content{
    padding: 60px 170px 60px 0px;
  }
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
  .main .main-content {
      max-width: 470px;
      padding: 20px 85px 20px 0px;
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
      background-size: 100%;
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
            <div class="main-content text-center">
                <h1 >{{__('Secure & Scalable Link Shortening')}}</h1>
                <p class="banner-p">{{__('Create unique and traceable branded short links for your digital marketing campaigns.')}}</p>
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
                        <p >{{__('NED.link helps get the most out of every short link you create and share.')}}</p>
                        <p >{{__('Use actionable insights from this simple and powerful tool to measure what’s working and what needs improvement for increased organic shares, click-throughs, and conversions.')}}</p>
                        <!-- <button class="product-btn">{{__('Start Uploading')}}</button> -->
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="product-image"data-aos="zoom-in" data-aos-duration="1000" data-aos-easing="ease-out-cubic" class="aos-init aos-animate">
                        <img src="{{asset('frontside/dist/img/device/product-page6.png')}}" class="img-responsive">
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
                        <img src="{{asset('frontside/dist/img/device/ned-01.png')}}" class="img-responsive">
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="add-on-content">
                        <h2 >{{__('Scalable Branding')}}</h2>
                        <p >{{__('NED.link is an industry-leading solution for digital marketers and customer support teams.')}}</p>
                        <p >{{__('This flexible and cyber-protected URL shortening solution allows you to create a seamless user experience while tracking and optimizing every touchpoint.')}}</p>
                        <!-- <ul class="list add-on-list">
                            <li class="list-item p"><i class="fa fa-circle add-on-circle"></i>Transfer your files in groups or all at once</li>
                            <li class="list-item p"><i class="fa fa-circle add-on-circle"></i>Ensures time-saving and cost reduction</li>
                            <li class="list-item p"><i class="fa fa-circle add-on-circle"></i>Quickly move files with data security </li>
                          </ul> -->
                          <!-- <div class="add-on-btn-img">
                            <a href=""><img src="{{asset('frontside/dist/img/device/add-on-btn-img.png')}}" class="img-responsive add-images border-image"></a>
                          </div> -->
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
                        <h2 >{{__('Campaigns Optimization')}}</h2>
                        <p >{{__('Manage and optimize your marketing campaigns that support your unique brand value.')}}</p>
                        <p >{{__('Integrate NED.link in your business processes and marketing operations, which will help you organize your workflows. Use branded short links to increase your campaign’s click-through rate necessary for business growth.')}}</p>
                        <!-- <ul class="list add-on-list">
                            <li class="list-item "><i class="fa fa-circle add-on-circle"></i>Ensures smooth file transfer and data fidelity</li>
                            <li class="list-item "><i class="fa fa-circle add-on-circle"></i>Removes complexity, saves money and resources</li>
                            <li class="list-item "><i class="fa fa-circle add-on-circle"></i>Password-protected links transmissions for uploaded files</li>
                        </ul> -->
                          <!-- <div class="add-on-btn-img add-in-btn-img">
                            <a href=""><img src="{{asset('frontside/dist/img/device/add-in-btn-img.png')}}" class="img-responsive add-images border-image"></a>
                          </div> -->
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="add-on-image product-image"data-aos="zoom-in" data-aos-duration="1000" data-aos-easing="ease-out-cubic" class="aos-init aos-animate">
                        <img src="{{asset('frontside/dist/img/device/ned-02.png')}}" class="img-responsive">
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
                        <img src="{{asset('frontside/dist/img/device/ned-03.png')}}" class="img-responsive">
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="add-on-content">
                        <h2 >{{__('Customer Relationship')}}</h2>
                        <p >{{__('Reinforce consumer interaction with your brand through custom short links and domains.')}}</p>
                        <p >{{__('Build and foster long-lasting customer relationships through a robust marketing and responsive service delivery, smart multi-channel campaigns, and link management.')}}</p>
                        <!-- <ul class="list add-on-list">
                            <li class="list-item "><i class="fa fa-circle add-on-circle"></i>Send large files (up to 20GB) via emails as embedded links</li>
                            <li class="list-item "><i class="fa fa-circle add-on-circle"></i>Handy account and subscriptions management</li>
                            <li class="list-item "><i class="fa fa-circle add-on-circle"></i>Track your emails history with a single tap</li>
                        </ul> -->
                        <!-- <div class="app-images">
                            <div class="add-on-btn-img">
                                <a href=""><img src="{{asset('frontside/dist/img/device/app-store-img.png')}}" class="img-responsive add-images images-width border-image"></a>
                            </div>
                            <div class="add-on-btn-img">
                                <a href=""><img src="{{asset('frontside/dist/img/device/google-play-img.png')}}" class="img-responsive add-images images-width border-image"></a>
                            </div>
                        </div>   -->
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
                <p class="chrac-p">{{__('Empower your brand with exclusive short links that multiply your business revenue.')}}</p>
            </div>
            <div class="row custom-row-section">
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/URL-Shortener.jpg')}}" class="img-responsive img-shadow">
                        </div>
                        <h4 >{{__('URL Shortener')}}</h4>
                        <p>{{__('Create short links and customize them to meet your brand needs. Track and optimize your advertising campaigns with branded links and custom domains.')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/Brand-Control.jpg')}}" class="img-responsive img-shadow">
                        </div>
                        <h4 >{{__('Brand Control')}}</h4>
                        <p>{{__('Improve brand awareness and engagement by customizing the short URLs. Branded links with custom domains will skyrocket your CTR.')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/Campaigns-Management.jpg')}}" class="img-responsive img-shadow">
                        </div>
                        <h4>{{__('Campaigns Management')}}</h4>
                        <p>{{__('Leverage unprecedented control on your digital marketing campaigns. Monitor, test, and optimize short links for improved performance of your business campaigns.')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/Link-Analytics.jpg')}}" class="img-responsive img-shadow">
                        </div>
                        <h4 >{{__('Link Analytics')}}</h4>
                        <p>{{__('Get reliable insights to develop more robust marketing strategies. A quick visualization of real-time data points helps improve your marketing efficiency.')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/Data-Protection.jpg')}}" class="img-responsive img-shadow">
                        </div>
                        <h4 >{{__('Data Protection')}}</h4>
                        <p>{{__('Safely integrate your branded short links into existing workflows, tools, and processes encrypted with HTTPS to protect against counterfeiting.')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/Cohesive-Collaboration.jpg')}}" class="img-responsive img-shadow">
                        </div>
                        <h4>{{__('Cohesive Collaboration')}}</h4>
                        <p>{{__('Increase your work efficiency by connecting NED.link with tools you use every day. Use custom links across the organization with a uniform strategy.')}}</p>
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
                    <p >{{__('Grow Your Business With Every Custom Short Link')}}</p>
                    <div class="get-started-buttons">
                        <a href="https://www.ned.link" target="_blank" class="try-now-btn">{{__('Try Now')}}</a>
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
                    <a href="https://www.transferimmunity.com" target="_blank">
                        <div class="payment-address "><img src="{{asset('frontside/dist/img/device/link-img-01.svg')}}" ></div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <a href="https://www.moveimmunity.com" target="_blank">
                        <div class="payment-address  ned-image"><img src="{{asset('frontside/dist/img/device/link-img-02.svg')}}"></div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    {{-- <a href="https://overmail.vm37.qdns1.com" target="_blank"> --}}
                    <a href="{{ route('frontside.comingsoon.index') }}" target="_blank">
                        <div class="payment-address"><img src="{{asset('frontside/dist/img/device/link-img-04.svg')}}"></div>
                    </a>
                </div>
            </div>
            <div class="question-button">
              <a href="{{route('frontside.contact.index')}}" class="any-question">{{__('Any Questions? Let’s Talk!')}}</a>
            </div>
        </div>
    </section>

@endsection
@section('script')
   <!-- Script Goes Here -->
@endsection
