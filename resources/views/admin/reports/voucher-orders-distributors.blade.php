@extends('admin.layouts.app')
@section('title', __('Reports'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />

<style>
   .nav.navbar-nav li a {
        padding-right: 15px;
        padding-left: 15px;
    }
    input#date_filter,
    select[name=product],
    select[name=status],
    input[name=name-email]{
        border: none;
    }
    .ranges li {
        color: #009a71;
    }
    .ranges li:hover,.ranges li.active,.daterangepicker td.active, .daterangepicker td.active:hover {
        background-color: #009a71;
        border-color: #009a71;
    }

    .daterangepicker td.in-range {
        background-color: #009a7152;
    }
    .status-action-btn {
        padding: 0 15px;
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
        <section class="content-header top-header">
            <div class="row">
                <div class="col-md-6">
                    <h2>
                        {{ __('Voucher Orders') }}
                    </h2>
                </div>
            </div>

        </section>
        <!-- Table content -->
        @can('Advance Filter Voucher Orders')
        <section class="pt-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" id="date_filter" class="form-control" name="date_range" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="product" class="form-control">
                                    <option value="">---{{ __('Select a product') }}---</option>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">---{{ __('Select a status') }}---</option>
                                    <option value="0">{{ __('Pending') }}</option>
                                    <option value="1">{{ __('Approved') }}</option>
                                    <option value="2">{{ __('Rejected') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text"  name="name-email" placeholder="Enter Distributor Name/Email" value="{{ isset($distributor_email)? $distributor_email : "" }}" autocomplete="off" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text"  name="voucher_code" placeholder="Enter Voucher Code" value="" autocomplete="off" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text"  name="customer_name_email" placeholder="Enter Customer Name/Email" value="" autocomplete="off" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="action_status" id="action_status" class="form-control">
                                    <option value="">---{{  __('Select active status')}}---</option>
                                    <option value="1">Active</option>
                                    <option value="0">InActive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endcan
        <section class="content kks-subscription-box-sections">
            <div class="box pt-1">
                <div class="row box-body">
                    <div class="col-xs-12 table-responsive">
                        @include('frontside.layouts.partials.message')
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="vouchers" class="table table-striped table-bordered  nowrap no-footer" style="width:100%">
                                        <thead>
                                            <tr role="row">
                                                <th>{{__('Order ID')}}</th>
                                                @canany(['Vouchers Listing','Vouchers Payment','Download Vouchers'])
                                                <th>{{ __('Actions') }}</th>
                                                @endcanany
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Active Status') }}</th>
                                                <th>{{ __('Distributor') }}</th>
                                                <th>{{ __('Product') }}</th>
                                                <th>{{ __('Quantity') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Used') }}</th>
                                                <th>{{ __('Remaining') }}</th>
                                                <th>{{ __('Discount (%)') }}</th>
                                                <th>{{ __('Unit Price') }}</th>
                                                <th>{{ __('Taxes') }}</th>
                                                <th>{{ __('Total Payable Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        <!-- /.box -->
                    </div>
                </div>
            </div>
        </section>
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
<script src="{{ asset('backend/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script>
        var ajax_data = [];
        ajax_data['start_date'] = '';
        ajax_data['end_date'] = '';
        ajax_data['product_id'] = '';
        ajax_data['variation_id'] = '';
        ajax_data['name_email'] = '';
        ajax_data['status'] = '';
        ajax_data['currency'] = '';
        ajax_data['voucher_code'] = '';
        ajax_data['customer_name_email'] = '';
        ajax_data['country_id'] = '';
        ajax_data['action_status'] = '';
        tableajaxurl = '{{ route("admin.reports.voucher.distributors") }}';

        var table = $("#vouchers").DataTable({
            "order": [],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                },

              'copy', 'csv', 'excel'
            ],
            lengthChange: false,
            // responsive: true,
            scrollX: true,
            orderCellsTop: true,
            serverSide: true,
            "ajax": {
                "url": tableajaxurl,
                "data": function(d){
                    d.start_date = ajax_data['start_date'];
                    d.end_date = ajax_data['end_date'];
                    d.product_id = ajax_data['product_id'];
                    d.variation_id =  ajax_data['variation_id'];
                    d.name_email = ajax_data['name_email'];
                    d.status = ajax_data['status'];
                    d.currency = ajax_data['currency'];
                    d.voucher_code = ajax_data['voucher_code'];
                    d.customer_name_email= ajax_data['customer_name_email'];
                    d.country_id = ajax_data['country_id'];
                    d.action_status = ajax_data['action_status'];
                },
                "beforeSend": function() {
                    if (table && table.hasOwnProperty('settings')) {
                        table.settings()[0].jqXHR.abort();
                    }
                }
            },
            columns: [
                {
                    data:'order_id',
                    name:'order_id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'statuss',
                    name: 'statuss'
                },
                {
                    data: 'active_status',
                    name: 'active_status'
                },
                {
                    data: 'distributor',
                    name: 'distributor'
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'used_quantity',
                    name: 'used_quantity'
                },
                {
                    data: 'remaining_quantity',
                    name: 'remaining_quantity'
                },
                {
                    data: 'discount',
                    name: 'discount'
                },
                {
                    data: 'unit_price',
                    name: 'unit_price'
                },
                {
                    data: 'taxes',
                    name: 'taxes'
                },
                {
                    data: 'total_payable_amount',
                    name: 'total_payable_amount'
                },

            ],
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ]
        });
        /***  Filters JQuery Start ***/

        // Date Range Picker Start
        $('#date_filter').daterangepicker({
            "showDropdowns": true,
            "autoApply": true,
            ranges: {
                '{{ __("Today") }}': [moment(), moment()],
                '{{ __("Yesterday") }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '{{ __("Last 7 Days") }}': [moment().subtract(6, 'days'), moment()],
                '{{ __("Last 30 Days") }}': [moment().subtract(29, 'days'), moment()],
                '{{ __("This Month") }}': [moment().startOf('month'), moment().endOf('month')],
                '{{ __("Last Month") }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "alwaysShowCalendars": true,
            locale: {
                cancelLabel: '{{ __("Clear") }}',
                "customRangeLabel": "{{ __('Custom') }}",
            }
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
            ajax_data['start_date'] = start.format('YYYY-MM-DD');
            ajax_data['end_date'] = end.format('YYYY-MM-DD');
            refreshDataTable();
        });
        $("#date_filter").val('Select Date Range');
        // Date Rage Picker End
        $("body").on('change','select[name=action_status]',function(){
            ajax_data['action_status'] = $(this).val();

            refreshDataTable();
        });

        $("body").on('change','select[name=country_id]',function(){
            ajax_data['country_id'] = $(this).val();
            refreshDataTable();
        });
        // Selecting the product Start
        $('body').on('change', 'select[name=product]', function(){
            ajax_data['product_id'] = $('option:selected',this).attr('data-product_id');
            ajax_data['variation_id'] = $('option:selected',this).attr('data-variation_id');
            refreshDataTable();
        });
        // Selecting the product End
        // Selecting the Status Start
        $('body').on('change', 'select[name=status]', function(){
            ajax_data['status'] = $(this).val();
            refreshDataTable();
        });
        // Selecting the Status End
        // Selecting the Currency Start
        $('body').on('change', 'select[name=currency]', function(){
            ajax_data['currency'] = $(this).val();
            refreshDataTable();
        });
        // Selecting the Cuurency End
        // Input the Name/Email Start
        $('body').on('input', 'input[name=name-email]', function(){
            ajax_data['name_email'] = $(this).val();
            refreshDataTable();
        });

        // Voucher Code Event
        $('body').on('input', 'input[name=voucher_code]', function(){
            ajax_data['voucher_code'] = $(this).val();
            refreshDataTable();
        });

        $('body').on('input', 'input[name=customer_name_email]', function(){
            ajax_data['customer_name_email'] = $(this).val();

            refreshDataTable();
        });
        @isset($distributor_email)
        $('input[name=name-email]').trigger('input');
        @endisset
        // Input the Name/Email End
        function refreshDataTable(){
            table.ajax.reload();
        }

        //On Distributor Selection
        $('body').on('change','select[name=distributor_id]',function(){
            vat_percentage = $(this).find(':selected').data('vat');
            name = $(this).find(':selected').data('name');
            email = $(this).find(':selected').data('email');
            $('[name=vat_percentage]').val(vat_percentage);
            $('[name=distributor_email]').val(email);
            $('[name=distributor_name]').val(name);
        });
        // On product Change from get more vouchers form
        $('body').on('change', 'select[name=product_id]', function(){
            variation_count = $(this).find(':selected').data('variation-count');
            if(variation_count > 0){
                url = "{{ route('get-product-variations', ':id') }}";
                url = url.replace(":id", $(this).val());
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if(data['success'] == 'true'){
                            // console.log(data)
                            variations = data.data.variations;
                            $('select[name=variation_id]').find('option').remove().end()
                            $.each(variations,function(index,value){
                                $('select[name=variation_id]').append('<option value="'+value.hashedid+'">'+data.data.product_name+' '+value.variation_name+'</option>')
                            })
                            $('.variation_selection').show();
                        }
                    },
                    complete:function(data){

                    }
                })
            }else{
                $('.variation_selection').hide();
            }
            html = '';
            length = $('option:selected',this).data('secondary-projects').length;
            $('option:selected',this).data('secondary-projects').forEach(function(val, ind){
                html += val;
                if(ind < length - 1){
                    html += ', ';
                }
            });
            if(length > 0){
                $('.secondary_projects_div').show();
                $('.secondary_projects_div .data').html(html);
            }else{
                $('.secondary_projects_div').hide();
            }
        });
        // On changing the country
        $('body').on('change', '[name=country_id]',function(){
            vat_percentage = 0;
            selected_option = $('option:selected',this);
            if(selected_option.data('is_default_vat') == 1){
                vat_percentage = selected_option.data('default_vat');
            }else{
                vat_percentage =  selected_option.data('vat_vercentage');
            }
            $("[name=vat_percentage]").val(vat_percentage)
            $(".vat_percentage").html(vat_percentage)
        })
        $('#voucher_form').validate();
    </script>
@endsection
