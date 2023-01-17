@extends('frontside.layouts.app')
@section('title')   {{ $page_details->meta_title }} @endsection
@section('style')
@endsection
@section('content')
<div class="container cms-page-des">
   {!! translation( $page_details->id,22,app()->getLocale(),'description',$page_details->description) !!}
</div>
@endsection
@section('script')
@endsection
