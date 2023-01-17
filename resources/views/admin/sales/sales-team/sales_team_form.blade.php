@extends('admin.layouts.app')
@section('title', __('Sales Team'))
@section('content')
@section('styles')
<link href="{{ asset('backend/plugins\datatables\dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend/plugins\datatables\buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
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
                            {{ __('Sales Teams') }} /
                            <small>@if($action == "Add") {{ __('Add') }} @else {{ __('Edit') }} @endif</small>
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
            <div class="col-md-4">
                <a class="skin-gray-light-btn btn save-sales-team-d" href="javascript:void(0)">@if($action == 'Edit') {{ __('Update') }} @else {{ __('Save') }} @endif</a>
                <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2" href="{{ route('admin.sales-team.index') }}">{{ __('Discard') }}</a>
            </div>
            @if($action == "Edit")
                <div class="col-md-4 text-center">
                @canany(['Archive / Unarchive Sales Team','Duplicate Sales Team'])
                 <div class="quotation-right-side content-center">
                    <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                       <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                       {{ __('Action') }} <span class="caret"></span>
                       </a>
                       <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @can('Archive / Unarchive Sales Team')
                          <a class="dropdown-item" href="javascript:void(0)" onclick="archiveSaleTeam($(this))" data-model-id= "{{ Hashids::encode($model->id) }}" data-archive ="1" id="archive" @if($model->is_archive == 1) style="display: none" @endif>{{ __('Archive') }}</a>
                          <a class="dropdown-item" href="javascript:void(0)" onclick="archiveSaleTeam($(this))" data-model-id= "{{ Hashids::encode($model->id) }}" data-archive ="0"  @if($model->is_archive == 0) style="display: none" @endif  id="unarchive">{{ __('Unarchive') }}</a>
                        @endcan
                        @can('Duplicate Sales Team')
                          <a class="dropdown-item" href="{{ route('admin.sale-team.duplicate',['id'=> Hashids::encode($model->id)]) }}">{{ __('Duplicate') }}</a>
                          @endcan
                       </div>
                    </div>
                 </div>
                 @endcanany
                </div>
            @endif
        </div>
    </section>
    <section class="content">
        <div class="row box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 left-side-box" style="margin-left:0;border-left:0px;">
                        <form class="timmunity-custom-dashboard-form" id="salesteam-form" method="POST" action="{{ route('admin.sales-team.store') }}" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="{{ $action }}">
                            <input type="hidden" name="id" value="{{ isset($model) ? Hashids::encode($model->id) : '' }}">
                            <input type="hidden" name="member_list_ids" value="">
                            <input type="hidden" name="remove_member_ids" value="">

                            <div class="row">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6" style="padding-left: 0px;">
                                            <div class="form-group">
                                                <h3>{{ __('Sales Name') }}<small class="asterik" style="color:red">*</small></h3>
                                                <input type="text" name="name" class="form-control" required value="{{ isset($model) ? @$model->name : '' }}" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 pl-0 pt-1">
                                            <div class="can_be_sold">
                                                <label>
                                                    <input type="checkbox" value="1" {{ isset($model) && $model->type == 1 ? 'checked' : '' }} value="1" name="type" />
                                                    {{ __('Quotations') }}
                                                </label>
                                            </div>
                                            <!-- <div class="can_be_purchased pt-1">
                                                <input type="checkbox">
                                                <span class="color-black">Pipeline</span>
                                            </div> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 pt-3 pl-0">
                                        <div class="form-group col-sm-4">
                                            <label>{{ __('Team Leader') }}</label>
                                            <select class="form-control" name="team_lead_id">
                                                <option value="">---{{ __('Select a team lead') }}---</option>
                                                @foreach($team_leads as $lead)
                                                <option value="{{ Hashids::encode($lead->id) }}" @if(old('team_lead_id', isset($model) && $lead->id == $model->team_lead_id)) selected @endif>{{ $lead->firstname. ' ' . $lead->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label>{{ __('Invoicing Target') }}</label>
                                            <input type="text" name="invoicing_target" class="form-control" placeholder="0.00" value="{{ isset($model) ? @$model->invoicing_target : '' }}"  />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @canany(['Sales Team Member Listing','Add Team Member'])
                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-tabs mt-3">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#team-members">{{ __('Team Members') }}</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="team-members" class="tab-pane fade in active">
                                            @can('Add Team Member')
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#all_user_list" id="user_list">{{ __('Add') }}</a>
                                                </div>
                                            </div>
                                            @endcan
                                            @can('Sales Team Member Listing')
                                                <div class="row" id="list-member-d">
                                                @if(isset($members) && $members->count() > 0)
                                                    @foreach($members as $member)
                                                    <div class="col-sm-6 col-md-3 member-parent" data-member-id="{{ Hashids::encode($member->team_members->id) }}">
                                                       <a href="javascript:void(0)" onclick = "updateMember(this)" data-id ="{{ Hashids::encode($member->team_members->id) }}">
                                                          <div class="customer-box">
                                                             <div class="customer-img">
                                                                <img src="{{checkImage(asset("storage/uploads/admin/" . Hashids::encode($member->team_members->id) . '/' . $member->team_members->image),'avatar5.png')}}"  alt="User Image" width="100%" height="100%">
                                                             </div>
                                                             <div class="customer-content col-md-6">
                                                                <h3 class="customer-heading">{{ $member->team_members->firstname. ' ' .$member->team_members->lastname }}</h3>
                                                             </div>
                                                          </div>
                                                       </a>
                                                    </div>
                                                    @endforeach
                                                @endif
                                                </div>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endcanany
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal For Add New Invite user -->
        <div class="modal fade" id="all_user_list" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog big-modal1" role="document">
                <div class="modal-content">
                    <div class="sales-team-modal-generate-d">
                        <div class="modal-header">
                            <h3 class="modal-title col-sm-9" id="exampleModalLabel">
                                <span>{{ __('Add') }}:</span> {{ __('Channel Members') }}
                            </h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 pull-right mr-3">
                                      <div class="form-group">
                                          <label><strong>{{ __('Filter Record') }}</strong></label>
                                          <select class="form-control" id="filter">
                                              <option value="">{{ __('All Users') }}</option>
                                              <option value="0" selected>{{ __('Internal Users') }}</option>
                                              <option value="1">{{ __('Inactive Users') }}</option>
                                          </select>
                                      </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 ml-3 ">
                                        <span class="badge badge-success" id="totalCount"></span>
                                    </div>
                                </div>
                                <div class="row box-body">
                                    <table id="user-list-datatable" class="table table-bordered table-striped" width="100%">
                                       <thead>
                                          <tr>
                                            <th width="50px"><input type="checkbox" class="checkbox-input" name="deleteCheck[]" id="chk_all"></th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Login') }}</th>
                                            <th>{{ __('Language') }}</th>
                                            <th>{{ __('Latest Authentication') }}</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                       </tbody>
                                    </table>
                                 </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 text-left">
                                <button type="submit" class="btn btn-primary" data-url="{{ route('admin.bulk.member.selection') }}" id="select_member" disabled>{{ __('Select') }}</button>
                                <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#member-modalbox-d" onclick="resetForm()">{{ __('Create') }}</</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Discard') }}</button>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
        </div>
        <!-- Modal For Add New Invite user -->
        <div class="modal fade" id="member-modalbox-d" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sales-team-modal-generate-d">
                        <div class="modal-header">
                            <h3 class="modal-title col-sm-9" id="exampleModalLabel">
                                <span id="member_model_title"></span> {{ __('Channel Members') }}
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
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">{{ __('First Name') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input class="form-control" type="text" name="firstname" id="firstname" required />
                                                    <label id="firstname-error" class="error" for="firstname"></label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">{{ __('Last Name') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input class="form-control" type="text" name="lastname" id="lastname" required />
                                                    <label id="lastname-error" class="error" for="lastname"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                               <div class="form-group">
                                                    <label for="email">{{ __('Email') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input class="form-control" type="email" name="email" id="email" required />
                                                    <label id="email-error" class="error" for="email"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">{{ __('Phone') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input class="form-control" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxLength="15" type="text" name="phone" id="phone" required />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mobile">{{ __('Mobile') }}<small class="asterik" style="color:red">*</small></label>
                                                    <input class="form-control" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxLength="15" type="text" name="mobile" id="mobile" required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                     <!-- FILE UPLOAD -->
                                    <div class="col-md-3 pull-right">
                                        <div class="member-image avatar-upload form-group">
                                          <div class="avatar-fileds hover-effect">
                                            <div class="avatar-edit">
                                              <input type="file" class="form-control" id="memberImageUpload" name="image" />
                                              <label for="memberImageUpload"></label>
                                            </div>
                                          </div>
                                          <div class="avatar-preview" id="img_append">
                                          <img id="memberImagePreview"
                                             src="{!!checkImage(asset('storage/uploads/admin/' . Hashids::encode(@$model->id) . '/' . @$model->image),'avatar5.png')!!}" width="100%" height="100%" alt='{{asset("backend/dist/img/avatar5.png")}}' />
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 text-left">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                <button type="submit" class="btn btn-primary save-member-d" form-action="save">{{ __('Save') }}</button>
                                <button type="button" class="btn btn-danger" onclick="removeTeamMember()" id="remove_member">{{ __('Remove') }}</button>
                            </div>
                        </div>
                        </form>
                        </div>
                    </div>
                </div>
        </div>
    </section>
    @if(@$action == "Edit")
    @canany(['Add Note','View Log Note','Add Schedule Activity','View Schedule Activity','Send Message','View Send Messages'])
      <section class="bottom-section">
        <div class="row box">
           <div class="row activity-back-color">
            <div class="col-md-12">
                <div class="custom-tabs mt-3 mb-2">
                  <div class="row">
                    <div class="col-md-8">
                     @canany(['View Send Messages','Send Message','View Log Note','Add Note','View Schedule Activity','Add Schedule Activity'])
                      <ul class="nav nav-tabs">
                        @canany(['View Send Messages','Send Message'])
                        <li class="active"><a data-toggle="tab" href="#send_message">{{ __('Send Message') }}</a></li>
                        @endcanany
                        @canany(['View Log Note','Add Note'])
                        <li @if(!auth()->user()->can('View Send Messages') && !auth()->user()->can('Send Message')) class="active" @endif><a data-toggle="tab" href="#log_note">{{ __('Log Note') }}</a></li>
                        @endcanany
                        @canany(['View Schedule Activity','Add Schedule Activity'])
                        <li @if(!auth()->user()->can('View Send Messages') && !auth()->user()->can('Send Message') && !auth()->user()->can('View Log Note') && !auth()->user()->can('Add Note')) class="active" @endif><a data-toggle="tab" href="#schedual_activity">{{ __('Schedule Activity') }}</a></li>
                        @endcanany
                      </ul>
                      @endcanany
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
                      @canany(['Send Message','View Send Messages'])
                      <div id="send_message" class="tab-pane fade active in">
                        <div class="row tab-form pt-3">
                          <div class="row">
                            <div class="col-md-3">
                              @can('Send Message')
                              <a class="skin-green-light-btn btn" type="button" data-toggle="modal"  data-target="#send-message-model" onclick="clearMessageForm()"><i class="fa fa-paper-plane"></i>&nbsp;{{ __('Send Message') }}</a>
                              {!! $send_messages_view !!}
                              @endcan
                            </div>
                          </div>
                          @can('View Send Messages')
                          {!! $send_message_tab_partial_view !!}
                          @endcan
                        </div>
                      </div>
                      @endcanany
                      <!-- Log Note -->
                      @canany(['Add Note','View Log Note'])
                      <div id="log_note" class="tab-pane fade">
                        <div class="row tab-form pt-3">
                          <div class="row">
                            <div class="col-md-3">
                              @can('Add Note')
                              <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#log-note-model" onclick="clearNoteForm()"><i class="fa fa-plus"></i>&nbsp;{{ __('Add Note') }}</a>
                              {!! $log_notes_view !!}
                              @endcan
                            </div>
                          </div>
                          @can('View Log Note')
                          {!! $notes_tab_partial_view !!}
                          @endcan
                        </div>
                      </div>
                      @endcanany
                      <!-- Schedule Activity -->
                      @canany(['Add Schedule Activity','View Schedule Activity'])
                     <div id="schedual_activity" class="tab-pane fade">
                        <div class="row tab-form pt-3">
                          <div class="row">
                            <div class="col-md-3">
                               @can('Add Schedule Activity')
                              <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#schedule-activity-model" onclick="ClearScheduleActivity()"><i class="fa fa-clock-o"></i>&nbsp;{{ __('Add Schedule Activity') }}</a>
                              {!! $schedual_activities_view !!}
                              @endcan
                            </div>
                          </div>
                          @can('View Schedule Activity')
                          {!! $schedual_activity_tab_partial_view !!}
                          @endcan
                        </div>
                      </div>
                      @endcanany
                    </div>
                </div>
             </div>
          </div>
        </div>
      </section>
      @endcanany
    @endif
</div>

@endsection

@section('scripts')
<script>
var allids = [];
var remove_member_ids = [];
$('#salesteam-member-form').validate({

    onkeyup: false,
    onclick: false,
    onfocusout: false,
    ignore: [],
    errorPlacement: function (error, element) {
        //console.log(error,"Errors are listing here!");
        error.insertAfter(element);
        //toastr.error(error);
    },
    submitHandler: function (form, event) {
        event.preventDefault();
        var file_date = $('#memberImageUpload').prop('files')[0];
        var fd = new FormData();
        fd.append('_token', $('input[name="_token"]').val());
        fd.append('action', $("#req_action").val());
        fd.append('id', $("#uid").val());
        fd.append('firstname', $('#firstname').val());
        fd.append('lastname', $('#lastname').val());
        fd.append('email', $('#email').val());
        fd.append('phone', $('#phone').val());
        fd.append('mobile', $('#mobile').val());
        fd.append('image', file_date);
        $.ajax({
          url: '{{ route('admin.sales-team-member.store') }}',
          data: fd,
          type: 'POST',
          processData: false,
          contentType: false,
          success: function (resp) {
             if (resp.data && resp.data.name && resp.status == 1) {
                let _html = '';
                if($("#req_action").val() == "add") {
                _html+= '<div class="col-sm-6 col-md-3 member-parent" data-member-id='+resp.data.id+'>';
                _html+= '<a href="javascript:void(0)" onclick = "updateMember(this)" data-id ='+resp.data.id+'>';
                _html+= '<div class="customer-box">';
                _html+= '<div class="customer-img">';
                _html+= resp.data.image;
                _html+= '</div>';
                _html+= '<div class="customer-content col-md-6">';
                _html+= '<h3 class="customer-heading">' + resp.data.name +'</h3>';
                _html+= '</div>';
                _html+= '</div>';
                _html+= '</a>';
                _html+= '</div>';
                $('#list-member-d').append(_html);
                allids.push(resp.data.id);
                $("input[name=member_list_ids]").val(allids.join(", "));
            }
            else {
                    $('div[data-member-id='+resp.data.id+'] .customer-box .customer-img').html(resp.data.image);
                    $('div[data-member-id='+resp.data.id+'] .customer-content .customer-heading').html(resp.data.name);
               }

               $('#member-modalbox-d').modal('hide');
            }
            else {
                 $('#req_action').val()
                 $("#firstname-error").css('display','block');
                 $("#lastname-error").css('display','block');
                 $("#firstname-error").text(resp.data.firstname);
                 $("#lastname-error").text(resp.data.lastname);
                 $("#email-error").text(resp.data.email);
            }
          },
        });
    }
});

</script>
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
<script type="text/javascript">
   $("#user_list").click(function(){
     $("#totalCount").hide();
     $("#chk_all").prop('checked', false);
     $("#select_member").prop('disabled', true);
        var existingIds = [];
          $(".member-parent").each(function() {
              existingIds.push($(this).attr('data-member-id'));
          });
          console.log(existingIds);
        var table = $('#user-list-datatable').DataTable({
            orderCellsTop: true,
            lengthChange: false,
            responsive: true,
            serverSide: true,
            searching: false,
            bDestroy: true,
            pageLength: 8,
            ajax: {
              url: "{{ route('admin.admin-users.list') }}",
              type: 'GET',
              data: function (d) {

                d.is_archive = $("#filter").val();
                d.existingIds = existingIds;
              }
             },
            fnDrawCallback: function( oSettings ) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [
              {
                  data: 'delete_check',
                  name: 'delete_check',
                  orderable:false,
                  searchable:false
              },
              {
                  data: 'name',
                  name: 'name',
                  orderable:true
              },
              {
                  data: 'login',
                  name: 'login',
                  orderable:true
              },
              {
                  data: 'language',
                  name: 'language',
                  orderable:true
              },
              {
                  data: 'latest_authentication',
                  name: 'latest_authentication',
                  orderable:true
              },
            ]
        });
        $('#filter').change(function(){
          $('#user-list-datatable').DataTable().draw();
        });
    });
</script>
<script type="text/javascript">
function checkBoxActions(context) {
  // Checked Parent Checkbox
  var checkCount = $('[name="deleteCheck[]"]:checked').length;
 if ($('[name="deleteCheck[]"]:checked').length > 0) {
      $('#totalCount').show();
      $('#totalCount').text(checkCount+' '+"{{ __('selected') }}");
      $('#select_member').attr("disabled", false);
  } else {
      $('#totalCount').hide();
      $('#select_member').attr("disabled", true);
  }
  // Show / Hide Delete Button
  if($('.countChecks:checked').length == $('.countChecks').length) {
    $('#totalCount').text($('.countChecks:checked').length+' '+"{{ __('selected') }}");
    $('#chk_all').prop('checked',true);
  }
  else{
    $('#totalCount').text($('.countChecks:checked').length+' '+"{{ __('selected') }}");
    $('#chk_all').prop('checked',false);
  }
}
</script>
<script type="text/javascript">
  $(document).ready(function () {
        $('#chk_all').on('click', function() {
         if($(this).is(':checked',true))
         {
            var checkCount = $('.countChecks').length;
            $('#totalCount').show();
            $('#totalCount').text(checkCount+' '+"{{ __('selected') }}");
            $('#select_member').attr("disabled", false);
            $(".sub_chk").prop('checked', true);
         } else {
            $('#totalCount').hide();
            $('#select_member').attr("disabled", true);
            $(".sub_chk").prop('checked',false);
         }
        });
        // Selected Records
        $('#select_member').on('click', function(e) {


          var allVals = [];
          $(".sub_chk:checked").each(function() {
              allVals.push($(this).attr('data-id'));
          });

            var joinSelectedIds = allVals.join(",");
              $.ajax({
                  url: $(this).data('url'),
                  type: 'DELETE',
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                  data: 'ids='+joinSelectedIds,
                  beforeSend: function(){
                      // Show loader container
                      $("#ajax_loader").show();
                  },
                  success: function (resp) {
                      if (resp.member_list && resp.member_list && resp.status == 1) {
                            $('#list-member-d').append(resp.member_list);
                            $('#all_user_list').modal('hide');
                            allids.push(resp.ids);
                            $("input[name=member_list_ids]").val(allids.join(", "));
                        }

                  },
                  complete:function(data){
                      // Hide loader container
                      $("#ajax_loader").hide();
                 },
                 error: function (data) {
                    Swal.fire("{{ __('Error') }}",data.responseText,"warning");
                }
              });
    });
});
var member_modal_title1 = "{{ __('Add') }}";
var member_modal_title2 = "{{ __('Open') }}";
var swt_alert_title = "{{ __('Are you sure?') }}";
var swt_alert_txt = "{{ __('Are you sure that you want to archive this record?') }}";
var swt_confirm_btn_txt = "{{ __('Yes!') }}";
var swl_fire_title1 = "{{ __('Archived') }}";
var swl_fire_title2 = "{{ __('Warning') }}";
var swl_fire_title3 = "{{ __('Unarchived') }}";
</script>
<script src="{{ asset('backend/dist/js/common.js') }}"></script>
<script src="{{ asset('backend/dist/js/sales-team.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\jszip.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\pdfmake.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\vfs_fonts.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\buttons.html5.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\buttons.print.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\buttons.colVis.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('backend\plugins\datatables\responsive.bootstrap4.min.js') }}"></script>
@endsection
