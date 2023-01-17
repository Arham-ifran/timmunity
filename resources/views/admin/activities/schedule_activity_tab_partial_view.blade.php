<div id="planned_activities">
    @if ($schedule_activities->count() > 0)
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                    <h4 class="panel-title">
                        <a role="button" onclick="togglePlannedActivity()" data-toggle="collapse"
                            data-parent="#accordion" href="#collapseActivities" aria-expanded="true"
                            aria-controls="collapseOne">
                            {{ __('Planned Activities') }}
                            <span id="schedule_activites_counters" style="display: none">
                                <span class="badge badge-danger" id="count_overdue"></span>
                                <span class="badge badge-warning" id="count_today"></span>
                                <span class="badge badge-success" id="count_planned"></span>
                            </span>
                        </a>
                    </h4>
                </div>
                <div id="collapseActivities" class="panel-collapse collapse in" role="tabpanel"
                    aria-labelledby="headingOne">
                    <div class="panel-body">
                        @foreach ($schedule_activities as $activity)
                            @php
                                $due_date = \Carbon\Carbon::parse($activity->due_date);
                                $now = \Carbon\Carbon::now();
                                $current_date = $now->toDateString();
                                $diffDays = $due_date->diffInDays($current_date);
                            @endphp
                            @if ($diffDays == 0)
                                @php
                                    $date = __('Today');
                                    $bgClr = 'bg-warning-full';
                                    $dueDateTextClr = 't-today';
                                @endphp
                            @elseif($diffDays == 1 && $due_date < $current_date) @php
                                    $date = __('Yesterday');
                                    $bgClr = 'bg-danger-full';
                                    $dueDateTextClr = 't-overdue';
                                @endphp
                                @elseif($diffDays> 1 && $due_date < $current_date) @php
                                        $date = $diffDays . ' ' . __('days overdue');
                                        $bgClr = 'bg-danger-full';
                                        $dueDateTextClr = 't-overdue';
                                    @endphp
                                    @elseif($diffDays==1 && $due_date> $current_date)
                                        @php
                                            $date = __('Tommorrow');
                                            $bgClr = 'bg-success-full';
                                            $dueDateTextClr = 't-planned';
                                        @endphp
                                    @elseif($diffDays > 1 && $due_date > $current_date)
                                        @php
                                            $date = __('Due in') . ' ' . $diffDays . ' ' . __('days');
                                            $bgClr = 'bg-success-full';
                                            $dueDateTextClr = 't-planned';
                                        @endphp
                            @endif
                            <!-- Fetch model ID by module name -->
                            @if ($module == 'kaspersky')
                                @php $schedule_model_id = $activity->kss_subscription_id; @endphp
                            @elseif($module == "vouchers")
                                @php  $schedule_model_id = $activity->voucher_id; @endphp
                            @elseif($module == "quotations")
                                @php  $schedule_model_id = $activity->quotation_id; @endphp
                            @elseif($module == "contacts")
                                @php $schedule_model_id = $activity->contact_id;@endphp
                            @elseif($module == "customers")
                                @php $schedule_model_id = $activity->customer_id;@endphp
                            @elseif($module == "products")
                                @php $schedule_model_id = $activity->product_id;@endphp
                            @elseif($module == "productVariants")
                                @php $schedule_model_id = $activity->variant_id;@endphp
                            @elseif($module == "saleTeams")
                                @php $schedule_model_id = $activity->sales_team_id;@endphp
                            @endif
                            <!-- Activity Icon by activity types -->
                            @if ($activity->activity_type_id == 1)
                                @php $activityIcon = "fa-envelope"; @endphp
                            @elseif($activity->activity_type_id == 2)
                                @php $activityIcon = "fa-tasks"; @endphp
                            @elseif($activity->activity_type_id == 3)
                                @php $activityIcon = "fa-phone"; @endphp
                            @elseif($activity->activity_type_id == 4)
                                @php $activityIcon = "fa-users"; @endphp
                            @elseif($activity->activity_type_id == 5)
                                @php $activityIcon = "fa-upload"; @endphp
                            @endif
                            <div class="t_Activity t_ActivityBox_activity">
                                <div class="t_Activity_sidebar">
                                    <div class="t_Activity_user">
                                        <img class="t_Activity_userAvatar" src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode($activity->schedule_by_users->id) . '/' . $activity->schedule_by_users->image), 'avatar5.png') !!}"
                                            alt="{{ __('User Image') }}">
                                        <div class="t_Activity_iconContainer {{ $bgClr }}"><i
                                                class="t_Activity_icon fa {{ $activityIcon }}"></i></div>
                                    </div>
                                </div>
                                <div class="t_Activity_core">
                                    <!-- Activity Info -->
                                    <div class="t_Activity_info">
                                        <div class="t_Activity_dueDateText {{ $dueDateTextClr }}">
                                            {{ $date ?? 'null' }}:</div>
                                        <div class="t_Activity_summary">
                                            “{{ translation($activity->id, 24, app()->getLocale(), 'summary', $activity->summary) ?? $activity->activity_types->name }}”
                                        </div>
                                        <div class="t_Activity_userName">{{ __('for') }}
                                            {{ $activity->assign_to_users->firstname . ' ' . $activity->assign_to_users->lastname }}
                                        </div>
                                        <a role="button" class="t_Activity_detailsButton btn btn-link"
                                            onclick="showActivityDetail(this)"
                                            data-schedule-id="{{ Hashids::encode($activity->id) }}"><i role="img"
                                                title="Info" class="fa fa-info-circle"></i></a>
                                    </div>
                                    <!-- Activity Details -->
                                    <div class="t_Activity_details"
                                        id="t_activity_details_{{ Hashids::encode($activity->id) }}">
                                        <dl class="dl-horizontal">
                                            <dt>{{ __('Activity Type') }}</dt>
                                            <dd class="t_Activity_type">{{ $activity->activity_types->name }}</dd>
                                            <dt>{{ __('Created') }}</dt>
                                            <dd class="t_Activity_detailsCreation">
                                                {{ date('m/d/Y h:i:s A', strtotime($activity->created_at)) }}
                                                <img src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode($activity->schedule_by_users->id) . '/' . $activity->schedule_by_users->image), 'avatar5.png') !!}"
                                                    title="{{ $activity->schedule_by_users->firstname . ' ' . $activity->schedule_by_users->lastname }}"
                                                    alt="{{ $activity->schedule_by_users->firstname . ' ' . $activity->schedule_by_users->lastname }}"
                                                    class="t_Activity_detailsUserAvatar t_Activity_detailsCreatorAvatar"><span
                                                    class="t_Activity_detailsCreator">
                                                    {{ $activity->schedule_by_users->firstname . ' ' . $activity->schedule_by_users->lastname }}</span>
                                            </dd>
                                            <dt>{{ __('Assigned to') }}</dt>
                                            <dd class="t_Activity_detailsAssignation"><img src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode($activity->assign_to_users->id) . '/' . $activity->assign_to_users->image), 'avatar5.png') !!}"
                                                    title="{{ $activity->assign_to_users->firstname . ' ' . $activity->assign_to_users->lastname }}"
                                                    alt="{{ $activity->assign_to_users->firstname . ' ' . $activity->assign_to_users->lastname }}"
                                                    class="t_Activity_detailsUserAvatar t_Activity_detailsAssignationUserAvatar">
                                                {{ $activity->assign_to_users->firstname . ' ' . $activity->assign_to_users->lastname }}
                                            </dd>
                                            <dt>{{ __('Due on') }}</dt>
                                            <dd class="t_Activity_detailsDueDate"><span
                                                    class="t_Activity_deadlineDateText {{ $dueDateTextClr }}">{{ date('m/d/Y', strtotime($activity->due_date)) }}</span>
                                            </dd>
                                        </dl>
                                    </div>
                                    <!-- Activity Description  -->
                                    <div class="t_Activity_note">
                                        <p>{!! translation($activity->id, 24, app()->getLocale(), 'details', $activity->details) !!}<br></p>
                                    </div>
                                    <!-- Activity Tools -->
                                    <div name="tools" class="t_Activity_tools">
                                        <div class="popover-markup">
                                            @if ($activity->activity_type_id != 5)
                                                <a title="Mark Done"
                                                    class="t_Activity_toolButton t_Activity_markDoneButton"
                                                    onclick="showPopover()" data-toggle="popover"
                                                    data-placement="right"><i class="fa fa-check"></i>
                                                    {{ __('Mark Done') }} </a>
                                            @else
                                                <a title="Upload Document"
                                                    class="t_Activity_toolButton t_Activity_markDoneButton"><input
                                                        type="file" name="files[]" id="files" class="inputfile"
                                                        onchange="uploadScheduleFile(this)"
                                                        data-schedule-id="{{ Hashids::encode($activity->id) }}"
                                                        data-schedule-model-id="{{ Hashids::encode($schedule_model_id) }}"
                                                        data-schedule-module-name="{{ $module }}"
                                                        data-schedule-url="{{ route('admin.schedule.done.activity') }}"
                                                        data-action="Done" multiple />
                                                    <label for="files"><i
                                                            class="fa fa-upload"></i>&nbsp;{{ __('Upload Document') }}</label></a>
                                            @endif
                                            <div class="head hide"><span
                                                    style="color: #ea5959;">{{ __('Mark Done') }}</span></div>
                                            <div class="content hide">
                                                <textarea id="activity_feedback_{{ Hashids::encode($activity->id) }}"
                                                    name="activity_feedback_{{ Hashids::encode($activity->id) }}"
                                                    rows="4" cols="40"
                                                    placeholder="{{ __('Write Feedback') }}"></textarea>
                                            </div>
                                            <div class="footer hide">
                                                <a type="button" class="skin-green-light-btn btn"
                                                    onclick="markAsDone(this)"
                                                    data-schedule-id="{{ Hashids::encode($activity->id) }}"
                                                    data-schedule-model-id="{{ Hashids::encode($schedule_model_id) }}"
                                                    data-schedule-module-name="{{ $module }}"
                                                    data-mark-as-done-url="{{ route('admin.schedule.done.activity') }}"
                                                    data-action="DoneAndNext">{{ __('Done & Schedule Next') }}</a>
                                                <a type="button" class="skin-green-light-btn btn"
                                                    onclick="markAsDone(this)"
                                                    data-schedule-id="{{ Hashids::encode($activity->id) }}"
                                                    data-schedule-model-id="{{ Hashids::encode($schedule_model_id) }}"
                                                    data-schedule-module-name="{{ $module }}"
                                                    data-mark-as-done-url="{{ route('admin.schedule.done.activity') }}"
                                                    data-action="Done">{{ __('Done') }}</a>
                                                <a style="border-bottom: 2px solid #009a71;padding: 0;"
                                                    class="btn ml-2" href="javascript:void(0)"
                                                    onclick="hidePopover()">{{ __('Discard') }}</a>
                                            </div>
                                        </div>
                                        <a class="t_Activity_toolButton t_Activity_editButton"
                                            onclick="updateActivitySchedule(this)"
                                            data-schedule-id="{{ Hashids::encode($activity->id) }}"
                                            data-update-schedule-url="{{ route('admin.schedule.update.activity') }}"><i
                                                class="fa fa-pencil"></i> {{ __('Edit') }} </a><a
                                            class="t_Activity_toolButton t_Activity_cancelButton"
                                            onclick="cancelPlannedActivity(this)"
                                            data-schedule-id="{{ Hashids::encode($activity->id) }}"
                                            data-schedule-model-id="{{ Hashids::encode($schedule_model_id) }}"
                                            data-schedule-module-name="{{ $module }}"
                                            data-summary="{{ translation($activity->id, 24, app()->getLocale(), 'summary', $activity->summary) }}"
                                            data-cancel-schedule-url="{{ route('admin.schedule.cancel.activity') }}"><i
                                                class="fa fa-times"></i> {{ __('Cancel') }} </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<!-- Schedule Log Section -->
<!--  Enable: When Today Time Slot Message Exist In Existing Slots -->
<div class="row" id="add_sa_today_time_slot" style="display: none">
    <div class="user-info-bar">
        <hr class="col-md-5">
        <span class="create-day text-center" style="z-index: 999"><b
                style="color: #009a71;">{{ __('Today') }}</b></span>
        <hr class="col-md-5 pull-right">
    </div>
</div>
<!--  Append Data: When Today Time Slot Message Exist In Existing Slots -->
<div id="today_sa_time_shift"></div>
@php
$arr = [];
@endphp
@forelse($scheduled_done_activities as $row)
    <!-- Activity Icon by activity types -->
    @if ($row->activity_type_id == 1)
        @php $activityIcon = "fa-envelope"; @endphp
    @elseif($row->activity_type_id == 2)
        @php $activityIcon = "fa-tasks"; @endphp
    @elseif($row->activity_type_id == 3)
        @php $activityIcon = "fa-phone"; @endphp
    @elseif($row->activity_type_id == 4)
        @php $activityIcon = "fa-users"; @endphp
    @elseif($row->activity_type_id == 5)
        @php $activityIcon = "fa-upload"; @endphp
    @endif
    @php
        $date = date('F d, Y', strtotime($row->updated_at));
        $diffHours = $row->updated_at->diffInHours();
        $assingned_user_name = $row->assign_to_users->firstname . ' ' . $row->assign_to_users->lastname;
        $img_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    @endphp
    @if ($diffHours < 24)
        @php $date= __('Today') ; @endphp
        @elseif($diffHours> 24 && $diffHours < 48) @php $date= __('Yesterday') ; @endphp @endif
            @if ($row->assign_user_id != Auth::user()->id)
                @php
                    // $orignal_txt = __('originally assigned to');
                    $assigned_to = '<span>(' . __('originally assigned to') . ' ' . $assingned_user_name . ')</span>';
                @endphp
            @else
                @php
                    $assigned_to = '';
                @endphp
            @endif
            @if (!in_array($date, $arr))
                <div class="row">
                    <div class="user-info-bar">
                        <hr class="col-md-5">
                        <span class="create-day text-center" style="z-index: 999"><b
                                style="color: #009a71;">{{ $date }}</b></span>
                        <hr class="col-md-5 pull-right">
                    </div>
                </div>
                <input type="hidden" class="saTimeSlots" value="{{ $date }}">
                @php array_push($arr,$date); @endphp
            @endif
            <div id="new_done_activity"></div>
            <div class="box-widget">
                <div class="box-header with-border">
                    <div class="user-block">
                        <img class="img-circle" src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode($row->schedule_by_users->id) . '/' . $row->schedule_by_users->image), 'avatar5.png') !!}" alt="{{ __('User Image') }}">
                        <span class="username mb-3"><a
                                href="{{ route('admin.admin-user.edit', ['admin_user' => Hashids::encode($row->schedule_by_users->id)]) }}"><span
                                    class="activity-style"></span>{{ $row->schedule_by_users->firstname . ' ' . $row->schedule_by_users->lastname ?? '' }}</a>
                            <span class="activity-style">{{ $row->created_at->diffForHumans() }}</span></span>
                        <span class="description margin-bottom-15"><i
                                class="fa {{ $activityIcon }}"></i>&nbsp;<b>{{ $row->activity_types->name }}
                                {{ __('done') }} </b>{!! $assigned_to !!} @if (isset($row->summary)) : <span>{{ translation($row->id, 24, app()->getLocale(), 'summary', $row->summary) }}</span> @endif</span>
                        @if (isset($row->activity_feedback))
                            <span
                                class="description margin-bottom-15"><span>{{ translation($row->id, 24, app()->getLocale(), 'activity_feedback', $row->activity_feedback) }}</span></span>
                        @endif
                        @if (isset($row->details))
                            <span
                                class="description"><b>{{ __('Original note') }}:</b><br><span>{!! translation($row->id, 24, app()->getLocale(), 'details', $row->details) !!}</span></span>
                        @endif
                        @foreach ($row->activity_attachments as $attachment)
                            @if ($attachment->module_name == 'kaspersky')
                                @php $attachment_model_id = $attachment->kss_subscription_id; @endphp
                            @elseif($attachment->module_name == "vouchers")
                                @php $attachment_model_id = $attachment->voucher_id; @endphp
                            @elseif($attachment->module_name == "quotations")
                                @php $attachment_model_id = $attachment->quotation_id; @endphp
                            @elseif($attachment->module_name == "contacts")
                                @php $attachment_model_id = $attachment->contact_id; @endphp
                            @elseif($attachment->module_name == "customers")
                                @php $attachment_model_id = $attachment->customer_id; @endphp
                            @elseif($attachment->module_name == "products")
                                @php $attachment_model_id = $attachment->product_id; @endphp
                            @elseif($attachment->module_name == "productVariants")
                                @php $attachment_model_id = $attachment->variant_id; @endphp
                            @elseif($attachment->module_name == "saleTeams")
                                @php $attachment_model_id = $attachment->sales_team_id; @endphp
                            @endif
                            @if (in_array($attachment->file_extension, $img_ext))
                                <span class="description"
                                    id="remove_sa_file_{{ Hashids::encode($attachment->id) }}">
                                    <div class="customer-box" style="width: 410px;">
                                        <div class="customer-img">
                                            <img src="{{ asset('storage/uploads/attachements/ScheduleActivites/' . $attachment->module_name . '/' . Hashids::encode($attachment->schedule_activity_id) . '/' . $attachment->file_name) }}"
                                                style="height: 100%">
                                        </div>
                                        <div class="customer-content col-md-3">
                                            <h3 class="customer-heading">{{ __('File Name') }}:
                                                {{ $attachment->file_name }}</h3>
                                            <span class="email">{{ __('File Type') }}:
                                                {{ $attachment->file_extension }}</span>
                                            <a type="button" class="price"
                                                href="{{ asset('storage/uploads/attachements/ScheduleActivites/' . $attachment->module_name . '/' . Hashids::encode($attachment->schedule_activity_id) . '/' . $attachment->file_name) }}"
                                                download>{{ __('Download') }}</a>
                                            <a href="javascript:void(0)" type="button" class="price"
                                                onclick="removeSaFile($(this))"
                                                data-sa-attachment-id="{{ Hashids::encode($attachment->id) }}"
                                                data-log-sa-id="{{ Hashids::encode($attachment->schedule_activity_id) }}"
                                                data-sa-file-name="{{ $attachment->file_name }}"
                                                data-sa-file-url="{{ route('admin.log.remove-sa-file') }}"
                                                data-sa-model-id="{{ Hashids::encode($attachment_model_id) }}"
                                                data-sa-module-name="{{ $attachment->module_name }}"><i
                                                    class="fa fa-close"></i>&nbsp;{{ __('Remove') }}</a>
                                        </div>
                                    </div>
                                </span>
                            @else
                                <span class="description margin-bottom-15"
                                    id="remove_sa_file_{{ Hashids::encode($attachment->id) }}"><b>{{ __('File') }}:</b>
                                    <a style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;"
                                        class=" btn ml-2"
                                        href="{{ asset('storage/uploads/attachements/SendMessage/' . $attachment->module_name . '/' . Hashids::encode($attachment->schedule_activity_id) . '/' . $attachment->file_name) }}"
                                        download>{{ $attachment->file_name }} &nbsp;<i class="fa fa-download"></i></a>
                                    <a href="javascript:void(0)"
                                        style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;"
                                        class=" btn ml-2" onclick="removeSaFile($(this))"
                                        data-sa-attachment-id="{{ Hashids::encode($attachment->id) }}"
                                        data-log-sa-id="{{ Hashids::encode($attachment->schedule_activity_id) }}"
                                        data-sa-file-name="{{ $attachment->file_name }}"
                                        data-sa-file-url="{{ route('admin.log.remove-sa-file') }}"
                                        data-sa-model-id="{{ Hashids::encode($attachment_model_id) }}"
                                        data-sa-module-name="{{ $attachment->module_name }}"><i
                                            class="fa fa-close"></i>&nbsp;{{ __('Remove') }}</a>
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @empty
                <div class="row">
                    <div class="user-info-bar">
                        <hr class="col-md-5">
                        <span class="create-day text-center" style="z-index: 999"><b
                                style="color: #009a71;">{{ __('Today') }}</b></span>
                        <hr class="col-md-5 pull-right">
                    </div>
                </div>
                <div class="row">
                    <input type="hidden" class="saTimeSlots" value="Today">
                    <div id="new_done_activity"></div>
                    <div class="box-widget" id="emptySaBox">
                        <div class="box-header with-border">
                            <div class="user-block">
                                <span class="description"
                                    style="text-align: center;margin: 0;"><b>{{ __("Currently there's no log here") }}:</b></span>
                            </div>
                        </div>
                    </div>
                </div>
        @endforelse
