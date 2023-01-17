@extends('admin.layouts.app')
@section('title',  __('Permissions'))
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header top-header" style="margin-left: 0px; margin-right: 0px;">
      <div class="row">
         <div class="col-md-6">
            <h2>
               {{ __('Permission') }} / @if($action == "Add") {{ __('Add') }} @else {{ __('Edit') }} @endif
            </h2>
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
                     <h3 class="box-title">@if($action == "Add") {{ __('Add New Permission') }} @else {{ __('Edit Permission') }} @endif</h3>
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
                           <form class="timmunity-custom-dashboard-form mt-2 form-validate" action="{{ route('admin.permissions.store') }}" method="post" enctype="multipart/form-data">
                              @csrf
                              <input type="hidden" name="id" value="{!!@$permission->id!!}">
                              <input type="hidden" name="action" value="{!!$action!!}">
                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label for="name">{{ __('Name') }}</label>
                                       <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ $permission->name ?? '' }}" maxlength="255" aria-describedby="name" required />
                                      @error('name')
                                      <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                      @enderror
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="row pull-right">
                                     <button type="submit" class="skin-green-light-btn btn ml-2">{{ __('Save') }}</button>
                                    <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ route('admin.permissions.index') }}">{{ __('Discard') }}</a>
                                  </div>
                                </div>
                               <!-- /.box -->
                                <div class="row">
                                   <div class="col-md-12">
                                      <div class="custom-tabs mt-3 mb-2">
                                         <ul class="nav nav-tabs">
                                            <li class="active"><a data-toggle="tab" href="#access-rights">{{ __('Assign Roles to Permission') }}</a></li>
                                         </ul>
                                         <div class="tab-content custom-tabs-style custom-tabs-pd-set">
                                            <!--  Access Rights -->
                                            <div id="access-rights" class="tab-pane fade active in">
                                               <div class="row tab-form pt-3">
                                                  <!-- Tab Col No 01 -->
                                                  <div class="col-md-6">
                                                     @if(!$roles->isEmpty())
                                                     <div class="row">
                                                        <h3 class="col-md-12">{{ __('Roles') }}</h3>
                                                        <div class="col-sm=6 form-group">
                                                           @foreach ($roles as $role)
                                                             @if(isset($assignedRoles) && in_array($role->id, $assignedRoles))
                                                                 @php 
                                                                 $check = 'checked';
                                                                 @endphp
                                                             @else
                                                                 @php 
                                                                 $check = '';
                                                                 @endphp
                                                             @endif
                                                             <div class="form-check"> 
                                                                <input class="form-check-input" type="checkbox" id="roles" value="{{$role->id ?? '' }}" name="roles[]" {{$check}}>
                                                                <label class="form-check-label" for="{{$role->name}}">
                                                                  {{ucfirst($role->name)}}
                                                                </label>
                                                             </div>
                                                             @endforeach
                                                        </div>
                                                     </div>
                                                     @endif
                                                  </div>
                                               </div>
                                            </div>
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