@extends('admin.layouts.app')
@section('title', 'Plans')
@section('styles')
<link href="{{ asset('admin\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('admin\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('admin\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('admin\css\loader.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
<div class="breadcrumbbar">
    <div class="loader-parent" id="ajax_loader">
         <div class="loader">
             <div class="square"></div>
             <div class="path">
                 <div></div>
                 <div></div>
                 <div></div>
                 <div></div>
                 <div></div>
                 <div></div>
                 <div></div>
             </div>
         </div>
     </div>
    <div class="row align-items-center">
        <div class="col-md-8 col-lg-8">
            <h4 class="page-title">
                Plans
            </h4>
            <div class="breadcrumb-list">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Plans
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-sm table-bordered" style="width: 100%;">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Cost</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('admin\plugins\datatables\jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin\plugins\datatables\dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin\plugins\datatables\dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('admin\plugins\datatables\buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin\plugins\datatables\jszip.min.js') }}"></script>
<script src="{{ asset('admin\plugins\datatables\pdfmake.min.js') }}"></script>
<script src="{{ asset('admin\plugins\datatables\vfs_fonts.js') }}"></script>
<script src="{{ asset('admin\plugins\datatables\buttons.html5.min.js') }}"></script>
<script src="{{ asset('admin\plugins\datatables\buttons.print.min.js') }}"></script>
<script src="{{ asset('admin\plugins\datatables\buttons.colVis.min.js') }}"></script>
<script src="{{ asset('admin\plugins\datatables\dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('admin\plugins\datatables\responsive.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#datatable-buttons').DataTable({
            lengthChange: false,
            responsive: true,
            serverSide: true,
            scrollX: true,
            ajax: "{{ route('user.plans.index') }}",
            fnDrawCallback: function( oSettings ) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [
            {
                data: 'id',
                name: 'id'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'cost',
                name: 'cost'
            },
            {
                data: 'description',
                name: 'description'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
            ]
        });
    });
</script>
@endsection
