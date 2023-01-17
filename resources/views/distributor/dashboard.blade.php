@extends('distributor.layouts.app')
@section('title', __('Distributor dashboard'))
@section('styles')
    <link rel="stylesheet" href="{{ asset('backend/bower_components//morris.js/morris.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/bower_components/jvectormap/jquery-jvectormap.css') }}">
    <link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
    <style>
        .info-box .small-box-footer {
            position: relative;
            text-align: center;
            padding: 3px 0;
            color: #fff;
            color: rgba(255,255,255,0.8);
            display: block;
            z-index: 10;
            background: rgba(0,0,0,0.1);
            text-decoration: none;
        }
        .info-box:hover .small-box-footer {
            color: #fff;
            background: rgba(0,0,0,0.15);
        }
        .info-box:hover .ion{
            font-size: 55px;
        }
        .info-box .ion{
            transition: all .3s linear;
        }
        </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="loader-parent" id="ajax_loader">
            <div class="loader">
            <div class="square"></div>
                <div class="path">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                </div>
            </div>
        </div>
        <!-- Content Header (Page header) -->
        <section class="content-header top-header dashboard-top">
            <div class="row">
                <div class="col-md-4">
                    <h2>
                        {{ __('Distributor Dashboard') }}
                    </h2>
                </div>
                <div class="col-md-4">
                </div>
            </div>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3 id="quotation_count">{{ count($distributor_products)}}</h3>
                            <p>{{ __('Total Products') }} </p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-quote"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3 id="active_count">{{count($active_distributor_products)}}</h3>
                            <p>{{ __('Active Products') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3 id="inactive_count">{{count($inactive_distributor_products)}}</h3>
                            <p>{{ __('In Active Products') }} </p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ __('Products By Distributor') }}</h3>
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th>{{__('Product Name')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Extra Price')}}</th>
                                </tr>
                                @foreach ($distributor_products as $product_detail)
                                    <tr>
                                        <td>{{$product_detail->product->product_name}}</td>
                                        <td>
                                            <select readonly name="product_status[{{$product_detail->product->id}}]"  class="product_status form-control">
                                                <option value="1" @if($product_detail->is_active == 1) selected="selected" @endif>Active</option>
                                                <option value="0" @if($product_detail->is_active == 0) selected="selected" @endif>In-Active</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input readonly  type="number" class="form-control" name="extra_price[{{$product_detail->product->id}}]" value="{{$product_detail->extra_price}}" step="0.01" min='0' >
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <div class="box-body">
                        </div>
                    </div>
                </div>



        </section>
        <!-- /.content -->
    </div>
@endsection
@section('scripts')

    <script src="{{ asset('backend/bower_components/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('backend/bower_components/morris.js/morris.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('backend/bower_components/jquery-knob/dist/jquery.knob.min.js') }}"></script>

@endsection
