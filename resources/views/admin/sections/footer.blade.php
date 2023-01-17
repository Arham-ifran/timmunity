@php
$last_segment = request()->segment(count(request()->segments()));
@endphp
<!-- /.footer -->
<footer class="main-footer text-center" @if($last_segment == 'settings' || $last_segment == 'sales') style="margin-left: 230px; width:83%" @endif>
    {{-- <div class="text-center">
      <span>{{ __('Copyright © TIMmunity GmbH -') }}</span>
    </div> --}}
    <div class="copy-right row">
        <div class="col-md-12 text-center pt-1">
            <span class="footer-logo-text">Copyright © TIMmunity GmbH. All Rights Reserved.</span>
        </div>
        {{-- <div class="col-md-6 text-right pr-0">
            <span class="footer-logo-text">Powered by ArhamSoft (Pvt) Ltd.</span>
            <a href="https://www.arhamsoft.com/" target="_blank">
                <img src="{{asset('frontside/dist/img/ar-logo.svg')}}" class="img-fluid" alt="" />
            </a>
        </div> --}}
    </div>
</footer>
