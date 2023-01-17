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
  line-height: 40px;
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
.custom-row-section{
  display: flex;
  flex-wrap: wrap;
}
.add-on-content p, .add-on-content li{
  font-size: 20px;
}
.h2{
  font-size: 54px;
}

/* Header */

/* Main */
.main {
  background-image: url("{{asset('frontside/dist/img/device/main-ten.png')}}");
  background-repeat: no-repeat;
  background-size: 100% 100%;
}
.main-content {
  /* max-width: 860px; */
  margin: 0px auto;
  padding: 170px 0px;
}
.main .main-content {
    color: #fff;
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
.competative-features-content-image img{
    border-radius: 4px;
}
.competative-features-content p{
    min-height: 120px;
}
.p-content-height p{
  min-height: 60px;
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
            <div class="main-content text-center">
                <h1 >{{__('Powerful Tool for Mail Server Security')}}</h1>
                <p class="banner-p">{{__('A comprehensive defense system against email-borne cyber attacks')}}</p>
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
                        <p >{{__('Email Immunity empowers you to give your employees and clients a cyber-protected mail server. It quickly detects and prevents spammers from flooding your inbox with unwanted messages. In addition, it provides multi-layered targeted protection from malicious intrusions through emails into your systems and data.')}}</p>
                        <!-- <button class="product-btn">{{__('Start Uploading')}}</button> -->
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="product-image"data-aos="zoom-in" data-aos-duration="1000" data-aos-easing="ease-out-cubic" class="aos-init aos-animate">
                        <img src="{{asset('frontside/dist/img/device/email-01.png')}}" class="img-responsive">
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
                        <img src="{{asset('frontside/dist/img/device/email-02.png')}}" class="img-responsive">
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="add-on-content">
                        <h2 >{{__('All-Inclusive Security')}}</h2>
                        <p >{{__('Email Immunity provides cyber security to your inboxes across the board. The system logs everything (email subject, date, time, attachments, etc.) that helps find the lost email quickly. Besides, it gives you comprehensive control over your daily email traffic. You can also access your managed emails from a central mailbox and create queries and analysis parameters to ensure uninterruptable email supply.')}}</p>
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
                        <h2 >{{__('Reduced Spam Risks')}}</h2>
                        <p >{{__("Email Immunity provides a permanent solution for sustainable spam defense. It automatically scans and classifies all the incoming spam mail and reduces its amount through an individually configurable point system. It doesn’t block the spam emails reaching your inbox or filter out unsolicited messages but notifies the spam sender with a '550 user unknown' message. The sender will consider it as a non-existent mailbox and remove it from their database.")}}</p>
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
                        <img src="{{asset('frontside/dist/img/device/email-03.png')}}" class="img-responsive">
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
                        <img src="{{asset('frontside/dist/img/device/email-04.png')}}" class="img-responsive">
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="add-on-content">
                        <h2 >{{__('Prevent Data Loss')}}</h2>
                        <p >{{__('Email Immunity protects you from spamming and cyber threats and takes a quick backup of your email data using the Replay module. It stores all incoming mails in a ring buffer that replaces the oldest backup with the newly backed up inbound messages. Depending on your mail volume, the buffer size is customizable to meet your need for message recovery and backups for several weeks to months.')}}</p>
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
                <h2 >{{__('Characteristic Features')}}</h2>
                <p class="chrac-p">{{__('Stop cyber threats and spamming to keep your business, data, and users safe')}}</p>
            </div>
            <div class="row custom-row-section">
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content p-content-height text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/D-_Fully-integrated-solutions.jpg')}}" class="img-responsive img-shadow">
                        </div>
                        <h4 >{{__('Fully Integrated Solution')}}</h4>
                        <p >{{__('A secure email gateway to minimize business disruptions')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content p-content-height text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/Scaleable-Arcitecture.jpg')}}" class="img-responsive img-shadow">
                        </div>
                        <h4 >{{__('Scalable Architecture')}}</h4>
                        <p >{{__('Scale up to manage sudden changes and meet increased workloads')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content p-content-height text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/Quieck-Massage-Recovery.jpg')}}" class="img-responsive img-shadow">
                        </div>
                        <h4>{{__('Quick Message Recovery')}}</h4>
                        <p >{{__('Automated backup and systematic recovery of email data')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content p-content-height text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/Premium-Spam-Control.jpg')}}" class="img-responsive img-shadow">
                        </div>
                        <h4 >{{__('Premium Spam Control')}}</h4>
                        <p >{{__('Protect users from a high influx of unsolicited messages in their inbox')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content p-content-height text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/Data-Retention.jpg')}}" class="img-responsive img-shadow">
                        </div>
                        <h4 >{{__('Data Retention')}}</h4>
                        <p >{{__('Protect the data stored in your mailbox to ensure nothing is lost')}}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="competative-features-content p-content-height text-center">
                        <div class="competative-features-content-image">
                            <img src="{{asset('frontside/dist/img/device/Everything-logged.jpg')}}" class="img-responsive img-shadow">
                        </div>
                        <h4>{{__('Everything Logged')}}</h4>
                        <p >{{__('Control your daily mail traffic and quickly find your lost emails')}}</p>
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
                    <p >{{__('Improve Your Email Security At Scale')}}</p>
                    <div class="get-started-buttons">
                        <a href="{{route('frontside.comingsoon.index')}}" class="try-now-btn">{{__('Try Now')}}</a>
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
              <a href="{{route('frontside.contact.index')}}" class="any-question">{{__('Any Questions? Let’s Talk!')}}</a>
            </div>
        </div>
    </section>

@endsection
@section('script')
   <!-- Script Goes Here -->
@endsection
