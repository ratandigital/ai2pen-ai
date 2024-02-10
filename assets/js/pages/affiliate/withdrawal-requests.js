"use strict";

$(document).ready(function() {

	function validateEmail(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/; 
		return regex.test(email);
	}

	$(document).on('click', '.add_request', function(event) {
		event.preventDefault();

		if(method_numbers == 0) {
			Swal.fire(global_lang_warning,method_link,'warning');
			return false;
		}

		$('html, body').animate({
            scrollTop: $("#new_form_div").offset().top
        }, 100);

		$(".new_request_button").hide();
		$(".form_header_title").html(new_request);
	});

	$(document).on('click', '.reverse_form', function(event) {
		event.preventDefault();


		$(".new_request_button").show();
		$("#new_requests_form").trigger('reset');
		$("#withdrawal_account").val('').trigger("change");
		$("#tableId").val("");
	});


	$(document).on('click', '.edit_request', function(event) {
		event.preventDefault();
		$('.add_request').hide();
		$(".new_request_button").hide();
		$("#add_request_submit").attr('submit_action', 'edit');
		var tableid = $(this).attr('table_id');
		$(".form_header_title").html(affliate_edit_request);

		$.ajax({
			url: affiliate_system_get_requests_info,
			type: 'POST',
			data: {table_id: tableid},
			headers: { 'X-CSRF-TOKEN': csrf_token },
			dataType: "json",
			success:function(response) {
				$("#tableId").val(tableid);
				$("#withdrawal_account").html(response.option_str).prop('selected', true).trigger('change');
				$("#requested_amount").val(response.requested_amount);
				$("#previous_amount").val(response.requested_amount);
				$('html, body').animate({
		            scrollTop: $("#new_form_div").offset().top
		        }, 100);
			}
		})

	});

	$(document).on('click', '#add_request_submit', function(event) {
		event.preventDefault();
		var withdrawal_account = $("#withdrawal_account").val();
		var requested_amount = $("#requested_amount").val();
		var previous_amount = $("#previous_amount").val();
		var tableId = $("#tableId").val();
		var submit_action = $(this).attr('submit_action');

		if(withdrawal_account == '') {
			Swal.fire(global_lang_warning,select_method,'warning');
			return;
		}

		if(requested_amount == '' || requested_amount < 50) {
			Swal.fire(global_lang_warning,requested_amount_error,'warning');
			return;
		}

		$(this).addClass('btn-progress');

		$.ajax({
			context:this,
			url: affiliate_system_issue_new_request,
			type: 'POST',
			data: {withdrawal_account: withdrawal_account,requested_amount: requested_amount,submit_action:submit_action,tableId:tableId,previous_amount:previous_amount},
			headers: { 'X-CSRF-TOKEN': csrf_token },
			dataType: 'json',
			success:function(response) {

				$(this).removeClass('btn-progress');

				if(response.status == '1') {
					var span = document.createElement("span");
					var report_link = affiliate_withdrawal_requests;
					Swal.fire(global_lang_success,response.response_success,'success').then((result)=>{
						if(result.isConfirmed) {
							location.reload();
						}
					});
				}

				if(response.status == '0') {
					var span = document.createElement("span");
					Swal.fire(global_lang_warning, response.response_error,'warning');
				}
				if(response.status == '2') {
					var span = document.createElement("span");
					Swal.fire(global_lang_warning, response.response_edit_fail,'warning');
				}
			}
		})

	});


	$(document).on('click','.delete_request',function(e){
		e.preventDefault();
		var id = $(this).attr('table_id');
		Swal.fire(global_lang_delete,delete_notice,'delete').then((result)=>{
			$.ajax({
					context: this,
					type:'POST' ,
					url: affiliate_delete_withdrawal_requests,              
					data: {id:id},
					headers: { 'X-CSRF-TOKEN': csrf_token },
					success:function(response){ 
						var report_link = affiliate_withdrawal_requests;
						if(response.error)
						{
							Swal.fire(global_lang_warning, response.message,'warning').then((result)=>{
								if(result.isConfirmed) {
									location.reload();
								}
							});						
						}
						else
						{
							Swal.fire(global_lang_success,response.message,'success').then((result)=>{
								if(result.isConfirmed) {
									location.reload();
								}
							});
						}


					}
				});
		});
	});

	$(document).on('click', '.method_details', function(event) {
		event.preventDefault();
		/ Act on the event /
		var method_name = $(this).attr("method_name");
		var details = $(this).attr("details");
		$("#method_name").html(method_name);
		$("#method_details").html(details);
		$("#method_details_modal").modal();

	});

});


