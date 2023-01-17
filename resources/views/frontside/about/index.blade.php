@extends('frontside.layouts.app')
@section('title') About @endsection
@section('style')
<style>
	h3{
		font-size: 55px;
	}
	/* b,strong{
		font-size: 18px;
	} */
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
</style>
@endsection
@section('content')	<!-- Banner Section -->
	<section class="banner-01 about-main bg-dark-green">
		<div class="banner-bg-img" style="background: none;">
			<div class="container">
				<div class="row">
					<div class="col-lg-6">
						<div class="banner-content-section" data-aos="fade-right" data-aos-duration="2000">
							<h2>{{ __('About Us') }}</h2>
							<span>{{ __('We Cyber-Guard Your Data, Devices & E-Communications') }}</span>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="banner-img1">
						<img src="{{asset('frontside/dist/img/about-img.png')}}">
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Content Section -->


	<section class="container">
		<div class="row ">
				<div class="row">
					<div class="col-md-12 text-center mt-4 about-us-content" data-aos="fade-down" data-aos-duration="2000">
						<h3>{{ __('Powerful & Real-Time Cyber Protection')}}</h3>
						<p>{{ __('TIMmunity is an eCommerce platform that provides endpoint security solutions to individuals and organizations for data protection from cyber threats. Whether you transfer an email account, perform cloud storage migration, or move large files as embedded links in emails, everything uploaded to our system and shared through it is always checked for viruses and malware.') }}</p>
						<p>{{__('In addition, TIMmunity GmbH is the official distributor of Avast and Acronis licenses and more.')}}</p>
						<br>
						<!-- <a class="btn bg-green" href="#">{{ __('Read more') }}</a> -->

					</div>
				</div>
	</section>
	<section class="about-bg">
		<div class="container">
					<div class="row mt-4 about-row">
						<div class="col-lg-6">
							<div class="banner-content-section mt-4 about-us-content" data-aos="fade-right" data-aos-duration="2000" style="color: #333;">
								<h3>{{ __('The Chronicles of TIMmunity GmbH') }}</h3>
								<p>{{ __('Founded in December 2019 by TIM Sebastian Steffens, at the age of 22, TIMmunity GmbH aims to offer high-quality solutions to protect your data, devices, applications, and everything in between from modern cyber-attacks. We provide all-inclusive, end-point online security solutions at competitive prices to deliver our customers the best possible cyber protection.') }} </p>
								<p><b>{{ __('From where does the idea comes in handy?') }}</b> </p>
								<p>{{ __('It all began in early 2015 when TIM Sebastian Steffens was working on the email server and cloud cyber protection projects – Inbox.de & AikQ. He decided to deliver these experiences based on the unique selling proposition “Immunity” and built the following combination to shake things up.') }} </p>
								<p><strong>TIM </strong>{{ __('= founder name') }}</p>
								<p><strong>IMMUNITY  </strong>{{ __('= the protection of customers') }}</p>
								<p><strong>MUNITY  </strong>{{ __('= the community of all customers') }}</p>
								<p>{{ __('This is how TIMmunity GmbH came into existence, from personal academic experiences to commercial cyber security solutions and services. TIMmunity solutions framework is developed to deliver multi-layered cyber security and unified data migration and protection from digital threats.') }}</p>
							</div>
						</div>
						<div class="col-lg-6" data-aos="fade-left" data-aos-duration="2000">
							<div class="banner-img1">
							<img alt="" src="{{asset('frontside/dist/img/Chronicles.png')}}">
							</div>
						</div>
					</div>
		</div>
	</section>
	<section class="container">
					<div class="row about-row">
						<div class="col-lg-6" data-aos="fade-left" data-aos-duration="2000">
							<div class="banner-img1">
							<img alt="" src="{{asset('frontside/dist/img/innovative-img.png')}}">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="banner-content-section mt-4 about-us-content" data-aos="fade-right" data-aos-duration="2000" style="color: #333;">
								<h3>{{ __('Innovative Solutions Developed to Meet Future Challenges') }}</h3>
								<p>{{ __('TIMmunity GmbH products are designed and developed for individuals, businesses, and service providers to manage, move and cyber-protect their personal and official assets across all platforms.') }} </p>
								<p>{{ __('TIMmunity tools and software solutions dive into the best high-fidelity, scalable and secure set of integrated technologies.') }} </p>
								<p>{{ __('Our intuitive products belong to different categories, including data protection, device immunity, short links and campaigns management, file transfer, voucher selling, and much more.') }} </p>
							</div>
						</div>
					</div>
					<div class="row mt-4 about-row">
						<div class="col-lg-6">
							<div class="banner-content-section mt-4 about-us-content" data-aos="fade-right" data-aos-duration="2000" style="color: #333;">
								<h3>{{ __('Act Smart & Stay Secure with TIMmunity!') }}</h3>
								<p>{{ __('TIMmunity GmbH helps people manage and protect their personal and organizational data, devices, and business operations with our state-of-the-art products delivering compressive security from viruses and malware.') }} </p>
								<p>{{ __('We empower people to use our product vouchers as a force for profitable business success. Our voucher selling system allows vendors to buy and sell vouchers to encourage people to use TIMmunity products.') }} </p>
							</div>
						</div>
						<div class="col-lg-6" data-aos="fade-left" data-aos-duration="2000">
							<div class="banner-img1">
							<img alt="" src="{{asset('frontside/dist/img/act-smart-img.png')}}">
							</div>
						</div>
					</div>
					<div class="question-button">
						{{-- <button class="any-question">Any Questions? Let’s Talk!</button> --}}
						<a href="{{route('frontside.contact.index')}}" class="any-question">{{__('Any Questions? Let’s Talk!')}}</a>
					</div>
		</div>
	</section>

 	<!-- <div class="mt-4 get-quote-section" style="background: #009a71; color: #fff;">
 		<div class="bg-image text-center">
 			<div class="row">
 			<div class="bottom-content">
 				<i class="fa fa-phone-square" aria-hidden="true"></i>
 				<h2>{{ __('Get a Quote if you have any query!') }}</h2>
 				<div class="btn quote-button"><i class="fa fa-phone" aria-hidden="true"></i><a href="{{route('frontside.contact.index')}}">{{ __("Get a Quote!") }}</a></div>
 			</div>
 			</div>
 		</div>
 	</div> -->
@endsection
@section('script')
@endsection
