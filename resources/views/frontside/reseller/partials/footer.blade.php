<style>

    .image{

        height:100px;

    }
</style>
<footer style="margin: auto; text-align: center; float: none;">
    <div class="green-line container"></div>
    <div class="row bg-gray content-footer">
        <div class="footer-logo">
            @if($reseller->logo)
                <img class="image" src="{{ asset('storage/uploads/redeem-page/' . $reseller->logo) }}" />
            @else
                <img src="{{ asset('frontside/dist/img/site_logo.png') }}" />
            @endif
        </div>
        <div class="footer-sub-heading"><span></span></div>
        @if(@$reseller->terms_of_use != '' || @$reseller->privacy_policy != '' || @$reseller->imprint != ''  )
        <div class="footer-links">
            <ul>
                @if(@$reseller->terms_of_use != '')
                <li>
                    {{-- <a target="_blank" href="{{ @$reseller->reseller_redeem_page->domain.'/'.str_replace(" ", "-", strtolower(@$reseller->title)).'/'.Hashids::encode(@$reseller->reseller_id).'/terms-of-use' }}">{{ __('Terms of Use')}}</a> --}}
                    <a target="_blank" href="{{ @$reseller->domain.'/terms-of-use' }}">{{ __('Terms of Use')}}</a>
                </li>
                @endif
                @if( @$reseller->privacy_policy != '' )
                <li>
                    {{-- <a target="_blank" href="{{ @$reseller->reseller_redeem_page->domain.'/'.str_replace(" ", "-", strtolower(@$reseller->title)).'/'.Hashids::encode(@$reseller->reseller_id).'/privacy-policy' }}">{{ __('Privacy Policy')}}</a> --}}
                    <a target="_blank" href="{{ @$reseller->domain.'/privacy-policy' }}">{{ __('Privacy Policy')}}</a>
                </li>
                @endif
                @if( @$reseller->imprint != '' )
                <li>
                    {{-- <a target="_blank" href="{{ @$reseller->reseller_redeem_page->domain.'/'.str_replace(" ", "-", strtolower(@$reseller->title)).'/'.Hashids::encode(@$reseller->reseller_id).'/imprint' }}">{{ __('Imprint')}}</a> --}}
                    <a target="_blank" href="{{ @$reseller->domain.'/imprint' }}">{{ __('Imprint')}}</a>
                </li>
                @endif
            </ul>
        </div>
        @endif
        <div class="contact-info">
            <ul>
                @if($reseller->email != '')
                <li>
                    {{__('Email')}}: <a href="mailto:{{$reseller->email}}">{{$reseller->email}}</a>
                </li>
                @else
                <li>
                {{__('Email')}}: <a href="mailto:{{$reseller->user->email}}">{{$reseller->user->email}}</a>
                </li>
                @endif

                @if($reseller->phone != '')
                <li>
                    {{ __('Call') }}: <a href="tel:{{$reseller->phone}}">{{$reseller->phone}}</a>
                </li>
                @endif
            </ul>
        </div>
        <div class="social-icons footer-icons">
            @isset($site_settings[0])
                @if ($site_settings[0]->facebook != null && $site_settings[0]->facebook != '')
                    <a class="facebook icon-cirle" href="{{ $site_settings[0]->facebook  }}"><img src="{{asset('frontside/dist/img/facebook-icon.png')}}"></a>
                @endif
                @if ($site_settings[0]->twitter != null && $site_settings[0]->twitter != '')
                    <a class="twiter icon-cirle" href="{{ $site_settings[0]->twitter }}"><img src="{{asset('frontside/dist/img/twitter.png')}}"></a>
                @endif
                @if ($site_settings[0]->linkedin != null && $site_settings[0]->linkedin != '')
                    <a class="linkdin icon-cirle" href="{{ $site_settings[0]->linkedin }}"><img src="{{asset('frontside/dist/img/linkdin.png')}}"></a>
                @endif
                @if ($site_settings[0]->pinterest != null && $site_settings[0]->pinterest != '')
                    <a class="pinterest icon-cirle" href="{{ $site_settings[0]->pinterest }}"><img src="{{asset('frontside/dist/img/pinterest.png')}}"></a>
                @endif
            @endisset
        </div>
    </div>
    </div>

    <div class="copy-right row footer-text-align">
        <div class="col-md-6 col-sm-12 text-center  ">
            <p class="footer-logo-text">Copyright © TIMmunity GmbH. All Rights Reserved.</p>
        </div>
        {{-- <div class="col-md-6 col-sm-12 text-right">
            <span class="footer-logo-text">Powered by ArhamSoft (Pvt) Ltd.</span>
            <!-- <a href="https://www.arhamsoft.com/" target="_blank">
                <img src="{{asset('frontside/dist/img/ar-logo.svg')}}" class="img-fluid" alt="" />
            </a> -->
        </div> --}}
    </div>
</footer>
