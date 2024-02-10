"user strict";

$(document).ready(function() {

	if($("#by_signup_common").prop('checked')==true) {
	    $("#signup_sec_div_common").show(500);

	} else {   
	    $("#signup_sec_div_common").hide(500);
	}

	if($("#by_payment_common").prop('checked')==true) {
	    $("#payment_sec_div_common").show(500);

	} else {   
	    $("#payment_sec_div_common").hide(500);
	}
	
	$(document).on('change', '#by_signup_common', function(event) {
		event.preventDefault();

		if($(this).prop('checked')==true) {
		    $("#signup_sec_div_common").show(500);
		} else {   
		    $("#signup_sec_div_common").hide(500);
		}
	});
	
	$(document).on('change', '#by_payment_common', function(event) {
		event.preventDefault();

		if($(this).prop('checked')==true) {
		    $("#payment_sec_div_common").show(500);
		} else {   
		    $("#payment_sec_div_common").hide(500);
		    $("#payment_type_common").prop('checked', false);
		}
	});


	$(document).on('change', '#payment_type_common', function(event) {
		event.preventDefault();

		if($(this).val() == 'fixed') {
			$("#fixed_amount_div_common").show(500);
			$("#percentage_div_common").hide(500);
		} else {
			$("#fixed_amount_div_common").hide(500);
		}

		if($(this).val() == 'percentage') {
			$("#percentage_div_common").show(500);
		} else {
			$("#percentage_div_common").hide(500);

		}
	});

	$(document).on('click', '#submit_commission', function(event) {
		event.preventDefault();
		var alldatas = new FormData($("#affiliate_commission_settings_form")[0]);

		$.ajax({
			url: affiliate_common_commision_set,
			type: 'POST',
			dataType: 'json',
			data: alldatas,
			cache: false,
			contentType: false,
			processData: false,
			headers: { 'X-CSRF-TOKEN': csrf_token},
			success: function(response) {
				if(response.error) {
					Swal.fire(global_lang_error,response.message,'error');
					return false;
				} else {
					Swal.fire(global_lang_success,response.message,'success').then((result)=>{
						if(result.isConfirmed) {
							location.reload();
						}
					});

				}
  
			}
		})		
	});

});