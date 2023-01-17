@extends('admin.layouts.app')
@section('title',  __('Languages Translations'))
@section('styles')
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <div class="loader-parent" id="ajax_loader">
     <div class="loader">
         <div class="square"></div>
         <div class="path">
             <div></div>
             <div></div>
             <div></div>
             <div></div>
             <div></div>
             <div></div>
             <div></div>
         </div>
     </div>
   </div>
   <section class="content-header top-header mb-1">
      <div class="row">
        <div class="box-header">
           <div class="row">
               <div class="col-md-4">
                  <h2>
                     {{ __('Languages Translations') }}
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
                     <h3 class="box-title">{{ __('Languages Translations') }}</h3>
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
                           <form class="mt-2 form-validate" action="{{ route('admin.language-translations.store') }}" method="post" enctype="multipart/form-data">
                              @csrf

                              <input type="hidden" name="action" value="{{$action}}" />
                              <input name="id" type="hidden" value="{{ $model->id }}" />

                              @if($action == 'Add')
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label class="control-label">{{ __('Language Modules') }}</label>
                                    <select class="form-control" name="language_module_id" required="">
                                       <option value="">{{ __('Select Module') }}</option>
                                       @foreach ($language_modules as $language_module)
                                          <option value="{{$language_module->id}}">
                                             {{$language_module->name}}
                                          </option>
                                       @endforeach
                                    </select>
                                 </div>
                              </div>

                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label class="control-label">{{ __('Languages') }}</label>
                                    <select class="form-control" name="translate_language">
                                       <option value="">{{ __('All Lanugages') }}</option>
                                       @foreach($languages as $lang)
                                       <option value="{{$lang->id}}">{{$lang->name}}</option>
                                       @endforeach
                                    </select>
                                 </div>
                              </div>

                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label class="control-label">{{ __('Translation') }}</label>
                                    <br>
                                    @php $translation_flag = 1; @endphp
                                    <label class="fancy-radio">
                                       <input name="translation_flag" value="1" type="radio"
                                          {{ ($translation_flag == 1) ? 'checked' : '' }}>
                                       <span><i></i>{{ __('Apply On All Records') }}</span>
                                    </label>
                                    <label class="fancy-radio">
                                       <input name="translation_flag" value="2" type="radio"
                                          {{ ($translation_flag == 2) ? 'checked' : '' }}>
                                       <span><i></i>{{ __('Skip Custom Translated Records') }}</span>
                                    </label>
                                 </div>
                              </div>

                              @else
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label class="control-label">{{ __('Language Module') }}</label>
                                    <input type="text" class="form-control" value="{{ $model->languageModule->name}}"
                                       readonly="">
                                 </div>
                              </div>

                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label class="control-label">{{ __('Language Name') }}</label>
                                    <input type="text" class="form-control" value="{{ $model->language->name}}"
                                       readonly="">
                                 </div>
                              </div>

                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label class="control-label">{{ __('Language Code') }}</label>
                                    <input type="text" class="form-control" value="{{ $model->language->iso_code}}"
                                       readonly="">
                                 </div>
                              </div>

                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label class="control-label">{{ __('Item Id') }}</label>
                                    <input type="text" class="form-control" value="{{ $model->item_id}}"
                                       readonly="">
                                 </div>
                              </div>

                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label class="control-label">{{ __('Column Name') }}</label>
                                    <input type="text" class="form-control" value="{{ $model->column_name}}"
                                       readonly="">
                                 </div>
                              </div>

                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label class="control-label">{{ __('Item Value') }}</label>
                                    <textarea name="item_value" class="form-control" required=""
                                       rows="5">{{ $model->item_value }}</textarea>
                                 </div>
                              </div>

                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label class="control-label">{{ __('Custom Translation') }}</label>
                                    <br>
                                    @php $custom = ($action == 'Add') ? old('custom') : $model->custom @endphp
                                    <label class="fancy-radio">
                                       <input name="custom" value="1" type="radio"
                                          {{ ($custom == 1) ? 'checked' : '' }}>
                                       <span><i></i>{{ __('Yes') }}</span>
                                    </label>
                                    <label class="fancy-radio">
                                       <input name="custom" value="0" type="radio"
                                          {{ ($custom == 0) ? 'checked' : '' }}>
                                       <span><i></i>{{ __('No') }}</span>
                                    </label>
                                 </div>
                              </div>

                              @endif

                              <br>
                              <div class="row clearfix">
                                <div class="box-header">
                                   <div class="row">
                                      <div class="col-md-4 pl-0">
                                         <button type="submit" class="skin-green-light-btn btn ml-2">{{ __('Save') }}</button>
                                         <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.language-translations.index') }}">{{ __('Discard') }}</a>
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