"use strict";
var perscroll;
var table =''; 
var table_name = 'mytable';
var is_thirdparty_api = 1;

$(document).ready(function() {

    $('#ai_settings_modal').on("hidden.bs.modal", function (e) { 
        location.reload();
    });
    setTimeout(function(){
        if(table==''){
            table = $("#"+table_name).DataTable({
                fixedHeader: false,
                colReorder: true,
                serverSide: true,
                processing:true,
                bFilter: true,
                order: [[ 4, "desc" ]],
                pageLength: 5,
                lengthMenu: [5, 10, 20, 50, 100],
                ajax:
                    {
                        "url": ai_chat_settings_data,
                        "type": 'POST',
                        data: function ( d )
                        {
                            d.is_thirdparty_api = is_thirdparty_api;
                        },
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                        },
                    },
                language:
                    {
                        url: global_url_datatable_language
                    },
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                columnDefs: [
                    {
                        targets: [1,4],
                        visible: false
                    },
                    {
                        targets: [3,5],
                        className: 'text-center'
                    },
                    {
                        targets: [3],
                        sortable: false
                    }
                ],
                fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                    if(areWeUsingScroll)
                    {
                        if (perscroll) perscroll.destroy();
                        perscroll = new PerfectScrollbar('#'+table_name+'_wrapper .dataTables_scrollBody');
                    }
                    var $searchInput = $('div.dataTables_filter input');
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function(e) {
                        if(this.value.length > 2 || this.value.length==0) {
                            table.search( this.value ).draw();
                        }
                    });
                },
                scrollX: 'auto',
                fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
                    if(areWeUsingScroll)
                    {
                        if (perscroll) perscroll.destroy();
                        perscroll = new PerfectScrollbar('#'+table_name+'_wrapper .dataTables_scrollBody');
                    }
                }
            });
        }
        else table.draw();
    }, 500);


    // reload_default_ai_chat_settings();
    $(document).on('click','#new-ai-chat-profile',function(e){
        e.preventDefault();

        $("#ai-chat-update-id").val('0');
        $("#ai_settings_modal").modal('show');
        $("#ai_settings_modal form").find(":input:not([reset=false])").val('');
        $("#ai_settings_modal form").find(":input").prop('readonly',false);
        // $("[name='system_prompt']").val("You are an AI assistan");

        setTimeout(function(){           
            var api_type = $("#new-ai-chat-profile").attr('data-type');
            if(api_type=='openai') $(".thirdparty-api-block #openai-block-link").tab("show");
        }, 500);
    });
    
   
    $(document).on('click', '#save_ai_chat_api_settings', function(e) {
        e.preventDefault();
    
        var href = $('.thirdparty-api-block .nav-link.active').attr('href');
        var form_id = href + '-form';
        form_id = form_id.replace('#', '');
    
        var form = document.getElementById(form_id);
        var missing_input = false;
    
        for (var i = 0; i < form.elements.length; i++) {
            if (form.elements[i].value === '' && !form.elements[i].hasAttribute('not-required')) {
                missing_input = true;
            }
        }
    
        if (missing_input) {
            Swal.fire({
                title: global_lang_warning,
                text: global_lang_fill_required_fields,
                icon: 'warning',
                confirmButtonText: global_lang_ok
            });
            return false;
        }
    
        $("#save_ai_chat_api_settings").attr('disabled', true);
        var update_id = $("#ai-chat-update-id").val();
        var api_name = form_id.replace('-block-form', '');
    
        var formData = new FormData(form); // Create a FormData object and append the form fields
    
        formData.append('update_id', update_id);
        formData.append('api_name', api_name);
        formData.append('is_thirdparty_api', 1);
        formData.append('logo', $('#logo')[0].files[0]); // Append the uploaded file
    
        $.ajax({
            url: ai_chat_settings_action,
            method: "POST",
            data: formData,
            dataType: 'JSON',
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting content type
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success: function(response) {
                $("#save_ai_chat_api_settings").removeAttr('disabled');
                if (response.error == '1') {
                    Swal.fire({
                        title: global_lang_error,
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: global_lang_ok
                    });
                } else {
                    Swal.fire({
                            title: global_lang_success,
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: global_lang_ok
                        })
                        .then(function() {
                            $("#ai_settings_modal").modal('hide');
                            // reload_default_ai_chat_settings();
                        });
                }
            },
            error: function(xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({
                    icon: 'error',
                    title: global_lang_error,
                    html: msg
                });
                return false;
            }
        });
    });
    

    $('#ai_settings_modal').on('hidden.bs.modal', function (e) {
      $("#api_type").trigger('change');
      if(typeof(table)!=='undefined') table.draw();
    });



    $(document).on('click','.update-ai-settings-row',function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		var modal_id = '#ai_settings_modal';
		var update_field = '#ai-chat-update-id';

		$(update_field).val(id);

		$.ajax({
            url: edit_ai_chat_settings_action,
            method: "POST",
            data: {id},
            dataType: 'JSON',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success:function(response)
            {
                $(modal_id).modal('show');
                $('#profile_name').val(response.profile_name);
                $('#custom_prompt').val(response.custom_prompt);
                $('#chat-model').val(response.chat_model);
                if(response.profile_img !='') $('#profile_img').attr('src',response.profile_img);
            },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            }

        });
	});

    

});


// function reload_default_ai_chat_settings() {

//     $.ajax({
//         url: ai_chat_profile_dropdown,
//         method: "POST",
//         data: {icon:true},
//         beforeSend: function (xhr) {
//             xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
//         },
//         success:function(response)
//         {
//             $("#default-main-container2").html(response);
//         }

//     });
// }
