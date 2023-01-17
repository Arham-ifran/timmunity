@php
$segment = Request::segment(2);
$segment = Request::segment(3);
@endphp
@if($segment == 'contacts' || $segment == 'contacts-tags' || $segment == 'contacts-titles' || $segment == 'contacts-sectors-activities' || $segment == 'currencies' || $segment == 'contacts-countries' || $segment == 'contacts-fed-states' || $segment == 'contacts-countries-groups' || $segment == 'contacts-banks' || $segment == 'contacts-bank-accounts' || $segment == 'companies')

<div class="navbar-header">
   <a href="{{ route('admin.dashboard') }}" class="navbar-brand"><i class="fa fa-th"></i></a>
   <a href="javascript:void(0)" class="navbar-brand"><b>{{ __('Contacts') }}</b></a>
   <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
      <i class="fa fa-bars"></i>
   </button>
</div>
<!-- Collect the nav links, forms, and other content for toggling -->
<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
   <ul class="nav navbar-nav">
      <li class="@if(stripos(Request::url(),'contacts') && (!stripos(Request::url(),'contacts-tags')
      && !stripos(Request::url(),'contacts-titles')
      && !stripos(Request::url(),'contacts-sectors-activities')
      && !stripos(Request::url(),'currencies')
      && !stripos(Request::url(),'contacts-countries')
      && !stripos(Request::url(),'contacts-fed-states')
      && !stripos(Request::url(),'contacts-countries-groups')
      && !stripos(Request::url(),'contacts-banks')
      && !stripos(Request::url(),'contacts-bank-accounts'))
      )
      active
  @endif">
  @can('Contact Listing')
  <a href="{{ route('admin.contacts.index') }}">{{ __('Contacts') }}</a>
  @endcan
  </li>
      <li class="dropdown @if(stripos(Request::url(),'contacts-tags')
                || stripos(Request::url(),'contacts-titles')
                || stripos(Request::url(),'contacts-sectors-activities')
                || stripos(Request::url(),'currencies')
                || stripos(Request::url(),'contacts-countries')
                || stripos(Request::url(),'contacts-fed-states')
                || stripos(Request::url(),'contacts-countries-groups')
                || stripos(Request::url(),'contacts-banks')
                || stripos(Request::url(),'contacts-bank-accounts')
                )
                active
            @endif">
        @canany(['Contact Tags Listing','Contact Titles Listing','Contact Sector of Activities Listing','Contact Currencies Listing','Company Listing','Contact Countries Listing','Contact Fed. States Listing','Contact Country Groups Listing','Contact Banks Listing','Contact Bank Accounts Listing'])
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Configuration') }} <span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            @can('Contact Tags Listing')
            <li @if(stripos(Request::url(),'contacts-tags')) class="active" @endif><a href="{{route('admin.contacts-tags.index')}}">{{ __('Contact Tags') }}</a></li>
            @endcan
            @can('Contact Titles Listing')
            <li @if(stripos(Request::url(),'contacts-titles')) class="active" @endif><a href="{{route('admin.contacts-titles.index')}}">{{ __('Contact Titles') }}</a></li>
            @endcan
            @can('Contact Sector of Activities Listing')
            <li @if(stripos(Request::url(),'contacts-sectors-activities')) class="active" @endif><a href="{{route('admin.contacts-sectors-activities.index')}}">{{ __('Sectors of Activity') }}</a></li>
            @endcan
            @can('Contact Currencies Listing')
            <li @if(stripos(Request::url(),'currencies')) class="active" @endif><a href="{{route('admin.currencies.index')}}">{{ __('Contact Currency') }}</a></li>
            @endcan
            @can('Company Listing')
            <li @if(stripos(Request::url(),'companies')) class="active" @endif><a href="{{ route('admin.companies.index') }}">{{ __('Companies') }}</a></li>
            @endcan
            @canany(['Contact Countries Listing','Contact Fed. States Listing','Contact Country Groups Listing'])
            <li class="dropdown-submenu">
               <a class="dropdown-toggle @if(stripos(Request::url(),'contacts-countries')
                || stripos(Request::url(),'contacts-fed-states')
                || stripos(Request::url(),'contacts-countries-groups')
                || stripos(Request::url(),'contacts-bank-accounts')
                )
                active

             @endif" data-toggle="dropdown" aria-expanded="false">{{ __('Localization') }}</a>
               <ul class="dropdown-submenu">
                  @can('Contact Countries Listing')
                  <li @if(stripos(Request::url(),'contacts-countries') && (!stripos(Request::url(),'contacts-countries-groups'))) class="active" @endif><a href="{{route('admin.contacts-countries.index')}}">{{ __('Countries') }}</a></li>
                  @endcan
                  @can('Contact Fed. States Listing')
                  <li @if(stripos(Request::url(),'contacts-fed-states')) class="active" @endif><a href="{{route('admin.contacts-fed-states.index')}}">{{ __('Fed. State') }}</a></li>
                  @endcan
                  @can('Contact Country Groups Listing')
                  <li @if(stripos(Request::url(),'contacts-countries-groups')) class="active" @endif><a href="{{route('admin.contacts-countries-groups.index')}}">{{ __('Country Group') }}</a></li>
                  @endcan
                </ul>
            </li>
            @endcanany
            @canany(['Contact Banks Listing','Contact Bank Accounts Listing'])
            <li class="dropdown-submenu">
               <a class="dropdown-toggle @if(stripos(Request::url(),'contacts-banks')
                || stripos(Request::url(),'contacts-bank-accounts')
                )
                active
            @endif" data-toggle="dropdown" aria-expanded="false">{{ __('Bank Accounts') }}</a>
               <ul class="dropdown-submenu">
                   @can('Contact Banks Listing')
                   <li @if(stripos(Request::url(),'contacts-banks')) class="active" @endif><a href="{{route('admin.contacts-banks.index')}}">{{ __('Banks') }}</a></li>
                   @endcan
                   @can('Contact Bank Accounts Listing')
                    <li @if(stripos(Request::url(),'contacts-bank-accounts')) class="active" @endif><a href="{{route('admin.contacts-bank-accounts.index')}}">{{ __('Banks Accounts') }}</a></li>
                   @endcan
               </ul>
            </li>
            @endcanany
         </ul>
         @endcanany
      </li>
   </ul>
</div>
@elseif ($segment == 'settings')
<div class="navbar-header">
   <a href="{{ route('admin.dashboard') }}" class="navbar-brand"><i class="fa fa-th"></i></a>
   <a href="javascript:void(0)" class="navbar-brand"><b> {{ __('Settings') }}</b></a>
   <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
      <i class="fa fa-bars"></i>
   </button>
</div>
<!-- Collect the nav links, forms, and other content for toggling -->
<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
   <ul class="nav navbar-nav">
      <li class="@if(stripos(Request::url(),'settings') && (!stripos(Request::url(),'settings/admin-user')
      && !stripos(Request::url(),'settings/companies')
      && !stripos(Request::url(),'settings/roles')
      && !stripos(Request::url(),'settings/site-settings')
      && !stripos(Request::url(),'settings/cms'))
      && !stripos(Request::url(),'settings/languages'))
      )
      active
  @endif">
        @can('View General Settings')
        <a href="{{ route('admin.settings') }}">{{ __('General Settings') }}</a>
        @endcan
      </li>
      <li class="dropdown
            @if(stripos(Request::url(),'settings/admin-user')
                || stripos(Request::url(),'settings/companies')
                || stripos(Request::url(),'settings/roles')
                || stripos(Request::url(),'settings/cms')
                )
                active
            @endif">
        @canany(['User Listing','CMS Page Listing','Role Listing'])
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('User & Roles') }} <span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
            @can('User Listing')
            <li @if(stripos(Request::url(),'settings/admin-user')) class="active" @endif><a href="{{ route('admin.admin-user.index') }}">{{ __('Users') }}</a></li>
            @endcan
            @can('Roles Listing')
            <li @if(stripos(Request::url(),'settings/roles')) class="active" @endif><a href="{{ route('admin.roles.index') }}">{{ __ ('Roles') }}</a></li>
            @endcan
            {{-- <li @if(stripos(Request::url(),'settings/permissions')) class="active" @endif><a href="{{ route('admin.permissions.index') }}">{{ __('Permissions') }}</a></li> --}}
            @can('CMS Page Listing')
            <li @if(stripos(Request::url(),'settings/cms')) class="active" @endif><a href="{{ route('admin.cms.index') }}">{{ __('CMS Pages') }}</a></li>
            @endcan
        </ul>
        @endcanany
      </li>
      @canany(['Email Templates Listing','Email Template Labels Listing'])
      <li class="dropdown
            @if(stripos(Request::url(),'settings/email-templates')
                || stripos(Request::url(),'settings/email-template-labels')
                )
                active
            @endif">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Email Templates') }} <span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            @can('Email Templates Listing')
            <li @if(stripos(Request::url(),'settings/email-templates')) class="active" @endif><a href="{{ route('admin.email-templates.index') }}">{{ __('Email Templates Listing') }}</a></li>
            @endcan
            @can('Email Template Labels Listing')
            <li @if(stripos(Request::url(),'settings/email-template-labels')) class="active" @endif><a href="{{ route('admin.email-template-labels.index') }}">{{ __('Email Template Labels') }}</a></li>
            @endcan
         </ul>
      </li>
      @endcanany
      <li @if(stripos(Request::url(),'settings/site-settings')) class="active" @endif>
        @can('View Site Settings')
        <a href="{{ route('admin.site.settings') }}">{{ __('Site Settings') }}</a>
        @endcan
      </li>
      {{-- <li @if(stripos(Request::url(),'email-templates')) class="active" @endif>
        <a href="{{ route('admin.email-templates.index') }}">{{ __('Email Templates') }} </a>
      </li> --}}
       @canany(['Languages Listing','Language Modules Listing','Language Translations Listing','Label Translations Listing','View Text Translations'])
      <li class="dropdown
            @if(stripos(Request::url(),'settings/languages')
               || stripos(Request::url(),'settings/language-modules')
               || stripos(Request::url(),'settings/language-translations')
               || stripos(Request::url(),'settings/label-translations')
               || stripos(Request::url(),'settings/text-translations')
                )
                active
            @endif">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Languages') }} <span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            @can('Languages Listing')
            <li @if(stripos(Request::url(),'settings/languages')) class="active" @endif><a href="{{ route('admin.languages.index') }}">{{ __('Languages') }}</a></li>
            @endcan
            @can('Language Modules Listing')
            <li @if(stripos(Request::url(),'settings/language-modules')) class="active" @endif><a href="{{ route('admin.language-modules.index') }}">{{ __('Language Modules') }}</a></li>
            @endcan
            @can('Language Translations Listing')
            <li @if(stripos(Request::url(),'settings/language-translations')) class="active" @endif><a href="{{ route('admin.language-translations.index') }}">{{ __('Language Translations') }}</a></li>
            @endcan
            @can('Create Label Translations')
            <li @if(stripos(Request::url(),'settings/label-translations')) class="active" @endif><a href="{{ route('admin.label-translations.index') }}">{{ __('Label Translations') }}</a></li>
            @endcan
            @can('Create Text Translations')
            <li @if(stripos(Request::url(),'settings/text-translations')) class="active" @endif><a href="{{ route('admin.text-translations.index') }}">{{ __('Text Translations') }}</a></li>
            @endcan
        </ul>
      </li>
      @endcanany
   </ul>
</div>

@elseif ($segment == 'kss')
<div class="navbar-header">
   <a href="{{ route('admin.dashboard') }}" class="navbar-brand"><i class="fa fa-th"></i></a>
   <a href="javascript:void(0)" class="navbar-brand"><b>{{__('KSS') }}</b></a>
   <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
      <i class="fa fa-bars"></i>
   </button>
</div>
<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
    <ul class="nav navbar-nav">
       <li @if(stripos(Request::url(),'licences')) class="active" @endif>
          <a href="{{ route('admin.kss.licenses') }}">{{ __('Licenses') }} </a>
       </li>
       <li @if(stripos(Request::url(),'vouchers')) class="active" @endif>
          <a href="{{ route('admin.kss.vouchers') }}">{{ __('Vouchers') }} </a>
       </li>
    </ul>
 </div>
@elseif ($segment == 'f-secure')
<div class="navbar-header">
   <a href="{{ route('admin.dashboard') }}" class="navbar-brand"><i class="fa fa-th"></i></a>
   <a href="javascript:void(0)" class="navbar-brand"><b>{{__('F-Secure') }}</b></a>
   <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
      <i class="fa fa-bars"></i>
   </button>
</div>
<!-- Collect the nav links, forms, and other content for toggling -->
<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
   <ul class="nav navbar-nav">
      <li @if(stripos(Request::url(),'f-secure') && !stripos(Request::url(),'f-secure/logs')) class="active" @endif>
         <a href="{{ route('admin.f-secure.index') }}">{{ __('F-Secure Subscription') }} </a>
      </li>
      <li @if(stripos(Request::url(),'f-secure/logs')) class="active" @endif>
         <a href="{{ route('admin.f-secure.fescureLog') }}">{{ __('F-Secure Log') }}</a>
      </li>
   </ul>
</div>
@elseif($segment == 'voucher')
<div class="navbar-header">
    <a href="{{ route('admin.dashboard') }}" class="navbar-brand"><i class="fa fa-th"></i></a>
    <a href="javascript:void(0)" class="navbar-brand"><b>{{ __('Voucher') }}</b></a>
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
</div>
@canany(['Voucher Dashboard','Voucher Order Listing'])
 <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
    <ul class="nav navbar-nav">
      @can('Voucher Dashboard')
       <li class=" @if( stripos(Request::url(),'voucher/dashboard')) active @endif">
          <a href="{{ route('admin.voucher.dashboard') }}">{{ __('Voucher Dashboard') }} <span class="sr-only">{{ __('(current)') }}</span></a>
       </li>
      @endcan
      @can('Voucher Order Listing')
       <li class=" @if( stripos(Request::url(),'voucher/orders') || stripos(Request::url(),'voucher/order-vouchers/') || stripos(Request::url(),'voucher/payment/')) active @endif">
          <a href="{{ route('admin.voucher.orders') }}">{{ __('Voucher Orders') }} <span class="sr-only">{{ __('(current)') }}</span></a>
       </li>
       <li class=" @if( stripos(Request::url(),'voucher/invoices') ) active @endif">
          <a href="{{ route('admin.voucher.invoices') }}">{{ __('Invoices') }} <span class="sr-only">{{ __('(current)') }}</span></a>
       </li>
       <li class=" @if( stripos(Request::url(),'voucher/distributor-invoices') ) active @endif">
          <a href="{{ route('admin.voucher.distributor-invoices') }}">{{ __('Distributor Invoices') }} <span class="sr-only">{{ __('(current)') }}</span></a>
       </li>
      @endcan
    </ul>
 </div>
@endcanany
@elseif($segment == 'license' || $segment == 'license-files')
<div class="navbar-header">
    <a href="{{ route('admin.dashboard') }}" class="navbar-brand"><i class="fa fa-th"></i></a>
    <a href="javascript:void(0)" class="navbar-brand"><b>{{ __('License') }}</b></a>
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
</div>
@canany(['License Dashboard','Licenses Listing'])
 <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
    <ul class="nav navbar-nav">
       @can('License Dashboard')
       <li class=" @if( stripos(Request::url(),'license/dashboard')) active @endif">
          <a href="{{ route('admin.license.dashboard') }}">{{ __('Licenses Dashboard') }} <span class="sr-only">{{ __('(current)') }}</span></a>
       </li>
      @endcan
      @can('Licenses Listing')
       <li class=" @if( stripos(Request::url(),'license') && !stripos(Request::url(),'license/dashboard') && !stripos(Request::url(),'license-files')) active @endif">
          <a href="{{ route('admin.license.index') }}">{{ __('Licenses') }} <span class="sr-only">{{ __('(current)') }}</span></a>
       </li>
       <li class=" @if( stripos(Request::url(),'license-files')) active @endif">
          <a href="{{ route('admin.license.files') }}">{{ __('License Files') }} <span class="sr-only">{{ __('(current)') }}</span></a>
       </li>
      @endcan
    </ul>
 </div>
 @endcanany
 @elseif($segment == 'website' || $segment == 'reseller-package')
 <div class="navbar-header">
    <a href="{{ route('admin.dashboard') }}" class="navbar-brand"><i class="fa fa-th"></i></a>
    <a href="javascript:void(0)" class="navbar-brand"><b>{{ __('Website') }}</b></a>
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
 </div>
 <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
    <ul class="nav navbar-nav">
        @can('Website Dashboard')
        <li class=" @if( stripos(Request::url(),'website/dashboard') ) active @endif">
            <a href="{{ route('admin.website.dashboard') }}">{{ __('Website Dashboard') }} <span class="sr-only">{{ __('(current)') }}</span></a>
        </li>
        @endcan
        @can('Website Abandoned Cart Listing')
        <li class=" @if( stripos(Request::url(),'website/abandoned-carts') ) active @endif">
            <a href="{{ route('admin.website.abandoned.carts') }}">{{ __('Abandoned Carts') }} <span class="sr-only">{{ __('(current)') }}</span></a>
        </li>
        @endcan
        @canany(['Products Listing','Projects Listing'])
        <li class=" @if( stripos(Request::url(),'projects') ) active @endif">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Products') }} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                @can('Products Listing')
                <li @if( stripos(Request::url(),'products')  ) active @endif><a href="{{ route('admin.products.index') }}">{{ __('Products') }}</a></li>
                @endcan
                @can('Projects Listing')
                <li @if(stripos(Request::url(),'projetcs')) class="active" @endif><a href="{{ route('admin.website.projects') }}">{{ __('Projects') }}</a></li>
                @endcan
            </ul>
        </li>
        @endcanany
        @canany(['Visitors Listing','Views Listing'])
        <li class=" @if( stripos(Request::url(),'website/visitor') || stripos(Request::url(),'website/views') ) active @endif">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Visitors') }} <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    @can('Visitors Listing')
                    <li @if(stripos(Request::url(),'website/visitor')) class="active" @endif><a href="{{ route('admin.website.visitors') }}">{{ __('Visitors') }}</a></li>
                    @endcan
                    @can('Views Listing')
                    <li @if(stripos(Request::url(),'website/views')) class="active" @endif><a href="{{ route('admin.website.views') }}">{{ __('Views') }}</a></li>
                    @endcan
                </ul>
        </li>
        @endcanany
        @canany(['Reseller Listing','Lawful Interception Listing'])
        <li class=" @if( stripos(Request::url(),'website/reseller') || stripos(Request::url(),'website/lawful-interception') || stripos(Request::url(),'reseller-package') ) active @endif">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Resellers') }} <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    @can('Reseller Listing')
                    <li @if(stripos(Request::url(),'website/reseller')) class="active" @endif><a href="{{ route('admin.website.resellers') }}">{{ __('Resellers') }}</a></li>
                    @endcan
                    @can('Lawful Interception Listing')
                    <li @if(stripos(Request::url(),'website/lawful-interception')) class="active" @endif><a href="{{ route('admin.website.lawfulinterception') }}">{{ __('Lawful Interception') }}</a></li>
                    @endcan
                    <li @if(stripos(Request::url(),'reseller-package')) class="active" @endif><a href="{{ route('admin.reseller-package.index') }}">{{ __('Reseller Packages') }}</a></li>
                </ul>
        </li>
        @endcanany
        @canany(['FAQs Listing','Contact Us Queries Listing','Payment Gateway Settings'])
        <li class=" @if( stripos(Request::url(),'website/reseller') || stripos(Request::url(),'website/lawful-interception') ) active @endif">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Settings') }} <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    @can('FAQs Listing')
                    <li @if(stripos(Request::url(),'website/faqs')) class="active" @endif><a href="{{ route('admin.faqs.index') }}">{{ __('FAQs') }}</a></li>
                    @endcan
                    @can('Contact Us Queries Listing')
                    <li @if(stripos(Request::url(),'website/contact-us-queries')) class="active" @endif><a href="{{ route('admin.contact-us-queries.index') }}">{{ __('Contact Us Queries') }}</a></li>
                    @endcan
                    @can('Payment Gateway Settings')
                    <li @if(stripos(Request::url(),'/payment-gateways')) class="active" @endif><a href="{{ route('admin.website.payment.gateways') }}">{{ __('Payment Gateways') }}</a></li>
                    @endcan
                </ul>
        </li>
        @endcanany
    </ul>
 </div>
@elseif ($segment == 'sales-management')
<div class="navbar-header">
   <a href="{{ route('admin.dashboard') }}" class="navbar-brand"><i class="fa fa-th"></i></a>
   <a href="javascript:void(0)" class="navbar-brand"><b>{{ __('Sales') }}</b></a>
   <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
</div>
<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
   <ul class="nav navbar-nav">
      @can('Sales Dashboard')
      <li class=" @if( stripos(Request::url(),'sales-management/dashboard')) active @endif">
         <a href="{{ route('admin.sales-dashboard') }}">{{ __('Sales Dashboard') }} <span class="sr-only">{{ __('(current)') }}</span></a>
      </li>
      @endcan
      @canany(['Quotations Listing','Orders Listing','View Sales Analytics','Customers Listing'])
      <li class="dropdown
                @if( stripos(Request::url(),'sales-management/quotations')
                    || stripos(Request::url(),'sales-management/sales-orders')
                    || stripos(Request::url(),'sales-management/sales-team-analytics')
                    || stripos(Request::url(),'sales-management/customers')
                    )
                     active
                @endif">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Orders') }}<span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            @can('Quotations Listing')
            <li class=" @if( stripos(Request::url(),'sales-management/quotations')) active @endif"><a href="{{ route('admin.quotations.index') }}">{{ __('Quotations') }}</a></li>
            @endcan
            @can('Orders Listing')
            <li class=" @if( stripos(Request::url(),'sales-management/sales-orders')) active @endif"><a href="{{ route('admin.quotation.sales.orders') }}">{{  __('Orders') }}</a></li>
            @endcan
            @can('View Sales Analytics')
            <li class=" @if( stripos(Request::url(),'sales-management/sales-team-analytics')) active @endif"><a href="{{ route('admin.sales-team.analytics') }}">{{ __('Sales Analytics') }}</a></li>
            @endcan
            @can('Customers Listing')
            <li class=" @if( stripos(Request::url(),'sales-management/customers')) active @endif"><a href="{{ route('admin.customers.index') }}">{{ __('Customers') }}</a></li>
            @endcan
            <li class="@if( stripos(Request::url(),'sales-management/order-to-invoice')) active @endif"><a href="{{ route('admin.quotation.sales.orders.toinvoice') }}">{{ __('Orders To Invoice') }}</a></li>
         </ul>
      </li>
      {{-- <li class="dropdown  @if( stripos(Request::url(),'sales-management/order-to-invoice')) active @endif">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('To Invoice') }} <span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
         </ul>
      </li> --}}
      {{-- <li class="dropdown  @if( stripos(Request::url(),'sales-management/invoices')) active @endif">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Invoices') }} <span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            <li class="@if( stripos(Request::url(),'sales-management/invoices')) active @endif"><a href="{{ route('admin.invoices.index') }}">{{ __('Invoices') }}</a></li>
         </ul>
      </li> --}}
      @endcanany
      @canany(['Products Listing','Product Variant Listing','Price Lists Listing','Manufacturer'])
      <li class="dropdown
                @if( stripos(Request::url(),'products') || stripos(Request::url(),'product-variant') || stripos(Request::url(),'price-lists') )
                    active
                @endif
            ">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Products') }} <span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            @can('Products Listing')
            <li class="@if( stripos(Request::url(),'products')  ) active @endif"><a href="{{ route('admin.products.index') }}">{{ __('Products') }}</a></li>
            @endcan

            <li class="@if( stripos(Request::url(),'manufacturers')  ) active @endif"><a href="{{route('admin.manufacturer.index')}}">{{ __('Manufacturers') }}</a></li>

            @can('Product Variant Listing')
            @if(@$sales_settings['product_catalog_variants'] == 1)
            <li class="@if( stripos(Request::url(),'product-variant') ) active @endif"><a href="{{ route('admin.product-variant.index') }}">{{ __('Products Variants') }}</a></li>
            @endif
            @endcan
            @can('Price Lists Listing')
            @if(@$sales_settings['pricing_pricelist'] == 1)
            <li class="@if( stripos(Request::url(),'price-lists') ) active @endif"><a href="{{ route('admin.price-lists.index') }}">{{ __('Price lists') }}</a></li>
            @endif
            @endcan
         </ul>
      </li>
      @endcanany
      @can('Sales Team Analysis')
      <li class="dropdown">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Reporting') }}<span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            <li><a href="{{ route('admin.sales-team.analysis') }}">{{ __('Sales') }}</a></li>
         </ul>
      </li>
      @endcan
      @canany(['View Sales Settings','Sales Team Listing','Taxes Listing','Ecommerce Categories Listing','Attributes Listing','Email Templates Listing'])
      <li class="dropdown @if( stripos(Request::url(),'configuration')
                            || stripos(Request::url(),'email-templates')
                            || stripos(Request::url(),'attribute')
                            || stripos(Request::url(),'eccomerce-categories')
                            || stripos(Request::url(),'taxes')) active @endif">
         <a href="#" class="dropdown-toggle " data-toggle="dropdown" aria-expanded="false">{{ __('Configuration') }}<span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            @can('View Sales Settings')
            <li class="@if( stripos(Request::url(),'settings/sales')  ) active @endif"><a href="{{ route('admin.sales.settings') }}">{{ __('Settings') }}</a></li>
            @endcan
            @can('Sales Team Listing')
            <li class="@if( stripos(Request::url(),'sales-team')  ) active @endif"><a href="{{ route('admin.sales-team.index') }}">{{ __('Sales Team') }}</a></li>
            @endcan
            @can('Taxes Listing')
            <li class="@if( stripos(Request::url(),'taxes')  ) active @endif"><a href="{{ route('admin.taxes.index') }}">{{ __('Taxes') }}</a></li>
            @endcan
            {{-- @can('Ecommerce Categories Listing')
            <li class="@if( stripos(Request::url(),'eccomerce-categories')  ) active @endif"><a href="{{ route('admin.eccomerce-categories.index') }}">{{ __('Ecommerce Categories') }}</a></li>
            @endcan --}}
            @can('Attributes Listing')
            @if(@$sales_settings['product_catalog_variants'] == 1)
                <li class="@if( stripos(Request::url(),'attribute')  ) active @endif"><a href="{{ route('admin.attribute.index') }}">{{__('Attributes') }}</a></li>
            @endif
            @endcan
            @can('Email Templates Listing')
            <li @if(stripos(Request::url(),'email-templates')) class="active" @endif><a href="{{ route('admin.email-templates.index') }}">{{ __('Email Templates') }}</a></li>
            @endcan
         </ul>
      </li>
      @endcanany
      {{-- @can('Channel Pilot') --}}
        <li class="dropdown
            @if( stripos(Request::url(),'channel-pilot-sales'))
                active
            @endif
            ">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Channel Pilot') }} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <li class="@if( stripos(Request::url(),'channel-pilot-sales')) active @endif"><a href="{{ route('admin.channel-pilot-sales-analytics') }}">{{ __('Analytics') }}</a></li>
                <li class="@if( stripos(Request::url(),'channel-pilot-api-logs')) active @endif"><a href="{{ route('admin.channel-pilot.api.logs') }}">{{ __('API Logs') }}</a></li>
                <li class="@if( stripos(Request::url(),'channel-pilot-get-marketplace-orders')) active @endif"><a href="{{ route('admin.channel-pilot.marketplace.orders') }}">{{ __('Marketplace Orders') }}</a></li>
            </ul>
        </li>
      {{-- <li class=" @if( stripos(Request::url(),'channel-pilot-sales')) active @endif">
        <a href="{{ route('admin.channel-pilot-sales-analytics') }}">{{ __('Channel Pilot') }} <span class="sr-only">{{ __('(current)') }}</span></a>
      </li> --}}
      {{-- @endcan --}}
   </ul>
</div>

@elseif ($segment == 'reports')
<div class="navbar-header">
   <a href="{{ route('admin.dashboard') }}" class="navbar-brand"><i class="fa fa-th"></i></a>
   <a href="javascript:void(0)" class="navbar-brand"><b>{{ __('Reports') }}</b></a>
   <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
</div>
<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
   <ul class="nav navbar-nav">
      <li class="dropdown @if(stripos(Request::url(),'sales-analysis') || stripos(Request::url(),'website-analysis') || stripos(Request::url(),'reports/voucher-orders')) 
         active
         @endif">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Sales') }} <span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            <li class=" @if( stripos(Request::url(),'reports/sales-analysis')) active @endif">
               <a href="{{ route('admin.reports.sales-report-dashboard') }}">{{ __('Sales Analysis') }} <span class="sr-only">{{ __('(current)') }}</span></a>
            </li>
            <li class=" @if( stripos(Request::url(),'reports/website-analysis')) active @endif">
               <a href="{{ route('admin.reports.website-dashboard') }}">{{ __('Website Analysis') }}</a>
            </li>
            <li class=" @if( stripos(Request::url(),'reports/voucher-orders')) active @endif">
               <a href="{{ route('admin.reports.voucher.orders') }}">{{ __('Voucher Orders') }}</a>
            </li>
            <li class=" @if( stripos(Request::url(),'distributors')) active @endif">
               <a href="{{ route('admin.reports.voucher.distributors') }}">{{ __('Distributor Orders') }}</a>
            </li>
         </ul>
      </li>
      <li class="dropdown @if(stripos(Request::url(),'invoices') || stripos(Request::url(),'voucher-payment'))
         active
         @endif">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Invoices') }} <span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            <li class=" @if( stripos(Request::url(),'reports/invoices')) active @endif">
               <a href="{{ route('admin.reports.invoices') }}">{{ __('Sales Invoices') }}</a>
            </li>
            <li class=" @if( stripos(Request::url(),'reports/voucher-payment')) active @endif">
               <a href="{{ route('admin.reports.voucher.payment') }}">{{ __('Voucher Payment') }}</a>
            </li>
            <li class=" @if( stripos(Request::url(),'reports/distributor-voucher-payment')) active @endif">
               <a href="{{ route('admin.reports.voucher.distributorVoucherPayment') }}">{{ __('Distributor Invoices') }}</a>
            </li>

         </ul>
      </li>

      <li class=" @if( stripos(Request::url(),'reports/licenses')) active @endif">
         <a href="{{ route('admin.reports.licenses') }}">{{ __('Licenses') }}</a>
      </li>

      <li class=" @if( stripos(Request::url(),'reports/abandoned-carts')) active @endif">
         <a href="{{ route('admin.reports.abandoned.carts') }}">{{ __('Abandoned Carts') }}</a>
      </li>
      <li class="dropdown @if(stripos(Request::url(),'distributors') || stripos(Request::url(),'manufacturers')))
         active
         @endif">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Vendors') }} <span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            <li class=" @if( stripos(Request::url(),'manufacturers')) active @endif">
               <a href="{{ route('admin.reports.manufacturers') }}">{{ __('Manufacturers') }}</a>
            </li>
            <li class=" @if( stripos(Request::url(),'distributors')) active @endif">
               <a href="{{ route('admin.reports.voucher.distributors') }}">{{ __('Distributors') }}</a>
            </li>

         </ul>
      </li>
      <li class=" @if( stripos(Request::url(),'reports/market-place-orders')) active @endif">
         <a href="{{ route('admin.reports.voucher.market-place-orders') }}">{{ __('Channel Pilot') }}</a>
      </li>
      <li class="dropdown @if(stripos(Request::url(),'kaspersky')))
         active
         @endif">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('KSS') }} <span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            <li class=" @if( stripos(Request::url(),'licenses')) active @endif">
               <a href="{{ route('admin.reports.voucher.kss.licenses') }}">{{ __('Licences') }}</a>
            </li>
            <li class=" @if( stripos(Request::url(),'vouchers')) active @endif">
               <a href="{{ route('admin.reports.voucher.kss.vouchers') }}">{{ __('Vouchers') }}</a>
            </li>

         </ul>
      </li>
   </ul>
</div>
@elseif ($segment == 'distributor')
<div class="navbar-header">
   <a href="{{ route('admin.dashboard') }}" class="navbar-brand"><i class="fa fa-th"></i></a>
   <a href="javascript:void(0)" class="navbar-brand"><b>{{ __('Distributor') }}</b></a>
   <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
</div>
<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
    <ul class="nav navbar-nav">

      <li class=" @if( stripos(Request::url(),'distributor')) active @endif">
         <a href="{{ route('admin.distributor.index') }}">{{ __('Distributors') }}</a>
      </li>

   </ul>
</div>
@endif
