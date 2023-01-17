@extends('admin.layouts.app')
@section('title', __('Product Variants'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<style>
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #499a72;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
    color:white;
}
span.tagged {
    border: 2px solid #009a71;
    border-radius: 20px;
    padding: 0px 5px;
}
#productsTable{
    width: 100%;
}
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header top-header">
        <div class="row">
            <div class="col-md-12">
                <h2>
                    {{ __('Product Variants') }}
                </h2>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Variants -->
                        <table id="productsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr role="row">
                                    <th>{{ __('SKU') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Sales Price') }}</th>
                                    <th>{{ __('Reseller Sales Price') }}</th>
                                    <th>{{ __('Cost') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    @canany(['Edit Product Variant','Import License Keys'])
                                    <th>{{ __('Action') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="import-license-modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Import Keys') }}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
                    </button>
                </div>
                <div class="modal-body ">
                    <div class="row">
                        <form action="{{ route('admin.license.import') }}" method="POST" enctype="multipart/form-data">

                            <input type="hidden" name="product_id">
                            <input type="hidden" name="variation_id">
                            {{ csrf_field() }}
                            <h2 class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="file" accept="csv" multiple="" name="file[]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <input type="submit" class="btn btn-success" value="{{ __('Import License') }}"/>
                                </div>
                            </h2>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
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

    $('body').on('click','.import-modal', function(e){
        e.preventDefault();
        $('#import-license-modal').modal('show');
        $('#import-license-modal [name="product_id"]').val($(this).data('product-id'));
        $('#import-license-modal [name="variation_id"]').val($(this).data('variation-id'));
        $('#import-license-modal').modal('show');
    });
    search_data = []
    $(document).ready(function(){
        var table = $('#productsTable').DataTable({
                "dom": '<"top"fi>rt<"bottom"lp><"clear">',
                orderCellsTop: true,
                lengthChange: false,
                responsive: true,
                serverSide: true,
                stateSave: true,
                stateLoadParams: function(settings, data) {
                    for (i = 0; i < 2; i++) {
                        var col_search_val = data.columns[i].search.search;
                        search_data[i] = col_search_val
                        // if (col_search_val != "") {
                        //     $("input", $("#productsTable thead tr"))[i].val(col_search_val);
                        // }
                    }
                },
                ajax: "{{ route('admin.product-variant.index') }}",
                "aaSorting": [],
                columns: [
                    {
                        data: 'sku',
                        name: 'sku'
                    },
                    {
                        data: 'productName',
                        name: 'productName'
                    },
                    {
                        data: 'sales_price',
                        name: 'sales_price'
                    },
                    {
                        data: 'reseller_sales_price',
                        name: 'reseller_sales_price'
                    },
                    {
                        data: 'cost_price',
                        name: 'cost_price'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    @canany(['Edit Product Variant','Import License Keys'])
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
            $('#productsTable thead tr:eq(1) th').removeClass('sorting')
            $('#productsTable thead tr:eq(1) th').each( function (i) {
                if(count < 2) {
                    var title = $(this).text();
                    $(this).html( '<input value="'+search_data[i]+'"  class ="form-control" type="text" name="" placeholder="{{__('Search')}} '+title+'" />' );
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
            } );
        })
</script>
@endsection
