@extends('admin.layouts.app')
@section('title', __('Fed. State'))
@section('styles')
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
      <div class="row">
         <div class="col-md-6">
            <h2>
                {{ __('Contact Fed. States') }} /   @if($action == "Add") {{ __('Add') }} @else {{ __('Edit') }} @endif
            </h2>
         </div>
         <div class="col-md-6">
            <div class="search-input-das">
               <form>
                  <input type="text" name="search" placeholder="{{ __('Search') }}...">
               </form>
            </div>
         </div>
      </div>
   </section>
   <!-- Table content -->
   <section class="content">
      <div class="main-box box">
         <div class="row mt-3">
            <div class="col-xs-12">
               <div class="box box-success box-solid">
                  <div class="box-header with-border">
                     <h3 class="box-title">@if($action == "Add") {{ __('Add New Contact Fed. State') }} @else {{ __('Edit Contact Fed. State') }} @endif</h3>
                     <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                     </div>
                     <!-- /.box-tools -->
                  </div>
                  <!-- /.box-header -->
                  <div class="row">
                    <div class="col-md-8">
                       <form class="timmunity-custom-dashboard-form mt-2 form-validate" action="{{ route('admin.contacts-fed-states.store') }}" method="post" enctype="multipart/form-data">
                          @csrf
                          <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                          <input type="hidden" name="action" value="{!!$action!!}">
                          <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ __('Name') }}<small class="asterik" style="color:red">*</small></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $model->name ?? '') }}" maxlength="255" aria-describedby="name" required />
                                @error('name')
                                <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                @enderror
                             </div>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">{{ __('State Code') }}<small class="asterik" style="color:red">*</small></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $model->code ?? '') }}" maxlength="255" aria-describedby="code" required />
                                @error('code')
                                <div id="code-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                @enderror
                             </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                                <label for="country-id">{{ __('Country') }}<small class="asterik" style="color:red">*</small></label>
                                 <select id="country-id" name="country_id" class="form-control @error('country_id') is-invalid @enderror" aria-describedby="country_id" required>

                                    <option value="">---{{ __('Select a country') }}---</option>
                                    @if($contact_countries->count() > 0)
                                    @foreach($contact_countries as $country)
                                    <option value="{{$country->id}}"
                                        @if(isset($model) && $country->id == $model->country_id) selected @endif>{{$country->name}}</option>
                                    @endforeach
                                    @endif
                                  </select>
                                  @error('country-id')
                                  <div id="country-id-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                  @enderror
                             </div>
                          </div>



                          <div class="col-md-6 mt-3">
                            <div class="form-group">
                             <button type="submit" class="skin-green-light-btn btn ">{{ __('Save') }}</button>
                             <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.contacts-fed-states.index') }}">{{ __('Discard') }}</a>

                            </div>
                        </div>
                     </div>
                    <!-- FILE UPLOAD -->

                    </div>
                 </div>
                        </div>
                     </div>

                    </form>
                  </div>
                </div>
               </div>
            </div>
            <!-- /.box-body -->
         </div>
      </div>
    </div>
<!-- /.box -->
  </div>
 </div>
 </section>
<!-- /.content -->
</div>

@endsection
@section('scripts')
<script src="{{ asset('backend/dist/js/custom.js') }}">

</script>
@endsection
