@extends('admin.layouts.app')
@section('title',  __('F Secure'))
@section('content')
@section('styles')
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
@endsection
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
  <section class="content-header top-header">
      <div class="row">
          <div class="col-md-12">
            <h2>
            F-Secure Subscription /
            <small>{{ $model->license_key ?? ''}}</small>
          </h2>
          </div>
      </div>
      <div class="row">
          <div class="box-header">
            <div class="row">
                <div class="col-md-4">
                  <a class="skin-gray-light-btn btn hide-action" id="edit_btn" href="{{ route('admin.f-secure.edit',['f_secure'=> Hashids::encode($model->id)]) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                  @if($model->status != 1 && $model->status != 3)
                   <a class="skin-gray-light-btn btn" id="edit_btn" href="{{ route('admin.f-secure.edit',['f_secure'=> Hashids::encode($model->id)]) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                  @endif
                   <a class="skin-green-light-btn btn @if($model->status !=1 && $model->status !=3) ml-2 @endif" id="dlt_btn" href="{{ route('admin.f-secure.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                   <a style="border-bottom: 2px solid #009a71;" class="btn ml-2" href="{{ route('admin.f-secure.index') }}">Discard</a>
                </div>
                <div class="col-md-4 text-center">
                   <div class="quotation-right-side content-center">
                        <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                          <!-- <i class="fa fa-filter" aria-hidden="true"></i> -->
                          <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                          Action <span class="caret"></span>
                          </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <form action="{{ url('admin/f-secure', Hashids::encode($model->id)) }}" method="POST">
                                @method('delete')
                                @csrf
                                <a class="dropdown-item" type="button" onclick="deleteAlert(this)" >Delete</a>
                                <input class="hidden deleteSubmit" type="submit" value="Delete">
                              </form>
                              <a class="dropdown-item" href="{{ route('admin.f-secure.duplicate',['id'=> Hashids::encode($model->id)]) }}">Duplicate</a>
                            </div>
                        </div>
                   </div>
                </div>
                {{-- <div class="col-md-4">
                  <ol class="breadcrumb pages-arrow pull-right">
                          <li><a href="#"> 1-8</a></li>
                          <li class="active">1</li>
                          <a href="#"> <i class="fa fa-angle-left"> </i></a>
                          <a href="#"><i class="fa fa-angle-right"> </i></a>
                    </ol>
                </div> --}}
            </div>
          </div>
      </div>
  </section>

  <!--  content -->
  <section class="content">
    <div class="box">
          <div class="row">
              <div class="box-header">
                <div class="row main-breadcrumb-div">
                    <div class="col-md-7 pl-0">
                       <div class="breadcrumb mb-0">
                          @if($model->status == 1 && $model->end_date > date('Y-m-d'))
                          <a class="active" id="soft_cancel" onclick="licenseHold($(this))" data-id="{{ Hashids::encode($model->id) }}">Soft Cancel</a>
                          <a class="active" id="hard_cancel" onclick="hardCancel($(this))" data-id="{{ Hashids::encode($model->id) }}">Hard Cancel</a>
                          <a class="active" id="pause" onclick="licenseHold($(this))" data-id="{{ Hashids::encode($model->id) }}">Pause</a>
                          <a class="active" id="renew" href="{{ route('admin.f-secure.edit',['f_secure'=> Hashids::encode($model->id)]) }}"> Renew</a>
                          <a class="active hide-action" id="resume" onclick="resumedLicense($(this))" data-id="{{ Hashids::encode($model->id) }}"> Resume</a>
                          <a class="active" id="get_info" href="{{ route('admin.f-secure.show',['f_secure'=> Hashids::encode($model->id)]) }}">Get Info</a>
                          @elseif($model->status == 3)
                          <a class="active" id="resume" onclick="resumedLicense($(this))" data-id="{{ Hashids::encode($model->id) }}">Resume</a>
                          <a class="active hide-action" id="soft_cancel" onclick="licenseHold($(this))" data-id="{{ Hashids::encode($model->id) }}">Soft Cancel</a>
                          <a class="active hide-action" id="hard_cancel" onclick="hardCancel($(this))" data-id="{{ Hashids::encode($model->id) }}">Hard Cancel</a>
                          <a class="active hide-action" id="pause" onclick="licenseHold($(this))" data-id="{{ Hashids::encode($model->id) }}">Pause</a>
                          <a class="active hide-action" id="renew" href="{{ route('admin.f-secure.edit',['f_secure'=> Hashids::encode($model->id)]) }}"> Renew</a>
                          <a class="active" id="get_info" href="{{ route('admin.f-secure.show',['f_secure'=> Hashids::encode($model->id)]) }}">Get Info</a>
                          @elseif(($model->end_date < date('Y-m-d') && $model->status == 1) || ($model->status == 4))
                          <a class="active" id="renew" href="{{ route('admin.f-secure.edit',['f_secure'=> Hashids::encode($model->id)]) }}"> Renew</a>
                          <a class="active" id="get_info" href="{{ route('admin.f-secure.show',['f_secure'=> Hashids::encode($model->id)]) }}">Get Info</a>
                          @else
                          <a class="active" id="get_info" href="{{ route('admin.f-secure.show',['f_secure'=> Hashids::encode($model->id)]) }}">Get Info</a>
                          @endif
                       </div>
                    </div>
                    <div class="col-md-5 pull-right text-right">
                       <ul class="breadcrumb custom-breadcrumb mb-0">
                          {{-- @if(isset($model->products->product_name) && $model->status == 0)
                          <li class="active" id="breadcrumb_draft">Draft</li>
                          <li class="active hide-status" id="breadcrumb_active">Active</li>
                          <li class="active hide-status" id="breadcrumb_hold">Hold</li>
                          <li class="active hide-status" id="breadcrumb_hard_cancel">Hard Cancel</li> --}}
                          @if($model->end_date > date('Y-m-d') && $model->status == 1)
                          {{-- <li>Draft</li> --}}
                          <li class="active" id="breadcrumb_active">Active</li>
                          <li class="active hide-status" id="breadcrumb_hold">Hold</li>
                          <li class="active hide-status" id="breadcrumb_hard_cancel">Hard Cancel</li>
                          @elseif(!isset($model->products->product_name) || $model->status == 2)
                          {{-- <li>Draft</li> --}}
                          <li>Active</li>
                          <li class="active">In Error</li>
                          @elseif($model->status == 3)
                          {{-- <li>Draft</li> --}}
                          <li id="breadcrumb_active">Active</li>
                          <li class="active" id="breadcrumb_hold">Hold</li>
                          <li class="active hide-status" id="breadcrumb_hard_cancel">Hard Cancel</li>
                          @elseif($model->status == 4)
                          {{-- <li>Draft</li> --}}
                          <li>Active</li>
                          <li class="active">Hard Cancel</li>
                          @elseif($model->end_date < date('Y-m-d') && $model->status == 1)
                          {{-- <li>Draft</li> --}}
                          <li>Active</li>
                          <li class="active">Expired</li>
                          @endif
                       </ul>
                    </div>
                </div>
              </div>
              <!-- Soft cancel Popup -->
              <div class="modal fade" id="hardCancel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h3 class="modal-title col-md-9 pl-0" id="exampleModalLabel">Hard Cancel</h3>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                       <i class="fa fa-times"></i>
                      </button>
                    </div>
                    <div class="modal-body">
                        <span>Are you sure want to cancel the licence?</span>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                      <a href="kss-subscription.html" type="button" class="btn btn-primary">Yes</a>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End Hard Cancel -->
          </div>
          <!-- /.box-header -->
          <div class="box-body">
          <div class="col-md-12">
            <div class="row">
                <div class="row">
                  <div class="col-md-9 pl-0">
                    <h1 class="green-title">{{ $model->license_key ?? ''}}</h1>
                  </div>
                </div>
                <hr>
                <div class="row pb-3">
                      <!--  Tab Col No 01 -->
                    <div class="col-md-6 pl-0">
                      <div class="row">
                        <div class="col-sm-4 static-content">
                          <h4>Partner</h4>
                        </div>
                        <div class="col-sm-8 dynamic-content">
                          <h4><a href="{{ route('admin.admin-user.edit',['admin_user'=> Hashids::encode($model->id)]) }}">{{ $model->partners->name ?? ''}}</a></h4>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-4 static-content">
                          <h4>Subscriber ID</h4>
                        </div>
                        <div class="col-sm-8 dynamic-content">
                          <h4>{{ $model->subscriber_id ?? ''}}</h4>
                      </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-4 static-content">
                          <h4>Product</h4>
                        </div>
                        <div class="col-sm-8 dynamic-content">
                          <h4><a href="javascript:void(0)">{{ isset($model->products->product_name) ? $model->products->product_name.'('. $value .',' . $duration .' '.'Year'.')' : '' }}</a></h4>
                        </div>
                      </div>
                    </div>
                       <!--  Tab Col No 02 -->
                    <div class="col-md-6">
                      <div class="row">
                        <div class="col-sm-4 static-content">
                          <h4>Start Date</h4>
                        </div>
                        <div class="col-sm-8 dynamic-content">
                          <h4>{{ date('m/d/Y', strtotime($model->start_date)) }}</h4>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-4 static-content">
                          <h4>End Date</h4>
                        </div>
                        <div class="col-sm-8 dynamic-content">
                          <h4>{{ date('m/d/Y', strtotime($model->end_date)) }}</h4>
                      </div>
                      </div>
                      @if(($type == "Year" && $diffInDays < $duration*365) || ($type == "Month" && $diffInMonths < $duration))
                      <div class="row">
                        <div class="col-sm-4 static-content">
                          <h4>Error</h4>
                        </div>
                        <div class="col-sm-8 dynamic-content">
                          <h4 style="border-bottom: 1px dashed #00000078;">Incorrect subscription end date:the end date specified in the order is less than selected subscription period.</h4>
                        </div>
                      </div>
                      @elseif(($type == "Year" && $diffInDays > $duration*365) || ($type == "Month" && $diffInMonths > $duration))
                      <div class="row">
                        <div class="col-sm-4 static-content">
                          <h4>Error</h4>
                        </div>
                        <div class="col-sm-8 dynamic-content">
                          <h4 style="border-bottom: 1px dashed #00000078;">Incorrect subscription end date:the end date specified in the order is greater than selected subscription period.</h4>
                        </div>
                      </div>
                      @elseif(!isset($model->products->product_name))
                      <div class="row">
                        <div class="col-sm-4 static-content">
                          <h4>Error</h4>
                        </div>
                        <div class="col-sm-8 dynamic-content">
                          <h4 style="border-bottom: 1px dashed #00000078;">Incorrect subscription variant :The subscription variant has been missing from the system.</h4>
                        </div>
                      </div>
                      @endif
                    </div>
                </div>
            </div>
          </div>
      </div>
  </section>
    <!-- /.content -->
     <!-- /.box -->
 <section class="bottom-section">
    <div class="row box">
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
</div>
<!-- /.content-wrapper -->
@endsection
@section('scripts')
<!-- Select2 -->
<script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript">
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
