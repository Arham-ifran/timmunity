@php
    $subdomain = str_replace('https://','',$reseller->user->reseller_redeem_page->url);
    $subdomain = str_replace(env('reseller_domain'),'',$subdomain);
    $subdomain = explode('.',$subdomain);
    // dd($subdomain);
    $subdomain = str_replace(' ','',$subdomain[0]);
    $subdomain = $subdomain == '' ? 'www' : $subdomain;
    // dd($subdomain);
@endphp
<form id="redeem_form" action="{{ route('voucher.redeem.post',["subdomain" => $subdomain]) }}" method="POST" enctype="multipart/form-data">
{{-- <form id="redeem_form" action="{{ $reseller->user->reseller_redeem_page->domain.'/redeem-voucher' }}" method="POST" enctype="multipart/form-data"> --}}
    @csrf
    <div class="form-group col-md-12 ">
        <input type="hidden" name="reseller_id" value="{{ Hashids::encode($reseller->reseller_id) }}">
        <div id="voucher-div">
            <label for="voucher_code">{{ __('Have a Voucher Code? Click below button to Apply Voucher Code.') }}</label>
            <input type="text" class="form-control" name="voucher_code" placeholder="Voucher Code">
        </div>
        <div class="checkbox">
            <p ><strong> {{__('Information on the early expiry of the right of withdrawal')}}</strong></p>
            <label>
                <input type="checkbox" value="one" name="accept">
                {{__('You agree and expressly wish that the execution of the ordered service is started before the expiry of the revocation period. You are aware that you lose your right of withdrawal upon complete fulfilment of the contract. Once the voucher code has been redeemed and the licence key has been sent to you, the revocation is no longer possible')}}
            </label>
            <label for="accept"></label>
        </div>
    </div>
        <div class="col-md-12">
            <hr>
        </div>
        <div class="col-md-12">
            <p class="text-center"><strong>{{__('Kindly fill the following information to redeem the voucher')}}</strong></p>
            <div class="form-group row has-feedback">
                <div class="col-md-6">
                    <label for="name" class="control-label">{{__('Full Name')}}<small class="asterik" style="color:red">*</small></label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="David Beckham">
                    @error('name')
                        <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="control-label">{{__('Email')}}<small class="asterik" style="color:red">*</small></label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="email@company.com">
                    @error('email')
                        <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-group row has-feedback">
                <div class="col-md-12">
                    <label for="country_id" class="control-label">{{ __('Select Country') }}<small class="asterik" style="color:red">*</small></label>
                    <select name="country_id" id="country_id" class="form-control @error('country_id') is-invalid @enderror">
                        <option value="">{{ __('Select Country') }}</option>
                        @foreach($countries as $key => $country)
                            <option value="{{ $country->id }}" {{old ('country_id') == $country->id ? 'selected' : ''}}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <div id="country_id-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-group row has-feedback" >
                <div class="form-group row mb-0">
                    <label for="new_account" class="control-label col-md-12">
                        <input type="checkbox" name="new_account" id="">
                        {{__('Create a new account')}}
                    </label>
                </div>
            </div>
            <div class="form-group row has-feedback password_group" style="display:none">
                <div class="col-md-6">
                    <label for="password" class="control-label">{{__('Password')}}<small class="asterik" style="color:red">*</small></label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="xxxxxxx">
                    @error('password')
                        <div id="password-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirm" class="control-label">{{__('Confirm Password')}}<small class="asterik" style="color:red">*</small></label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="xxxxxxx">
                    @error('password_confirm')
                        <div id="password_confirm-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    <div class="form-group col-md-12 text-center">
        <div class="form-action-btn btn-line">
            <button class="btn btn-primary btn-lg ">{{__('Redeem Voucher')}}</button>
        </div>
    </div>
</form>
