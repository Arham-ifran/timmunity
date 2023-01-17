 <!--  Enable: When Today Time Slot Not Exist In Existing Slots -->
  <div class="row" id="add_today_time_slot" style="display: none">
    <div class="user-info-bar">
      <hr class="col-md-5">
      <span class="create-day text-center" style="z-index: 999"><b style="color: #009a71;">{{ __('Today') }}</b></span>
      <hr class="col-md-5 pull-right">
    </div>   
  </div>
  <!--  Append Data: When Today Time Slot Not Exist In Existing Slots -->
  <div id="today_time_shift"></div>
  @php 
  $arr = [];
  @endphp
  @forelse($log_notes as $row)
  @php
  $date = date('F d, Y',strtotime($row->created_at));
  $diffHours =  $row->created_at->diffInHours();
  $img_ext = array("jpg", "jpeg", "png", "gif","webp");
  @endphp
  @if($diffHours < 24) @php $date =  __('Today'); @endphp @elseif($diffHours > 24 && $diffHours < 48) @php $date=__('Yesterday'); @endphp @endif
  @if(!in_array($date, $arr))
  <div class="row">
    <div class="user-info-bar">
      <hr class="col-md-5">
      <span class="create-day text-center" style="z-index: 999"><b style="color: #009a71;">{{ $date }}</b></span>
      <hr class="col-md-5 pull-right">
    </div>   
  </div>
  <input type="hidden" class="timeSlots" value="{{ $date }}">
  @php array_push($arr,$date); @endphp
  @endif
  <div id="new_log_note"></div>
  <div class="box-widget">
    <div class="box-header with-border">
      <div class="user-block">
        <img class="img-circle" src="{!!checkImage(asset('storage/uploads/admin/' . Hashids::encode($row->log_note_users->id) . '/' . $row->log_note_users->image),'avatar5.png')!!}" alt="{{ __('User Image') }}">
        <span class="username mb-3"><a href="{{ route('admin.admin-user.edit',['admin_user'=> Hashids::encode($row->log_note_users->id)]) }}"><span class="activity-style"></span>{{ $row->log_note_users->firstname .' '. $row->log_note_users->lastname ?? ''}}</a> <span class="activity-style">{{ $row->created_at->diffForHumans() }}</span></span>
        <span class="description" style="margin-bottom: 15px"><b>{{ __('Note') }}:</b><span>{{ strip_tags(translation($row->id,15,app()->getLocale(),'note',$row->note)) }}</span></span>
        @foreach ($row->activity_attachments as $attachment)
            @if($attachment->module_name == "kaspersky") 
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
          @if(in_array($attachment->file_extension, $img_ext))
            <span class="description" id="remove_note_file_{{ Hashids::encode($attachment->id) }}">
              <div class="customer-box" style="width: 410px;">
                <div class="customer-img">
                  <img src="{{ asset('storage/uploads/attachements/LogNote/'. $attachment->module_name . '/' . Hashids::encode($attachment->log_note_id) . '/' . $attachment->file_name ) }}" style="height: 100%">
                  </div>
                  <div class="customer-content col-md-3">
                  <h3 class="customer-heading">{{ __('File Name') }}: {{  $attachment->file_name }}</h3>                                
                  <span class="email">{{ __('File Type') }}: {{  $attachment->file_extension }}</span>
                  <a type="button" class="price" href="{{ asset('storage/uploads/attachements/LogNote/'. $attachment->module_name . '/' . Hashids::encode($attachment->log_note_id) . '/' . $attachment->file_name ) }}" download>{{ __('Download') }}</a>
                  <a href="javascript:void(0)" type="button" class="price" onclick="removeNoteFile($(this))" data-note-attachment-id="{{ Hashids::encode($attachment->id) }}"  data-log-note-id ="{{ Hashids::encode($attachment->log_note_id) }}" data-note-file-name="{{ $attachment->file_name }}" data-note-file-url="{{ route('admin.log.remove-note-file') }}" data-note-model-id="{{ Hashids::encode($attachment_model_id) }}" data-note-module-name ="{{ $attachment->module_name }}"><i class="fa fa-close"></i>&nbsp;{{ __('Remove') }}</a>
                </div>
              </div>
            </span>
            @else
            <span class="description margin-bottom-15" id="remove_note_file_{{ Hashids::encode($attachment->id) }}"><b>{{ __('File') }}:</b>
            <a style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;" class=" btn ml-2" href="{{ asset('storage/uploads/attachements/LogNote/'. $attachment->module_name . '/' . Hashids::encode($attachment->log_note_id) . '/' . $attachment->file_name ) }}" download>{{$attachment->file_name}} &nbsp;<i class="fa fa-download"></i></a>
            <a href="javascript:void(0)" style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;" class=" btn ml-2" onclick="removeNoteFile($(this))" data-note-attachment-id ="{{ Hashids::encode($attachment->id) }}" data-log-note-id ="{{ Hashids::encode($attachment->log_note_id) }}" data-note-file-name="{{ $attachment->file_name }}" data-note-file-url="{{ route('admin.log.remove-note-file') }}" data-note-model-id="{{ Hashids::encode($attachment_model_id) }}" data-note-module-name ="{{ $attachment->module_name }}"><i class="fa fa-close"></i>&nbsp;{{ __('Remove') }}</a>
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
      <span class="create-day text-center" style="z-index: 999"><b style="color: #009a71;">{{ __('Today') }}</b></span>
      <hr class="col-md-5 pull-right">
    </div>   
  </div>
  <input type="hidden" class="timeSlots" value="Today">
  <div id="new_log_note"></div>
  <div class="box-widget" id="emptyBox">
    <div class="box-header with-border">
      <div class="user-block">
        <span class="description" style="text-align: center;margin: 0;"><b>{{ __("Currently there's no log here") }}:</b></span>
      </div>
    </div>
  </div>
  @endforelse