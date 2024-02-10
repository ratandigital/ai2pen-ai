"use strict";
Dropzone.autoDiscover = false;
var maxFilesizeVar = tools_var_api_group=='image' ? 4 : 100;
var acceptedFilesVar = tools_var_api_group=='image' ? '.png' : '.mp3,.mp4,.mpeg,.mpga,.m4a,.wav,.webm';
$(document).ready(function(){ 

    var editorTextarea = $('.code-editor');
    for (var i = 0; i < editorTextarea.length; i++) {
        if ($("#editor-"+i).length){            
            var editor = ace.edit("editor-"+i);
            editor.setTheme("ace/theme/cobalt");
            editor.getSession().setMode("ace/mode/python");
            editor.setOptions({
              fontSize: "10pt",
              maxLines: Infinity
            });
            editor.container.style.lineHeight = 1.5
            editor.renderer.updateFontSize()
            document.getElementById("editor-"+i);
        }
    }

    

    $('.search-result').trigger('blur');

    $(document).on('click', '#generate', function(event) {
        event.preventDefault();

        if ($("#generate-form [required]").filter(function(){ return !this.value; }).length > 0) {
          Swal.fire({icon: 'warning',title: global_lang_warning,html: global_lang_fill_required_fields});
          return false;
        }

        
        $(this).trigger('blur');
        $(this).addClass('btn-progress');

        var id = $("#hidden-id").val();
        var ai_template_id = $("#ai-template-id").val();
        var group_slug = $("#group-slug").val();
        var template_slug = $("#template-slug").val();
        var document_name = $("#document_name").val();
        var media_url = $("#media_url").val();
        var media_duration = $("#media_duration").val();
        if(media_duration=='') media_duration = 0;
        var language = $("#language").val();
        var temperature = $("#temperature").val();
        var frequency_penalty = $("#frequency_penalty").val();
        var presence_penalty = $("#presence_penalty").val();
        var output_size = $("#output_size").val();
        var max_tokens = $("#max_tokens").val();
        var variation = $("#variation").val();
    
        var paramName = $('.paramName').map(function() {
          return $(this).val();
        }).get();

        var paramValue = $('.paramValue').map(function() {
          return $(this).val();
        }).get();

        var that = $(this);

        $.ajax({
            url: tools_url_generate_action,
            type: 'POST',
            dataType: 'json',
            data: {id,ai_template_id,group_slug,template_slug,document_name,media_url,media_duration,language,temperature,frequency_penalty,presence_penalty,output_size,max_tokens,variation,paramName,paramValue},
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success: function(response) {
                if(response.status=='1') {
                    Swal.fire({icon: 'success',title: global_lang_success,html: response.message});    
                    window.location.href=response.redirect             
                }
                else Swal.fire({icon: 'error',title: global_lang_error,html: response.message});
                $(that).removeClass('btn-progress');
            },
            error: function (xhr, statusText) {
                $(that).removeClass('btn-progress');
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            }
        });

    });

    var upload_template_url = tools_url_input_media_upload;
    $("#media-url-dropzone").dropzone({
        url: upload_template_url,
        maxFilesize:maxFilesizeVar,
        uploadMultiple:false,
        paramName:"file",
        createImageThumbnails:true,
        acceptedFiles: acceptedFilesVar,
        maxFiles:1,
        addRemoveLinks:false,
        headers: {'X-CSRF-TOKEN': csrf_token},
        success:function(file, response) {
            if (response.error) {
                Swal.fire({
                    icon: 'error',
                    text: response.error,
                    title: global_lang_error
                });
                return;
            }
            if (response.filename) {
                $("#media_url").val(response.filename);
                $("#media_duration").val(response.playtime);
            }
        }
    });
});