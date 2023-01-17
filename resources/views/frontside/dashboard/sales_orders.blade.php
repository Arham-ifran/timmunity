@extends('frontside.layouts.app')
@section('style')

<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<style>
    #sales-datatable tbody td{
        cursor: pointer;
    }
    #sales-datatable tbody tr:hover{
        background: #009a7140;
    }

</style>
@endsection
@section('content')

    <div class="row dark-green div-breadcrumbs" style="background: #009a71; color: white; padding: 10px;">
        <div class="container">
            <div>
                <a style="color:white;font-weight:500;" href="{{ route('user.dashboard') }}">{{ __('Dashboard') }}</a> /
                {{ __('Sales Orders') }}
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
                            <h3>{{ __('Sales Orders') }}</h3>
                            <table id="sales-datatable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Sales Order #') }}</th>
                                        <th>{{ __('Order Date') }}</th>
                                        <th>{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach($sales_orders as $sales_order)
                                    <tr>
                                        <td><a href="{{ route('user.dashboard.quotations.detail') }}">S{{ str_pad($sales_order->id, 5, '0', STR_PAD_LEFT) }}</a></td>
                                        <td>{{ \Carbon\Carbon::parse($sales_order->created_at)->format('d/M/Y') }}</td>
                                        <td>{{ $sales_order->total }}</td>
                                    </tr>
                                    @endforeach --}}
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
    var table = $('#sales-datatable').DataTable({
            lengthChange: false,
            responsive: true,
            serverSide: true,
            orderCellsTop:true,
            scrollCollapse: true,
            "order": [[ 1, "desc" ]],
            fixedColumns: true,
            ajax: '{{ route("user.dashboard.sales_order") }}',
            columns: [
                {
                    data: 'ordernumber',
                    name: 'ordernumber'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'total',
                    name: 'total'
                }
            ]
        });

        $('#sales-datatable tbody').on('click', 'td', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            document.location.href = row.data().link;
        } );
</script>
@endsection

