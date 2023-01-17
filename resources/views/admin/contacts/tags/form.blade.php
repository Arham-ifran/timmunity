@extends('admin.layouts.app')
@section('title',  __('Contact-tags'))
@section('styles')
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
      <div class="row">
         <div class="col-md-6">
            <h2>
                {{ __('Contact Tags') }} / {{ __('New') }} @if($action == "Add") {{ __('Add') }} @else {{ __('Edit') }} @endif
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
                     <h3 class="box-title">@if($action == "Add") {{ __('Add New Contact Tag') }} @else {{ __('Edit Contact Tag') }} @endif</h3>
                     <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                     </div>
                     <!-- /.box-tools -->
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                     <div class="row">
                        <div class="col-md-8">
                           <form class="timmunity-custom-dashboard-form mt-2 form-validate" action="{{ route('admin.contacts-tags.store') }}" method="post">
                              @csrf
                              <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                              <input type="hidden" name="action" value="{!!$action!!}">
                              <div class="col-md-8">
                                 <div class="form-group">
                                     {{-- {{dd($model->contacts())}} --}}
                                    <label for="name">{{ __('Tag Name') }}<small class="asterik" style="color:red">*</small></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $model->name ?? '') }}" maxlength="255" aria-describedby="tagname" required />
                                    @error('name')
                                    <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>

                                 <div class="form-group">
                                    <label>{{ __('Status') }}<small class="asterik" style="color:red">*</small></label>
                                    <div class="form-check">
                                       <input class="form-check-input" type="radio" name="active" id="active" value="1" @if(isset($model->active) && $model->active == 1) checked @endif required>
                                       <label class="form-check-label" for="active">
                                         {{ __('Active') }}
                                       </label>
                                    </div>
                                    <div class="form-check">
                                       <input class="form-check-input" type="radio" name="active" id="active" value="0" @if(isset($model->active) && $model->active == 0) checked @endif>
                                       <label class="form-check-label" for="de_active">
                                           {{ __('In Active') }}
                                       </label>
                                    </div>
                                 </div>

                                 <button type="submit" class="skin-green-light-btn btn ">{{ __('Save') }}</button>
                                 <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.contacts-tags.index') }}">{{ __('Discard') }}</a>
                              </div>
                         </div>

                        </div>
                     </div>

                    </form>
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
