@extends('admin.layouts.app')
@section('title',  __('FAQs'))
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header mb-1">
      <div class="row">
        <div class="box-header">
           <div class="row">
              <div class="col-md-4">
                <h2>
                   {{ __('FAQ') }} / @if($action == "Add") {{ __('Add') }}  @else {{ __('Edit') }} @endif
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
                     <h3 class="box-title">@if($action == "Add") {{ __('Add New FAQ') }} @else {{ __('Edit FAQ') }} @endif</h3>
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
                           <form class="mt-2 form-validate" action="{{ route('admin.faqs.store') }}" method="post" enctype="multipart/form-data">
                              @csrf
                              <input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                              <input type="hidden" name="action" value="{!!$action!!}">
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="name">{{ __('Question') }}</label>
                                    <input type="text" class="form-control @error('question') is-invalid @enderror" id="question" name="question" value="{{ translation(@$model->id,27,app()->getLocale(),'question',@$model->question) ?? '' }}" maxlength="200" aria-describedby="name" required />
                                    @error('question')
                                    <div id="question-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="answer">{{ __('Answer') }}</label>
                                    <textarea class="form-control @error('answer') is-invalid @enderror" name="answer" rows="4" required>{{ translation(@$model->id,27,app()->getLocale(),'answer',@$model->answer) ?? '' }}</textarea>
                                    @error('answer')
                                    <div id="answer-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="display_order">{{ __('Display Order') }}</label>
                                        <input type="number" class="form-control @error('display_order') is-invalid @enderror" id="display_order" name="display_order" min="0" value="{{ old('display_order', $model->display_order ?? '') }}" maxlength="100" aria-describedby="display_order" required />
                                       @error('display_order')
                                       <div id="display_order-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                       @enderror
                                    </div>
                              </div>
                              @if($action == "Add") @php $checked = "checked"; @endphp @endif
                              <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Status') }}</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status1" value="1" @if(isset($model->status) && $model->status == 1) checked @endif>
                                        <label class="form-check-label" for="status1">
                                            {{ __('Active') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status2" value="0" @if(isset($model->status) && $model->status == 0) checked @endif {{@$checked}}>
                                        <label class="form-check-label" for="status2">
                                            {{ __('In Active') }}
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
                                            <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.faqs.index') }}">{{ __('Discard') }}</a>
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
