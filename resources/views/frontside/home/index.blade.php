@extends('frontside.layouts.app')
@section('title')   Home @endsection
@section('style')
<link rel="stylesheet" href="{{asset('frontside/dist/css/faqs.css')}}">
<style>
    .home-row-listing{
        display: flex;
        flex-wrap: wrap;
    }
</style>
@endsection
@section('content')
    <section class="banner-01 bg-dark-green">
        <div class="banner-bg-img">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="banner-content-section" data-aos="fade-right" data-aos-duration="2000">
                            <h2>{{ __('All-in-One, True Cyber Protection') }}</h2>
                            <span>{{ __('TIMmunity offers a range of products to protect your data, applications, and systems from modern cyber threats while eliminating the complexity and cost of managing multiple tools.') }}</span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="banner-img1">
                           <img alt="" src="{{asset('frontside/dist/img/home_banner.svg')}}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Logos Section -->
    <section class="content-section">
        <div class="container">
                    <div class="row bottom-space">
                        <div class="folg-text" data-aos="fade-up" data-aos-duration="2000">
                            <h3>TIMmunity Security Product Framework</h3>
                        </div>
                    </div>

            <div class="container clearfix">
                <!-- Icons Row One -->
                <div class="row home-row-listing">
                    <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/icon-03.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('Email Immunity')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('An endpoint email security tool that helps control mail flow, clean it and protect against malware and unwanted email spam.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.emailimmunity') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/icon-02.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('device immunity')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('A trusted cyber security solution with virus and malware protection that provides a backup for complete data protection.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.deviceimmunity') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/icon-04.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('office immunity')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('A comprehensive anti-malware solution that protects everything by delivering multilayered advanced threat protection.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.officeimmunity') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/icon-02b.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('backup immunity')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('An all-inclusive, unified solution that integrates data backup and cyber security, protecting your your entire digital life.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.backupimmunity') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/icon-01.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('move immunity')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('An integrated solution that helps relocate email accounts and/or cloud storage with a single click in a protected environment.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.moveimmunity') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/icon-10.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('transfer immunity')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('An innovative, fast, and secure SaaS platform for sharing and transferring large files via emails as embedded links.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.transferimmunity') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/icon-08.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('Product Immunity')}}</h4>
                            </div>
                            <div class="col-text">
                               <p>{{__('A centralized system built for vendors to buy and sell vouchers for different TIMmunity products and earn handsome profits.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.productimmunity') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/icon-11.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('Vpn Immunity')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('Leverage end-point protection in terms of securing your data and server. Make your online information fully encrypted.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.vpnimmunity') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div> -->
                    <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/QRCode.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('QR Code')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('An all-inclusive QR code generator. Create, customize, manage and track all your QR codes in one place with unified cyber security.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.qr') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/icon-12.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('NED.link')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('Best-in-class, scalable, and secure URL shortening and campaigns management platform to make your digital life easy.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.nedlink') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/AskQ.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('aikQ')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('Send, receive, monitor, and manage your emails through encrypted connections using this reliable and cyber-secure mail server.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.aikQ') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue title-img">
                                <img src="{{ asset('frontside/dist/img/inbox.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('Inbox')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('Store, access, share and manage your emails from any device using this mail server with uninterrupted speed and comprehensive cyber protection.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.inbox') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/Maili.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('maili.de')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('A high-fidelity mail server to help users access their emails instantly and securely from all their mail clients or mobile devices.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.maili') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue title-img">
                                <img src="{{ asset('frontside/dist/img/overmail.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('overmail')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('Access, move, and manage your emails from any mail client or mobile device through encrypted connections ensuring cyber security.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.overmail') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/QRCode.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('QR Code')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('An all-inclusive QR code generator. Create, customize, manage and track all your QR codes in one place with unified cyber security.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.qr') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="custom-pb">
                            <div class="col-img-box dark-blue">
                                <img src="{{ asset('frontside/dist/img/Maili.svg') }}">
                            </div>
                            <div class="col-heading">
                                <h4>{{__('Maili.de')}}</h4>
                            </div>
                            <div class="col-text">
                                <p>{{__('A high-fidelity mail server to help users access their emails instantly and securely from all their mail clients or mobile devices.')}}</p>
                            </div>
                            <div class="col-button">
                                <a class="primary-button" href="{{ route('frontside.page.maili') }}">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div> -->
                    <!-- @isset($homepage_pages)
                    @foreach($homepage_pages as $index => $page)
                    @php
                        $img_index = $index + 1;
                    @endphp
                        <div class="col-md-4 col-sm-6 col-box" style="text-align: center;" data-aos="zoom-in" data-aos-duration="2000">
                            <div class="custom-pb">
                                <div class="col-img-box dark-blue">
                                    <img src="@if( file_exists( asset('storage/uploads/cms/'.$page->image) ) ) {{ asset('storage/uploads/cms/'.$page->image) }} @else {{ asset('frontside/dist/img/icon-'.$img_index.'.png') }} @endif">
                                </div>
                                <div class="col-heading">
                                    <h4>{{ translation( $page->id,22,app()->getLocale(),'title',$page->title) }}</h4>
                                </div>
                                <div class="col-text">
                                    <p>{!! translation( $page->id,22,app()->getLocale(),'short_description',$page->short_description) !!}</p>
                                </div>
                                <div class="col-button">
                                    <a class="primary-button" href="{{ route('frontside.page.details', $page->seo_url) }}">{{ __('View Details') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @endisset -->
                </div>
            </div>
        </div>
    </section>
    <section class="faq-section">
    <div class="container">
        <div class="faq-title text-center pb-3">
            <h2>{{ __('Frequently Asked Questions')}}</h2>
        </div>
        <div class="accordion">
            @foreach($faqs as $index => $faq)
                @if($index <= 4)
                <div class="accordion-item">
                    <button id="accordion-button-1" aria-expanded="false"><span class="accordion-title">{{ translation(@$faq->id,27,app()->getLocale(),'question',@$faq->question) }}</span><span class="icon" aria-hidden="true"></span></button>
                    <div class="accordion-content">
                    <p>{{ translation(@$faq->id,27,app()->getLocale(),'answer',@$faq->answer) }}</p>
                    </div>
                </div>
                @elseif($index > 4)
                <div class="accordion-item loadmore">
                    <button id="accordion-button-1" aria-expanded="false"><span class="accordion-title">{{ translation(@$faq->id,27,app()->getLocale(),'question',@$faq->question) }}</span><span class="icon" aria-hidden="true"></span></button>
                    <div class="accordion-content">
                    <p>{{ translation(@$faq->id,27,app()->getLocale(),'answer',@$faq->answer) }}</p>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
      @if($faqs->count() > 4)
      <div class="btn-container pt-3">
        <button class="btn btn-primary" id="toggle">{{ __('Load More') }}&nbsp;<i class="fa fa-angle-double-down"></i></button>
      </div>
      @endif
   </div>
</section>
@endsection
@section('script')
<script type="text/javascript">
    const items = document.querySelectorAll(".accordion button");

    function toggleAccordion() {
      const itemToggle = this.getAttribute('aria-expanded');

      for (i = 0; i < items.length; i++) {
        items[i].setAttribute('aria-expanded', 'false');
      }

      if (itemToggle == 'false') {
        this.setAttribute('aria-expanded', 'true');
      }
    }
    items.forEach(item => item.addEventListener('click', toggleAccordion));

    $("#toggle").click(function() {
    var elem = $("#toggle").html();
    console.log(elem);
    if (elem == '{{ __('Load More') }}&nbsp;<i class="fa fa-angle-double-down"></i>') {
      //Stuff to do when btn is in the read more state
      $("#toggle").html("{{ __('Load Less') }}&nbsp;<i class='fa fa-angle-double-up'></i>");
      $(".loadmore").slideDown();
    } else {
      //Stuff to do when btn is in the read less state
      $("#toggle").html("{{ __('Load More') }}&nbsp;<i class='fa fa-angle-double-down'></i>");
      $(".loadmore").slideUp();
    }
  });
</script>
@endsection
