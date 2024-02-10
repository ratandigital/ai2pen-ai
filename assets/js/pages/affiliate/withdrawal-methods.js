"use strict";

var table_withdrawal_request;
var perscroll_withdrawal;
var drop_menu = '<a class="btn btn-primary btn-lg send_email_ui float-end" href="#"><i class="fas fa-paper-plane"></i> '+subscription_list_user_lang_email+'</a>';

$(document).ready(function() {

    table_withdrawal_request = $("#mytable").DataTable({
        fixedHeader: false,
        colReorder: true,
        serverSide: true,
        processing:true,
        bFilter: true,
        order: [[ 1, "desc" ]],
        pageLength: 10,
        ajax:
            {
                "url": affiliate_withdrawal_methods_data,
                "type": 'POST',
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
                targets: [1],
                visible: false
            },
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: [0,1,2,4],
                sortable: false
            }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll)
            {
                if (perscroll_withdrawal) perscroll_withdrawal.destroy();
                perscroll_withdrawal = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
            var $searchInput = $('div.dataTables_filter input');
            $searchInput.unbind();
            $searchInput.bind('keyup', function(e) {
                if(this.value.length > 2 || this.value.length==0) {
                    table_withdrawal_request.search( this.value ).draw();
                }
            });
        },
        scrollX: 'auto',
        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
            if(areWeUsingScroll)
            {
                if (perscroll_withdrawal) perscroll_withdrawal.destroy();
                perscroll_withdrawal = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        }
    });

    $(document).on('click', '.add_method', function(event) {
        event.preventDefault();

        $("#add_witdrawalMethod_modal").modal('show');
    });

    $(document).on('change', '#method_type', function(event) {
        event.preventDefault();

        var methodType = $("#method_type").val();

        if(methodType == 'paypal') {
            $("#paypal_email_div").css('display','block');
            $("#bank_acc_div").css('display','none');
        }

        if(methodType == 'bank_account') {
            $("#paypal_email_div").css('display','none');
            $("#bank_acc_div").css('display','block');
        }

        if(methodType == '') {
            $("#paypal_email_div").css('display','none');
            $("#bank_acc_div").css('display','none');
        }
    });

    $(document).on('change', '#edit_method_type', function(event) {
        event.preventDefault();

        var methodType = $("#edit_method_type").val();

        if(methodType == 'paypal') {
            $("#edit_paypal_email_div").css('display','block');
            $("#edit_bank_acc_div").css('display','none');
        }

        if(methodType == 'bank_account') {
            $("#edit_paypal_email_div").css('display','none');
            $("#edit_bank_acc_div").css('display','block');
        }

        if(methodType == '') {
            $("#edit_paypal_email_div").css('display','none');
            $("#edit_bank_acc_div").css('display','none');
        }
    });


    $(document).on('click', '#save_method_info', function(event) {
        event.preventDefault();

        var method_type = $("#method_type").val();
        var paypal_email = $("#paypal_email").val();
        var bank_acc_no = $("#bank_acc_no").val();

        if(method_type == '') {
            Swal.fire(global_lang_warning, global_lang_fill_required_fields, 'warning');
            return false;
        }

        if(method_type == 'paypal' && paypal_email == '') {
            Swal.fire(global_lang_warning,global_lang_fill_required_fields, 'warning');
            return false;
        }

        if(method_type == 'bank_account' && bank_acc_no == '') {
            Swal.fire(global_lang_warning, global_lang_fill_required_fields, 'warning');
            return false;
        }

        $(this).addClass('btn-progress disabled');

        $.ajax({
            context:this,
            url: affiliate_create_withdrawal_method,
            type: 'POST',
            dataType:'json',
            data: {method_type: method_type,paypal_email: paypal_email,bank_acc_no: bank_acc_no},
            headers: { 'X-CSRF-TOKEN': csrf_token },
            success:function(response) {

                $(this).removeClass('btn-progress disabled');

                if(!response.error) {
                    Swal.fire(global_lang_success, response.message, 'success').then((result)=>{
                        if(result.isConfirmed) {
                            $("#add_witdrawalMethod_modal").modal('hide');
                            $("#witdrawalMethod_add_form").trigger('reset');
                            $("#method_type").val('').trigger('change');
                            table_withdrawal_request.draw();
                        }
                    });
                }
                
                if(response.error) {
                    Swal.fire(global_lang_warning, response.message, 'warning');
                }


            }
        })
        
    });


    $(document).on('click', '.edit_method', function(event) {
        event.preventDefault();


        var method_id = $(this).attr("table_id");

        var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';
        $("#method_update_body").html(loading);

        $(".action_div").attr('style', 'display: none !important');

        $.ajax({
            url: affiliate_get_withdrawal_method_info,
            type: 'POST',
            data: {table_id: method_id},
            headers: { 'X-CSRF-TOKEN': csrf_token },
            success:function(response) {
                $("#method_update_body").html(response);
                $(".action_div").attr('style', 'display: block !important');
            }
        })

        $("#edit_witdrawalMethod_modal").modal('show');
    });


    $(document).on('click', '#update_method_info', function(event) {
        event.preventDefault();

        var table_id = $("#table_id").val();
        var method_type = $("#edit_method_type").val();
        var paypal_email = $("#edit_paypal_email").val();
        var bank_acc_no = $("#edit_bank_acc_no").val();

        if(method_type == '') {
            Swal.fire(global_lang_warning, global_lang_fill_required_fields, 'warning');
            return false;
        }

        if(method_type == 'paypal' && paypal_email == '') {
            Swal.fire(global_lang_warning, global_lang_fill_required_fields, 'warning');
            return false;
        }

        if(method_type == 'bank_acc' && bank_acc_no == '') {
            Swal.fire(global_lang_warning, global_lang_fill_required_fields, 'warning');
            return false;
        }

        $(this).addClass('btn-progress disabled');

        $.ajax({
            context:this,
            url: affiliate_update_withdrawal_method_info,
            type: 'POST',
            dataType: 'json',
            data: {table_id: table_id,method_type: method_type,paypal_email: paypal_email,bank_acc_no: bank_acc_no},
            headers: { 'X-CSRF-TOKEN': csrf_token },
            success:function(response) {

                $(this).removeClass('btn-progress disabled');

                if(!response.error) {
                    Swal.fire(global_lang_success, response.message, 'success').then((result)=>{
                        if(result.isConfirmed) {
                            $("#edit_witdrawalMethod_modal").modal('hide');
                            $("#witdrawalMethod_edit_form").trigger('reset');
                            $("#edit_method_type").val('').trigger('change');
                            table_withdrawal_request.draw();
                        }
                    });
                }

                if(response.error) {
                    Swal.fire(global_lang_warning, response.message, 'warning');
                }
            }
        })
    });

    $("#add_witdrawalMethod_modal").on('hidden.bs.modal',function() {
        $("#witdrawalMethod_add_form").trigger('reset');
        $("#method_type").val('').trigger('change');
        table_withdrawal_request.draw();
    });

    $("#edit_witdrawalMethod_modal").on('hidden.bs.modal',function() {
        table_withdrawal_request.draw();
    });


    $(document).on('click','.delete_method',function(e){
      e.preventDefault();
      var id = $(this).attr('table_id');
      var somethingwentwrong = "<?php echo $this->lang->line('Something went wrong, please try once again.'); ?>";

      Swal.fire({
        title: global_lang_confirmation,
        text: global_lang_delete_confirmation,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
            $(this).removeClass('btn-outline-danger');
            $(this).addClass('btn-progress btn-danger');

            $.ajax({
              context: this,
              type:'POST' ,
              url: affiliate_withdrawal_method_delete,              
              data: {id:id},
              headers: { 'X-CSRF-TOKEN': csrf_token },
              success:function(response){ 

                 $(this).removeClass('btn-progress btn-danger');
                 $(this).addClass('btn-outline-danger');

                 if(response == '1') {
                    Swal.fire(global_lang_success,global_lang_deleted_successfully,'success').then((result) => {
                        if(result.isConfirmed) {
                            table_withdrawal_request.draw();
                        }
                    });
                 } else {
                   Swal.fire(global_lang_error, global_lang_something_wrong, 'error');
                 }


              }
            });
        } 
      });
    });

    $(document).on('click', '.method_details', function(event) {
        event.preventDefault();
        /* Act on the event */
        var method_name = $(this).attr("method_name");
        var method_name = method_name.replace("_"," ");
        var details = $(this).attr("details");
        $("#method_name").html(method_name.charAt(0).toUpperCase()+method_name.slice(1));
        $("#method_details").html(details);
        $("#method_details_modal").modal('show');

    });

});