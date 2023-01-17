<div class="row" style="border-bottom: 1px solid #ddd; margin-bottom: 20px;">
    <div class="row box-bar top-box-bar pull-right">
        <div class="container-fluid">
            <div class="col-md-12">
                @if(isset($model))
                @if(count(@$model->invoices) > 0)
                    @can('Customer Preview')
                    <a target="_blank" href="{{ route('user.dashboard.quotations.detail',Hashids::encode($model->id)) }}"><i class="fa fa-globe" aria-hidden="true"></i>&nbsp;{{ __('Customer Preview') }}</a>
                    @endcan
                    @can('Quotation Invoices')
                    <a href="{{ route('admin.quotation.invoice.index',Hashids::encode($model->id)) }}"><i class="fa fa-file-text" aria-hidden="true"></i>{{ count(@$model->invoices) }} {{ __('Invoices') }}</a>
                    @endcan
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
