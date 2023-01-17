<footer id="footer" style="margin: auto; text-align: center; float: none;">
    <div class="green-line container"></div>
    <div class="row bg-gray content-footer">
        <div class="footer-logo"><img src="{{asset('frontside/dist/img/logo.png')}}"></div>
        <div class="footer-sub-heading"><span>Erftstr. 15 Braunschweig 38120 Germany</span></div>
        <div class="footer-links">
            <ul>
                <li>
                    <a href="{{ route('frontside.page.details', 'general-terms-and-conditions') }}">{{ __('General Terms and Conditions')}}</a>
                </li>
                <li>
                    <a href="{{ route('frontside.page.details', 'privacy-policy') }}">{{ __('Privacy Policy')}}</a>
                </li>
                <li>
                    <a href="{{ route('frontside.page.details', 'imprint') }}">{{ __('Imprint')}}</a>
                </li>
                <li>
                    <a href="{{ route('frontside.page.details', 'cancellation-policy') }}">{{ __('Cancellation Policy')}}</a>
                </li>
            </ul>
        </div>
        <div class="contact-info">
            <ul>
                <li>
                    {{__('Email')}}: <a href="mailto:info@timmunity.com">info@timmunity.com</a>
                </li>
                <li>
                    {{__('Call')}}: <a href="tel:4915792301998">+49 1579 2301998</a>
                </li>
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
    </div>
</footer>
