@extends('frontside.layouts.app')
@section('style')

<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<style>

#invoice-datatable tbody td{
    cursor: pointer;
}
#invoice-datatable tbody tr:hover{
    background: #009a7140;
}
</style>
@endsection
@section('content')

    <div class="row dark-green div-breadcrumbs" style="background: #009a71; color: white; padding: 10px;">
        <div class="container">
            <div>
                <a style="color:white;font-weight:500;" href="{{ route('user.dashboard') }}">{{ __('Dashboard') }}</a> /
                {{ __('Invoices') }}
            </div>
        </div>
    </div>
    <section class="content-section" id="account-page">
        <div class="container">
            <div class="mt-2 row bottom-space">
                <div class="container">
                    <div class="col-md-2"></div>
                    <div class="col-lg-8">
                        <div class="row">
                            <h3 class="invoice-heading">{{ __('Invoices') }}</h3>
                                <table id="invoice-datatable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Invoice #') }}</th>
                                            <th>{{ __('Invoice Date') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('script')

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
    var table = $('#invoice-datatable').DataTable({
            lengthChange: false,
            responsive: true,
            serverSide: true,
            orderCellsTop:true,
            scrollCollapse: true,
            scrollX: true,
            "order": [[ 1, "desc" ]],
            fixedColumns: true,
            ajax: '{{ route("user.dashboard.invoices") }}',
            columns: [
                {
                    data: 'invoicenumber',
                    name: 'invoicenumber'
                },
                {
                    data: 'invoicedate',
                    name: 'invoicedate'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'invoice_total',
                    name: 'invoice_total'
                }
            ]
        });

        $('#invoice-datatable tbody').on('click', 'td', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            document.location.href = row.data().link;
        } );
</script>
@endsection
