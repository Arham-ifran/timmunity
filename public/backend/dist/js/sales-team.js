toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "500",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}
$('#salesteam-form').validate({

    onkeyup: false,
    onclick: false,
    onfocusout: false,
    ignore: [],
    // rules: {
    //     "customer_id": {
    //         required: true
    //     },
    //     "expires_at": {
    //         required: true
    //     },
    //     "invoice_address": {
    //         required: true
    //     },
    //     "pricelist_id": {
    //         required: true
    //     },
    //     "delivery_address": {
    //         required: true
    //     },
    //     "payment_terms": {
    //         required: true
    //     },
    //     // "payment_due_date":{
    //     //     required:true
    //     // },
    //     "otherinfo[sales_person]": {
    //         required: true
    //     },
    //     // "otherinfo[sales_team]":{
    //     //     required:true
    //     // },
    // },
    messages: {
        "name": {
            required: "Add sales team name"
        },
        // "expires_at": {
        //     required: "Mention the expiry date"
        // },
        // "invoice_address": {
        //     required: "Select the invoice address"
        // },
        // "pricelist_id": {
        //     required: "Select the price list"
        // },
        // "delivery_address": {
        //     required: "Select the delivery address"
        // },
        // "payment_terms": {
        //     required: "Select payment term"
        // },
        // "payment_due_date": {
        //     required: "Mention the due date."
        // },
        // "otherinfo[sales_person]": {
        //     required: "Select the sales person"
        // },
        // "otherinfo[sales_team]": {
        //     required: "Select the sales team"
        // }
    },
    errorPlacement: function (error, element) {
        //console.log(error,"Errors are listing here!");
        //error.insertAfter(element);
        toastr.error(error);
    },
    submitHandler: function (form, event) {
        (async () => {
            event.preventDefault();
            var data = {
                _token: CSRF_TOKEN,
                form_data: $(form).serialize()
            };

            let create_sales_team = await prepare_ajax_request(salesteam_form_action, data);
            if (create_sales_team.data.redirect) {
                window.location.href = create_sales_team.data.redirect;
            }
        })();

    }
});

$('body').on('click', '.save-sales-team-d', function () {
    $('#salesteam-form').submit();
});

// $('body').on('click', '.sales-member-d , .save-member-d', async function () {
//     $('.sales-team-modal-generate-d').html();

//     let url, method = '';
//     let = '';
//     if ($(this).hasClass('sales-member-d')) {
//         url = ADMIN_URL + "/sales-management/configuration/sales-team-member/create";
//         method = 'get';

//     }
//     else if ($(this).hasClass('save-member-d')) {
//         // url = ADMIN_URL + "/sales-management/configuration/sales-team-member";
//         //method = 'post';
//     }

//     var data = {
//         _token: CSRF_TOKEN,
//     };

//     if (url) {

//         let open_sales_team = await prepare_ajax_request(url, data, method);
//         if (open_sales_team.html) {
//             $('.sales-team-modal-generate-d').html(open_sales_team.html);
//             $('#member-modalbox-d').modal('show');
//         }

//     }

// });
function readURL(input) {

    var file = document.querySelector("#memberImageUpload");
    if (/\.(jpe?g|png)$/i.test(file.files[0].name) === true) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#memberImagePreview').attr('src', e.target.result);
            $('#memberImagePreview').hide();
            $('#memberImagePreview').fadeIn(650);
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        console.log(file, 'file');
    }

}
$("#memberImageUpload").change(function () {
    readURL(this);
});
function updateMember(_context) {
  $("#member-modalbox-d").modal('show');
  $("#remove_member").show();
  $("#member_model_title").text(member_modal_title2+':');
  var id = $(_context).attr('data-id');
  var action = "Edit";
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', id);
  fd.append('action', action);
  $.ajax({
      url: update_member_url,
      data: fd,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function (resp) {
        myObj = resp['model'];
        $("#req_action").val(resp['action'])
        $("#uid").val(id);
        $("#firstname").val(myObj['firstname']);
        $("#lastname").val(myObj['lastname']);
        $("#email").val(myObj['email']);
        $("#phone").val(myObj['phone']);
        $("#mobile").val(myObj['mobile']);
        $("#img_append").html(resp['image']);

      },
  });
}
 // Member Details
 function memberDetails(_context) {
  $("#member-modalbox-d").modal('show');
  $("#member_model_title").text(member_modal_title2+':');
  var id = $(_context).attr('data-id');
  var action = "Edit";
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', id);
  fd.append('action', action);
  $.ajax({
      url: update_member_url,
      data: fd,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function (resp) {
        myObj = resp['model'];
        $("#req_action").val(resp['action'])
        $("#uid").val(id);
        var name = myObj['firstname']+ ' '+myObj['lastname']; 
        $("#name_info").html(name);
        $("#email_info").text(myObj['email']);
        $("#phone_info").text(myObj['phone']);
        $("#mobile_info").text(myObj['mobile']);
        $("#img_info").html(resp['image']);

      },
  });
} 
// Remove Team Member
function removeTeamMember() {
    var member_id = $("#uid").val();
    remove_member_ids.push(member_id);
    $("input[name=remove_member_ids]").val(remove_member_ids.join(", "));
    $("#member-modalbox-d").modal('hide');
    $('div[data-member-id='+member_id+']').remove();
}
// Reset Team Member Form
function resetForm() {
    document.getElementById("salesteam-member-form").reset();
    var validator = $("#salesteam-member-form").validate();
    validator.resetForm();
    $("#req_action").val('add');
    $("#uid").val('');
    $("#remove_member").hide();
    $("#all_user_list").modal('hide');
    $("#member_model_title").text(member_modal_title1+':');
    var attr_soruce = $('#memberImagePreview').attr('alt');
    $('#memberImagePreview').attr('src', attr_soruce);
}
// Method for Archive Sales Team
function archiveSaleTeam(_context) {
  var sale_team_id = $(_context).attr('data-model-id');
  var archive = $(_context).attr('data-archive');
  // alert(sale_team_id);
  // return false;
  var fd = new FormData();
  fd.append('_token', $('input[name="_token"]').val());
  fd.append('id', sale_team_id);
  fd.append('is_archive', archive);
    Swal.fire({
    title: swt_alert_title,
    text: swt_alert_txt,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: swt_confirm_btn_txt
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
          url: is_archived_url,
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

                if(archive == 1) {
                   $("#archived_ribbon").show();
                   $("#archive").hide();
                   $("#unarchive").show();
                   Swal.fire(swl_fire_title1,resp['success'], "success"); 
                }
                else {
                   $("#archived_ribbon").hide();
                   $("#archive").show();
                   $("#unarchive").hide();
                   Swal.fire(swl_fire_title3,resp['success'], "success");
                }
            }
            else {
              Swal.fire(swl_fire_title2,resp['error'], "warning");
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