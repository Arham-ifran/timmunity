// Method For License Activation
function activeLicense(_context) {
  var id = $(_context).attr('data-id');
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', id);
      $.ajax({
          url: activation_url,
          data: fd,
          type: 'POST',
          processData: false,
          contentType: false,
          beforeSend: function(){

              // Show loader container
              $("#ajax_loader").show();
          },
          success: function (resp) {
            if (resp['success']) {
                $("#active").hide();
                $("#soft_cancel").show();
                $("#hard_cancel").show();
                $("#pause").show();
                $("#renew").show();
                $("#get_info").show();
                $("#breadcrumb_draft").removeClass('active');
                $("#breadcrumb_active").css('display','inline-block');
                $("#edit_btn").hide();
                $("#dlt_btn").removeClass('ml-2');
                Swal.fire("Activated",resp['success'], "success");
            }
            else {
              Swal.fire("Warning",resp['error'], "warning");
            }
          },
          complete:function(data){
          // Hide loader container
              $("#ajax_loader").hide();
         }
      });
}
// Method For Temporarliy Hold License
function licenseHold(_context) {
  var id = $(_context).attr('data-id');
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', id);
      $.ajax({
          url: hold_url,
          data: fd,
          type: 'POST',
          processData: false,
          contentType: false,
          beforeSend: function(){

              // Show loader container
              $("#ajax_loader").show();
          },
          success: function (resp) {
            if (resp['success']) {
                $("#soft_cancel").hide();
                $("#hard_cancel").hide();
                $("#pause").hide();
                $("#renew").hide();
                $("#resume").show();
                $("#get_info").show();
                $("#breadcrumb_active").removeClass('active');
                $("#breadcrumb_hold").css('display','inline-block');
                Swal.fire("Hold",resp['success'], "success");
            }
          },
          complete:function(data){
          // Hide loader container
              $("#ajax_loader").hide();
         }
      });
}
// Method For Resumed Temporarliy Hold License
function resumedLicense(_context) {
  var id = $(_context).attr('data-id');
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', id);
      $.ajax({
          url: resumed_url,
          data: fd,
          type: 'POST',
          processData: false,
          contentType: false,
          beforeSend: function(){

              // Show loader container
              $("#ajax_loader").show();
          },
          success: function (resp) {
            if (resp['success']) {
                $("#soft_cancel").show();
                $("#hard_cancel").show();
                $("#pause").show();
                $("#renew").show();
                $("#resume").hide();
                $("#get_info").show();
                $("#breadcrumb_hold").hide();
                $("#breadcrumb_active").addClass('active');
                Swal.fire("Resumed",resp['success'], "success");
            }
          },
          complete:function(data){
          // Hide loader container
              $("#ajax_loader").hide();
         }
      });
}
// Method For Hard Cancel/Blocked
function hardCancel(_context) {
  var id = $(_context).attr('data-id');
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', id);
  Swal.fire({
    title: 'Are you sure?',
    text: "Are you sure that you want to permanantly cancel this license?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
          url: blocked_url,
          data: fd,
          type: 'POST',
          processData: false,
          contentType: false,
          beforeSend: function(){
              // Show loader container
              $("#ajax_loader").show();
          },
          success: function (resp) {
            if (resp['success']) {
                $("#soft_cancel").hide();
                $("#hard_cancel").hide();
                $("#pause").hide();
                $("#renew").show();
                $("#get_info").show();
                $("#edit_btn").css('display','inline-block');
                $("#dlt_btn").addClass('ml-2');
                $("#breadcrumb_active").removeClass('active');
                $("#breadcrumb_hard_cancel").css('display','inline-block');
                Swal.fire("Cancelled",resp['success'], "success");
            }
          },
          complete:function(data){
          // Hide loader container
              $("#ajax_loader").hide();
         }
      });
    }
  })
}