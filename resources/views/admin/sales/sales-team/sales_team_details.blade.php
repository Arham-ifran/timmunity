@extends('admin.layouts.app')
@section('title', __('Sales Team Details'))
@section('styles')
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
<style>
    span.select2.select2-container.select2-container--default.select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #499a72;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
    }

    span.tagged {
        border: 3px solid;
        border-radius: 30px;
        padding: 0 10px;
    }

    span.tagged.quote {
        border-color: #f5f91a;
        background: #f5f91a85;
    }

    span.tagged.success {
        border-color: #06f50e;
        background: #06f50e66;
    }

    span.tagged.warning {
        border-color: #f9aa1a;
        background: #f9aa1a8c;
    }

    span.tagged.danger {
        border-color: #f91a1a;
        background: #f91a1a7a;
    }

    .o_image {
        display: inline-block;
        width: 38px;
        height: 38px;
        background-image: url('https://plp123.odoo.com//web/static/src/img/mimetypes/unknown.svg');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
    }

    .o_image[data-mimetype='application/pdf'] {
        background-image: url('https://plp123.odoo.com//web/static/src/img/mimetypes/pdf.svg');
    }
    tr.order_lnes {
        cursor: pointer;
    }
    
</style>
    @endsection
    @section('content')
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
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h2>
                                    {{ __('Sales Team') }} /
                                    <small>{{ isset($model) ? @$model->name : '' }}</small>
                                </h2>
                            </div>
                            <div class="col-md-6">
                                <div class="ribbon ribbon-top-right o_widget" id="archived_ribbon" @if(@$model->is_archive == 0) style="display: none" @endif>
                                    <span class="bg-danger">
                                        {{ __('Archived') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row pb-3">
                    <div class="col-md-4 form-save-btn-div">
                        @can('Edit Sales Team')
                        <a class="skin-gray-light-btn btn" href="{{ route('admin.sales-team.edit',['sales_team'=> Hashids::encode($model->id)]) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        @endcan
                        <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.sales-team.create') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    </div>
                     {{-- @if($action == "Edit")
                        <div class="col-md-4 text-center">
                         <div class="quotation-right-side content-center">
                            <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                               <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                               {{ __('Action') }} <span class="caret"></span>
                               </a>
                               <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                  <a class="dropdown-item" href="javascript:void(0)" onclick="archiveSaleTeam($(this))" data-model-id= "{{ Hashids::encode($model->id) }}" data-archive ="1" id="archive" @if($model->is_archive == 1) style="display: none" @endif>{{ __('Archive') }}</a>
                                  <a class="dropdown-item" href="javascript:void(0)" onclick="archiveSaleTeam($(this))" data-model-id= "{{ Hashids::encode($model->id) }}" data-archive ="0"  @if($model->is_archive == 0) style="display: none" @endif  id="unarchive">{{ __('Unarchive') }}</a>
                                  <a class="dropdown-item" href="{{ route('admin.sale-team.duplicate',['id'=> Hashids::encode($model->id)]) }}">{{ __('Duplicate') }}</a>
                               </div>
                            </div>
                         </div>
                        </div>
                    @endif --}}
                </div>
            </section>
            <section class="content">
                <div class="row box">
                    <div class="box-body">
                        <div class="col-md 12">
                            <div class="row">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h1 class="green-title">{{ isset($model) ? @$model->name : '' }}</h1>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <!--  Tab Col No 01 -->
                                    <div class="col-md-6 pl-0">
                                        <div class="row">
                                            <div class="col-sm-4 static-content">
                                                <h4>
                                                    <label>
                                                        <input type="checkbox" value="1" {{ isset($model) && $model->type == 1 ? 'checked' : '' }} value="1" name="type" disabled="disabled" />
                                                        {{ __('Quotations') }}
                                                    </label>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pt-2 pb-3">
                                    <!--  Tab Col No 01 -->
                                    <div class="col-md-6 pl-0">
                                        <div class="row">
                                            <div class="col-sm-4 static-content">
                                                <h4>{{ __('Team Leader') }}</h4>
                                            </div>
                                            <div class="col-sm-8 dynamic-content">
                                                @if(isset($model->team_lead_id))
                                                <h4><a href="{{ route('admin.admin-user.edit',['admin_user'=> Hashids::encode($model->team_lead_id)]) }}">{{ ucfirst($team_leader->firstname . ' ' . $team_leader->lastname) }}</a></h4>
                                                @else
                                                <h4>-------</h4>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!--  Tab Col No 02 -->
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-sm-4 static-content">
                                                <h4>{{ __('Invoicing Target') }}</h4>
                                            </div>
                                            <div class="col-sm-8 dynamic-content">
                                                <h5>{{ $model->invoicing_target ?? '' }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2 Fields -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="custom-tabs pb-3">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#team-members">{{ __('Team Members') }}</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="team-members" class="tab-pane fade in active">
                                        <div class="row" id="list-member-d">
                                        @if(isset($members) && $members->count() > 0) 
                                            @foreach($members as $member)
                                            <div class="col-sm-6 col-md-3 member-parent" data-member-id="{{ Hashids::encode($member->team_members->id) }}">
                                               <a href="javascript:void(0)" onclick = "memberDetails(this)" data-id ="{{ Hashids::encode($member->team_members->id) }}">
                                                  <div class="customer-box">
                                                     <div class="customer-img">
                                                        <img src="{{checkImage(asset("storage/uploads/admin/" . Hashids::encode($member->team_members->id) . '/' . $member->team_members->image),'avatar5.png')}}"  alt="User Image" width="100%" height="100%">
                                                     </div>
                                                     <div class="customer-content col-md-6 col-md-3">
                                                        <h3 class="customer-heading">{{ $member->team_members->firstname. ' ' .$member->team_members->lastname }}</h3>
                                                     </div>
                                                  </div>
                                               </a>
                                            </div>
                                            @endforeach
                                        @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="member-modalbox-d" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="sales-team-modal-generate-d">
                                    <div class="modal-header">
                                        <h3 class="modal-title col-sm-9" id="exampleModalLabel">
                                            <span id="member_model_title"></span>{{ __('Team Member') }}
                                        </h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                    <form id="salesteam-member-form" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="req_action" id="req_action" value="add" />
                                        <input type="hidden" name="id" id="uid" />
                                    <div class="modal-body">
                                        <div class="modal-body">
                                            <div class="alert alert-info text-center mb-3" role="alert">{{ __('You are inviting a new user.') }}</div>
                                            <div class="row">  
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <h1 class="green-title" id="name_info"></h1>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                           <h2 class="mt-0"><span id="email_info"></span></h2>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-2">
                                                            <strong>{{ __('Phone') }}</strong>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <span id="phone_info"></span>
                                                        </div>
                                                    </div> 
                                                    <div class="row mt-2">
                                                        <div class="col-md-2">
                                                            <strong>{{ __('Mobile') }}</strong>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <span id="mobile_info"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                 <!-- FILE UPLOAD -->
                                                <div class="col-md-3 pull-right">
                                                    <div class="member-image avatar-upload form-group">
                                                      <div class="avatar-preview" id="img_info">
                                                      </div>
                                                    </div>
                                                </div>
                                            </div>       
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                    </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Bottom- section -->
                  <section class="bottom-section">
                    <div class="row box">
                       <div class="row activity-back-color">
                        <div class="col-md-12">
                            <div class="custom-tabs mt-3 mb-2">
                              <div class="row">
                                <div class="col-md-8">
                                  <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#send_message">{{ __('Send Message') }}</a></li>
                                    <li><a data-toggle="tab" href="#log_note">{{ __('Log Note') }}</a></li>
                                    <li><a data-toggle="tab" href="#schedual_activity">{{ __('Schedule Activity') }}</a></li>
                                  </ul>
                                </div>
                                <div class="col-md-4 pull-right text-right follower-icons">
                                   <!-- Attachments View -->
                                   {!! $attachments_partial_view !!}
                                   @if($is_following == 1 )
                                     <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="7" id="following"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                                     <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="7" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                                  @else
                                      <a class="followButton" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="7"id="followBtn" >{{ __('Follow') }}</a>
                                        <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="7" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                                  @endif
                                  <a class="dropdown-toggle" href="javascript:void(0)" title="Show Followers"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;<span id="follower_counter">{{ $followers->count() }} </span></a>
                                  <!-- Follower List -->
                                  <ul class="follower_list" id="f_list">
                                    @forelse ($followers as $follower)
                                      <li><a href="{{ route('admin.contacts.edit',['contact'=> Hashids::encode($follower->contacts->id)]) }}" target="_blank">{{ $follower->contacts->name }}</a></li>
                                     @empty
                                     <li><div class="text-center">{{ __("Currently there's no follower") }}</div></li>
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
                                          <a class="skin-green-light-btn btn" type="button" data-toggle="modal"  data-target="#send-message-model" onclick="clearMessageForm()"><i class="fa fa-paper-plane"></i>&nbsp;{{ __('Send Message') }}</a>
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
                                          <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#log-note-model" onclick="clearNoteForm()"><i class="fa fa-plus"></i>&nbsp;{{ __('Add Note') }}</a>
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
                                          <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#schedule-activity-model" onclick="ClearScheduleActivity()"><i class="fa fa-clock-o"></i>&nbsp;{{ __('Add Schedule Activity') }}</a>
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
@endsection
@section('scripts')
<script>
var salesteam_form_action = "{{ route('admin.sales-team.store') }}";
var add_new_member_id = [];
var update_member_url = '{{ route('admin.sales-team-member.update.member') }}';
var is_archived_url = '{{ route('admin.sale-team.archive.record') }}';
var remove_member_url = '{{ route('admin.sale-team.remove.member') }}';
var add_new_contact_url = '{{ route('admin.log.add-new-contact') }}';
var do_follow_url = '{{ route('admin.log.user-following') }}';
var do_unfollow_url = '{{ route('admin.log.user-un-follow') }}';
</script>
<script src="{{ asset('backend/dist/js/common.js') }}"></script>
<script src="{{ asset('backend/dist/js/sales-team.js') }}"></script>
@endsection
