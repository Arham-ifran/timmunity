@extends('admin.layouts.app')
@section('title',  __('Contact Us Queries'))
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header mb-1">
      <div class="row">
        <div class="box-header">
           <div class="row">
              <div class="col-md-4">
                <h2>
                   {{ __('Contact Us Queries') }} / {{ __('Edit') }}
                </h2>
              </div>
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
                     <h3 class="box-title">{{ __('Edit Contact Us Queries') }}</h3>
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
                           <form class="mt-2 form-validate" action="{{ route('admin.contact-us-queries.store') }}" method="post" enctype="multipart/form-data">
                              @csrf
                              <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                              <input type="hidden" name="action" value="{!!$action!!}">
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input type="text" class="form-control" value="{{ old('name', $model->name ?? '') }}" aria-describedby="name" readonly />
                                 </div>
                              </div>
                              <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="email">{{ __('Email') }}</label>
                                        <input type="text" class="form-control" id="email" name="email" value="{{ old('email', $model->email ?? '') }}" aria-describedby="email" readonly />
                                    </div> 
                              </div>
                              <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="phone">{{ __('Phone') }}</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $model->phone ?? '') }}" aria-describedby="phone" readonly />
                                    </div> 
                              </div>
                              <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="subject">{{ __('Subject') }}</label>
                                        <input type="text" class="form-control" id="subject" name="subject" value="{{ translation(@$model->id,28,app()->getLocale(),'subject',@$model->subject) ?? '' }}" aria-describedby="subject" readonly />
                                    </div> 
                              </div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="description">{{ __('Message') }}</label>
                                    <textarea class="form-control" name="message" rows="4" readonly>{{ translation(@$model->id,28,app()->getLocale(),'message',@$model->message) ?? '' }}</textarea>
                                 </div>
                              </div>
                              <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Status') }}</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status1" value="1" @if(isset($model->status) && $model->status == 1) checked @endif>
                                        <label class="form-check-label" for="status1">
                                            {{ __('Completed') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status2" value="0" @if(isset($model->status) && $model->status == 0) checked @endif>
                                        <label class="form-check-label" for="status2">
                                            {{ __('Pending') }}
                                        </label>
                                    </div>
                                </div>
                              </div>
                              <br>
                              <div class="row clearfix">
                                   <div class="box-header">
                                      <div class="row">
                                         <div class="col-md-4 pl-0">
                                            <button type="submit" class="skin-green-light-btn btn ml-2">{{ __('Save') }}</button>
                                            <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.contact-us-queries.index') }}">{{ __('Discard') }}</a>
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