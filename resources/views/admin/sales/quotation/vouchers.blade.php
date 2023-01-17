@extends('admin.layouts.app')
@section('title', "Vouchers")
@section('styles')
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<style>
    span.tagged {
        border: 3px solid;
        border-radius: 30px;
        padding: 0 10px;
    }
    span.tagged.quote {
        border-color: #f5f91a;
        background: #f5f91a85;
    }
    span.tagged.success {
        border-color: #06f50e;
        background: #06f50e66;
    }
    span.tagged.warning {
        border-color: #f9aa1a;
        background: #f9aa1a8c;
    }
    span.tagged.danger {
        border-color: #f91a1a;
        background: #f91a1a7a;
    }
    table#example1 tr:hover {
        background: #009a7129;
        cursor: pointer;
    }
</style>

@endsection
@section('content')

<div class="content-wrapper">
    <section class="content-header top-header">
        <div class="row">
            <div class="col-md-6"><h2>
                {{ __('Quotation') }}  / {{ __('Vouchers') }}</h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
            </div>
    </section>
    <section class="content">
      <div class="box pt-1">
            <div class="row box-body mt-1">
                <table id="quotationTable" class="table table-bordered table-striped" >
                    <thead>
                        <tr>
                            {{-- <th><input type="checkbox"></th> --}}
                            <th>{{ __('Voucher Code') }}</th>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
          </div>
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
<script>

    var table = $('#quotationTable').DataTable({
        "dom": '<"top"fi>rt<"bottom"lp><"clear">',
        orderCellsTop: true,
        lengthChange: false,
        responsive: true,
        serverSide: true,
        "ajax": {
            "url": '{{route("admin.quotation.voucher.list",$quotation_id)}}',
            "beforeSend": function() {
                if (table && table.hasOwnProperty('settings')) {
                    table.settings()[0].jqXHR.abort();
                }
            }
        },
        // ajax: ajaxurl,
        fnDrawCallback: function(oSettings) {
            $('[data-toggle="popover"]').popover();
            $('[data-toggle="tooltip"]').tooltip();
        },
        columns: [
            {
                data: 'voucher_code',
                name: 'voucher_code'
            },
            {
                data: 'product',
                name: 'product'
            },
            {
                data: 'customer',
                name: 'customer'
            },
            {
                data: 'status',
                name: 'status'
            },
            @canany(['Edit Quotation','View Quotation','Delete Quotation'])
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
            @endcanany
        ]
    });
    table.columns.adjust().draw();
    var count = 0;
    $('#quotationTable thead tr').clone(true).appendTo( '#quotationTable thead' );
    $('#quotationTable thead tr:eq(1) th').removeClass('sorting')
    // $('table.table thead tr').clone(true).appendTo( 'table.table thead' );
    $('#quotationTable thead tr:eq(1) th').each( function (i) {
    // $('table.table thead tr:eq(1) th').each( function (i) {
        if(count < 9) {
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
