@extends('admin.layouts.app')
@section('title', __('Visitor Detail'))
@section('styles')
<style>
    span.tagged{
        border:2px solid #ccc;
        border-radius: 20px;
        /* padding:5px 20px; */
        padding:0px 20px;
    }
    span.tagged:hover{
        background: #aaaaaa;
        cursor:pointer;
    }
</style>
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                    {{ __('Visitor Detail') }}/ {{ $visitor->user_name }}

                    </h2>
                </div>
            </div>
        </section>
        <!-- Table content -->
        <section class="content">
            <div class="main-box box">
                <div class="row mt-3">
                    <div class="col-xs-12">
                        <div class="box box-success box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"> {{ __('Visitor Detail') }}</h3>
                                <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3><strong>Website Visitor 2</strong></h3>
                                        <p><strong>Country:</strong> {{ @$visitor->ip_info['country'] }}</p>
                                        {{-- <p><strong>Language:</strong> English</p> --}}
                                    </div>
                                    <div class="col-md-6">
                                        <h3><strong>Visits</strong></h3>
                                        <p><strong>First connection date:</strong> {{ \Carbon\Carbon::parse($visitor->first_visit->created_at)->format('d M Y ') }} </p>
                                        <p><strong>Last connection:</strong> {{ \Carbon\Carbon::parse($visitor->last_visit->created_at)->format('d M Y ') }}</p>
                                        <p><strong>Visited Pages:</strong>
                                            {{-- @foreach($visitor->activities as $key => $activity) --}}
                                            @foreach($visitor->activities()->groupBy('url')->get() as $key => $activity)
                                                @php
                                                    $array = explode('/',$activity->url);
                                                @endphp
                                                @if(strpos($activity->url,'product-detail' ) == false)
                                                    {{-- <span class="tagged" data-toggle="modal" data-target="#visited-pages-modal">{{ end($array) == null || end($array) == '' ? 'Home' : end($array) }}</span> --}}
                                                    <span class="tagged">{{ end($array) == null || end($array) == '' ? 'Home' : end($array) }}</span>
                                                @endif
                                            @endforeach
                                        </p>
                                        <p><strong>Visited Products:</strong>

                                            {{-- @foreach($visitor->activities as $key => $activity) --}}
                                            @foreach($visitor->activities()->groupBy('url')->get() as $key => $activity)
                                                @php
                                                    $array = explode('/',$activity->url);
                                                @endphp
                                                @if(strpos($activity->url,'product-details' ) !== false)
                                                    {{-- <span class="tagged" data-toggle="modal" data-target="#visited-pages-modal">{{ end($array) }}</span> --}}
                                                    <span class="tagged" >{{ end($array) }}</span>
                                                @endif
                                            @endforeach
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="visited-pages-modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Pages Visited') }}
                    </h3>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"
                                aria-hidden="true"></i></span>
                    </button>
                </div>
                <div class="modal-body ">
                    <!-- Form Start Here  -->
                    <div class="row">
                        <div class="col-md-12">
                            <ul>
                                <li><p>/home</p></li>
                                <li><p>/shop</p></li>
                                <li><p>/about</p></li>
                            </ul>
                        </div>
                    </div>
                    <!-- End Here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="visited-products-modal" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Products Visited') }}
                    </h3>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"
                                aria-hidden="true"></i></span>
                    </button>
                </div>
                <div class="modal-body ">
                    <!-- Form Start Here  -->
                    <div class="row">
                        <div class="col-md-12">
                            <ul>
                                <li><p>Avast</p></li>
                                <li><p>Kss</p></li>
                            </ul>
                        </div>
                    </div>
                    <!-- End Here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}">

    </script>
@endsection
