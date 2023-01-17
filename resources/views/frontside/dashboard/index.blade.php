@extends('frontside.layouts.app')

@section('content')
<section class="content-section" id="account-page">
    <div class="container">
        <div class="mt-4 row bottom-space">
            <div class="container">
                <div class="col-lg-8">
                    <div class="row">
                        <h3>{{ __('Documents') }}</h3>
                        <div class="o_portal_docs list-group">
                            <a class="list-group-item list-group-item-action d-flex align-items-center justify-content-between" href="{{ route('user.dashboard.quotations') }}" title="Quotations">
                                {{ __('Quotations') }}
                                <span class="badge badge-secondary badge-pill">{{ $quotation_count }}</span>
                            </a>
                            <a class="list-group-item list-group-item-action d-flex align-items-center justify-content-between" href="{{ route('user.dashboard.sales_order') }}" title="Sales Orders">
                                {{ __('Sales Orders') }}
                                <span class="badge badge-secondary badge-pill">{{ $sales_order_count }}</span>
                            </a>
                            <a class="list-group-item list-group-item-action d-flex align-items-center justify-content-between" href="{{ route('user.dashboard.invoices') }}" title="Invoices &amp; Bills">
                                {{ __('Invoices & Bills') }}
                                <span class="badge badge-secondary badge-pill">{{ $invoice_count }}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 shop-search-bar">
                    <div class="col-lg-12">
                        <h3> {{ Auth::user()->name }} </h3>
                        <address class="mb-0">
                            <div itemprop="address" itemscope="itemscope" itemtype="http://schema.org/PostalAddress">
                                <div class="d-flex align-items-baseline">
                                    <i class="fa fa-map-marker fa-fw" role="img" aria-label="Address" title="Address"></i>
                                    @if(Auth::user()->contact->street_1 != null)
                                    <span class="w-100 o_force_ltr d-block" itemprop="streetAddress">{{ Auth::user()->contact->street_1 }}<br>{{ Auth::user()->contact->city }}  {{ Auth::user()->contact->zipcode }}<br>{{ Auth::user()->contact->contact_countries->name  }}</span>
                                    @elseif(@Auth::user()->contact->contact_addresses[0]->street_1 != null)
                                    <span class="w-100 o_force_ltr d-block" itemprop="streetAddress">{{ Auth::user()->contact->contact_addresses[0]->street_1 }}<br>{{ Auth::user()->contact->contact_addresses[0]->city }}  {{ Auth::user()->contact->zipcode }}<br>{{ Auth::user()->contact->contact_addresses[0]->contact_countries->name  }}</span>
                                    @endif
                                </div>
                                <div>
                                    <i class="fa fa-phone fa-fw" role="img" aria-label="Phone" title="Phone"></i>
                                    <span class="o_force_ltr" itemprop="telephone">{{ Auth::user()->contact->mobile ? Auth::user()->contact->mobile : Auth::user()->contact->phone  }}</span>
                                </div>
                                <div>
                                    <i class="fa fa-envelope fa-fw" role="img" aria-label="Email" title="Email"></i>
                                    <span itemprop="email">{{ Auth::user()->email }}</span>
                                </div>
                                <div>
                                    <a href="{{ route('user.dashboard.profile') }}">{{__('Edit Profile')}}</a>
                                </div>
                            </div>
                        </address>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
