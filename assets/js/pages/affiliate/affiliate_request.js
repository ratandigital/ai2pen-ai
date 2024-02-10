"use strict";

var perscroll;
$(document).ready(function() {
  $(document).on('click','#verify_button',function(event){
    $(this).prop('disabled', true);
    var email = $('#email').val();
    $.ajax({
      url: affiliate_send_whatsapp_otp,
      type: 'POST',
      data:{'email':email} ,
      headers: { 'X-CSRF-TOKEN': csrf_token},
      success: function(response) {
        if(response.error) {
          Swal.fire(global_lang_error,response.message,'error');
          return false;
        } 
        else{
           setTimeout(function(){
             $("#verify_button").html('Resend OTP');
             $("#verify_button").prop('disabled',false);
           },5000);
           return true;
        }
      }
    })  

  });
});

