"use strict";

  $(document).ready(function() {

    const input = document.getElementById("send_message");
 
    window.addEventListener("load", (e)=>{
      input.focus(); 
    })


    $("#send_message").on('keydown', function(event) {
        if (event.keyCode == 13 && !event.shiftKey) { // if Enter key is pressed without Shift key
        event.preventDefault(); // prevent line break
        $("#final_send_button").trigger('click'); // trigger click event on final_send_button
        var element = document.getElementById("conversation_modal_body");
        element.scrollTop = element.scrollHeight;
        }
    });

      $(document).on('click','#final_send_button',function(e){
    e.preventDefault();
    $("#custom_staating_msg").hide();  
    var send_message = $("#send_message").val().trim();
    var conversation_id = $("#conversation_id").val();
    var system_prompt_value = $("#system_prompt_value").val();
    var custom_prompt_id = $("#custom_prompt_id").val();
    var system_prompt_model = $("#system_prompt_model").val();
    


    if(send_message == '') return false;

    $("#send_message").val('');

    var user_msg = '<li class="sent"><img src="'+user_pic+'" alt="" /><p>'+send_message+'</p></li>';
    var bot_msg = '<li class="replies" id="ai_msg"><img src="'+ai_pic+'" alt="" /><p><img src="' + loading_gif + '" alt="GIF"/></p></li>';

    var element = document.getElementById("conversation_modal_body");
    element.scrollTop = element.scrollHeight;

    $("#conversation_modal_body").append(user_msg);
    $("#conversation_modal_body").append(bot_msg);
    $("#final_send_button").addClass('disabled');
    $.ajax({
        url:livechat_conversation,
        type:'POST',
        data:{from_user_id:from_user_id,send_message:send_message,conversation_id:conversation_id,system_prompt_value:system_prompt_value,system_prompt_model:system_prompt_model,custom_prompt_id:custom_prompt_id},
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
        },
        success:function(response){
          var con = JSON.parse(response);
          if (con.status=="0"){
            Swal.fire({icon: lang_error,title: con.message,});    
          }
          
          $("#ai_msg").remove();
          $("#conversation_modal_body").append(con.content);          
          $(".side_content").prepend(con.side_content);
          $("#conversation_id").val(con.conversation_id);
          var element = document.getElementById("conversation_body");
          element.scrollTop = element.scrollHeight;

          $("#final_send_button").removeClass('disabled');
        }

      });
  });


  $(document).on('click','#edit_btn',function(e){
    e.preventDefault();
    $('#contacts').off('click', '#side_chat_content', sidechat_load);
    var chatID = $(this).closest('.list-group').attr('id').replace('chat_', '');
    var sidechat_conv = document.getElementById('side_chat_' + chatID).textContent;
    var ai_first_reply = document.querySelector('.preview').textContent;
    $(".edit-hidden-input").val(chatID);
    var str ='<li class="contact"><div class="row"><div class="col-9" id="side_chat_content"><div class="wrap"><img src="'+chat_icon+'" alt="" /><input class="edit-hidden-input" type="hidden" id="chat_id_input_'+chatID+'"  value="'+chatID+'"><div class="meta"><input class="border" style="background:#2c3e50; color: #f5f5f5;" id="sidechat_edit_value" class="side_chat"  value="'+sidechat_conv+'"><div class="preview">'+ai_first_reply+'</div></div> </div></div><div class="col-3" style="padding-left: 30px"><span class="img_custom" style="padding-right: 10px"  id="edit_btn_confirm"><img  src="'+check_icon+'"/></span><span class="img_custom" id="edit_btn_rejection"><img src="'+close_icon+'"/></span></div></div><input class="" type="hidden" id="chat_id_input_id"  value="'+chatID+'"></li>'
    $("#chat_"+chatID).html(str);

    var edit_focus = document.getElementById("sidechat_edit_value");
 
    edit_focus.focus(); 
    

  });
  $(document).on('click','#edit_btn_rejection',function(e){
    e.preventDefault();
    var chatID = $(this).closest('.list-group').attr('id').replace('chat_', '');
    var sidechat_conv = $("#sidechat_edit_value").val();
    var ai_first_reply = document.querySelector('.preview').textContent;

    var str2 ='<input class="edit-hidden-input" type="hidden" id="chat_id_input_'+chatID+'"  value="'+chatID+'"><li class="contact"><div class="row"><div class="col-9" id="side_chat_content"><div class="wrap"><img src="'+chat_icon+'" alt="" /><div class="meta"><div class="name" id="side_chat_'+chatID+'"> '+sidechat_conv+'</div><div class="preview">'+ai_first_reply+'</div></div> </div></div><div class="col-3" style="padding-left: 30px"><span class="" style="padding-right: 8px"  id="edit_btn"><img class="img_custom" src="'+edit_icon+'"/></span><span class="" id="delete_btn"><img class="img_custom" src="'+delete_icon+'"/></span></div></div></li>'
    $("#chat_"+chatID).html(str2);

    $('#contacts').on('click', '#side_chat_content', sidechat_load);

  });

  $(document).on('click','#edit_btn_confirm',function(e){
    e.preventDefault();
    var sidechat_value = $("#sidechat_edit_value").val();
    var chatID = $("#chat_id_input_id").val();
    var ai_first_reply = document.querySelector('.preview').textContent;

    $.ajax({
      url:livechat_sidechat_edit,
      type:'POST',
      data: { sidechat_value: sidechat_value,chatID:chatID },
      beforeSend: function (xhr) {
          xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
      },
      success:function(response){        
        var con = JSON.parse(response);
        var str3 ='<ul class="list-group list-group-flush" id="chat_'+con.chat_id+'"><input class="edit-hidden-input" type="hidden" id="chat_id_input_'+con.chat_id+'" value="'+con.chat_id+'"><li class="contact"><div class="row"><div class="col-9" id="side_chat_content"><div class="wrap"><img src="'+chat_icon+'" alt="" /><div class="meta"><div class="name" id="side_chat_'+con.chat_id+'"> '+con.sidechat_value+'</div><div class="preview">'+ai_first_reply+'</div></div> </div></div><div class="col-3" style="padding-left: 30px"><span class="" style="padding-right: 8px"  id="edit_btn"><img class="img_custom" src="'+edit_icon+'" alt="" /></span><span class="" id="delete_btn"><img class="img_custom" src="'+delete_icon+'" alt="" /></span></div></div></li></ul>'
        $("#chat_"+chatID).html(str3);
        $('#contacts').on('click', '#side_chat_content', sidechat_load);     
      }


    });

  });

  $(document).on('click','#delete_btn',function(e){
    e.preventDefault();
    var chatID = $(this).closest('.list-group').attr('id').replace('chat_', '');
    var sidechat_conv = document.getElementById('side_chat_' + chatID).textContent;
    var ai_first_reply = document.querySelector('.preview').textContent;

    $(".edit-hidden-input").val(chatID);
    var str ='<input class="edit-hidden-input" type="hidden" id="chat_id_input_'+chatID+'"  value="'+chatID+'"><li class="contact"><div class="row"><div class="col-9" id="side_chat_content"><div class="wrap"><img src="'+chat_icon+'" alt="" /><div class="meta"><div class="name" id="side_chat_'+chatID+'"> '+sidechat_conv+'</div><div class="preview">'+ai_first_reply+'</div></div> </div></div><div class="col-3" style="padding-left: 30px"><span class="img_custom" style="padding-right: 10px"  id="delete_btn_confirm"><img src="'+check_icon+'"/></span><span class="img_custom pl-1" id="delete_btn_rejection"><img src="'+close_icon+'"/></span></div></div></li>'
    $("#chat_"+chatID).html(str);


    

  });
  $(document).on('click','#delete_btn_rejection',function(e){
    e.preventDefault();
    var chatID = $(this).closest('.list-group').attr('id').replace('chat_', '');
    var sidechat_conv = document.getElementById('side_chat_' + chatID).textContent;
    var ai_first_reply = document.querySelector('.preview').textContent;

    var str2 ='<input class="edit-hidden-input" type="hidden" id="chat_id_input_'+chatID+'"  value="'+chatID+'"><li class="contact"><div class="row"><div class="col-9" id="side_chat_content"><div class="wrap"><img src="'+chat_icon+'" alt="" /><div class="meta"><div class="name" id="side_chat_'+chatID+'"> '+sidechat_conv+'</div><div class="preview">'+ai_first_reply+'</div></div> </div></div><div class="col-3" style="padding-left: 30px"><span class="" style="padding-right: 8px"  id="edit_btn"><img class="img_custom" src="'+edit_icon+'"/></span><span class="" id="delete_btn"><img class="img_custom" src="'+delete_icon+'"/></span></div></div></li>'
    $("#chat_"+chatID).html(str2);

  });


  $(document).on('click','#delete_btn_confirm',function(e){
    e.preventDefault();
    var chatID = $(this).closest('.list-group').attr('id').replace('chat_', '');

    $.ajax({
        url:livechat_side_conversation_delete,
        type:'POST',
        data: { chat_id: chatID },
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
        },
        success:function(response){
            Swal.fire({icon: lang_success,title: data_deleted_success,});    

          $("#chat_"+chatID).hide();
          $("#conversation_modal_body").empty();          
       
        }


      });
  });

  $(document).on('click','#new_chat',function(e){
    e.preventDefault();

    $("#conversation_modal_body").empty();  
    $("#custom_staating_msg").show();  
    $("#More_Specialized_AI_Chat_items").show();  
    $("#More_Specialized_AI_Chat").show();  
    $("#conversation_id").val('0');  

  });
  $(document).on('click','#chat_download',function(e){
    e.preventDefault();
    var chatID =$("#chat_download_id").val();; 
    $.ajax({
      url:livechat_conversation_download,
      type:'POST',
      data: { chat_id: chatID },
      beforeSend: function (xhr) {
          xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
      },
      success:function(response){
        if (response.success) {
          // Create a download link and click it
          var downloadLink = document.createElement("a");
          downloadLink.href = response.file_url;
          downloadLink.download = response.file_name;
          downloadLink.click();
      } else {
          // Handle the error response
          Swal.fire({icon: lang_error, title: lang_error, text: response.error_message});
      }         
     
      },
      error: function(jqXHR, textStatus, errorThrown) {
        // Handle the AJAX error
        Swal.fire({icon: lang_error, title: lang_error, text: textStatus});
    }


    });


  });

  $(document).on('click','#modal_send_btn',function(e){
    e.preventDefault();
    var system_prmpt_value = $("#user_choice_system_prompt").val().trim();
    $("#system_prompt_value").val(system_prmpt_value);
    $("#prompt_msg").html(system_prmpt_value);
    
    var system_prmpt_model =  $('#prompt-model').val();
    $("#system_prompt_model").val(system_prmpt_model);
    $("#prompt_model").html(system_prmpt_model);
    $('#AItraningModal').modal('hide');

    $.ajax({
      url:user_choice_system_prompt,
      type:'POST',
      data: {from_user_id:from_user_id,system_prmpt_value: system_prmpt_value,system_prmpt_model:system_prmpt_model },
      beforeSend: function (xhr) {
          xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
      },
      success:function(response){
        // $('#system_prompt_model').val(response.system_prmpt_model);
      }


    });

  });
  $('#contacts').on('click', '#side_chat_content', sidechat_load);

  function sidechat_load() {
    // Get the chat ID from the hidden input field
    input.focus();
    $("#custom_staating_msg").hide();  
    $("#More_Specialized_AI_Chat_items").hide();  
    $("#More_Specialized_AI_Chat").hide();  
    var chatID = $(this).closest('.list-group').attr('id').replace('chat_', '');
    $("#chat_download_id").val(chatID);
    var conversation_id = chatID;
    var system_prompt_value = $("#system_prompt_value").val();
  
    $("#conversation_id").val(chatID);  
  
    // Make an AJAX request to retrieve the conversation messages
    $.ajax({
      url: livechat_side_conversation,
      type: 'POST',
      data: { chat_id: chatID, conversation_id:conversation_id,system_prompt_value:system_prompt_value },
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
      },
      success:function(response){
        var con = JSON.parse(response);
        $("#conversation_modal_body").empty().append(con.content);  
        var element = document.getElementById("conversation_modal_body");
        element.scrollTop = element.scrollHeight;        
        $("#final_send_button").removeClass('disabled');
      }
    });
  }


  $(document).on('click', '.carousel-item .card', custom_prompt_load);

  function custom_prompt_load() {
    var promptID = $(this).attr('id').replace('card_', '');

    var redirectURL = base_url+'/livechat/custom_prompt/'+promptID;

    window.location.href = redirectURL;

  }

  


    var darkModeEnabled = false;
    var darkModeButton = document.getElementById("dark_button");
    var darkModeIcon = document.getElementById("dark_mode_icon");
  
    function toggleDarkMode() {
      darkModeEnabled = !darkModeEnabled;
      document.querySelector('.content').classList.toggle('dark-mode');
      document.getElementById('frame').classList.toggle('dark-mode');
  
      if (darkModeEnabled) {
        darkModeIcon.src = moon_img;
        darkModeButton.setAttribute('data-mode', 'dark');
      } else {
        darkModeIcon.src = sun_img;
        darkModeButton.setAttribute('data-mode', 'light');
      }
    }
  
    $(document).on('click', '#dark_button', function(e) {
      e.preventDefault();
      toggleDarkMode();
      input.focus();
    });
    
  
    





});



