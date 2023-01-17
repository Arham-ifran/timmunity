@extends('admin.layouts.app')
@section('title', __('Manufacturers'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<style>

    div#productsTable_paginate {
        float: right;
    }
</style>
@endsection
@section('content')

<div class="content-wrapper">
    <section class="content-header top-header">
        <div class="row">
            <div class="col-md-6">
                <h2>
                    {{ __('Manufacturers') }}
                </h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-4">
                        <a class="skin-green-light-btn btn ml-2" href="{{route('admin.manufacturer.create')}}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-md-4 text-center">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
    <section class="content">
        <div class="box pt-1">
            <div class="row box-body">
                <table id="manufacturer_table" class="table table-bordered table-striped">
                    <thead>
                        <tr role="row">
                            <th>{{ __('Manufacturer Name') }}</th>
                            <th>{{ __('Manufacturer Email') }}</th>
                            <th>{{ __('Associated Manufacturer') }}</th>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<div class="product-attribute-modalbox-d modal fade" id="product-attribte-modal"></div>
<form id="actionForm" action="" method="POST">
    @csrf

    <input type="hidden" name="id" id="delete_man" value=""/>
</form>
@endsection
@section('scripts')

<script>

    $(document).ready(function(){

        var table = $('#manufacturer_table').DataTable({
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            orderCellsTop: true,
            lengthChange: false,
            responsive: true,
            serverSide: true,
            // lengthChange: false,
            // responsive: true,
            // serverSide: true,
            // searching:false,

            ajax: "{{ route('admin.manufacturer.index') }}",

            "aaSorting": [],
            columns: [
                {
                    data: 'manufacturer_name',
                    name: 'manufacturer_name'
                },
                {
                    data: 'manufacturer_email',
                    name: 'manufacturer_email'
                },
                {
                    data: 'associated',
                    name: 'associated'
                },
                {
                    data: 'company',
                    name: 'company'
                },

                {
                    data: 'action',
                    name: 'action',

                }

            ]
        });

    });
    deleteurl = "{{ route('admin.manufacturer.delete') }}";

    $('body').on('click','.delete-btn', function(){
       alert()
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('Are you sure that you want to delete this record?') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Yes, delete it!') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $("#delete-man").val(id);
                $("#actionForm").attr('action', deleteurl);
                $("#actionForm").submit();
            }
        });
    });


</script>
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
@endsection
