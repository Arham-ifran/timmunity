@extends('admin.layouts.app')
@section('title', __('Contacts'))
@section('styles')
<!-- Daterange picker -->
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<style>
    .contact-tag {
        position: absolute;
        right: 16px;
        background: #009a71;
        color: white;
        padding: 5px 15px;
    }
    .contact-tag.reseller-ribbon {
        background: yellowgreen;
    }
    .contact-tag.admin-ribbon {
        background: #e08e0b;
    }
    .contact-tag.guest-ribbon {
        background: #9a0101;
    }
    .ranges li {
        color: #009a71;
    }
    .ranges li:hover,.ranges li.active,.daterangepicker td.active, .daterangepicker td.active:hover {
        background-color: #009a71;
        border-color: #009a71;
    }
</style>
@endsection
    <!-- Top Header Section -->
@section('content')
    <div class="content-wrapper">
        <section class="content-header top-header">
            <div class="row">
                <div class="col-md-6">
                    <h2>{{ __('Contacts') }} </h2>
                </div>
                <div class="col-md-6">
                    <div class="search-input-das">
                        <form>
                            <input type="text" id="search-contacts-d" name="search" placeholder="{{ __('Search') }}..." />
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="box-header">
                    <div class="row">
                        <div class="col-md-12 pl-0">
                            @can('Add New Contact')
                            <a class="skin-green-light-btn btn ml-2" href="{{ route('admin.contacts.create') }}">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </a>
                            @endcan
                            {{-- <a style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;" title="Export All" class=" btn ml-2" href="#">
                                <i class="fa fa-download"></i>
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="box product-box contact-detail-des" id="contats-append">
                <div class="box-header">
                    @canany(['Contact Listing'])
                        @can('Filter Record Contacts')
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="" id="contact-type-filter" class="form-control">
                                            <option value="0" >{{ __('All Contacts') }}</option>
                                            <option value="1" @if(Request::get('type') == 1) selected="selected" @endif>{{ __('Admin') }}</option>
                                            <option value="2" @if(Request::get('type') == 2) selected="selected" @endif>{{ __('Customers') }}</option>
                                            <option value="3" @if(Request::get('type') == 3) selected="selected" @endif>{{ __('Resellers') }}</option>
                                            <option value="4" @if(Request::get('type') == 4) selected="selected" @endif>{{ __('Guest') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" id="contact-creation-date-filter" class="form-control" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="" id="contact-created-by-filter" class="form-control">
                                            <option value="" >{{ __('Select Created By') }}</option>
                                            <option value="0" >{{ __('Website Registration') }}</option>
                                            @foreach ($contacts->admins as $admin)
                                                <option value="{{ Hashids::encode($admin->id) }}">{{ $admin->firstname.' '.$admin->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                               
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="" id="contact-contact-filter" class="form-control">
                                            <option value="3">{{ __('All') }}</option>
                                            <option value="1">{{ __('Individual') }}</option>
                                            <option value="2">{{ __('Company') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="" id="contact-company-filter" class="form-control">
                                            <option value="" >{{ __('Select Company') }}</option>
                                            @foreach ($contacts->companies as $company)
                                                <option value="{{ Hashids::encode($company->id) }}">{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="" id="contact-country-filter" class="form-control">
                                            <option value="" >{{ __('Select Country') }}</option>
                                            @foreach ($contacts->countries as $country)
                                                <option value="{{ Hashids::encode($country->id) }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="" id="contact-active-filter" class="form-control">
                                            <option value="">{{ __('Select Status ') }}</option>
                                            <option value="1">{{ __('Active') }}</option>
                                            <option value="0">{{ __('In-Active') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                       <a href="{{route('admin.contact.export')}}" class="btn btn-primary" id="export-report-btn">Export Report to Excel</a>
                                       <span id="contact_count">&nbsp;(Total {{$contact_count}} Records)</span>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    @endcanany
                </div>
                <div class="box-body row">
                    @can('Contact Listing')
                    @include('admin.contacts.contact-lists')
                    @endcan
                    {{-- start div --}}
                    <div class="dashboard-products-row">
                        <div class="row clearfix bottom-space">
                        </div>
                    </div>
                    {{-- end div --}}
                </div>
            </div>
        </section>
    </div>
@endsection
@section('scripts')
<script src="{{ asset('backend/dist/js/custom.js') }}"></script>
<script src="{{ asset('backend/dist/js/contacts.js') }}"></script>
<script src="{{ asset('backend/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script>
var export_url = "{{route('admin.contact.export')}}";
var filter = '';
var type = '';
var active_status = '';
var created_by = '';
var search = '';
var start_date = '';
var end_date = '';
var company = '';
var country = '';

function searchResult()
{
    search = {
        s:jQuery("#search-contacts-d").val(),
        filter:filter,
        type:type,
        active_status:active_status,
        created_by:created_by,
        start_date:start_date,
        end_date:end_date,
        company:company,
        country:country,
    };
    var url = new URL(export_url);
    url.searchParams.set('s', jQuery("#search-contacts-d").val());
    url.searchParams.set('filter', filter);
    url.searchParams.set('type', type);
    url.searchParams.set('active_status', active_status);
    url.searchParams.set('created_by', created_by);
    url.searchParams.set('start_date', start_date);
    url.searchParams.set('end_date', end_date);
    url.searchParams.set('company', company);
    url.searchParams.set('country', country);
    $('#export-report-btn').attr('href',url);
    search_results("{{route('admin.contacts.index')}}",'GET',search,'#contats-append .box-body','#contact_count');
}
$('body').on('change','#contact-contact-filter',function(){
    filter = $(this).val();
    searchResult();
});
$('body').on('change','#contact-type-filter',function(){
    type = $(this).val();
    searchResult();
});
$('body').on('change','#contact-created-by-filter',function(){
    created_by = $(this).val();
    searchResult();
});
$('body').on('change','#contact-active-filter',function(){
    active_status = $(this).val();
    searchResult();
});
$('body').on('change','#contact-company-filter',function(){
    company = $(this).val();
    searchResult();
});
$('body').on('change','#contact-country-filter',function(){
    country = $(this).val();
    searchResult();
});
$('body').on("input", "#search-contacts-d", function (event) {
    searchResult();
});

$('#contact-creation-date-filter').daterangepicker({
    "showDropdowns": true,
    "autoApply": true,
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    "alwaysShowCalendars": true,
    locale: {
        cancelLabel: 'Clear'
    }
}, function(start, end, label) {
    start_date = start.format('YYYY-MM-DD');
    end_date = end.format('YYYY-MM-DD');
    searchResult();
});
$("#contact-creation-date-filter").val("{{ __('Creation Date Range') }}");

</script>
@endsection
