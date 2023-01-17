@extends('admin.layouts.app')
@section('title', $breadcrum)
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
    .select2-selection.select2-selection--single{
        display: block;
        width: 100%;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
        box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
        -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
        -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        border-radius: 0;
        box-shadow: none;
        border-color: #d2d6de;
    }
    .btn.btn-secondary.buttons-html5 {
        border: 1px solid #009a71;
    }
    .btn.btn-secondary.buttons-html5:hover {
        border: 1px solid #009a71;
        background: #009a71;
        color: #fff;
    }
</style>

@endsection
@section('content')

<div class="content-wrapper">
    <section class="content-header top-header">
        <div class="row">
            <div class="col-md-6"><h2>
                {{ __('Sales Dashboard') }}  / {{ $breadcrum }}</h2>
            </div>
        </div>
        <div class="row">
            <div class="box-header">
                <div class="row">
                    @can('Create Quotation')
                    <div class="col-md-4">
                        <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{route('admin.quotations.create')}}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-2"></div>
                    <div class="col-md-2 pull-right">
                        <div class="form-group text-right">
                            <select class="form-control" name="currency" id="currency">
                                <option value="">{{ __('All Currencies') }}</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency }}">{{ $currency }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
    </section>
    <section class="pt-2">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <input type="text" id="quotation_number" class="form-control" name="quotation_number" placeholder="Enter Quotation Number" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <input type="text" name="customer-name-email" id="customer-name-email" placeholder="Enter Customer Name/Email " value="" autocomplete="off" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <select name="product_id" id="product_id" class="form-control">
                        <option value="">---Select Product---</option>
                        @foreach($products as $ind => $product)
                            <option
                                data-variation_id="{{ $product['variation_id'] }}"
                                data-product_id="{{ $product['product_id'] }}"
                                >
                                {{ $product['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if (@$sales_order == true)
                <div class="col-md-3">
                    <div class="form-group">
                        <select name="invoice_status" class="form-control">
                            <option value="">---Select Invoice Status---</option>
                            <option value="0">Not Created</option>
                            <option value="1">Partially Paid</option>
                            <option value="2">Full Paid</option>
                            <option value="3">Unpaid</option>
                            <option value="4">Refunded</option>
                        </select>
                    </div>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <input type="text" id="amount" class="form-control" name="amount" placeholder="Enter Quotation Amount" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <select name="country_id" id="country_id" class="form-control">
                        <option value="">--- Select Country ---</option>
                        @foreach ($countries as $country)
                            <option value="{{$country->id}}">{{ $country->name.' VAT ('.$country->vat_in_percentage.'%)' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if (@$sales_order == true)
                <div class="col-md-3">
                    <div class="form-group">
                        <select name="payment" id="payment" class="form-control">
                            <option value="">--- Select Payment Type ---</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Online Payment">Online Payment</option>
                            <option value="Cash">Cash Payment</option>

                        </select>
                    </div>
                </div>
            @endif
        </div>
    </section>
    <section class="content">
      <div class="box pt-1">
            <div class="row box-body mt-1">
                <table id="quotationTable" class="table table-bordered table-striped" >
                    <thead>
                        <tr>
                            {{-- <th><input type="checkbox"></th> --}}
                            <th>{{ __('Order Number') }}</th>
                            <th>{{ __('Creation Date') }}</th>
                            <th>{{ __('Delivery Date') }}</th>
                            <th>{{ __('Expected Date') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Salesperson') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Invoice Status') }}</th>
                            @canany(['Edit Quotation','View Quotation','Delete Quotation'])
                            <th class="js-not-exportable">{{ __('Actions') }}</th>
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
    var ajax_data = [];
    ajax_data['currency'] = '';
    ajax_data['quotation_number'] = '';
    ajax_data['customer_name_email'] = '';
    ajax_data['product_id'] = '';
    ajax_data['variation_id'] = '';
    ajax_data['invoice_status'] = '';
    ajax_data['amount'] = '';
    ajax_data['country_id'] = '';
    ajax_data['payment'] = '';
    @if(isset($sales_order))
        @if($sales_order == true)
            ajaxurl = "{{ route('admin.quotation.sales.orders') }}";
        @else
            ajaxurl = "{{ route('admin.quotations.index') }}";
        @endif
    @elseif(isset($order_to_invoice))
        @if($order_to_invoice == true)
            ajaxurl = "{{ route('admin.quotation.sales.orders.toinvoice') }}";
        @else
            ajaxurl = "{{ route('admin.quotations.index') }}";
        @endif
    @else
        ajaxurl = "{{ route('admin.quotations.index') }}";
    @endisset
    // $(document).ready(function() {
    //     $('#product_id').select2();
    // });

    var table = $('#quotationTable').DataTable({
        // "dom": '<"top"fi>rt<"bottom"lp><"clear">',
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf'
        ],
        searching: false,
        stateSave: true,
        orderCellsTop: true,
        lengthChange: false,
        responsive: true,
        serverSide: true,
            "order": [[ 1, "desc" ]],
        scrollX: true,
        "ajax": {
            "url": ajaxurl,
            "data": function(d){
                d.currency = ajax_data['currency'];
                d.quotation_number = ajax_data['quotation_number'];
                d.customer_name_email = ajax_data['customer_name_email'];
                d.product_id = ajax_data['product_id'];
                d.variation_id = ajax_data['variation_id'];
                d.invoice_status = ajax_data['invoice_status'];
                d.amount = ajax_data['amount'];
                d.country_id = ajax_data['country_id'];
                d.payment = ajax_data['payment'];
            },
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
        columnDefs:[
            {width:'10%', targets:0},
            {width:'10%', targets:1},
            {width:'10%', targets:2},
            {width:'10%', targets:3},
            {width:'10%', targets:4},
            {width:'10%', targets:5},
            {width:'10%', targets:6},
            {width:'12%', targets:7},
            {width:'27%', targets:8},
            @canany(['Edit Quotation','View Quotation','Delete Quotation'])
            {width:'15%', targets:9}
            @endcanany
        ],
        columns: [
            {
                data: 'ordernumber',
                name: 'ordernumber'
            },
            {
                data: 'creationdate',
                name: 'creationdate'
            },
            {
                data: 'deliverydate',
                name: 'deliverydate'
            },
            {
                data: 'expecteddate',
                name: 'expecteddate'
            },
            {
                data: 'customer',
                name: 'customer'
            },
            {
                data: 'salesperson',
                name: 'salesperson'
            },
            {
                data: 'total',
                name: 'total'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'invoicestatus',
                name: 'invoicestatus'
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
    // table.columns.adjust().draw();
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
    // Selecting the Currency Start
    $('body').on('change', 'select[name=currency]', function(){
        ajax_data['currency'] = $(this).val();
        table.ajax.reload();
    });
    // Selecting the Cuurency End
    // Selecting the Country Start
    $('body').on('change', 'select[name=country_id]', function(){
        ajax_data['country_id'] = $(this).val();
        table.ajax.reload();
    });
    // Selecting the Country End
    // Selecting the Invoice Status
    $('body').on('change', 'select[name=invoice_status]', function(){
        ajax_data['invoice_status'] = $(this).val();
        table.ajax.reload();
    });
    // Selecting the Invoice Status
    // Searching the Order Number
    $('body').on('input', 'input[name=quotation_number]', function(){
        ajax_data['quotation_number'] = $(this).val();
        table.ajax.reload();
    });
    // Selecting the Cuurency End
    // Searching the amount
    $('body').on('input', 'input[name=amount]', function(){
        ajax_data['amount'] = $(this).val();
        table.ajax.reload();
    });
    // Selecting the amount End
    // Selecting the product Start
    $('body').on('change', 'select[name=product_id]', function(){
        ajax_data['product_id'] = $('option:selected',this).attr('data-product_id');
        ajax_data['variation_id'] = $('option:selected',this).attr('data-variation_id');
        table.ajax.reload();
    });
    // Selecting the product End
    // Selecting the payment type Start
    $('body').on('change', 'select[name=payment]', function(){
        ajax_data['payment'] = $(this).val();
        table.ajax.reload();
    });
    // Selecting the payment type End
    // Input the Name/Email Start
    $('body').on('input', 'input[name=customer-name-email]', function(){
        ajax_data['customer_name_email'] = $(this).val();
        table.ajax.reload();
    });
    // Input the Name/Email End
</script>
@endsection
