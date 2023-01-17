@extends('admin.layouts.app')
@section('title', __('Sales Analytics'))
@section('styles')

<link rel="stylesheet" href="{{ asset('backend/bower_components//morris.js/morris.css') }}">
@endsection
@section('content')

<div class="content-wrapper">
    <div class="row" >
        @foreach($sales_teams as $sales_team)
        <div class="col-lg-6">
            <section>
                <!-- solid sales graph -->
                <div class="box box-solid bg-teal-gradient">
                    <div class="box-header">
                        <i class="fa fa-th"></i>
                        <h3 class="box-title">{{ $sales_team->name }}</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body border-radius-none">
                        <div class="chart" id="sales-line-chart{{ $sales_team->id }}" style="height: 250px;"></div>
                    </div>
                    <!-- /.box-body -->
                    @can('View Sale Analysis')
                    <div class="box-footer no-border">
                        <div class="row">
                            <div class="text-center"><a href="{{ route('admin.sales-team.analysis').'?sales_team='.$sales_team->id }}" class="btn btn-success">{{ __('Sale Analysis') }}</a></div>
                        </div>
                        <!-- /.row -->
                    </div>
                    @endcan
                    <!-- /.box-footer -->
                </div>
                <!-- /.box -->
            </section>
        </div>
        @endforeach

    </div>
</div>
@endsection

@section('scripts')

<script src="{{ asset('backend/bower_components/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('backend/bower_components/morris.js/morris.min.js') }}"></script>
<script>
    @foreach($sales_teams as $sales_team)
    data{{ $sales_team->id }} = @json($sales_team->graph_data); // [{"date":"14-06-2021","sales":21.5},{"date":"13-06-2021","sales":5.75}]
    var sales_line{{ $sales_team->id }} = new Morris.Line({
        element          : 'sales-line-chart{{ $sales_team->id }}',
        resize           : true,
        data             : data{{ $sales_team->id }},
        xkey             : 'date',
        ykeys            : ['sales'],
        labels           : ['Sales'],
        lineColors       : ['#efefef'],
        lineWidth        : 2,
        hideHover        : 'auto',
        gridTextColor    : '#fff',
        gridStrokeWidth  : 0.4,
        pointSize        : 4,
        pointStrokeColors: ['#efefef'],
        gridLineColor    : '#efefef',
        gridTextFamily   : 'Open Sans',
        gridTextSize     : 10,
        parseTime        : false,
        xLabelAngle      : "30"
    });
    @endforeach

</script>
@endsection
