@extends('admin.layouts.app')
@section('title',  __('F Secure'))
@section('styles')
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
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
    <!-- Content Header (Page header) -->
      <section class="content-header top-header">
         <div class="row">
            <div class="col-md-12">
              <h2>
             F-Secure Subscription /
             @if($action == "Add") <small>New</small> @elseif($action == "Edit") <small>Edit</small> @else <small>Duplicate</small> @endif
            </h2>
            </div>
          </div>
      </section>
      <!-- Content -->
      <section class="content">
        <div class="main-box box">
            <div class="row">
                <div class="box-header">
                  <div class="row main-breadcrumb-div">
                     <div class="col-md-7 pl-0">
                       <div class="breadcrumb mb-0">
                          @if(!isset($model->products->product_name) && $action="Add")
                           <a class="active" id="active" onclick="activeLicense($(this))" data-id="{{ Hashids::encode(@$model->id) }}">Active</a>
                          <a class="active hide-action" id="soft_cancel" onclick="licenseHold($(this))" data-id="{{ Hashids::encode(@$model->id) }}">Soft Cancel</a>
                          <a class="active hide-action" id="hard_cancel" onclick="hardCancel($(this))" data-id="{{ Hashids::encode(@$model->id) }}">Hard Cancel</a>
                          <a class="active hide-action" id="pause" onclick="licenseHold($(this))" data-id="{{ Hashids::encode(@$model->id) }}">Pause</a>
                          <a class="active hide-action" id="renew" href="{{ (isset($model)) ? route('admin.f-secure.edit',['kaspersky'=> Hashids::encode(@$model->id)]) : ''}}"> Renew</a>
                          <a class="active hide-action" id="resume" onclick="resumedLicense($(this))" data-id="{{ Hashids::encode(@$model->id) }}"> Resume</a>
                          <a class="active hide-action" id="get_info" href="{{ (isset($model)) ? route('admin.f-secure.show',['kaspersky'=> Hashids::encode(@$model->id)]) : '' }}">Get Info</a>

                          @elseif(@$model->status == 3)
                          <a class="active" id="resume" onclick="resumedLicense($(this))" data-id="{{ Hashids::encode(@$model->id) }}">Resume</a>
                          <a class="active hide-action" id="soft_cancel" onclick="licenseHold($(this))" data-id="{{ Hashids::encode(@$model->id) }}">Soft Cancel</a>
                          <a class="active hide-action" id="hard_cancel" onclick="hardCancel($(this))" data-id="{{ Hashids::encode(@$model->id) }}">Hard Cancel</a>
                          <a class="active hide-action" id="pause" onclick="licenseHold($(this))" data-id="{{ Hashids::encode(@$model->id) }}">Pause</a>
                          <a class="active hide-action" id="renew" href="{{ (isset($model)) ? route('admin.f-secure.edit',['kaspersky'=> Hashids::encode(@$model->id)]) : '' }}"> Renew</a>
                          <a class="active" id="get_info" href="{{ (isset($model)) ? route('admin.f-secure.show',['kaspersky'=> Hashids::encode(@$model->id)]) : '' }}">Get Info</a>
                          @elseif(@$model->end_date < date('Y-m-d') && @$model->status == 1)
                          <a class="active" id="renew" href="{{ (isset($model)) ? route('admin.f-secure.edit',['kaspersky'=> Hashids::encode(@$model->id)]) : '' }}"> Renew</a>
                          <a class="active" id="get_info" href="{{ (isset($model)) ? route('admin.f-secure.show',['kaspersky'=> Hashids::encode(@$model->id)]) : '' }}">Get Info</a>
                          @else
                          <a class="active" id="get_info" href="{{ (isset($model)) ? route('admin.f-secure.show',['kaspersky'=> Hashids::encode(@$model->id)]) : '' }}">Get Info</a>
                          @endif
                       </div>
                     </div>
                     <div class="col-md-5 pull-right text-right">
                        <ul class="breadcrumb custom-breadcrumb mb-0">
                          @if(@$model->status == 2)
                          <li>Draft</li>
                          <li>Active</li>
                          <li class="active">In Error</li>
                          @elseif(@$model->status == 2 && $action == "Edit")
                          <li>Draft</li>
                          <li>Active</li>
                          <li class="active">In Error</li>
                          @elseif(@$model->status == 3)
                          <li>Draft</li>
                          <li id="breadcrumb_active">Active</li>
                          <li class="active" id="breadcrumb_hold">Hold</li>
                          <li class="active hide-status" id="breadcrumb_hard_cancel">Hard Cancel</li>
                          @elseif(@$model->status == 4)
                          <li>Draft</li>
                          <li>Active</li>
                          <li class="active">Hard Cancel</li>
                          @elseif(@$model->end_date < date('Y-m-d') && @$model->status == 1)
                          <li>Draft</li>
                          <li>Active</li>
                          <li class="active">Expired</li>
                          @endif
                        </ul>
                    </div>
                  </div>
                </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="quotations-form-container">
                  <div class="box box-success box-solid">
                      <div class="box-header with-border">
                        <h3 class="box-title">Create New F-Secure Subscription</h3>

                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                          </button>
                        </div>
                        <!-- /.box-tools -->
                      </div>
                      <!-- /.box-header -->
                      <div class="box-body" style="">
                          <form class="timmunity-custom-dashboard-form form-validate" action="{{ route('admin.f-secure.store') }}" method="post" enctype="multipart/form-data">
                          	@csrf
                          	<input type="hidden" name="id" value="{!! Hashids::encode(@$model->id) !!}">
                            <input type="hidden" name="action" value="{!!$action!!}">
                                  <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Partner</label>
                                        <select class="form-control @error('partner_id') is-invalid @enderror" name="partner_id" required>
                                         <option value="">---Select Partner---</option>
                                         @foreach($partners as $partner)
                                         <option value="{{ $partner->id }}" @if(old('partner_id', isset($model) && $partner->id == $model->partner_id)) selected @endif>{{ $partner->name }}</option>
                                         @endforeach
                                        </select>
                                        @error('partner_id')
		                                <div id="partner_id-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
		                                @enderror
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Subscriber ID</label>
                                        <input class="form-control @error('subscriber_id') is-invalid @enderror" type="text" name="subscriber_id" value="{{old('subscriber_id', $model->subscriber_id ?? uniqid())}}" required>
                                        @error('subscriber_id')
		                                <div id="subscriber_id-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
		                                @enderror
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Product</label>
                                        <select class="form-control @error('product_id') is-invalid @enderror" name="product_id" required>
                                         <option value="">---Select Product---</option>
                                         @foreach($products as $prod_details)
                                         <option @if(old('product_id', isset($model) && $prod_details->product->id == $model->product_id)) selected @endif value="{{ Hashids::encode($prod_details->id) }}">
                                              {{ $prod_details->product->product_name }}
                                              (
                                                   @foreach($prod_details->variation_details as $index => $variation_details)
                                                       @if($index > 0) , @endif {{ $variation_details->attribute_value .'  '. $variation_details->attached_attribute->attribute_name }}
                                                    @endforeach
                                              )
                                          </option>
                                         @endforeach
                                        </select>
                                        @error('product_id')
		                                <div id="product_id-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
		                                @enderror
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Start Date</label>
                                      <div class="input-group date">
                                        <div class="input-group-addon">
                                          <i class="fa fa-calendar"></i>
                                        </div>
                                         <input class="form-control @error('start_date') is-invalid @enderror" type="text" name="start_date" id="start_date" placeholder="mm/dd/yyyy" value="{{(isset($model) ? date('m/d/Y', strtotime($model->start_date)) : '' )}}" required>
                                      </div>
                                      @error('start_date')
		                                <div id="start_date-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
		                                @enderror
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>End Date</label>
                                      <div class="input-group date">
                                        <div class="input-group-addon">
                                          <i class="fa fa-calendar"></i>
                                        </div>
                                        <input class="form-control @error('end_date') is-invalid @enderror" type="text" name="end_date" id="end_date" placeholder="mm/dd/yyyy" value="{{(isset($model) ? date('m/d/Y', strtotime($model->end_date)) : '' )}}" required>
                                      </div>
                                    @error('end_date')
		                                <div id="end_date-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
		                                @enderror
                                    </div>
                                  </div>
                                  <div class="col-md-12 pt-4">
  	                                 <div class="row pull-right">
  	                                  <button type="submit"
  	                                     class="skin-green-light-btn btn ml-2">Save</button>
  	                                  <a style="border-bottom: 2px solid #009a71;" class=" btn ml-2"
  	                                     href="{{ route('admin.f-secure.index') }}">Discard</a>
  	                                </div>
  	                              </div>
                            </form>
                      </div>
                      <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
                </div>
                </div>
            </div>

        </div>
      </section>
      <!-- / Content -->
      <!-- /.box -->
      @if(@$action == "Edit")
      <section class="bottom-section">
        <div class="row bottom-section">
          <div class="row activity-back-color">
            <div class="col-md-12">
                <div class="custom-tabs mt-3 mb-2">
                  <div class="row">
                    <div class="col-md-8">
                      <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#send_message">Send Message</a></li>
                        <li><a data-toggle="tab" href="#log_note">Log Note</a></li>
                        <li><a data-toggle="tab" href="#schedual_activity">Schedule Activity</a></li>
                      </ul>
                    </div>
                    <div class="col-md-4 pull-right text-right follower-icons">
                      <!-- Attachments View -->
                      {!! $attachments_partial_view !!}
                      @if($is_following == 1 )
                         <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="0" id="following"><i class="fa fa-check"></i>&nbsp;Following</a>
                         <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="0" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;Following</a>
                      @else
                          <a class="followButton" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="0"id="followBtn" >Follow</a>
                          <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="0" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;Following</a>
                      @endif
                      <a class="dropdown-toggle" href="javascript:void(0)" title="Show Followers"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;<span id="follower_counter">{{ $followers->count() }} </span></a>
                      <!-- Follower List -->
                      <ul class="follower_list" id="f_list">
                        @forelse ($followers as $follower)
                          <li><a href="{{ route('admin.contacts.edit',['contact'=> Hashids::encode($follower->contacts->id)]) }}" target="_blank">{{ $follower->contacts->name }}</a></li>
                         @empty
                         <li><div class="text-center">Currently there's no follower</div></li>
                        @endforelse
                      </ul>

                    </div>
                  </div>
                    <div class="tab-content custom-tabs-style custom-tabs-pd-set">
                      <!--  Send Messages -->
                      <div id="send_message" class="tab-pane fade active in">
                        <div class="row tab-form pt-3">
                          <div class="row">
                            <div class="col-md-3">
                              <a class="skin-green-light-btn btn" type="button" data-toggle="modal"  data-target="#send-message-model" onclick="clearMessageForm()"><i class="fa fa-paper-plane"></i>&nbsp; Send Message</a>
                              {!! $send_messages_view !!}
                            </div>
                          </div>
                          {!! $send_message_tab_partial_view !!}
                        </div>
                      </div>
                      <!-- Log Note -->
                      <div id="log_note" class="tab-pane fade">
                        <div class="row tab-form pt-3">
                          <div class="row">
                            <div class="col-md-3">
                              <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#log-note-model" onclick="clearNoteForm()"><i class="fa fa-plus"></i>&nbsp; Add Note</a>
                              {!! $log_notes_view !!}
                            </div>
                          </div>
                          {!! $notes_tab_partial_view !!}
                        </div>
                      </div>
                      <!-- Schedule Activity -->
                     <div id="schedual_activity" class="tab-pane fade">
                        <div class="row tab-form pt-3">
                          <div class="row">
                            <div class="col-md-3">
                              <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#schedule-activity-model" onclick="ClearScheduleActivity()"><i class="fa fa-clock-o"></i>&nbsp; Add Schedule Activity</a>
                              {!! $schedual_activities_view !!}
                            </div>
                          </div>
                          {!! $schedual_activity_tab_partial_view !!}
                        </div>
                      </div>
                    </div>
                </div>
             </div>
          </div>
        </div>
      </section>
    @endif
  </div>
@endsection
@section('scripts')
<!-- Select2 -->
<script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript">

// For Start and end date validations
$(document).ready(function(){
 $("#start_date").datepicker({
     autoclose: true,
     todayHighlight: true
 }).on('changeDate', function (selected) {
     var minDate = new Date(selected.date.valueOf());
     $('#end_date').datepicker('setStartDate', minDate);
 });

 $("#end_date").datepicker({
     autoclose: true,
 }).on('changeDate', function (selected) {
         var minDate = new Date(selected.date.valueOf());
         $('#start_date').datepicker('setEndDate', minDate);
 });
});

// Actions URL's
var add_new_contact_url = '{{ route('admin.log.add-new-contact') }}';
var do_follow_url = '{{ route('admin.log.user-following') }}';
var do_unfollow_url = '{{ route('admin.log.user-un-follow') }}';
var activation_url = '{{ route('admin.license.activation') }}';
var hold_url = '{{ route('admin.license.hold') }}';
var resumed_url = '{{ route('admin.license.resumed') }}';
var blocked_url = '{{ route('admin.license.blocked') }}';
</script>
<script src="{{ asset('backend/dist/js/common.js') }}"></script>
<script src="{{ asset('backend/dist/js/kaspersky.js') }}"></script>
@endsection
