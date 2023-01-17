<!-- Popup log note -->
<div class="modal fade" id="send-message-model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title col-md-9 pl-0" id="exampleModalLabel">{{ __('Send Message') }}</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="fa fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form id="send_msg_form" method="POST"  action="javascript:void(0)" accept-charset="utf-8" enctype="multipart/form-data">
            <input type="hidden" id="msg_model_id" value="{{  Hashids::encode($model_id) }}">
            <input type="hidden" id="msg_module" value="{{ $module }}">
            <input type="hidden" id="data_msg_url" value="{{ route('admin.send.message') }}">
            <input type="hidden" id="msg_log_uid" value="{{ Hashids::encode($log_uid) }}">
            <input type="hidden" id="msg_module_type" value="{{ $module_type }}">
            <input type="hidden" id="msg_partner_id" value="{{ Hashids::encode($partner_id) }}">
            @csrf
            <div class="form-group">
              <label>{{ __('Recipients (Followers of the document and)') }}</label>
              <select id="dynamicRecipients" class="form-control select2" multiple="multiple" data-tags="true" data-placeholder="Add contacts to notify.." name="recipients[]" style="width: 100%"
              >
              @foreach($recipients as $recipient)
                <option value="{{ $recipient->id }}">{{ $recipient->name }}</option>
              @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="message-text" class="col-form-label">{{ __('Subject') }}</label>
              <input type="text" class="form-control" name="message_subject" value="{{ 'Re:'.' '.$partner }}" id="message_subject">
            </div>
            <label for="message-text" class="col-form-label">{{ __('Message') }}</label>
            <textarea class="summernote form-control" name="message" id="message"></textarea>
            {{-- Image Upload --}}
            <div class="row upload_image_row">
              <div class="col-md-9">
                <div class="input-group hdtuto control-group lst increment" >
                  <input type="file" name="filenames[]" id="filenames" class="myfrm form-control">
                  <div class="input-group-btn"> 
                    <button class="btn btn-success add_new_file" type="button"><i class="fldemo glyphicon glyphicon-plus"></i>&nbsp;{{ __('Add') }}</button>
                  </div>
                </div>
                <div class="clone hide">
                  <div class="hdtuto control-group lst input-group" style="margin-top:10px">
                    <input type="file" name="filenames[]" class="myfrm form-control">
                    <div class="input-group-btn"> 
                      <button class="btn btn-danger" type="button"><i class="fldemo glyphicon glyphicon-remove"></i>&nbsp;{{ __('Remove') }}</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
              <button type="submit" class="btn btn-success">{{ __('Send') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>
<!-- /log note -->