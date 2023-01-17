{{-- <a type="button" class="" data-toggle="modal" data-target="#log-note-model" onclick="clearForm()"> Log Note</a> --}}
<!-- Popup log note -->
<div class="modal fade" id="log-note-model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title col-md-9 pl-0" id="exampleModalLabel">{{ __('Log Note') }}</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="fa fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form id="log_note_form" method="POST"  action="javascript:void(0)" accept-charset="utf-8" enctype="multipart/form-data">
            <input type="hidden" id="model_id" value="{{  Hashids::encode($model_id) }}">
            <input type="hidden" id="module" value="{{ $module }}">
            <input type="hidden" id="data_url" value="{{ route('admin.log.note') }}">
            <input type="hidden" id="log_uid" value="{{ Hashids::encode($log_uid) }}">
            @csrf
            <div class="form-group">
              <label for="message-text" class="col-form-label">{{ __('Subject') }}</label>
              <input type="text" class="form-control" name="subject" value="{{ 'Re:'.' '.$partner }}" id="subject">
            </div>
              <label for="message-text" class="col-form-label">{{ __('Note') }}</label>
              <textarea class="summernote form-control" name="note" id="note"></textarea>
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
              <button type="submit" class="btn btn-success">{{ __('Log') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>
<!-- /log note -->