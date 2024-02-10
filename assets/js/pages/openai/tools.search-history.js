"use strict";

var perscroll;
var table;

$(document).ready(function() {

    table = $("#mytable").DataTable({
        fixedHeader: false,
        colReorder: true,
        serverSide: true,
        processing:true,
        bFilter: false,
        order: [[ 1, "desc" ]],
        pageLength: 10,
        ajax:
            {
                "url": tools_list_search_url_data,
                "type": 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                },
                data: function ( d )
                {
                    d.search_value = $('#search_value').val();
                    d.search_ai_template_id = $('#search_ai_template_id').val();
                }
            },
        language:
            {
                url: global_url_datatable_language
            },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columnDefs: [
            {
                targets: [1,6,7,9],
                visible: false
            },
            {
                targets: [5],
                className: 'text-center'
            },
            {
                targets: [0,3,5],
                sortable: false
            }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll)
            {
                if (perscroll) perscroll.destroy();
                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        },
        scrollX: 'auto',
        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
            if(areWeUsingScroll)
            {
                if (perscroll) perscroll.destroy();
                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        }
    });

    $(document).on('change', '#search_ai_template_id', function(e) {
        table.draw(false);
    });

    $(document).on('keyup', '#search_value', function(e) {
        if(e.which == 13 || $(this).val().length>2 || $(this).val().length==0) table.draw(false);
    });
});
