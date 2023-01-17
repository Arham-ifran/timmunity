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
                           <form class="mt-2 form-validate" action="{{url('admin/settings/language-translations/partial-translate')}}" method="post" enctype="multipart/form-data">
                              @csrf

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
                                    <label class="control-label">{{ __('Item Id') }}</label>
                                    <input type="text" name="item_id" class="form-control" required="">
                                 </div>
                              </div>

                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label class="control-label">{{ __('Column Name') }}</label>
                                    <input type="text" name="column_name" class="form-control" required="">
                                 </div>
                              </div>

                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label class="control-label">{{ __('Translation') }}</label>
                                    <textarea name="text" class="form-control" required="" rows="5"></textarea>
                                 </div>
                              </div>

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