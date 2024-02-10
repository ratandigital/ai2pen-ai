"use strict";
var perscroll1;
var table1='';
var hideCols = [1,10];
// var drop_menu = '<a class="btn btn-outline-dark btn-sm send_email_ui float-end" href="#"><i class="far fa-paper-plane"></i>'+subscription_list_user_lang_email+'</a>';
var drop_menu = '';

$(document).ready(function() {

    setTimeout(function(){
        $("#mytable1_filter").append(drop_menu);
    }, 1000);

    table1 = $("#mytable1").DataTable({
        fixedHeader: false,
        colReorder: true,
        serverSide: true,
        processing:true,
        bFilter: true,
        order: [[ 10, "desc" ]],
        pageLength: 10,
        ajax:
            {
                "url": affiliate_subscription_list_user_url_data,
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
                targets: hideCols,
                visible: false
            },
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: [0,1,2,9],
                sortable: false
            }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll)
            {
                if (perscroll1) perscroll1.destroy();
                perscroll1 = new PerfectScrollbar('#mytable1_wrapper .dataTables_scrollBody');
            }
            var $searchInput = $('#mytable1_filter input');
            $searchInput.unbind();
            $searchInput.bind('keyup', function(e) {
                if(this.value.length > 2 || this.value.length==0) {
                    table1.search( this.value ).draw();
                }
            });
        },
        scrollX: 'auto',
        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
            if(areWeUsingScroll)
            {
                if (perscroll1) perscroll1.destroy();
                perscroll1 = new PerfectScrollbar('#mytable1_wrapper .dataTables_scrollBody');
            }
        }
    });


    $('#v-pills-tab .nav-link').on('shown.bs.tab', function (e) {
        var link_id = $(this).attr('id');

        if(link_id=='v-pills-user-request-tab' && table2!='') table2.draw();
        else if(link_id=='v-pills-withdrawals-tab' && table3!='') table3.draw();
    });


    $(document).on('click', '#new-affiliate,.update_affiliate_user', function(event) {
        event.preventDefault();

        let actionType = $(this).attr("actionType");
        let load = '<i class="fas fa-spinner fa-spin blue text-center" style="font-size:60px"></i>';
        $(".waiting").html(load);
        if(actionType=='add') {
            setTimeout(function(){
                $(".waiting").html('').hide();
                $("#addOrUpdate").show();
                $("#action_type").val(actionType);
            },500)

        } else if(actionType=='update') {
            $(".waiting").show();
            $("#action_type").val(actionType);
            let affiliateId = $(this).attr('id');
            $("#affiliate_id").val(affiliateId);
            $.ajax({
                url: affiliate_user_get_info,
                type: 'POST',
                data: {affiliateId: affiliateId},
                headers: { 'X-CSRF-TOKEN': csrf_token },
                success: function(response) {
                    var result = JSON.parse(response);
                    $("#affiliate-user-form input[name='name']").val(result.name)
                    $("#affiliate-user-form input[name='email']").val(result.email)
                    $("#affiliate-user-form input[name='mobile']").val(result.mobile)
                    $("#affiliate-user-form textarea[name='address']").val(result.address)
                    if(result.status=='1') $("#affiliate-user-form input[name='status']").prop('checked',true)
                    else $("#affiliate-user-form input[name='status']").prop('checked',false)
                    if(result.is_recurring=='1') $("#affiliate-user-form input[name='is_recurring']").prop('checked',true)
                    else $("#affiliate-user-form input[name='is_recurring']").prop('checked',false)

                    if(result.individual_id !== null) {
                        $("#affiliate-user-form input[name='is_overwritten']").prop('checked',true)
                        $("#commission_section").show();
                        if(result.signup_commission=='1') {

                            $("#affiliate-user-form input[name='signup_commission']").prop('checked',true);
                            $("#signup_sec_div").show();
                            $("#affiliate-user-form input[name='signup_amount']").val(result.sign_up_amount);
                        }

                        if(result.payment_commission=='1') {

                            $("#affiliate-user-form input[name='is_payment']").prop('checked',true);
                            $("#payment_sec_div").show();

                            if(result.payment_type=="fixed") {
                                $("#affiliate-user-form input[value='fixed']").prop('checked',true);
                                $("#affiliate-user-form input[value='percentage']").prop('checked',false);
                                $("#affiliate-user-form input[name='fixed_amount']").val(result.fixed_amount);
                                $("#affiliate-user-form input[name='percent_amount']").val('');
                                $("#fixed_amount_div").show();
                                $("#percentage_div").hide();
                            }

                            if(result.payment_type=='percentage') {
                                $("#affiliate-user-form input[value='fixed']").prop('checked',false);
                                $("#affiliate-user-form input[value='percentage']").prop('checked',true);
                                $("#affiliate-user-form input[name='percent_amount']").val(result.percentage);
                                $("#affiliate-user-form input[name='fixed_amount']").val('');
                                $("#percentage_div").show();
                            }
                        }
                    }


                    setTimeout(function(){
                        $(".waiting").html('').hide();
                        $("#addOrUpdate").show();
                    },500)
                }
            })
        }
        $("#v-pills-users-table").hide();
    });

    $(".cancel").click(function(){
        $("#addOrUpdate").hide();
        $(".waiting").show();
        let load = '<i class="fas fa-spinner fa-spin blue text-center" style="font-size:60px"></i>';
        $(".waiting").html(load);
        setTimeout(function(){
            $(".waiting").html('');
            $("#v-pills-users-table").show();
            $("#affiliate-user-form").trigger('reset');
            $("#affiliate-user-form #is_overwritten").prop('checked',false);
            $("#commission_section").hide();
        },500); 
    });

    $(document).on('change', '#is_overwritten', function(event) {
        event.preventDefault();

        if($(this).prop('checked') == true) {
            $("#commission_section").show(200);
        } else {
            $("#commission_section").hide(200);
        }
    });

    $(document).on('change', '#by_signup', function(event) {
        event.preventDefault();

        if($(this).prop('checked')==true) {
            $("#signup_sec_div").show(200);
        } else {   
            $("#signup_sec_div").hide(200);
        }
    });
    
    $(document).on('change', '#by_payment', function(event) {
        event.preventDefault();

        if($(this).prop('checked')==true) {
            $("#payment_sec_div").show(200);
        } else {   
            $("#payment_sec_div").hide(200);
            $("#payment_type").prop('checked', false);
        }
    });


    $(document).on('change', '#payment_type', function(event) {
        event.preventDefault();

        if($(this).val() == 'fixed') {
            $("#fixed_amount_div").show(100);
            $("#percentage_div").hide(100);
        } else {
            $("#fixed_amount_div").hide(100);
        }

        if($(this).val() == 'percentage') {
            $("#percentage_div").show(100);
        } else {
            $("#percentage_div").hide(100);

        }
    });

    $(document).on('click', '#affiliate-user-form-submit', function(event) {
        event.preventDefault();
        $(this).addClass("btn-progress disabled");
        var formdata = new FormData($("#affiliate-user-form")[0]);

        $.ajax({
            context: this,
            url: affiliate_user_form_submission,
            type: 'POST',
            dataType: 'json',
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            headers: { 'X-CSRF-TOKEN': csrf_token},
            success: function(response) {
                $(this).removeClass("btn-progress disabled")
                if(response.error) {
                    Swal.fire(global_lang_error,response.message,'error');
                    return false;
                } else {
                    Swal.fire(global_lang_success,response.message,'success').then((result)=>{
                        if(result.isConfirmed) {
                            table1.draw();
                            $("#addOrUpdate").hide();
                            let load = '<i class="fas fa-spinner fa-spin blue text-center" style="font-size:60px"></i>';
                            $(".waiting").show();
                            $(".waiting").html(load);
                            setTimeout(function(){
                                $(".waiting").html('').hide();
                                $("#v-pills-users-table").show();
                                $("#affiliate-user-form").trigger('reset');
                                $("#affiliate-user-form #is_overwritten").prop('checked',false);
                                $("#commission_section").hide();
                            },200);
                        }
                    });

                }
        
            }
        })  
    });



    $(document).on('click', '.send_email_ui', function(e) {
        var user_ids = [];
        $(".datatableCheckboxRow:checked").each(function ()
        {
            user_ids.push(parseInt($(this).val()));
        });

        if(user_ids.length==0)
        {
            Swal.fire({
                title: global_lang_warning,
                text: subscription_list_user_lang_warning_select_user,
                icon: 'warning',
                confirmButtonText: global_lang_ok
            });
            return false;
        }
        else  $("#modal_send_sms_email").modal('show');
    });

     $(document).on('click', '.affiliating_process_information', function(e) {
        var info = $(this).attr('info');
        $("#affiliating_process_information").modal('show');
        $('#information').text(info);
    });

     $(document).on('click', '#send_sms_email', function(e) {
         var subject = $("#subject").val();
         var message = $("#message").val();
         var user_ids = [];
         $(".datatableCheckboxRow:checked").each(function ()
         {
             user_ids.push(parseInt($(this).val()));
         });

         if(user_ids.length==0)
         {
             Swal.fire({
                 title: global_lang_warning,
                 text: subscription_list_user_lang_warning_select_user,
                 icon: 'error',
                 confirmButtonText: global_lang_ok
             });
             return false;
         }

         if(subject=='')
         {
             $("#subject").addClass('is-invalid');
             return false;
         }
         else
         {
             $("#subject").removeClass('is-invalid');
         }

         if(message=='')
         {
             $("#message").addClass('is-invalid');
             return false;
         }
         else
         {
             $("#message").removeClass('is-invalid');
         }

         $(this).addClass('btn-progress');
         $("#show_message").html('');
         $.ajax({
             context: this,
             type:'POST' ,
             beforeSend: function (xhr) {
                 xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
             },
             url: affiliate_list_user_url_send_email,
             data:{message:message,user_ids:user_ids,subject:subject},
             success:function(response){
                 $(this).removeClass('btn-progress');
                 $("#show_message").addClass("alert alert-primary text-center");
                 $("#show_message").html(response);
             }
         });

     });
});