@extends('admin.layouts.app')
@section('title',  __('Country Group'))
@section('styles')
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('backend/bower_components/select2/dist/css/select2.min.css')}}">
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
      <div class="row">
         <div class="col-md-6">
            <h2>
                {{ __('Contact Country Group') }} / {{ __('Edit') }}
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
                     <h3 class="box-title">{{ __('Edit Contact Country Group') }}</h3>
                     <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                     </div>
                     <!-- /.box-tools -->
                  </div>
                  <!-- /.box-header -->
                  <div class="row">
                    <div class="col-md-8">
                       <form class="timmunity-custom-dashboard-form mt-2 form-validate" action="{{ route('admin.contacts-countries-groups.update',$model->id) }}" method="post" >
                          @csrf
                          @method('PATCH')
                          <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                          <input type="hidden" name="action" value="{!!$action!!}">
                          <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ __('Group Name') }} <small>(max 50 characters)</small> <small class="asterik" style="color:red">*</small></label>
                                <input type="text" maxlength="50" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ $model->name ?? '' }}" maxlength="255" aria-describedby="name" required />
                                @error('name')
                                <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                @enderror
                             </div>

                          </div>

                          <div class="col-md-12">
                            <div class="form-group" data-select2-id="27">
                                <label for="country-id">{{ __('Select Countries') }}<small class="asterik" style="color:red">*</small></label>
                                 <select multiple="multiple" id="country-id" name="country_id[]" class="form-control select2  @error('country_id') is-invalid @enderror" aria-describedby="country_id" required >
                                     @foreach ($contact_countries as $country)
                                        <option value="{{$country->id}}"
                                            @if(@$model->contact_countries)
                                          @foreach ($model->contact_countries as $contact_tag)
                                            @if ($contact_tag->id == $country->id)
                                            {{'selected="selected"'}}
                                            @endif
                                          @endforeach  @endif>

                                         {{ $country->name }} </option>
                                       @endforeach
                                </select>
                                  @error('country-id')
                                  <div id="country-id-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                  @enderror
                                </div>
                          </div>
                          <div class="col-md-6 mt-3">
                            <div class="form-group">
                             <button type="submit" class="skin-green-light-btn btn ">{{ __('Save') }}</button>
                             <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.contacts-countries-groups.index') }}">{{ __('Discard') }}</a>

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
<!-- Select2 -->
<script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript">

    $('.select2').select2();
    </script>
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
@endsection

