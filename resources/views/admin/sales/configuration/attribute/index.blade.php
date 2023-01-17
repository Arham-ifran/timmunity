@extends('admin.layouts.app')
@section('title', __('Attributes'))
@section('styles')
<link href="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">

@endsection
@section('content')

<div class="content-wrapper">
    <section class="content-header top-header">
        <div class="row">
            <div class="col-md-6">
                <h2>{{ __('Attributes') }}</h2>
            </div>
            <!-- <div class="col-md-6">
                <div class="search-input-das">
                    <form>
                        <input type="text" name="search" placeholder="Search...">
                    </form>
                </div>
            </div> -->
        </div>
        <div class="row">
            @can('Add Attributes')
                <div class="box-header">
                  <div class="row">
                      <div class="col-md-4">
                         <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{route('admin.attribute.create')}}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                      </div>
                  </div>
                </div>
            @endcan
        </div>
    </section>
    <section class="content">
        <div class="box pt-1">
            <div class="row box-body">
              <table id="attribute_table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('Attribute') }}</th>
                        <th>{{ __('Display Type') }}</th>
                        <th>{{ __('Variants Creation Mode') }}</th>
                        @canany(['Edit Attributes','Edit Attributes'])
                        <th>{{ __('Action') }}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach($attributes as $attribute)
                        <tr>
                            <th><input type="checkbox"></th>
                            <td><a href="{{route('admin.attribute.edit',Hashids::encode($attribute->id))}}">&#10016; {{$attribute->attribute_name}}</a></td>
                            <td>@if($attribute->display_type ==  1) Radio @elseif($attribute->display_type ==  2) Select @elseif($attribute->display_type ==  3) Color @endif</td>
                            <td>@if($attribute->variants_creation_mode ==  1) Instant @elseif($attribute->variants_creation_mode ==  2) Dynamic @elseif($attribute->variants_creation_mode ==  3) Never @endif</td>
                        </tr>
                    @endforeach --}}
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {

        var table = $('#attribute_table').DataTable({
            "dom": '<"top"fi>rt<"bottom"lp><"clear">',
            lengthChange: false,
            responsive: true,
            serverSide: true,
            orderCellsTop: true,
            "aaSorting": [],
            ajax: "{{ route('admin.attribute.index') }}",
            fnDrawCallback: function(oSettings) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [

                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'display_type',
                    name: 'display_type',

                },
                {
                    data: 'creation_mode',
                    name: 'creation_mode',

                },
                @canany(['Edit Attributes','Edit Attributes'])
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                @endcanany
            ]
        });
    });
</script>
<script src="{{ asset('backend\plugins\datatables\jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.js') }}"></script>
@endsection
