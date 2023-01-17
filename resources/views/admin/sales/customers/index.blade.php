@extends('admin.layouts.app')
@section('title', __('Customers'))
@section('content')

    <div class="content-wrapper">
        <section class="content-header top-header">
            <div class="row">
                <div class="col-md-6">
                    <h2>{{ __('Customers') }}</h2>
                </div>
                <div class="search-input-das">
                    <form>
                        <input type="text" id="search-contacts-d" name="search" placeholder="{{ __('Search') }}..." />
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="box-header">
                    <div class="row">
                        @can('Add Customer')
                            <div class="col-md-4">
                                <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2"
                                    href="{{ route('admin.customers.create') }}"><i class="fa fa-plus"
                                        aria-hidden="true"></i></a>
                            </div>
                        @endcan
                        <div class="col-md-4 text-center">
                            <div class="quotation-right-side content-center">

                            </div>
                        </div>
                        <div class="col-md-4">
                            <ol class="breadcrumb pages-arrow pull-right">
                                @if ($customers->hasPages())
                                    <li><a href="#"> {{ $customers->currentPage() . '-' . $customers->lastPage() }}</a></li>
                                    <li class="active">{{ $customers->currentPage() }}</li>
                                    @if ($customers->onFirstPage())
                                        <a class="disabled" href="#."> <i class="fa fa-angle-left"> </i></a>
                                    @else
                                        <a href="{{ $customers->previousPageUrl() }}"> <i class="fa fa-angle-left">
                                            </i></a>
                                    @endif
                                    @if ($customers->hasMorePages())
                                        <a class="disabled" href="{{ $customers->nextPageUrl() }}"><i
                                                class="fa fa-angle-right"> </i></a>
                                    @else
                                        <a href="#."><i class="fa fa-angle-right"> </i></a>
                                    @endif
                                @endif
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

        </section>
        <section class="content">
            <div class="box product-box" id="contats-append">
                <div class="dashboard-products-row">
                    <div class="row  bottom-space">
                        @foreach ($customers as $customer)
                            <div class="col-sm-6 col-md-4">
                                @can('Edit Customer')
                                <a href="{{ route('admin.customers.edit', Hashids::encode($customer->id)) }}">
                                    <div class="customer-box">
                                        <div class="customer-img">
                                            <img src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode(@$contact->admin_users->id) . '/' . @$contact->admin_users->image), 'avatar5.png') !!}" />
                                        </div>
                                        <div class="customer-content col-md-6">
                                            <h3 class="customer-heading">{{ $customer->name }}</h3>
                                            <h5 class="sub-heading">
                                                {{ @$customer->contact_countries->name }}
                                            </h5>
                                            <span class="email">{{ $customer->email }}</span>
                                        </div>
                                    </div>
                                </a>
                                @else
                                <div class="customer-box">
                                    <div class="customer-img">
                                        <img src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode(@$contact->admin_users->id) . '/' . @$contact->admin_users->image), 'avatar5.png') !!}" />
                                    </div>
                                    <div class="customer-content col-md-6">
                                        <h3 class="customer-heading">{{ $customer->name }}</h3>
                                        <h5 class="sub-heading">
                                            {{ @$customer->contact_countries->name }}
                                        </h5>
                                        <span class="email">{{ $customer->email }}</span>
                                    </div>
                                </div>
                                @endcan
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script src="{{ asset('backend/dist/js/contacts.js') }}"></script>
    <script>
        var filter = [];

        var search = '';
        $('body').on('click', '.contact-filter-d', function() {
            let index = filter.indexOf($(this).attr('data-id'));
            if (index === -1) {
                filter.push($(this).attr('data-id'));
            } else {
                filter.splice(index, 1);
            }

            search = {
                s: jQuery("#search-contacts-d").val(),
                filter: filter
            };
            search_results("{{ route('admin.customers.index') }}", 'GET', search, '#contats-append');
        });

        $(document).on("input", "#search-contacts-d", function(event) {
            search = {
                s: jQuery(this).val(),
                filter: filter
            };
            search_results("{{ route('admin.customers.index') }}", 'GET', search, '#contats-append');
        });
    </script>
@endsection
