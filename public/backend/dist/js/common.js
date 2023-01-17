// Ajax Method For select2 validations
    $('#dynamicRecipients').on('select2:select', function (e) {

    if (e.params.data.newTag) {
     $.ajax({
          url: add_new_contact_url,
          dataType: 'json',
          async: true,
          type: "POST",
          data: {
              tag: e.params.data.text,
              selected_post_tags: $('#dynamicRecipients').val(),
          },
          success: function (resp) {
           Swal.fire(cmn_error_title,resp['error'], "error");
          $("ul.select2-selection__rendered li.select2-selection__choice:nth-last-child(2)").remove();
           setTimeout(function(){
            $("ul.select2-results__options li:last-child").remove();
          }, 6000);


          },
      })
    }
  });

// The rel attribute is the userID you would want to follow

$('a.followButton').on('click', function(){
    $button = $(this);
    var follower_model_id = $button.attr('data-model-id');
    var partner_id = $button.attr('data-partner-id');
    var type = $button.attr('data-module-type');
    var fd = new FormData();
    fd.append('_token', $('input[name="_token"]').val());
    fd.append('model_id', follower_model_id);
    fd.append('partner_id', partner_id);
    fd.append('module_type', type);
    if($button.hasClass('following')){

        // Ajax Do Unfollow
         $.ajax({
          url: do_unfollow_url,
          data: fd,
          type: 'POST',
          processData: false,
          contentType: false,
          beforeSend: function(){
              // Show loader container
              $("#ajax_loader").show();
          },
          success: function (data) {
                var updated_list = data['updated_follower_list'];
                updated_list = updated_list.replace(/\"/g, "")
                $button.removeClass('following');
                $button.removeClass('unfollow');
                $button.text(cmn_follow_text);
                $("#follower_counter").html(data['follower_count']);
                $("#f_list").html(updated_list);
                if(data['follower_count'] == 0)
                {
                  $('#f_list').html("<li><div class='text-center'>"+cmn_follower_empty_msg+"</div></li>");
                }
          },
           complete:function(data){
          // Hide loader container
              $("#ajax_loader").hide();
         }
      });

    } else {
        // Ajax Do Follow
         $.ajax({
          url: do_follow_url,
          data: fd,
          type: 'POST',
          processData: false,
          contentType: false,
          beforeSend: function(){
              // Show loader container
              $("#ajax_loader").show();
          },
          success: function (data) {
               var updated_list = data['updated_follower_list'];
               updated_list = updated_list.replace(/\"/g, "")
               $button.addClass('following');
               $button.html('<i class="fa fa-check"></i>&nbsp;'+cmn_following_text);
               $("#follower_counter").html(data['follower_count']);
               $("#f_list").html(updated_list);
          },
          complete:function(data){
          // Hide loader container
              $("#ajax_loader").hide();
         }
      });

    }
});
// Method for hover following / un-follow button
$('a.followButton').hover(function(){
     $button = $(this);
    if($button.hasClass('following')){
        $button.addClass('unfollow');
        $button.html('<i class="fa fa-times"></i>&nbsp;'+cmn_unfollow_text);
    }
}, function(){
    if($button.hasClass('following')){
        $button.removeClass('unfollow');
        $button.html('<i class="fa fa-check"></i>&nbsp;'+cmn_following_text);
    }
});

// Follower List Dropdown Toggle

$(function () {
  // Dropdown toggle
  $(".dropdown-toggle").click(function () {
    $(this).next(".follower_list").toggle();
  });

  $(document).click(function (e) {
    var target = e.target;
    if (
      !$(target).is(".dropdown-toggle") &&
      !$(target).parents().is(".dropdown-toggle")
    ) {
      $(".follower_list").hide();
    }
  });
});

// Attachment List Dropdown Toggle

$(function () {
  // Dropdown toggle
  $(".dropdown-attachment-toggle").click(function () {
    $(this).next(".attachments_list").toggle();
  });

  $(document).click(function (e) {
    var target = e.target;
    if (
      !$(target).is(".dropdown-attachment-toggle") &&
      !$(target).parents().is(".dropdown-attachment-toggle")
    ) {
      $(".attachments_list").hide();
    }
  });
});
// Method For Add New Log Note
  $('#log_note_form').submit(function(e) {
  e.preventDefault();
  // Validatation
  $(".invalid-feedback").hide();
  var subject = $('#subject').val();
  var note = $('#note').val();
  var hasErrorSub = false;
  var hasErrorNote = false;
  if (subject == '') {
    $("#subject").after('<span class="invalid-feedback" id="note_subject_error">'+cmn_subject_valid_error+'</span>');
    hasErrorSub = true;
  }
  if(note == '') {
    $('.note-editor').css('margin-bottom',"0px");
    $('.upload_image_row').css('margin-top',"20px");
    $("#log_note_form .note-editor").after('<span class="invalid-feedback" id="note_summary_error">'+cmn_note_valid_error+'</span>');
    hasErrorNote = true;
  }

  if (hasErrorSub == true || hasErrorNote == true) {
    return false;
  }
  var fd = new FormData(this);
  let ajax_url = $('#data_url').val();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('uid', $('#log_uid').val());
  fd.append('model_id', $('#model_id').val());
  fd.append('module', $('#module').val());
  fd.append('subject', subject);
  fd.append('note', note);
    $.ajax({
      url: ajax_url,
      data: fd,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function (resp) {
      let timeslot_shift = 0;
      $('.timeSlots').each(function(){
        if($(this).val() == 'Today')
        {
            timeslot_shift = 1;
        }
      });

       if(timeslot_shift < 1) {
         $('#add_today_time_slot').show();
         var new_log_note = '<div class="box-widget"><div class="box-header with-border"><div class="user-block">'+resp['img']+'<span class="username"><a href="'+resp['user_url']+'"><span class="activity-style"></span>'+resp['username']+'</a><span class="activity-style"> '+resp['ago']+'</span></span><span class="description" style="margin-bottom: 15px"><b>Note:</b><span>'+resp['note']+'</span></span>'+resp['attachments']+'</div></div></div>';
           $(new_log_note).insertAfter("#today_time_shift");
           $("#log-note-model").modal('hide');
       }
       else {
         var new_log_note = '<div class="box-widget"><div class="box-header with-border"><div class="user-block">'+resp['img']+'<span class="username"><a href="'+resp['user_url']+'"><span class="activity-style"></span>'+resp['username']+'</a><span class="activity-style"> '+resp['ago']+'</span></span><span class="description" style="margin-bottom: 15px"><b>Note:</b><span>'+resp['note']+'</span></span>'+resp['attachments']+'</div></div></div>';
          $(new_log_note).insertAfter("#new_log_note");
          $("#log-note-model").modal('hide');
          $('#emptyBox').remove();
       }
       // Update Attachment counter and list
       $("#attachment_counter").html(resp['attachment_counter']);
       $("#updated_attachment_list").html(resp['attachment_list']);
      },
    });
  });

// Method For Send Messages
$('#send_msg_form').submit(function(e) {
  e.preventDefault();
  // Validatation
  $(".invalid-feedback").hide();
  var subject = $('#message_subject').val();
  var message = $('#message').val();
  var recipients = $('#dynamicRecipients').val();
  var module_type = $('#msg_module_type').val();
  var partner_id = $('#msg_partner_id').val();
  var hasErrorMsgSub = false;
  var hasErrorMsg = false;
  if (subject == '') {
    $("#message_subject").after('<span class="invalid-feedback" id="msg_subject_error">'+cmn_subject_valid_error+'</span>');
    hasErrorSub = true;
  }
  if(message == '') {
    $('.note-editor').css('margin-bottom',"0px");
    $('.upload_image_row').css('margin-top',"20px");
    $("#send-message-model .note-editor").after('<span class="invalid-feedback" id="msg_summary_error">'+cmn_msg_valid_error+'</span>');
    hasErrorMsg = true;
  }

  if (hasErrorMsgSub == true || hasErrorMsg == true) {
    return false;
  }
  var fd = new FormData(this);
  let ajax_url = $('#data_msg_url').val();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('uid', $('#msg_log_uid').val());
  fd.append('model_id', $('#msg_model_id').val());
  fd.append('module', $('#msg_module').val());
  fd.append('subject', subject);
  fd.append('message', message);
  fd.append('module_type', module_type);
  fd.append('partner_id', partner_id);
  fd.append('recipients', recipients);
    $.ajax({
      url: ajax_url,
      data: fd,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function (resp) {
      let timeslot_shift = 0;
      var follower_updated_lists = resp['updated_follower_list'];
      follower_updated_lists = follower_updated_lists.replace(/\"/g, "")
      $('.msgTimeSlots').each(function(){
        if($(this).val() == 'Today')
        {
            timeslot_shift = 1;
        }
      });

       if(timeslot_shift < 1) {
         $('#add_msg_today_time_slot').show();
         var new_send_message = '<div class="box-widget"><div class="box-header with-border"><div class="user-block">'+resp['img']+'<span class="username"><a href="'+resp['user_msg_url']+'"><span class="activity-style"></span>'+resp['username']+'</a><span class="activity-style"> '+resp['ago']+'</span></span><span class="description" style="margin-bottom: 15px"><b>Message:</b><span>'+resp['message']+'</span></span>'+resp['attachments']+'</div></div></div>';
           $(new_send_message).insertAfter("#today_msg_time_shift");
           $("#send-message-model").modal('hide');
       }
       else {
         var new_send_message = '<div class="box-widget"><div class="box-header with-border"><div class="user-block">'+resp['img']+'<span class="username"><a href="'+resp['user_msg_url']+'"><span class="activity-style"></span>'+resp['username']+'</a><span class="activity-style"> '+resp['ago']+'</span></span><span class="description" style="margin-bottom: 15px"><b>Message:</b><span>'+resp['message']+'</span></span>'+resp['attachments']+'</div></div></div>';
          $(new_send_message).insertAfter("#new_send_message");
          $("#send-message-model").modal('hide');
          $('#emptyMsgBox').remove();
       }
          if(resp['is_following'] == 1) {
             $('#followBtn').hide();
             $('#following').hide();
             $('#followingBtn').show();
             if($('#followingBtn').text() == "Follow") {
              $('#followingBtn').html('<i class="fa fa-check"></i>&nbsp;'+cmn_following_text);
              $('#followingBtn').addClass('following');
             }

          }
           // Update Follower counter and Lists
           $("#follower_counter").html(resp['follower_count']);
           $("#f_list").html(follower_updated_lists);

           // Update Attachment counter and list
           $("#attachment_counter").html(resp['attachment_counter']);
           $("#updated_attachment_list").html(resp['attachment_list']);
      },
    });
  });

 // Add New File Clone Method
 $(document).ready(function() {
    $(".add_new_file").click(function(){
        var lsthmtl = $(".clone").html();
        $(".increment").after(lsthmtl);
        $(".increment").addClass('newFiles');
    });
    $("body").on("click",".btn-danger",function(){
        $(this).parents(".hdtuto").remove();
    });
  });
// Remove Note File Method
function removeNoteFile(_context) {
  var id = $(_context).attr('data-note-attachment-id');
  var log_id = $(_context).attr('data-log-note-id');
  var file_name = $(_context).attr('data-note-file-name');
  var module_id = $(_context).attr('data-note-model-id');
  var module_name = $(_context).attr('data-note-module-name');
  file_name = '"<b>'+file_name+'</b>"';
  var remove_file_url = $(_context).attr('data-note-file-url');
  // return false;
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', id);
  fd.append('log_id', log_id);
  fd.append('model_id', module_id);
  fd.append('module_name', module_name);
  Swal.fire({
    title: cmn_swt_alert_title,
    html: cmn_swt_alert_text+" "+file_name+" ?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: cmn_swt_btn_text
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
          url: remove_file_url,
          data: fd,
          type: 'POST',
          processData: false,
          contentType: false,
          success: function (resp) {
            if (resp['deleted']) {
                $('#remove_note_file_'+id).remove();
                // $('#remove_note_attachment_'+id).remove();
                $("#updated_attachment_list").html(resp['attachment_list']);
                $("#attachment_counter").html(resp['attachment_counter']);
                Swal.fire(cmn_swt_delete_title,resp['deleted'], "success");
            }
          },
      });
    }
  })
}
// Remove Message File Method
function removeMsgFile(_context) {
  var id = $(_context).attr('data-msg-attachment-id');
  var log_id = $(_context).attr('data-log-msg-id');
  var file_name = $(_context).attr('data-msg-file-name');
  var module_id = $(_context).attr('data-msg-model-id');
  var module_name = $(_context).attr('data-msg-module-name');
  file_name = '"<b>'+file_name+'</b>"';
  var remove_file_url = $(_context).attr('data-msg-file-url');
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', id);
  fd.append('log_id', log_id);
  fd.append('model_id', module_id);
  fd.append('module_name', module_name);
  Swal.fire({
    title: cmn_swt_alert_title,
    html: cmn_swt_alert_text+" "+file_name+" ?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: cmn_swt_btn_text
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
          url: remove_file_url,
          data: fd,
          type: 'POST',
          processData: false,
          contentType: false,
          success: function (resp) {
            if (resp['deleted']) {
                $('#remove_msg_file_'+id).remove();
                // $('#remove_msg_attachment_'+id).remove();
                $("#updated_attachment_list").html(resp['attachment_list']);
                $("#attachment_counter").html(resp['attachment_counter']);
                Swal.fire(cmn_swt_delete_title,resp['deleted'], "success");
            }
          },
      });
    }
  })
}
// Remove Message File Method
function removeSaFile(_context) {
  var id = $(_context).attr('data-sa-attachment-id');
  var log_id = $(_context).attr('data-log-sa-id');
  var file_name = $(_context).attr('data-sa-file-name');
  var module_id = $(_context).attr('data-sa-model-id');
  var module_name = $(_context).attr('data-sa-module-name');
  file_name = '"<b>'+file_name+'</b>"';
  var remove_file_url = $(_context).attr('data-sa-file-url');
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', id);
  fd.append('log_id', log_id);
  fd.append('model_id', module_id);
  fd.append('module_name', module_name);
  Swal.fire({
    title: cmn_swt_alert_title,
    html: cmn_swt_alert_text+" "+file_name+" ?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: cmn_swt_btn_text
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
          url: remove_file_url,
          data: fd,
          type: 'POST',
          processData: false,
          contentType: false,
          success: function (resp) {
            if (resp['deleted']) {
                $('#remove_sa_file_'+id).remove();
                // $('#remove_msg_attachment_'+id).remove();
                $("#updated_attachment_list").html(resp['attachment_list']);
                $("#attachment_counter").html(resp['attachment_counter']);
                Swal.fire(cmn_swt_delete_title,resp['deleted'], "success");
            }
          },
      });
    }
  })
}
// Hide Selected Recipients from list
function hideSelected(value) {
  if (value && !value.selected) {
    return $('<span>' + value.text + '</span>');
  }
}
 // Clear Form Method
function clearNoteForm() {
  $('.summernote').each(function( index ) {
    $(this).summernote('reset');
  });
  $(".newFiles").nextAll('.hdtuto').remove();
  $('#note').val('');
  $('#filenames').val('');
  $('#note_subject_error').text('');
  $('#note_summary_error').text('');
 }

 function clearMessageForm() {
  $('.summernote').each(function( index ) {
    $(this).summernote('reset');
  });
  $("#dynamicRecipients").val("");
  $("#dynamicRecipients").trigger("change");
  //Initialize Select2 Elements
  $('#dynamicRecipients').select2({
    allowClear: true,
    tags: true,
    tokenSeparators: [',', ' '],
    minimumResultsForSearch: -1,
    templateResult: hideSelected,
    createTag: function (params) {
      var term = $.trim(params.term);

      if (term === '') {
        return null;
      }

      return {
        id: term,
        text: term,
        newTag: true // add additional parameters
      }
    }
  });
  $(".newFiles").nextAll('.hdtuto').remove();
  $('#message').val('');
  $('#filenames').val('');
  $('#msg_subject_error').text('');
  $('#msg_summary_error').text('');
 }

function ClearScheduleActivity() {
  // DatePicker Method
    $('#due_date').datepicker({
        format: 'mm/dd/yyyy',
        orientation: 'bottom',
        autoclose: true,
        todayHighlight: true
    });

  $('#due_date').datepicker('setDate', new Date());
  $('.summernote').each(function( index ) {
    $(this).summernote('reset');
  });
  $('#details').val('');
  $('#summary').val('');
  $("#action").val('Add');
  $("#id").val('');
  $("#schedule_btn").text(cmn_schedule_btn_text);
  $("#activity_type_id").prop("selectedIndex", 1).val();
  $("#assign_user_id").prop("selectedIndex", 0).val();
 }

// Show Planned activity detail Method
function showActivityDetail(_context) {
  var count = $(_context).attr('data-schedule-id');
  $("#t_activity_details_"+count).toggle();
}
// On Change Activity types Show/Hide Field
$('#activity_type_id').change(function(){

    var selected_type = $("#activity_type_id option:selected").text();
    if(selected_type == "Meeting") {
      $("#col_due_date").hide();
      $("#col_assing_to").hide();
      $("#row_activity_details").hide();
    }
    else{
      $("#col_due_date").show();
      $("#col_assing_to").show();
      $("#row_activity_details").show();
    }
});
function togglePlannedActivity() {
 var count_planned = $('.t_Activity_dueDateText.t-planned').length
 var count_overdue = $('.t_Activity_dueDateText.t-overdue').length
 var count_today = $('.t_Activity_dueDateText.t-today').length
 $("#count_overdue").text(count_overdue);
 $("#count_today").text(count_today);
 $("#count_planned").text(count_planned);
  var accordionId = $("#collapseActivities").hasClass("in");
  if (accordionId == true) {
    $('#schedule_activites_counters').show();
  } else {
    $('#schedule_activites_counters').hide();
  }
}
// Add/Update Schedule activity
function activitySchedule(_context) {
  var action = $("#action").val();
  $("#schedule-activity-model").modal('hide');
  var schedule_flag = $(_context).attr('data-schedule-flag');
  var id = $("#id").val();
  var schedule_partner_id = $(_context).attr('da-sa-partner-id');
  var module_type = $(_context).attr('data-sa-module-type');
  var schedule_model_id = $(_context).attr('data-sa-model-id');
  var schedule_module = $(_context).attr('data-sa-module');
  var schedule_url = $(_context).attr('data-sa-url');
  var log_user_id = $(_context).attr('date-sa-user-log-id');
  var actvity_type = $('#activity_type_id option:selected').val();
  var due_date = $('#due_date').val();
  var summary = $('#summary').val();
  var assign_to = $('#assign_user_id option:selected').val();
  var details = $("#details").val();
  if($('#action').val() == "Edit") {
    var action = $('#action').val();
  }
  else {
     var action = $(_context).attr('data-schedule-action');
  }
  // return false;
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('action', action);
  fd.append('schedule_flag', schedule_flag);
  fd.append('id', id);
  fd.append('schedule_partner_id', schedule_partner_id);
  fd.append('module_type', module_type);
  fd.append('schedule_model_id', schedule_model_id);
  fd.append('module', schedule_module);
  fd.append('log_user_id', log_user_id);
  fd.append('actvity_type', actvity_type);
  fd.append('due_date', due_date);
  fd.append('summary', summary);
  fd.append('assign_to', assign_to);
  fd.append('action', action);
  fd.append('details', details);
    $.ajax({
        url: schedule_url,
        data: fd,
        type: 'POST',
        processData: false,
        contentType: false,
       beforeSend: function(){
            // Show loader container
            $("#ajax_loader").show();
        },
        success: function (resp) {
        if(schedule_flag != 0) {
          let timeslot_shift = 0;
          $('.saTimeSlots').each(function(){
            if($(this).val() == 'Today')
            {
                timeslot_shift = 1;
            }
          });
           var myVal = resp['doneActivitiesValArr'];
           if(timeslot_shift < 1) {
             $('#add_sa_today_time_slot').show();

             var new_done_activity = '<div class="box-widget"><div class="box-header with-border"><div class="user-block">'+myVal['sa_img']+'<span class="username mb-3"><a href="'+myVal['sa_user_url']+'"><span class="activity-style"></span>'+myVal['sa_username']+'</a> <span class="activity-style">'+myVal['sa_ago']+'</span></span><span class="description margin-bottom-15"><i class="fa '+myVal['sa_type_icone']+'"></i>&nbsp;<b>'+myVal['sa_type_name']+' done </b>'+myVal['sa_assigned_to']+' '+myVal['sa_summary']+'</span>'+myVal['sa_feedback']+' '+myVal['sa_details']+'</div></div></div>';
               $(new_done_activity).insertAfter("#today_sa_time_shift");
               // $("#send-message-model").modal('hide');
           }
           else {
             var new_done_activity = '<div class="box-widget"><div class="box-header with-border"><div class="user-block">'+myVal['sa_img']+'<span class="username mb-3"><a href="'+myVal['sa_user_url']+'"><span class="activity-style"></span>'+myVal['sa_username']+'</a> <span class="activity-style">'+myVal['sa_ago']+'</span></span><span class="description margin-bottom-15"><i class="fa '+myVal['sa_type_icone']+'"></i>&nbsp;<b>'+myVal['sa_type_name']+' done </b>'+myVal['sa_assigned_to']+myVal['sa_summary']+'</span>'+myVal['sa_feedback']+myVal['sa_details']+'</div></div></div>';
              $(new_done_activity).insertAfter("#new_done_activity");
              $('#emptySaBox').remove();
           }
        }
          // Update Follower Details
          var follower_updated_lists = resp['updated_follower_list'];
          follower_updated_lists = follower_updated_lists.replace(/\"/g, "")
          $("#follower_counter").html(resp['follower_count']);
          $("#f_list").html(follower_updated_lists);
          // Update Following Toggle Button
          if(resp['is_following'] == 1) {
             $('#followBtn').hide();
             $('#following').hide();
             $('#followingBtn').show();
             if($('#followingBtn').text() == "Follow") {
              $('#followingBtn').html('<i class="fa fa-check"></i>&nbsp;'+cmn_following_text);
              $('#followingBtn').addClass('following');
             }

          }
          // Update Schedule Actvity Details
          if(schedule_flag == 2) {
              $("#planned_activities").html(resp['updated_schedule_activities']);
              $("#schedule-activity-model").modal('show');
              ClearScheduleActivity();
          }
          else {
            $("#planned_activities").html(resp['updated_schedule_activities']);
             ClearScheduleActivity();
          }

        },
        complete:function(resp){
          // Hide loader container
              $("#ajax_loader").hide();
         }
    });
}
// Upload Schedule Activity File
function uploadScheduleFile(_context) {
  var sa_id = $(_context).attr('data-schedule-id');
  var sa_model_id = $(_context).attr('data-schedule-model-id');
  var sa_module_name = $(_context).attr('data-schedule-module-name');
  var ajax_file_upload_url = $(_context).attr('data-schedule-url');
  var action = $(_context).attr('data-action');
  var files_uploaded = $('#files')[0].files;
  var fd = new FormData();
  if (files_uploaded) {
     $.map(files_uploaded, function(val) { return fd.append("files[]", val); });
  }
  fd.append('id', sa_id);
  fd.append('done_model_id', sa_model_id);
  fd.append('done_module_name', sa_module_name);
  fd.append('action', action);
  $.ajax({
      url: ajax_file_upload_url,
      data: fd,
      type: 'POST',
      processData: false,
      contentType: false,
     beforeSend: function(){
          // Show loader container
          $("#ajax_loader").show();
      },
      success: function (resp) {
       if (resp['mark_as_done']) {
          let timeslot_shift = 0;
            $('.saTimeSlots').each(function(){
              if($(this).val() == 'Today')
              {
                  timeslot_shift = 1;
              }
            });

             if(timeslot_shift < 1) {
               $('#add_sa_today_time_slot').show();

               var new_done_activity = '<div class="box-widget"><div class="box-header with-border"><div class="user-block">'+resp['sa_img']+'<span class="username mb-3"><a href="'+resp['sa_user_url']+'"><span class="activity-style"></span>'+resp['sa_username']+'</a> <span class="activity-style">'+resp['sa_ago']+'</span></span><span class="description margin-bottom-15"><i class="fa '+resp['sa_type_icone']+'"></i>&nbsp;<b>'+resp['sa_type_name']+' done </b>'+resp['sa_assigned_to']+' '+resp['sa_summary']+'</span>'+resp['sa_feedback']+' '+resp['sa_details']+resp['attachments']+'</div></div></div>';
                 $(new_done_activity).insertAfter("#today_sa_time_shift");
                 // $("#send-message-model").modal('hide');
             }
             else {
               var new_done_activity = '<div class="box-widget"><div class="box-header with-border"><div class="user-block">'+resp['sa_img']+'<span class="username mb-3"><a href="'+resp['sa_user_url']+'"><span class="activity-style"></span>'+resp['sa_username']+'</a> <span class="activity-style">'+resp['sa_ago']+'</span></span><span class="description margin-bottom-15"><i class="fa '+resp['sa_type_icone']+'"></i>&nbsp;<b>'+resp['sa_type_name']+' done </b>'+resp['sa_assigned_to']+resp['sa_summary']+'</span>'+resp['sa_feedback']+resp['sa_details']+resp['attachments']+'</div></div></div>';
                $(new_done_activity).insertAfter("#new_done_activity");
                $('#emptySaBox').remove();
             }
             // Update Attachment counter and list
           $("#attachment_counter").html(resp['attachment_counter']);
           $("#updated_attachment_list").html(resp['attachment_list']);
          if(action == "DoneAndNext") {
            $("#planned_activities").html(resp['updated_schedule_activities']);
            $("#schedule-activity-model").modal('show');
            ClearScheduleActivity();
          }
          else {
            $("#planned_activities").html(resp['updated_schedule_activities']);
            Swal.fire(cmn_swt_done_title,resp['mark_as_done'], "success");
          }
        }
      },
      complete:function(resp){
        // Hide loader container
            $("#ajax_loader").hide();
       }
  });
}
// Update Planned Activity Method
function updateActivitySchedule(_context) {
  $("#schedule-activity-model").modal('show');
  var id = $(_context).attr('data-schedule-id');
  var update_url = $(_context).attr('data-update-schedule-url');
  var action = "Edit";
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', id);
  fd.append('action', action);
  $.ajax({
      url: update_url,
      data: fd,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function (resp) {
        $('#due_date').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        myObj = resp['model'];
        var date = new Date(myObj['due_date']);
        var due_date = (((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear());
        $("#action").val(resp['action']);
        $("#id").val(id);
        $("#due_date").val(due_date);
        $('select[name="activity_type_id"]').val(myObj['activity_type_id']);
        $('#summary').val(myObj['summary']);
        $('select[name="assign_user_id"]').val(myObj['assign_user_id']);
        $("#details").siblings(".note-editor").find('.note-editable').html(myObj['details']);
        $("#schedule_btn").text(cmn_schedule_save_btn_text);
      },
  });
}
// Cancel Planned Activity Method
function cancelPlannedActivity(_context) {
  var id = $(_context).attr('data-schedule-id');
  var summary = $(_context).attr('data-summary');
  var cancel_module_name = $(_context).attr('data-schedule-module-name');
  var cancel_model_id = $(_context).attr('data-schedule-model-id');

  var cancel_url = $(_context).attr('data-cancel-schedule-url');
  summary = summary != "" ? '"<b>'+summary+'</b>"' : "";
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', id);
  fd.append('cancel_model_id', cancel_model_id);
  fd.append('cancel_module_name', cancel_module_name);
  Swal.fire({
    title: cmn_swt_alert_title,
    html:  cmn_swt_cancel_text+" "+summary+" "+cmn_swt_activity_title,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: cmn_swt_btn_text
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
          url: cancel_url,
          data: fd,
          type: 'POST',
          processData: false,
          contentType: false,
          success: function (resp) {
            if (resp['cancelled']) {
                $("#planned_activities").html(resp['updated_schedule_activities']);
                Swal.fire(cmn_swt_cancel_title,resp['cancelled'], "success");
            }
          },
      });
    }
  })
}
// Mark As Done Planned Activity
function markAsDone(_context) {
  var id = $(_context).attr('data-schedule-id');
  var done_model_id = $(_context).attr('data-schedule-model-id');
  var done_module_name = $(_context).attr('data-schedule-module-name');
  var activity_feedback = $('#activity_feedback_'+id).val();
  var mark_as_done_url = $(_context).attr('data-mark-as-done-url');
  var action = $(_context).attr('data-action');
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', id);
  fd.append('done_model_id', done_model_id);
  fd.append('done_module_name', done_module_name);
  fd.append('action', action);
  fd.append('activity_feedback', activity_feedback);
  $.ajax({
      url: mark_as_done_url,
      data: fd,
      type: 'POST',
      processData: false,
      contentType: false,
      beforeSend: function(){
          // Show loader container
          hidePopover();
          $("#ajax_loader").show();
      },
      success: function (resp) {
        if (resp['mark_as_done']) {
          let timeslot_shift = 0;
            $('.saTimeSlots').each(function(){
              if($(this).val() == 'Today')
              {
                  timeslot_shift = 1;
              }
            });

             if(timeslot_shift < 1) {
               $('#add_sa_today_time_slot').show();

               var new_done_activity = '<div class="box-widget"><div class="box-header with-border"><div class="user-block">'+resp['sa_img']+'<span class="username mb-3"><a href="'+resp['sa_user_url']+'"><span class="activity-style"></span>'+resp['sa_username']+'</a> <span class="activity-style">'+resp['sa_ago']+'</span></span><span class="description margin-bottom-15"><i class="fa '+resp['sa_type_icone']+'"></i>&nbsp;<b>'+resp['sa_type_name']+' done </b>'+resp['sa_assigned_to']+resp['sa_summary']+'</span>'+resp['sa_feedback']+' '+resp['sa_details']+'</div></div></div>';
                 $(new_done_activity).insertAfter("#today_sa_time_shift");
                 // $("#send-message-model").modal('hide');
             }
             else {
               var new_done_activity = '<div class="box-widget"><div class="box-header with-border"><div class="user-block">'+resp['sa_img']+'<span class="username mb-3"><a href="'+resp['sa_user_url']+'"><span class="activity-style"></span>'+resp['sa_username']+'</a> <span class="activity-style">'+resp['sa_ago']+'</span></span><span class="description margin-bottom-15"><i class="fa '+resp['sa_type_icone']+'"></i>&nbsp;<b>'+resp['sa_type_name']+' done </b>'+resp['sa_assigned_to']+resp['sa_summary']+'</span>'+resp['sa_feedback']+resp['sa_details']+'</div></div></div>';
                $(new_done_activity).insertAfter("#new_done_activity");
                $('#emptySaBox').remove();
             }
          if(action == "DoneAndNext") {
            $("#planned_activities").html(resp['updated_schedule_activities']);
            $("#schedule-activity-model").modal('show');
            ClearScheduleActivity();
          }
          else {
            $("#planned_activities").html(resp['updated_schedule_activities']);
            Swal.fire(cmn_swt_done_title,resp['mark_as_done'], "success");
          }
        }
      },
      complete:function(resp){
        // Hide loader container
          $("#ajax_loader").hide();
      }
  });
}
/* Create the popover with Header Content and Footer */
function showPopover() {
   $('.popover-markup>[data-toggle="popover"]').popover({
    html: true,
    animation: false,
    title: function() {
      return $(this).parent().find('.head').html();
    },
    /* In the content method, return a class 'popover-content1' wrapping the actual 'contents',
      concatenated with a class 'popover-footer' wrapping the footer. */
    content: function() {
        return '<div class="popover-content1">' + $(this).parent().find('.content').html() +
          '</div><div class="popover-footer">' + $(this).parent().find('.footer').html() +
          '</div>';
      }
  }).click(function(e) {
        $('.popover').not(this).hide(); /* optional, hide other popovers */
        $(this).popover('show'); /* show popover now it's setup */
        e.preventDefault();
    });
}
/** Allow the popover to be closed by clicking anywhere outside it.*/
$('body').on('click', function(e) {
  $('.popover-markup>[data-toggle="popover"]').each(function() {
    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
      $(this).popover('hide');
    }
  });
});
 /** Hide Popover On Clink Method */
function hidePopover() {
    $('.popover').popover('hide');
 }
