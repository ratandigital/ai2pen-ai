"use strict";

var perscroll;
var table;
$(document).ready(function() {

    setTimeout(function(){
        $('#date_range_picker').daterangepicker({
            format: "YYYY/MM/DD",
            separator: "-",
            startDate: moment().subtract(29, 'days'),
            endDate  : moment(),
            "applyLabel": global_lang_apply,
            "cancelLabel": global_lang_cancel,
            "fromLabel": global_lang_from,
            "toLabel": global_lang_to,
            "customRangeLabel": global_lang_custom
        }, function (start, end) {
            $('#date_range_val').val(start.format('YYYY/MM/DD') + '-' + end.format('YYYY/MM/DD')).trigger('change');
        });
    }, 1000);


    table = $("#mytable").DataTable({
        fixedHeader: false,
        colReorder: true,
        serverSide: true,
        processing:true,
        bFilter: true,
        order: [[ 2, "desc" ]],
        pageLength: 10,
        ajax:
            {
                "url": member_settings_list_api_log_url_data,
                "type": 'POST',
                data: function ( d )
                {
                    d.date_range = $('#date_range_val').val();
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
                targets: [1,2,8],
                visible: false
            },
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: [0,1,7,8],
                sortable: false
            }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll)
            {
                if (perscroll) perscroll.destroy();
                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
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
                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        }
    });

    $(document).on('change', '#date_range_val', function(event) {
        event.preventDefault();
        table.draw();
    });
});
