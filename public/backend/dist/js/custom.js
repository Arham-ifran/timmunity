$(document).ajaxStart(function () {
    showLoader();
});
$(document).ajaxStop(function () {
    hideLoader();
});

// toastr.options = {
//     "closeButton": true,
//     "debug": false,
//     "newestOnTop": true,
//     "progressBar": false,
//     "positionClass": "toast-top-right",
//     "preventDuplicates": false,
//     "onclick": null,
//     "showDuration": "300",
//     "hideDuration": "500",
//     "timeOut": "5000",
//     "extendedTimeOut": "1000",
//     "showEasing": "swing",
//     "hideEasing": "linear",
//     "showMethod": "fadeIn",
//     "hideMethod": "fadeOut"
// }

function showLoader() {
    if (typeof NProgress !== undefined && typeof NProgress !== 'undefined') {
        NProgress.start();
    }
}

function hideLoader() {
    if (typeof NProgress !== undefined && typeof NProgress !== 'undefined') {
        NProgress.done();
    }
}

// Image Uploading Function

function readURL(input) {

    var file = document.querySelector("#imageUpload");
    if (/\.(jpe?g|png)$/i.test(file.files[0].name) === true) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#imagePreview').attr('src', e.target.result);
            $('#imagePreview').hide();
            $('#imagePreview').fadeIn(650);
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        console.log(file, 'file');
    }

}
$("#imageUpload").change(function () {
    readURL(this);
});

// Form Validation Function

$(function () {
    jQuery(".form-validate").validate({
        ignore: [],
        errorClass: "invalid-feedback animated fadeInDown",
        errorElement: "div",
        errorPlacement: function (e, a) {
            jQuery(a).parents(".form-group").append(e);
        },
        highlight: function (e) {
            jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid");
            jQuery(e).closest(".form-group > .form-control").removeClass("is-invalid").addClass("is-invalid");
        },
        success: function (e) {
            jQuery(e).closest(".form-group").removeClass("is-invalid");
            jQuery(e).closest(".form-group").find('.form-control').removeClass("is-invalid");
            jQuery(e).remove();
        }
    });
    // $('.form-validate').validate({
    //         errorElement: 'div',
    //         errorClass: 'invalid-feedback animated fadeInDown',
    //         focusInvalid: true,
            
    //     errorPlacement: function (e, a) {
    //         jQuery(a).parents(".form-group").append(e);
    //     },
    //     highlight: function (e) {
    //         jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid");
    //         jQuery(e).closest(".form-group > .form-control").removeClass("is-invalid").addClass("is-invalid");
    //     },
    //     submitHandler: function (form,validator) {
    //             if($(validator.errorList).length == 0)
    //             {
    //                 document.getElementById("page-overlay").style.display = "block";
    //                 return true;
    //             }
    //         }
    // });
});


// Delete Record Confirmation Function

function deleteAlert(context) {
    var submitBtn = $(context).next('.deleteSubmit');
    Swal.fire({
        title: custom_swt_alert_title,
        text: custom_swt_alert_text,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: custom_swt_alert_confim_btn_text,
        cancelButtonText: custom_swt_alert_cancel_btn_text,
        closeOnConfirm: false,
        closeOnCancel: true
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            submitBtn.click();
        } else if (result.isDenied) {
            // Swal.fire('Changes are not saved', '', 'info')
        }
    });
}




//asynchronous generic ajac request
async function prepare_ajax_request(url, data, method = 'post') {


    if (method == 'get') {
        return new Promise((resolve, reject) => {
            jQuery.get(url, data)
                .then(function (res) {
                    resolve(res);
                })
                .catch(function (xhr, status, error) {
                    reject(true);
                });
        });
    }
    else {
        return new Promise((resolve, reject) => {
            jQuery.post(url, data)
                .then(function (res) {
                    resolve(res);
                })
                .catch(function (xhr, status, error) {
                    reject(true);
                });
        });
    }

}

// Langauage Activation Methods
function activateUpdate(_context) {
    $("#language_model").modal("show");
    var id = $(_context).attr('data-id');
    $('#language option:selected').attr("selected", false);
    $('.lang_modal_title').text(modal_title);
    $('#swith_lang_footer').hide();
    $('#lang_active_msg').hide();
    $('#lang_modal_body').show();
    $("#add_lang_section").show();
    if (typeof (id) !== "undefined") {
        $('select option[value="' + id + '"]').attr("selected", true);
    }
}
function archiveUpdate(_context) {
    var id = $(_context).attr('data-id');
    var name = $(_context).attr('data-name');
    $("#archive_language_model").modal("show");
    $("#a_lang_modal_body").show();
    $("#language_name").html(name);
    $("#archive_language_model .modal-footer .btn.btn-success").attr('data-id',id);
}
function archiveLanguage(_context){
    var id = $(_context).attr('data-id');
    var hasError = false;
    if (language == '') {
        $(_context).after('<span class="invalid-feedback">' + lang_validation_msg + '</span>');
        hasError = true;
    }

    if (hasError == true) {
        return false;
    }
    var dataTable = $(_context).attr('data-table-flag');
    if (dataTable != 0) {
        var table = $('#languages-datatable').DataTable();
    }
    var fd = new FormData();
    fd.append('_token', "{{ csrf_token() }}");
    fd.append('id', id);
    $.ajax({
        url: archive_language_url,
        data: fd,
        type: 'POST',
        processData: false,
        contentType: false,
        success: function (resp) {
            if (resp['success']) {
                $('#archive_language_model').modal('hide');
                toastr.success(resp['lang']+' '+archive_language_msg);
                table.ajax.reload();
            }
        }
    });
}
function addLanguage(_context) {
    var language = $('#language').val();
    var hasError = false;
    if (language == '') {
        $("#language").after('<span class="invalid-feedback">' + lang_validation_msg + '</span>');
        hasError = true;
    }

    if (hasError == true) {
        return false;
    }
    var dataTable = $(_context).attr('data-table-flag');
    if (dataTable != 0) {
        var table = $('#languages-datatable').DataTable();
    }
    var fd = new FormData();
    fd.append('_token', "{{ csrf_token() }}");
    fd.append('id', $('#language option:selected').val());
    $.ajax({
        url: add_language_url,
        data: fd,
        type: 'POST',
        processData: false,
        contentType: false,
        success: function (resp) {
            if (resp['success']) {
                let _lang_html = '<span><strong><span>' + resp['lang'] + '<span></strong>' + resp['success'] + '</span>';
                let _switch_lang_html = '<a class="btn btn-primary" href="' + resp['lang_switch_url'] + '">' + switch_btn_title + ' ' + resp['lang'] + '</a><button type="button" class="btn btn-secondary" data-dismiss="modal">' + close_btn_title + '</button>';
                $('.lang_modal_title').html(modal_change_title);
                $("#lang_modal_body").hide();
                $("#lang_active_msg").show();
                $("#lang_active_msg").html(_lang_html);
                $("#add_lang_section").hide();
                $('#swith_lang_footer').show();
                $("#switch_to_lang").html(_switch_lang_html);
                if (typeof (table) !== "undefined") {
                    table.ajax.reload();
                }
                else {
                    $('#language').val('');
                    $("#language_count").html(resp['lang_update_count']);
                }
            }
        }
    });
}
