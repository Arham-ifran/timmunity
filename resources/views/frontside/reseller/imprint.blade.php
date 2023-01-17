@extends('frontside.layouts.redeem-page-app')
@section('title') {{ __('Imprint') }} @endsection
@section('style')
    <link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<style>
        .btn{
            border-color:{{$reseller->color}} !important;
            background-color: {{$reseller->color}} !important;

        }
        ul.nav.navbar-nav.header-nav>li>a:hover {
            background-color: {{$reseller->color}} !important;
        }
        .nav>li>a:hover{
            background-color: {{$reseller->color}} !important;
        }
        ul.nav.navbar-nav.custom-margin.header-nav>li>a:active{
            background-color: {{$reseller->color}} !important;
        }
        .main-header{
            border-top: 1px solid {{$reseller->color}} !important;
        }
        .btn:hover{

           background-color: {{$reseller->color}} !important;
        }
        #voucher-div{
            background-color: {{$reseller->color}};
        }
        .copy-right {
            background-color: {{$reseller->color}} !important;
        }

        .green-line{
            background-color: {{$reseller->color}} !important;
        }
        .alert-success{
            background-color: {{$reseller->color}};
        }
</style>
@endsection
@section('content')

<div class="container">
        <div class="row cloud-row">
            <div class="col-md-12">
                <h3 class="voucher_heading">{{__('Imprint')}}</h3>
            </div>
            <div class="col-md-12">
                {!! translation( $reseller->id,31,app()->getLocale(),'imprint',$content) !!}
                {{-- {!! $content !!} --}}
            </div>
        </div>
    </div>
@endsection
@section('script')
@endsection
