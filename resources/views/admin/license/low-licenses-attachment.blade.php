@extends('frontside.layouts.app')
@section('style')
<style>
    header,footer{
        display:none;
    }
    .content,body,html{
        background: white;
    }
    table thead,table thead tr{
        background-color: #009A71;
    }
    table thead th{
        color: white;
    }
    .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
        border: 1px solid #0000004f;
    }
    .copy-right {
        position: fixed; 
        bottom: 0px; 
        left: 0px; 
        right: 0px;
    }
</style>
@endsection
<!-- Top Header Section -->
@section('content')

<section class="content">
    <div class="container">
        <div class="row text-center mt-5">
            <div class="col-md-12">
                <img src="{{ checkImage(asset('storage/uploads/' . $site_settings[0]->site_logo),'logo.png') }}" alt="">
            </div>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                {{-- <div class="row">
                    <div class="col-md-12">
                        <p><strong>Total Un-used Licenses remaining :</strong>50</p>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-12">
                        <table class="table  table-bordered  no-footer dataTable">
                            <thead style="background: #009A71 !important;">
                                <tr style="background: #009A71 !important;">
                                    <th style="background: #009A71 !important;">Product</th>
                                    <th style="background: #009A71 !important;">License Count</th>
                                    <th style="background: #009A71 !important;">Notification Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($low_licensed_products as $product)

                                    <tr>
                                        <td>
                                            {{ @$product->product }}
                                        </td>
                                        <td>
                                            {{ $product->available_license_count }}
                                        </td>
                                        <td>
                                            {{ $product->flag }}
                                        </td>
                                    </tr>


                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-2">

            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>

</script>
@endsection
