<!-- Attachment Count -->
<a class="dropdown-attachment-toggle" href="javascript:void(0)" title="{{ __('Show attachments') }}"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;<span id="attachment_counter">{{ $attachments->count() }}</span></a>
<!-- Attachment List -->
@php $img_ext = array("jpg", "jpeg", "png", "gif","webp"); @endphp
<ul class="attachments_list" id="updated_attachment_list">
   @php 
   $x= 0; 
   $y= 0; 
   $z= 0;
   @endphp
   @forelse ($attachments as $attachment)
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
   @if($attachment->log_note_id == null && $attachment->schedule_activity_id == null)
   @if($x == 0)
   <li class="attachment_heading"><strong>{{ __('Send Message') }}</strong></li>
   @endif
   <li>
      @if(in_array($attachment->file_extension, $img_ext))
      <span class="description" id="remove_msg_attachment_{{ Hashids::encode($attachment->id) }}">
         <div class="customer-box">
            <div class="customer-img">
               <img src="{{ asset('storage/uploads/attachements/SendMessage/'. $attachment->module_name . '/' . Hashids::encode($attachment->send_msg_id) . '/' . $attachment->file_name ) }}" style="height: 85%;width:85%">
            </div>
            <div class="customer-content col-md-3">
               <h3 class="customer-heading">{{ __('File Name') }}: {{  $attachment->file_name }}</h3>
               <span class="email">{{ __('File Type') }}: {{  $attachment->file_extension }}</span>
               <a type="button" class="icons" href="{{ asset('storage/uploads/attachements/SendMessage/'. $attachment->module_name . '/' . Hashids::encode($attachment->send_msg_id) . '/' . $attachment->file_name ) }}" download><i class="fa fa-download"></i>&nbsp;{{ __('Download') }}</a>
               <a href="javascript:void(0)" type="button" class="icons" onclick="removeMsgFile($(this))" data-msg-attachment-id="{{ Hashids::encode($attachment->id) }}"  data-log-msg-id ="{{ Hashids::encode($attachment->send_msg_id) }}" data-msg-file-name="{{ $attachment->file_name }}" data-msg-file-url="{{ route('admin.log.remove-msg-file') }}" data-msg-model-id="{{ Hashids::encode($attachment_model_id) }}" data-msg-module-name ="{{ $attachment->module_name }}"><i class="fa fa-close"></i>&nbsp;{{ __('Remove') }}</a>
            </div>
         </div>
      </span>
      @else
      <span class="description margin-left-15" id="remove_msg_attachment_{{ Hashids::encode($attachment->id) }}"><b>{{ __('File') }}:</b>
      <a class="attachment_file_list btn ml-2" href="{{ asset('storage/uploads/attachements/SendMessage/'. $attachment->module_name . '/' . Hashids::encode($attachment->send_msg_id) . '/' . $attachment->file_name ) }}" download>{{$attachment->file_name}} &nbsp;<i class="fa fa-download"></i>
      </a>
      <a href="javascript:void(0)" class="attachment_file_list btn ml-2" onclick="removeMsgFile($(this))" data-msg-attachment-id ="{{ Hashids::encode($attachment->id) }}" data-log-msg-id ="{{ Hashids::encode($attachment->send_msg_id) }}" data-msg-file-name="{{ $attachment->file_name }}" data-msg-file-url="{{ route('admin.log.remove-msg-file') }}" data-msg-model-id="{{ Hashids::encode($attachment_model_id) }}" data-msg-module-name ="{{ $attachment->module_name }}"><i class="fa fa-close"></i>&nbsp;{{ __('Remove') }}</a>
      </span>
      @endif
   </li>
   @php $x++; @endphp
   @elseif($attachment->send_msg_id == null && $attachment->schedule_activity_id == null)
   @if($y == 0)
   <li class="attachment_heading"><strong>{{ __('Log Note') }}</strong></li>
   @endif
   <li>
      @if(in_array($attachment->file_extension, $img_ext))
      <span class="description" id="remove_note_attachment_{{ Hashids::encode($attachment->id) }}">
         <div class="customer-box">
            <div class="customer-img">
               <img src="{{ asset('storage/uploads/attachements/LogNote/'. $attachment->module_name . '/' . Hashids::encode($attachment->log_note_id) . '/' . $attachment->file_name ) }}" style="height: 85%;width:85%">
            </div>
            <div class="customer-content col-md-3">
               <h3 class="customer-heading">{{ __('File Name') }}: {{  $attachment->file_name }}</h3>
               <span class="email">{{ __('File Type') }}: {{  $attachment->file_extension }}</span>
               <a type="button" class="icons" href="{{ asset('storage/uploads/attachements/LogNote/'. $attachment->module_name . '/' . Hashids::encode($attachment->log_note_id) . '/' . $attachment->file_name ) }}" download><i class="fa fa-download"></i>&nbsp;{{ __('Download') }}</a>
               <a href="javascript:void(0)" type="button" class="icons" onclick="removeNoteFile($(this))" data-note-attachment-id="{{ Hashids::encode($attachment->id) }}"  data-log-note-id ="{{ Hashids::encode($attachment->log_note_id) }}" data-note-file-name="{{ $attachment->file_name }}" data-note-file-url="{{ route('admin.log.remove-note-file') }}" data-note-model-id="{{ Hashids::encode($attachment_model_id) }}" data-note-module-name ="{{ $attachment->module_name }}"><i class="fa fa-close"></i>&nbsp;{{ __('Remove') }}</a>
            </div>
         </div>
      </span>
      @else
      <span class="description margin-left-15" id="remove_note_attachment_{{ Hashids::encode($attachment->id) }}"><b>File:</b>
      <a class="attachment_file_list btn ml-2" href="{{ asset('storage/uploads/attachements/LogNote/'. $attachment->module_name . '/' . Hashids::encode($attachment->log_note_id) . '/' . $attachment->file_name ) }}" download>{{$attachment->file_name}} &nbsp;<i class="fa fa-download"></i>
      </a>
      <a href="javascript:void(0)" class="attachment_file_list btn ml-2" onclick="removeNoteFile($(this))" data-note-attachment-id ="{{ Hashids::encode($attachment->id) }}" data-log-note-id ="{{ Hashids::encode($attachment->log_note_id) }}" data-note-file-name="{{ $attachment->file_name }}" data-note-file-url="{{ route('admin.log.remove-note-file') }}" data-note-model-id="{{ Hashids::encode($attachment_model_id) }}" data-note-module-name ="{{ $attachment->module_name }}"><i class="fa fa-close"></i>&nbsp;{{ __('Remove') }}</a>
      </span>
      @endif
   </li>
   @php $y++; @endphp
   @elseif($attachment->log_note_id == null && $attachment->send_msg_id == null)
   @if($z == 0)
   <li class="attachment_heading"><strong>{{ __('Schedule Activities') }}</strong></li>
   @endif
   <li>
      @if(in_array($attachment->file_extension, $img_ext))
      <span class="description" id="remove_sa_attachment_{{ Hashids::encode($attachment->id) }}">
         <div class="customer-box">
            <div class="customer-img">
               <img src="{{ asset('storage/uploads/attachements/ScheduleActivites/'. $attachment->module_name . '/' . Hashids::encode($attachment->schedule_activity_id) . '/' . $attachment->file_name ) }}" style="height: 85%;width:85%">
            </div>
            <div class="customer-content col-md-3">
               <h3 class="customer-heading">{{ __('File Name') }}: {{  $attachment->file_name }}</h3>
               <span class="email">{{ __('File Type') }}: {{  $attachment->file_extension }}</span>
               <a type="button" class="icons" href="{{ asset('storage/uploads/attachements/ScheduleActivites/'. $attachment->module_name . '/' . Hashids::encode($attachment->schedule_activity_id) . '/' . $attachment->file_name ) }}" download><i class="fa fa-download"></i>&nbsp;{{ __('Download') }}</a>
               <a href="javascript:void(0)" type="button" class="icons" onclick="removeSaFile($(this))" data-sa-attachment-id="{{ Hashids::encode($attachment->id) }}"  data-log-sa-id ="{{ Hashids::encode($attachment->schedule_activity_id) }}" data-sa-file-name="{{ $attachment->file_name }}" data-sa-file-url="{{ route('admin.log.remove-sa-file') }}" data-sa-model-id="{{ Hashids::encode($attachment_model_id) }}" data-sa-module-name ="{{ $attachment->module_name }}"><i class="fa fa-close"></i>&nbsp;{{ __('Remove') }}</a>
            </div>
         </div>
      </span>
      @else
      <span class="description margin-left-15" id="remove_sa_attachment_{{ Hashids::encode($attachment->id) }}"><b>{{ __('File') }}:</b>
      <a class="attachment_file_list btn ml-2" href="{{ asset('storage/uploads/attachements/ScheduleActivites/'. $attachment->module_name . '/' . Hashids::encode($attachment->schedule_activity_id) . '/' . $attachment->file_name ) }}" download>{{$attachment->file_name}} &nbsp;<i class="fa fa-download"></i>
      </a>
      <a href="javascript:void(0)" class="attachment_file_list btn ml-2" onclick="removeSaFile($(this))" data-sa-attachment-id ="{{ Hashids::encode($attachment->id) }}" data-log-sa-id ="{{ Hashids::encode($attachment->schedule_activity_id) }}" data-sa-file-name="{{ $attachment->file_name }}" data-sa-file-url="{{ route('admin.log.remove-sa-file') }}" data-sa-model-id="{{ Hashids::encode($attachment_model_id) }}" data-sa-module-name ="{{ $attachment->module_name }}"><i class="fa fa-close"></i>&nbsp;{{ __('Remove') }}</a>
      </span>
      @endif
   </li>
   @php $z++; @endphp
   @endif
   @empty
   <li class="empty_attachment_list">
      <div class="text-center">{{ __("Currently there's no attachments") }}</div>
   </li>
   @endforelse
</ul>