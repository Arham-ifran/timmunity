<div class="row main-breadcrumb-div">
    @canany(['Confirm Quotation','Lock Quotation','Unlock Quotation','Cancel Quotation','Send By Email','Send Pro-Forma Invoice','Create Quotation Invoice','Set To Quotation'])
    <div class="col-md-8 pl-0">
        <div class="breadcrumb">
            @if($action == 'Edit')
                @if (!isset($model))
                    @can('Send By Email')
                    <a class="active" data-toggle="modal" data-target="#send-by-email">{{ __('SEND BY
                        EMAIL') }}</a>
                    @endcan
                    @can('Send Pro-Forma Invoice')
                    @if(@$sales_settings['orders_proforma_invoice'] == 1)
                    <a class="active" data-toggle="modal" data-target="#send-proforma-invoice">{{ __('SEND PRO-FORMA INVOICE') }}</a>
                    @endif
                    @endcan
                    @can('Confirm Quotation')
                    <a href="#." class="change-status-btn" data-status="@if(@$sales_settings['orders_lock_confirmed_sale'] == 1) 2 @else 1 @endif" data-kss="0">{{ __('CONFIRM') }}</a>
                    @endcan
                    @can('Cancel Quotation')
                    <a href="#." class="change-status-btn" data-status="4">{{ __('CANCEL') }}</a>
                    @endcan
                @else
                    @if ($model->status == 0)
                        <!-- If Status is Quotation -->
                        @can('Send By Email')
                        <a class="active" data-toggle="modal" data-target="#send-by-email">{{ __('SEND BY EMAIL') }}</a>
                        @endcan
                        @can('Send Pro-Forma Invoice')
                        @if($sales_settings['orders_proforma_invoice'] == 1)
                        <a class="active" data-toggle="modal" data-target="#send-proforma-invoice">{{ __('SEND PRO-FORMA INVOICE') }}</a>
                        @endif
                        @endcan
                        @can('Confirm Quotation')
                        <a href="#." class="change-status-btn" data-status="@if(@$sales_settings['orders_lock_confirmed_sale'] == 1) 2 @else 1 @endif" data-kss="0">{{ __('CONFIRM') }}</a>
                        @endcan
                        @can('Cancel Quotation')
                        <a href="#." class="change-status-btn" data-status="4">{{ __('CANCEL') }}</a>
                        @endcan
                    @elseif($model->status == 2 )
                        <!-- If status is Locked -->
                        @can('Unlock Quotation')
                        <a href="#." class="change-status-btn" data-status="1">{{ __('UNLOCK') }}</a>
                        @endcan
                        @can('Send Pro-Forma Invoice')
                        @if(@$sales_settings['orders_proforma_invoice'] == 1)
                        <a class="active" data-toggle="modal" data-target="#send-proforma-invoice">{{ __('SEND PRO-FORMA INVOICE') }}</a>
                        @endif
                        @endcan
                    @elseif($model->status == 1 )
                        <!-- If status is Sales Order -->
                        {{-- @if(@$model->all_licences_attached == false)
                            <a class="active" href="{{route('admin.quotation.license.attach',Hashids::encode($model->id))}}" >{{ __('ATTACH LICENCES') }}</a>
                            @endif --}}
                        @if(@$model->all_vouchers_generated == false)
                            <a class="active" href="{{route('admin.quotation.voucher.attach',Hashids::encode($model->id))}}" >{{ __('ATTACH VOUCHERS') }}</a>
                        @endif
                        {{-- <form method="POST" action="{{route('admin.quotation.license.attach',Hashids::encode($model->id))}}">
                            @csrf
                            <input class="active" type="submit" value="Attache Licences">
                        </form> --}}
                        @can('Create Quotation Invoice')
                        @if($model->total > $model->invoicedamount)
                        <a class="active" href="{{ route('admin.quotation.invoice.create', Hashids::encode($model->id)) }}">{{ __('CREATE INVOICE') }}</a>
                        @endif
                        @endcan
                        @can('Send Pro-Forma Invoice')
                        @if(@$sales_settings['orders_proforma_invoice'] == 1)
                        <a class="active" data-toggle="modal" data-target="#send-proforma-invoice">{{ __('SEND PRO-FORMA INVOICE') }}</a>
                        @endif
                        @endcan
                        @can('Send By Email')
                        <a data-toggle="modal" data-target="#send-by-email">{{ __('SEND BY EMAIL') }}</a>
                        @endcan
                        @can('Cancel Quotation')
                        <a href="#." class="change-status-btn" data-status="4">{{ __('CANCEL') }}</a>
                        @endcan
                        @can('Lock Quotation')
                        <a href="#." class="change-status-btn" data-status="2">{{ __('LOCK') }}</a>
                        @endcan
                    @elseif($model->status == 4 )
                        <!-- If status is Cancelled -->
                        @can('Send Pro-Forma Invoice')
                        @if(@$sales_settings['orders_proforma_invoice'] == 1)
                        <a class="active" data-toggle="modal" data-target="#send-proforma-invoice">{{ __('SEND PRO-FORMA INVOICE') }}</a>
                        @endif
                        @endcan
                        @can('Set To Quotation')
                        <a href="#." class="change-status-btn" data-status="0">{{ __('SET TO QUOTATION') }}</a>
                        @endcan
                    @elseif($model->status == 3 )
                        <!-- If status is Quotation Sent -->
                        @can('Confirm Quotation')
                        <a href="#." class="active change-status-btn" data-status="2"
                            data-kss="@if(@$sales_settings['orders_lock_confirmed_sale'] == 1) 2 @else 1 @endif">{{ __('CONFIRM') }}</a>
                        @endcan
                       {{--  <a href="#." class="active change-status-btn" data-status="2"
                            data-kss="@if(@$sales_settings['orders_lock_confirmed_sale'] == 1) 2 @else 1 @endif">{{ __('CONFIRM WITHOUT KSS') }}</a> --}}
                        @can('Send By Email')
                        <a data-toggle="modal" data-target="#send-by-email">{{ __('SEND BY EMAIL') }}</a>
                        @endcan
                        @can('Send Pro-Forma Invoice')
                        @if(@$sales_settings['orders_proforma_invoice'] == 1)
                        <a class="active" data-toggle="modal" data-target="#send-proforma-invoice">{{ __('SEND PRO-FORMA INVOICE') }}</a>
                        @endif
                        @endcan
                        @can('Cancel Quotation')
                        <a href="#." class="change-status-btn" data-status="4">{{ __('CANCEL') }}</a>
                        @endcan
                    @endif
                @endif
            @endif
        </div>
    </div>
    @endcanany
    <div class="col-md-4 pull-right text-right">
        <ul class="breadcrumb custom-breadcrumb">
            <li class="active">{{ __('Quotation') }}</li>
            @isset($model)
                @switch($model->status)
                    @case(0)
                        @break
                    @case(1)
                        <li>{{ __('Quotations Sent') }}</li>
                        <li>{{ __('Sales Order') }}</li>
                        @break
                    @case(2)
                        <li>{{ __('Quotations Sent') }}</li>
                        <li>{{ __('Sales Order') }}</li>
                        @break
                    @case(3)
                        <li>{{ __('Quotations Sent') }}</li>
                        @break
                    @case(4)
                        @break
                    @default
                @endswitch
            @endisset
        </ul>
    </div>
</div>
