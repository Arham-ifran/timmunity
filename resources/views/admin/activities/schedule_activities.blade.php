<!-- Sechdule activity -->
 <div class="modal fade" id="schedule-activity-model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title col-md-9 pl-0" id="exampleModalLabel">{{ __('Schedule Activity') }}</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="schedule_activity_form" action="javascript:void(0)" accept-charset="utf-8" enctype="multipart/form-data">
            {{-- <input type="hidden" id="sa_model_id" value="{{  Hashids::encode($model_id) }}">
            <input type="hidden" id="sa_module" value="{{ $module }}">
            <input type="hidden" id="sa_msg_url" value="{{ route('admin.schedule.activity') }}">
            <input type="hidden" id="schedule_user_log_id" value="{{ Hashids::encode($schedule_user_log_id) }}"> --}}
            <input type="hidden" id="action" value="">
            <input type="hidden" id="id" value="">
            <div class="row">
              <div class="form-group col-md-6">
                <label>{{ __('Activity Type') }}</label>
                <select class="form-control" name="activity_type_id" id="activity_type_id">
                  @foreach($schedule_activity_types as $type)
                  <option value="{{ $type->id }}">{{ $type->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6" id="col_due_date">
                <div class="form-group">
                <label>{{ __('Due Date') }}</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                   <input class="form-control" type="text" name="due_date" id="due_date">
                </div>
              </div>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6">
                <label>{{ __('Summary') }}</label>
                <input type="text" class="form-control" placeholder="{{ __('e.g. Discuss porposal') }}" name="summary" id="summary">
              </div>
              <div class="form-group col-md-6" id="col_assing_to">
              <label>{{ __('Assigned to') }}</label>
              <select class="form-control" name="assign_user_id" id="assign_user_id">
                @foreach($schedule_users as $user)
                <option value="{{ $user->id }}" @if($user->id == Auth::user()->id) selected @endif>{{ $user->firstname .' '. $user->lastname }}</option>
                @endforeach
              </select>
              </div>
            </div>
            <div class="row" id="row_activity_details">
              <div class="form-group col-md-12">
                 <textarea class="summernote form-control" name="details" id="details"></textarea>
              </div>
            </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="text-left col-md-12">
              <button type="button" class="btn btn-primary" id="schedule_btn" onclick="activitySchedule(this)" data-sa-model-id = "{{  Hashids::encode($model_id) }}" da-sa-partner-id ="{{ Hashids::encode($partner_id) }}" data-sa-module-type ="{{ $module_type }}" data-sa-module ="{{ $module }}" data-sa-url = "{{ route('admin.schedule.activity') }}" date-sa-user-log-id= "{{ Hashids::encode($schedule_user_log_id) }}" data-schedule-action = "Add" data-schedule-flag= "0">{{ __('Schedule') }}</button>
              <button type="button" class="btn btn-secondary" onclick="activitySchedule(this)" data-sa-model-id = "{{  Hashids::encode($model_id) }}" da-sa-partner-id ="{{ Hashids::encode($partner_id) }}" data-sa-module-type ="{{ $module_type }}" data-sa-module ="{{ $module }}" data-sa-url = "{{ route('admin.schedule.activity') }}" date-sa-user-log-id= "{{ Hashids::encode($schedule_user_log_id) }}" data-schedule-action = "Done" data-schedule-flag= "1">{{ __('Mark as done') }}</button>
              <button type="button" class="btn btn-secondary" onclick="activitySchedule(this)" data-sa-model-id = "{{  Hashids::encode($model_id) }}" da-sa-partner-id ="{{ Hashids::encode($partner_id) }}" data-sa-module-type ="{{ $module_type }}" data-sa-module ="{{ $module }}" data-sa-url = "{{ route('admin.schedule.activity') }}" date-sa-user-log-id= "{{ Hashids::encode($schedule_user_log_id) }}" data-schedule-action = "DoneAndNext" data-schedule-flag= "2">{{ __('Done & Schedule Next') }}</button>
              <button type="button" class="btn btn-secondary"  data-dismiss="modal">{{ __('Discard') }}</button>
            </div>
        </div>
      </div>
    </div>
 </div>

<!-- / Sechdule activity -->