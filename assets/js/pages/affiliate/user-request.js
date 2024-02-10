"use strict";

var perscroll2;
var table2='';
$(document).ready(function() {
    table2 = $("#mytable2").DataTable({
        fixedHeader: false,
        colReorder: true,
        serverSide: true,
        processing:true,
        bFilter: true,
        order: [[ 1, "desc" ]],
        pageLength: 10,
        ajax:
            {
                "url": affiliate_user_request_list,
                "type": 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN',csrf_token);
                },
            },
        language:
            {
                url: global_url_datatable_language
            },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columnDefs: [
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: '',
                sortable: false
            },
             {
                targets: [1,2,7],
                visible: false
            }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll)
            {
                if (perscroll2) perscroll2.destroy();
                perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
            }
            var $searchInput = $('#mytable2_filter input');
            $searchInput.unbind();
            $searchInput.bind('keyup', function(e) {
                if(this.value.length > 2 || this.value.length==0) {
                    table2.search( this.value ).draw();
                }
            });
        },
        scrollX: 'auto',
        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
            if(areWeUsingScroll)
            {
                if (perscroll2) perscroll2.destroy();
                perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
            }
        }
    });
    $(document).on('click', '.request_status_change', function(event){
        var request_status = $(this).attr('status_value');
        var request_id = $(this).attr('data-id');
        if(request_status!=0){
          
           Swal.fire({
               title: global_lang_confirmation,
               text: global_lang_affiliate_user_response,
               icon: 'warning',
               buttons: true,
               dangerMode: true,
               showCancelButton: true,
           })
           .then((result) => {
               if (result.isConfirmed) {

                   $.ajax({
                       url: affiliate_request_status_change,
                       type: 'POST',
                       data:{'status':request_status,'request_id':request_id} ,
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

               } 
           });
        }  
    });
    
});

