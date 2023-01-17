@extends('admin.layouts.app')
@section('title', __('Payment Gateways'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />

@endsection
    <!-- Top Header Section -->
@section('content')
    <div class="content-wrapper">
        <section class="content-header top-header">
            <div class="row">
                <div class="col-md-6">
                    <h2>{{ __('Payment Gateways') }} </h2>
                </div>
            </div>
        </section>
        <section class="content">
            <form class="timmunity-custom-dashboard-form mt-2 form-validate"  action="{{ route('admin.website.payment.gateways.update') }}" method="post">
                <div class="main-box box">
                    <div class="row mt-3">
                        <div class="col-xs-12">
                            <div class="box box-success box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">
                                        Mollie
                                    </h3>
                                    <!-- /.box-tools -->
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mt-2">
                                        <div class="form-group">
                                            <label for="mollie_sandbox_key">{{ __('Mollie Sandbox Key') }}</label>
                                            <input type="text"
                                                class="form-control @error('mollie_sandbox_key') is-invalid @enderror"
                                                id="mollie_sandbox_key" name="mollie_sandbox_key"
                                                value="{{ old('mollie_sandbox_key', $gateways[0]->sandbox_api_key ?? '') }}"
                                                aria-describedby="mollie_sandbox_key"  />
                                            @error('mollie_sandbox_key')
                                                <div id="mollie_sandbox_key-error" class="invalid-feedback animated fadeInDown">
                                                    {{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="mollie_live_key">{{ __('Mollie Live Key') }}</label>
                                            <input type="text"
                                                class="form-control @error('mollie_live_key') is-invalid @enderror"
                                                id="mollie_live_key" name="mollie_live_key"
                                                value="{{ old('mollie_live_key', $gateways[0]->live_api_key ?? '') }}"
                                                aria-describedby="mollie_live_key"  />
                                            @error('mollie_live_key')
                                                <div id="mollie_live_key-error" class="invalid-feedback animated fadeInDown">
                                                    {{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="mollie_mode">{{ __('Mollie Mode') }}</label>
                                            <br>
                                            <label for="mollie_mode">
                                                <input type="radio" @if($gateways[0]->mode == 0) checked="checked" @endif value="0" @error('mollie_mode') is-invalid @enderror name="mollie_mode" required />
                                                {{ __('Sandbox') }}
                                            </label>
                                            <label for="mollie_mode">
                                                <input type="radio" @if($gateways[0]->mode == 1) checked="checked" @endif value="1" @error('mollie_mode') is-invalid @enderror name="mollie_mode" required />
                                                {{ __('Live') }}
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="mollie_status">{{ __('Mollie Status') }}</label>
                                            <br>
                                            <label for="mollie_status">
                                                <input type="radio" @if($gateways[0]->status == 1) checked="checked" @endif value="1" @error('mollie_status') is-invalid @enderror name="mollie_status" required />
                                                {{ __('Active') }}
                                            </label>
                                            <label for="mollie_status">
                                                <input type="radio" @if($gateways[0]->status == 0) checked="checked" @endif value="0" @error('mollie_status') is-invalid @enderror name="mollie_status" required />
                                                {{ __('In Active') }}
                                            </label>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <div class="form-group">
                            <button type="submit" class="skin-green-light-btn btn ">{{ __('Save') }}</button>
                            <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
                                href="{{ route('admin.taxes.index') }}">{{ __('Discard') }}</a>
                        </div>
                    </div>
                </div>
            </form>
        </section>
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
    <script src="{{ asset('backend/bower_components/moment/moment.js') }}"></script>
    <script src="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>


        var table = $("#resellers").DataTable({
            lengthChange: false,
            responsive: true,
            orderCellsTop: true,
            serverSide: true,
            scrollCollapse: true,
            fixedColumns: true,
            // ajax: '{{ route("admin.voucher.orders") }}',
            "ajax": {
                "url": "{{ route('admin.website.resellers') }}",
                "beforeSend": function() {
                    if (table && table.hasOwnProperty('settings')) {
                        table.settings()[0].jqXHR.abort();
                    }
                }
            },
            columns: [

                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'active',
                    name: 'active'
                },
                {
                    data: 'actions',
                    name: 'actions'
                }
            ]
        });


    </script>
@endsection
