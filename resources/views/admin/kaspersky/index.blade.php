@extends('admin.layouts.app')
@section('title',  __('F Secure'))
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header">
      <div class="row">
         <div class="col-md-6">
            <h2>
               F-Secure Subscription
            </h2>
         </div>
      {{--    <div class="col-md-6">
            <div class="search-input-das">
               <form>
                  <input type="text" name="search" placeholder="Search...">
               </form>
            </div>
         </div> --}}
      </div>
      <div class="row">
         <div class="box-header">
            <div class="row">
               <div class="col-md-4 pl-0">
                  <a class="skin-green-light-btn btn ml-2" href="{{ route('admin.f-secure.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                  <a style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;" class=" btn ml-2" href="#"> <i class="fa fa-download"></i></a>
               </div>
               {{-- <div class="col-md-4 text-center">
                  <div class="quotation-right-side">
                     <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                        <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Filters <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                           <a class="dropdown-item" href="#">Action</a>
                           <a class="dropdown-item" href="#">Another action</a>
                           <a class="dropdown-item" href="#">Something</a>
                        </div>
                     </div>
                     <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                        <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Group By <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                           <a class="dropdown-item" href="#">Action</a>
                           <a class="dropdown-item" href="#">Another action</a>
                           <a class="dropdown-item" href="#">Something</a>
                        </div>
                     </div>
                     <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                        <i class="fa fa-star" aria-hidden="true"></i>
                        <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Favorites <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                           <a class="dropdown-item" href="#">Action</a>
                           <a class="dropdown-item" href="#">Another action</a>
                           <a class="dropdown-item" href="#">Something</a>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <ol class="breadcrumb pages-arrow pull-right">
                     <!-- <li><a href="#"> 1-8</a></li>
                        <li class="active">1</li> -->
                     <!-- <a href="#"> <i class="fa fa-angle-left"> </i></a>
                        <a href="#"><i class="fa fa-angle-right"> </i></a> -->
                     <a href="#"><i class="fa fa-th"> </i></a>
                     <a href="#"><i class="fa fa-bars"> </i></a>
                  </ol>
               </div> --}}
            </div>
         </div>
      </div>
   </section>
   <!-- Table content -->
   <section class="content kss-subscription-box-sections">
      <div class="row">
         <div class="col-md-3">
            <div class="box box-success">
               <div class="box-header with-border">
                  <h3 class="box-title">Active</h3>
                  <div class="box-tools pull-right">
                     <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                     </button>
                  </div>
               </div>
               <div class="box-body">
                  @php $count = ''; @endphp
                  @foreach ($all_subscriptions as $subscription)
                    @if($subscription->end_date > date('Y-m-d') && $subscription->status == 1)
                    @php $count = 1; @endphp
                     <a href="{{ route('admin.f-secure.show',['f_secure'=> Hashids::encode($subscription->id)]) }}">
                        <div class="kss-subscription-body-box">
                           <h3 class="sub-heading">{{ @$subscription->partners->name ?? '' }}</h3>
                           <h3 class="dynamic-heading">{{ @$subscription->license_key }}</h3>
                           <span class="caption">{{ @$subscription->products->product_name.'('. $value .',' . $duration .' '. $type.')' }}</span>
                           <span class="date">From {{date('F jS, Y', strtotime($subscription->start_date))}} To {{date('F jS, Y', strtotime($subscription->end_date))}}</span>
                        </div>
                     </a>
                     @endif
                  @endforeach
                  @if($count == '')
                     <div class="kss-subscription-empty-box">
                        <h3>No Active License Here</h3>
                     </div>
                  @endif
               </div>
            </div>
         </div>
         <div class="col-md-3">
            <div class="box box-success">
               <div class="box-header with-border">
                  <h3 class="box-title">Soft Cancelled</h3>
                  <div class="box-tools pull-right">
                     <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                     </button>
                  </div>
               </div>
               <div class="box-body">
                  @php $count = ''; @endphp
                  @foreach ($all_subscriptions as $subscription)
                    @if($subscription->status == 3 || $subscription->status == 5)
                    @php $count = 1; @endphp
                     <a href="{{ route('admin.f-secure.show',['f_secure'=> Hashids::encode($subscription->id)]) }}">
                        <div class="kss-subscription-body-box">
                           <h3 class="sub-heading">{{ $subscription->partners->name ?? '' }}</h3>
                           <h3 class="dynamic-heading">{{ $subscription->license_key }}</h3>
                           <span class="caption">{{ @$subscription->products->product_name.'('. $value .',' . $duration .' '. $type.')' }}</span>
                           <span class="date">From {{date('F jS, Y', strtotime($subscription->start_date))}} To {{date('F jS, Y', strtotime($subscription->end_date))}}</span>
                        </div>
                     </a>
                     @endif
                  @endforeach
                  @if($count == '')
                     <div class="kss-subscription-empty-box">
                        <h3>No Soft Cancel License Here</h3>
                     </div>
                  @endif
               </div>
            </div>
         </div>
         <div class="col-md-3">
            <div class="box box-success">
               <div class="box-header with-border">
                  <h3 class="box-title">Hard Cancelled</h3>
                  <div class="box-tools pull-right">
                     <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                     </button>
                  </div>
               </div>
               <div class="box-body">
                  @php $count = ''; @endphp
                  @foreach ($all_subscriptions as $subscription)
                    @if($subscription->status == 4)
                    @php $count = 1; @endphp
                     <a href="{{ route('admin.f-secure.show',['f_secure'=> Hashids::encode($subscription->id)]) }}">
                        <div class="kss-subscription-body-box">
                           <h3 class="sub-heading">{{ $subscription->partners->name ?? '' }}</h3>
                           <h3 class="dynamic-heading">{{ $subscription->license_key }}</h3>
                           <span class="caption">{{ @$subscription->products->product_name.'('. $value .',' . $duration .' '. $type.')' }}</span>
                           <span class="date">From {{date('F jS, Y', strtotime($subscription->start_date))}} To {{date('F jS, Y', strtotime($subscription->end_date))}}</span>
                        </div>
                     </a>
                     @endif
                  @endforeach
                  @if($count == '')
                     <div class="kss-subscription-empty-box">
                        <h3>No Hard Cancel License Here</h3>
                     </div>
                  @endif
               </div>
            </div>
         </div>
         <div class="col-md-3">
            <div class="box box-success">
               <div class="box-header with-border">
                  <h3 class="box-title">Expired</h3>
                  <div class="box-tools pull-right">
                     <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                     </button>
                  </div>
               </div>
               <div class="box-body">
                  @php $count = ''; @endphp
                  @foreach ($all_subscriptions as $subscription)
                    @if($subscription->end_date < date('Y-m-d') && $subscription->status == 1)
                    @php $count = 1; @endphp
                     <a href="{{ route('admin.f-secure.show',['f_secure'=> Hashids::encode($subscription->id)]) }}">
                        <div class="kss-subscription-body-box">
                           <h3 class="sub-heading">{{ $subscription->partners->name }}</h3>
                           <h3 class="dynamic-heading">{{ $subscription->license_key }}</h3>
                           <span class="caption">{{ @$subscription->products->product_name.'('. $value .',' . $duration .' '. $type.')' }}</span>
                           <span class="date">From {{date('F jS, Y', strtotime($subscription->start_date))}} To {{date('F jS, Y', strtotime($subscription->end_date))}}</span>
                        </div>
                     </a>
                     @endif
                  @endforeach
                  @if($count == '')
                     <div class="kss-subscription-empty-box">
                        <h3>No Expired License Here</h3>
                     </div>
                  @endif
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- /.content -->
</div>
@endsection
