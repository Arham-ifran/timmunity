@extends('admin.layouts.app')
@section('title',  __('Email Templates'))
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header mb-1">
      <div class="row">
        <div class="box-header">
           <div class="row">
              <div class="col-md-4">
                <h2>
                   {{ __('Email Template') }} / {{ __('Edit') }}
                </h2>
              </div>
           </div>
        </div>
     </div>
   </section>
   <!-- Table content -->
   <section class="content">
      <div class="main-box box">
      	<div class="row pt-3">
      		<div class="col-md-12">
      			@if($action == 'Edit' && !empty($model->info))
				<div class="alert-info" role="alert" style="padding: 20px;">
					<p>On sending email, following keywords with double curly brackets e.g
						<strong>@{{ keyword }}</strong> will be replaced by their values:</p>
					@foreach(json_decode($model->info,true) as $key => $value)
					<li><strong>{{$key}}</strong> : {{$value}} </li>
					@endforeach
				</div>
				<br>
				@endif
      		</div>
      	</div>
         <div class="row mt-3">
            <div class="col-xs-12">
               <div class="box box-success box-solid">
                  <div class="box-header with-border">
                     <h3 class="box-title">@if($action == "Add") {{ __('Add New Email Template') }} @else {{ __('Edit Email Template') }} @endif</h3>
                     <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                     </div>
                     <!-- /.box-tools -->
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                     <div class="row">
                        <div class="col-md-9">
                           <form class="mt-2 form-validate" action="{{ route('admin.email-templates.store') }}" method="post" enctype="multipart/form-data">
                              @csrf
                              <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                              <input type="hidden" name="action" value="{!!$action!!}">
                              @if($action == 'Add')
                                <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="type">{{ __('Type') }}</label>
                                    <input type="text" class="form-control @error('type') is-invalid @enderror" id="type" name="type" maxlength="250" value="{{ $model->type ?? '' }}" aria-describedby="type" required />
                                    @error('type')
                                    <div id="type-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>
                                </div>
							   @endif
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="subject">{{ __('Subject') }}</label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" maxlength="250" value="{{ translation(@$model->id,14,app()->getLocale(),'subject',@$model->subject) ?? '' }}" aria-describedby="name" required />
                                    @error('question')
                                    <div id="question-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="content">{{ __('Content') }}</label>
                                    <textarea class="form-control summernote" name="content" rows="15" required>{{ $model->content ?? '' }}</textarea>
                                    @error('content')
                                    <div id="content-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>
                              </div>
                              <br>
                              <div class="row clearfix">
                                   <div class="box-header">
                                      <div class="row">
                                         <div class="col-md-4 pl-0">
                                            <button type="submit" class="skin-green-light-btn btn ml-2">{{ __('Save') }}</button>
                                            <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.email-templates.index') }}">{{ __('Discard') }}</a>
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
    </section>
</div>
<!-- /.content -->
@endsection
@section('scripts')
@endsection