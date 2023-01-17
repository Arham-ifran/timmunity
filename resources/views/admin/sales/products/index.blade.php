@extends('admin.layouts.app')
@section('title', __('Products'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<style>

    div#productsTable_paginate {
        float: right;
    }
    .dropdown-menu.export-drop {
        min-width: auto;
        padding: 0;
        margin-left: 65px;
    }
    .dropdown-menu.export-drop ul {
        padding: 0px;
        margin: auto;
        list-style: none;
    }
    .dropdown-menu.export-drop ul li {
        padding: 5px 15px;
    }
    .dropdown-menu.export-drop ul li:hover, .dropdown-menu.export-drop ul li:hover > a {
        color: white;
        background: #009a71;
    }
</style>
@endsection
@section('content')

<div class="content-wrapper">
    <section class="content-header top-header">
        <div class="row">
            <div class="col-md-6">
                <h2>
                    {{ __('Products') }}
                </h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
                @can('Add Product')
                <div class="row">
                    <div class="col-md-8">
                        <a class="skin-green-light-btn btn ml-2" href="{{ route('admin.products.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                     
                            @if(env('APP_ENV') == "production")
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Export Product Feed                            </button>
                                <div class="dropdown-menu export-drop" aria-labelledby="dropdownMenuButton">
                                    <ul>
                                        <li>
                                            <a class="dropdown-item archive-btn" href="{{route('admin.channel-pilot.export.products.data','xlsx')}}">{{ __('Excel Format') }}</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item archive-btn" href="{{route('admin.channel-pilot.export.products.data','csv')}}">{{ __('CSV Format') }}</a>
                                        </li>
                                    </ul>
                                </div>
                               
                                <a href="{{route('admin.channel-pilot-export-feed')}}" target="_blank" id="exportBtn" class="btn btn-primary ">Export Feed To Channel pilot</a>
                            @endif
                    </div>
                    <div class="col-md-4 text-center">
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </section>
    <section class="content">
        <div class="box pt-1">
            <div class="row box-body">
                <table id="productsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr role="row">
                            {{-- <th style="width: 32px;"></th> --}}
                            <th>{{ __('Product Name') }}</th>
                            <th>{{ __('Internal Reference') }}</th>
                            <th>{{ __('Sales Price') }}</th>
                            <th>{{ __('Cost Price') }}</th>
                            <th>{{ __('Display Order') }}</th>
                            <th>{{ __('Status') }}</th>
                            @canany(['Edit Product','Delete Product'])
                            <th>{{ __('Action') }}</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection
@section('scripts')

<script>
    $(document).ready(function(){
        search_data = ['','','','','',''];
        var table = $('#productsTable').DataTable({
                "dom": '<"top"fi>rt<"bottom"lp><"clear">',
                orderCellsTop: true,
                lengthChange: false,
                stateSave: true,
                responsive: true,
                serverSide: true,
                stateSave: true,
                stateLoadParams: function(settings, data) {
                    for (i = 0; i < 6; i++) {
                        var col_search_val = data.columns[i].search.search;
                        search_data[i]=col_search_val
                        if (col_search_val != "") {
                            $("input", $("#productsTable thead tr")[i]).val(col_search_val);
                        }
                    }
                },
                ajax: "{{ route('admin.products.index') }}",
                fnDrawCallback: function(oSettings) {
                    $('[data-toggle="popover"]').popover();
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "aaSorting": [],
                columns: [
                    {
                        data: 'productName',
                        name: 'productName'
                    },
                    {
                        data: 'internal_reference',
                        name: 'internal_reference'
                    },
                    {
                        data: 'sales_price',
                        name: 'sales_price'
                    },
                    {
                        data: 'cost_price',
                        name: 'cost_price'
                    },
                    {
                        data: 'order_number',
                        name: 'order_number'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    @canany(['Edit Product','Delete Product'])
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                    @endcanany
                ]
            });
            // table.columns.adjust().draw();
            var count = 0;
            $('#productsTable thead tr').clone(true).appendTo( '#productsTable thead' );
            $('#productsTable thead tr:eq(1) th').removeClass('sorting');
            $('#productsTable thead tr:eq(1) th').each( function (i) {
                if(count < 6) {
                    var title = $(this).text();
                    $(this).html( '<input value="'+search_data[i]+'" class ="form-control" type="text" placeholder="{{__('Search')}} '+{{__('title') }}+'" />' );
                    $( 'input', this ).on( 'keyup change', function () {
                        if ( table.column(i).search() !== this.value ) {
                            table
                                .column(i)
                                .search( this.value )
                                .draw();
                        }
                    } );
                    count++;
                }
                else  {
                    $(this).html('');
                }
            });
        })


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
