@extends('frontside.layouts.app')
@section('title')   @endsection
@section('style')
<style>
    .comingsoon{
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .cs-image{
        /* width: 100%; */
    }
    .coming-soon-content{
        position: absolute;
        text-align: center;
    }
    .coming-soon-content h1{
        font-size: 131.31px;
        text-transform: uppercase;
        color: white;
        font-weight: bold;
    }
    @media screen and (max-width: 1199px) {
        .coming-soon-content h1 {
        font-size: 101.31px;
    }
    }
    @media screen and (max-width: 991px) {
        .coming-soon-content h1 {
        font-size: 81.31px;
    }
    }
    @media screen and (max-width: 767px) {
        .coming-soon-content h1 {
        font-size: 61.31px;
    }
    }
    @media screen and (max-width: 575px) {
        .coming-soon-content h1 {
        font-size: 51.31px;
    }
    }
</style>
@endsection
@section('content')
     <!-- Main -->
    <section class="comingsoon">
        <div class="comingsoon-img">
            <img src="{{asset('frontside/dist/img/coming-bg.png')}}" class="img-fluid cs-image">
        </div>
        <div class="coming-soon-content">
            <h1>{{__('Coming soon')}}</h1>
        </div>
    </section>
@endsection
@section('script')
   <!-- Script Goes Here -->
@endsection
