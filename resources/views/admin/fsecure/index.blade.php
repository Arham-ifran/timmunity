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
    </div>
    <div class="row">
        <div class="box-header">
            <div class="row">
            <div class="col-md-4 pl-0">
                <!-- <a class="skin-green-light-btn btn ml-2" href="{{ route('admin.f-secure.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a> -->
                {{-- <a style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;" class=" btn ml-2" href="#"> <i class="fa fa-download"></i></a> --}}
            </div>
            </div>
        </div>
    </div>
</section>
<!-- Table content -->
<section class="content kss-subscription-box-sections">
    <div class="row">
        <div class="col-md-4">
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
                @foreach ($licenses as $license)
                    @if($license->status == 1)
                        @php $count = 1; @endphp
                        <div class="kss-subscription-body-box">
                            <h3 class="sub-heading">{{ @$license->partners->name ?? '' }}</h3>
                            <h3 class="dynamic-heading">{{ @$license->license_key }}</h3>
                            <span class="caption">{{ @$license->product->product_name.' '.@$license->variation->variation_name }}</span>
                            <button data-id="{{ @$license->product_id }}" data-value="{{ @$license->license_key }}" class="btn btn-danger calcel_license"> Cancel License</button>
                        </div>
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

        <div class="col-md-4">
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
                @foreach ($licenses as $license)
                    @if($license->status == 3 && $license->expired != 1)
                        @php $count = 1; @endphp
                        {{-- <a href="{{ route('admin.f-secure.show',['f_secure'=> Hashids::encode($license->id)]) }}"> --}}
                            <div class="kss-subscription-body-box">
                                <h3 class="sub-heading">{{ $license->partners->name ?? '' }}</h3>
                                <h3 class="dynamic-heading">{{ $license->license_key }}</h3>
                            <span class="caption">{{ @$license->product->product_name.' '.@$license->variation->variation_name }}</span>
                            </div>
                        {{-- </a> --}}
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
        <div class="col-md-4">
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
                @foreach ($licenses as $license)
                    @if( $license->expired == 1)
                        @php $count = 1; @endphp
                        {{-- <a href="{{ route('admin.f-secure.show',['f_secure'=> Hashids::encode($license->id)]) }}"> --}}
                        <div class="kss-subscription-body-box">
                            <h3 class="dynamic-heading">{{ $license->license_key }}</h3>
                            <span class="caption">{{ @$license->product->product_name.' '.@$license->variation->variation_name }}</span>
                        </div>
                        {{-- </a> --}}
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

@section('scripts')
<script>
$(document).ready(function() {
$(document).on('click', '.calcel_license', function(e) {
    var product_id = $(this).data('id');
    var licenseKey = $(this).data('value');
    var title = "Warning";
    var custom_swt_alert_text = "Are you sure?";
    var custom_swt_alert_confim_btn_text = "Yes";
    var custom_swt_alert_cancel_btn_text = "No";
        Swal.fire({
            title: title,
            text: custom_swt_alert_text,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: custom_swt_alert_confim_btn_text,
            cancelButtonText: custom_swt_alert_cancel_btn_text,
            closeOnConfirm: false,
            closeOnCancel: true
        }).then((result) => {
            / Read more about isConfirmed, isDenied below /
            if (result.isConfirmed) {
            $.ajax({
                url: 'f-secure/cancel-license/{id}',
                type: 'GET',
                dataType: 'json',
                data: {
                    id: product_id,
                    licenseKey: licenseKey
                },
                success: function (response) {
                    swal.fire("Success!", "License has been cancelled successfully!", "success");
                    setTimeout(() => {
                        location.reload();
                    }, 2000);

                },
                error: function (err) {
                    console.log(err);
                }
            });
            } else if (result.isDenied) {
            // Swal.fire('Changes are not saved', '', 'info')
            }
        });

    });
});

</script>
@endsection
