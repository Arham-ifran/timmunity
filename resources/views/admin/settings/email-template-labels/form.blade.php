@extends('admin.layouts.app')
@section('title',  __('Email Template Labels'))
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header mb-1">
      <div class="row">
        <div class="box-header">
           <div class="row">
              <div class="col-md-4">
                <h2>
                   {{ __('Email Template Labels') }} / {{ __('Edit') }}
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
					<p>{{ __('On sending email, following keywords with double curly brackets e.g') }}
						<strong>@{{ keyword }}</strong> {{ __('will be replaced by their values:') }}</p>
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
                     <h3 class="box-title">@if($action == "Add") {{ __('Add New Email Template Label') }} @else {{ __('Edit Email Template Label') }} @endif</h3>
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
                           <form class="mt-2 form-validate" action="{{ route('admin.email-template-labels.store') }}" method="post" enctype="multipart/form-data">
                              @csrf
                              <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                              <input type="hidden" name="action" value="{!!$action!!}">
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="email_template_id">{{ __('Email Template') }}<small class="asterik" style="color:red">*</small></label>
                                    <select class="form-control @error('email_template_id') is-invalid @enderror" name="email_template_id" id="" required>
                                       <option value="">---{{ __('Select a email template') }}---</option>
                                       @foreach($email_templates as $email_template)
                                       @php $selected = ($action == 'Edit' && $email_template->id ==
                                       $model->email_template_id)
                                       ? 'selected' : ''; @endphp
                                       <option value="{{ $email_template->id }}" {{ $selected }}>
                                          {{ $email_template->subject }}
                                       </option>
                                       @endforeach
                                    </select>
                                    @error('email_template_id')
                                    <div id="email_template_id-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-md-12"><hr></div>
                              <div id="labels">
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="label">{{ __('Label') }}<small class="asterik" style="color:red">*</small></label>
                                       <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label[]" value="{{ $model->label ?? '' }}" aria-describedby="label" required />
                                       @error('label')
                                       <div id="label-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="value">{{ __('Value') }}<small class="asterik" style="color:red">*</small></label>
                                       <textarea class="form-control @error('value') is-invalid @enderror" id="value" name="value[]" rows="5" required>{{ $model->value ?? '' }}</textarea>
                                       @error('value')
                                       <div id="value-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                       @enderror
                                    </div>
                                 </div>
                              </div>
                              @if($action == 'Add')
                              <div class="col-md-12"><hr></div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <button type="button" id="add-label" class="pull-right btn btn-success">
                                       <i class="fa fa-plus"></i>
                                    </button>
                                 </div>
                              </div>
                              @endif
                              <br>
                              <div class="row clearfix">
                                   <div class="box-header">
                                      <div class="row">
                                         <div class="col-md-4 pl-0">
                                            <button type="submit" class="skin-green-light-btn btn ml-2">{{ __('Save') }}</button>
                                            <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.email-template-labels.index') }}">{{ __('Discard') }}</a>
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
<div id="dynamic_label_fields" style="display: none;">
   <div>
      <div class="col-md-12"><hr></div>
      <div class="col-sm-12">
         <label for="label">Label<small class="asterik" style="color:red">*</small></label>
         <div class="form-group">
            <input type="text" name="label[]" class="form-control">
         </div>
      </div>
      <div class="col-sm-12">
         <label for="value">Value<small class="asterik" style="color:red">*</small></label>
         <div class="form-group">
            <textarea name="value[]" class="form-control" rows="5"></textarea>
         </div>
      </div>
      <div class="col-sm-12">
         <div class="form-group">
            <button type="button" class="pull-right btn btn-danger remove_label"><i
                  class="fa fa-times"></i></button>
         </div>
      </div>
   </div>
</div>
<!-- /.content -->
@endsection
@section('scripts')
<script type="text/javascript">
   $('#add-label').on('click',function(){
      $('#labels').append($('#dynamic_label_fields').html());
     });

     $(document).on('click', '.remove_label', function(){
      $(this).parent().parent().parent().remove();
     });
</script>
@endsection
