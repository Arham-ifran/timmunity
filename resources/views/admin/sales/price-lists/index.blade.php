@extends('admin.layouts.app')
@section('title', __('Price Lists'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<style>
    #card-view{
        display:none;
    }
    .action-btn{
        display:none;
    }
    table#example1 tr:hover {
        background: #009a7129;
        cursor: pointer;
    }
    div#pricelistTable_paginate {
        float: right;
    }
</style>
@endsection
@section('content')

<div class="content-wrapper">
    <section class="content-header top-header">
        <div class="row">
            <div class="col-md-6">
                <h2>{{ __('Price Lists') }}</h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
              <div class="row">
                  <div class="col-md-4">
                     {{-- <a class="skin-gray-light-btn btn" href="#"><i class="fa fa-upload" aria-hidden="true"></i></a> --}}
                     @can('Add Price List')
                     <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{route('admin.price-lists.create')}}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                     @endcan
                     {{-- <a style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;" class=" btn ml-2" href="#"> <i class="fa fa-download"></i></a> --}}
                  </div>
                  <div class="col-md-4 pull-right pr-0">
                    @canany(['Archive / Unarchive Price List','Delete Price List'])
                        <div class="quotation-right-side">
                          <div class="btn-flat filter-btn dropdown custom-dropdown-buttons action-btn">
                            <i class="fa fa-bars" aria-hidden="true"></i>
                            <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('Actions') }} <span class="caret"></span>
                            </a>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                @can('Archive / Unarchive Price List','')
                                <a class="dropdown-item archive-btn" href="#">{{ __('Archive') }}</a>
                                <a class="dropdown-item un-archive-btn" href="#">{{ __('Unarchive') }}</a>
                                @endcan
                                @can('Delete Price List')
                                <a class="dropdown-item delete-btn" href="#">{{ __('Delete') }}</a>
                                @endcan
                              </div>
                          </div>
                        </div>
                      @endcanany
                  </div>
              </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="box pt-1">
            <div class="row bottom-space" id="card-view">
                @if(isset($pricelists))
                    @foreach($pricelists as $p_list)
                        <div class="col-sm-6 col-md-4">
                            <a href="{{route('admin.price-lists.edit',Hashids::encode($p_list->id))}}">
                                <div class="pro-box row">
                                    <div class="product-content col-sm-8">
                                        <h3 class="product-heading">
                                            {{$p_list->name}}
                                        </h3>
                                    </div>
                                    <div class="mt-2 col-md-4">
                                        <p>{{ $p_list->currency->code }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @else
                    <p class="text-center">{{ __('No record found!') }}</p>
                @endif


            </div>
            <div class="row box-body">
                <table id="pricelistTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Price List Name') }}</th>
                            {{-- <th>{{ __('Currency') }}</th> --}}
                            <th>{{ __('Selectable') }}</th>
                            <th>{{ __('Status') }}</th>
                            @canany(['Edit Price List','Delete Price List'])
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
</div>
<form id="actionForm" action="" method="POST">
    @csrf
    <input type="hidden" name="ids"/>
</form>
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
<script>
    deleteurl = "{{ route('admin.pricelist.delete') }}";
    archiveurl = "{{ route('admin.pricelist.archive') }}";
    unarchiveurl = "{{ route('admin.pricelist.unarchive') }}";
    $('body').on('click','.delete-btn', function(){
        favorite = [];
        $.each($("input[name='pricelistids[]']:checked"), function(){
            favorite.push($(this).val());
        });
        $("input[name=ids]").val(favorite.join(','));
        $("#actionForm").attr('action', deleteurl);
        $("#actionForm").submit();
    });
    $('body').on('click','.archive-btn', function(){
        favorite = [];
        $.each($("input[name='pricelistids[]']:checked"), function(){
            favorite.push($(this).val());
        });
        $("input[name=ids]").val(favorite.join(','));
        $("#actionForm").attr('action', archiveurl);
        $("#actionForm").submit();
    });
    $('body').on('click','.un-archive-btn', function(){
        favorite = [];
        $.each($("input[name='pricelistids[]']:checked"), function(){
            favorite.push($(this).val());
        });
        $("input[name=ids]").val(favorite.join(','));
        $("#actionForm").attr('action', unarchiveurl);
        $("#actionForm").submit();
    });
    $('body').on('click','#tabular-btn',function(){
        $('#tabular-view').show();
        $('#card-view').hide();
    });
    $('body').on('click','#card-btn',function(){
        $('#tabular-view').hide();
        $('#card-view').show();
    });
    $('body').on('click','#example1 input[type=checkbox]',function(){
        if ($('#example1 input[type=checkbox]:checked').length > 0){
            $('.action-btn') .show();
        }else{
            $('.action-btn') .hide();
        }
    });

    $('body').on('click','table#example1 tbody tr', function(){
        window.location = $(this).attr("data-href");
    });
    ajaxurl = "{{ route('admin.price-list.index') }}";
    var table = $('#pricelistTable').DataTable({
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            orderCellsTop: true,
            lengthChange: false,
            responsive: true,
            serverSide: true,
            ajax: ajaxurl,
            "aaSorting": [],
            fnDrawCallback: function(oSettings) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [
                {
                    data: 'name',
                    name: 'name'
                },
                // {
                //     data: 'currency',
                //     name: 'currency'
                // },
                {
                    data: 'selectable',
                    name: 'selectable'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                @canany(['Edit Price List','Delete Price List'])
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
                @endcanany
            ]
        });
        table.columns.adjust().draw();
        var count = 0;
            $('#pricelistTable thead tr').clone(true).appendTo( '#pricelistTable thead' );
            $('#pricelistTable thead tr:eq(1) th').removeClass('sorting')
            $('#pricelistTable thead tr:eq(1) th').each( function (i) {
                if(count < 5) {
                var title = $(this).text();
                $(this).html( '<input class ="form-control" type="text" placeholder="{{__('Search')}} '+{{__('title') }}+'" />' );
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
                else if(count == 9) {
                    $(this).html('');
                }
            } );
</script>
@endsection
