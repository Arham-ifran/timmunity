@extends('admin.layouts.app')
@section('title', __('License Dashboard'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header top-header">
            <div class="row" style="padding-bottom: 15px;">
                <div class="col-md-4">
                    <h2>
                        {{ __('License Dashboard') }}
                    </h2>
                </div>
                <div class="col-md-4">
                </div>
                <div class="col-md-4 pt-2">
                    <a href="{{route('admin.license.download.report')}}" target="_blank" id="exportBtn" class="btn btn-primary mt-2">Download Report</a>
                </div>
            </div>
        </section>

        <section class="content kks-subscription-box-sections">

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-default">
                        <div class="box-body" style="">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="small-box bg-aqua">
                                        <div class="inner" style="padding: 17px;">
                                            <h3>{{ $total_licenses }}</h3>
                                            <p>{{ __('Total Licenses') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-stats-bars"></i>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small-box bg-yellow">
                                        <div class="inner" style="padding: 17px;">
                                            <h3>{{ $used_licenses }}</h3>

                                            <p>{{ __('Used Licenses') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-stats-bars"></i>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small-box bg-green">
                                        <div class="inner" style="padding: 17px;">
                                            <h3>{{ $un_used_licenses - $expired_licenses }}</h3>

                                            <p>{{ __('Un-used Licenses') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-stats-bars"></i>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small-box bg-green">
                                        <div class="inner" style="padding: 17px;">
                                            <h3>{{ $expired_licenses }}</h3>

                                            <p>{{ __('Expired Licenses') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-stats-bars"></i>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>

            </div>
        </section>

        <!-- /.content -->
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('backend\plugins\datatables\jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\jszip.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\pdfmake.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\vfs_fonts.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\buttons.html5.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\buttons.print.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>

    </script>
@endsection
