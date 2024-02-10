"use strict";
var perscroll10; // template
var perscroll11; // template variables
var table10=''; // template
var table11=''; // template variables
var repCount = 1;

$(document).ready(function(){ 
    
   
   $(document).on('change', '.paramType', function(event){
        if ($(this).val() == 'dropdown') {
            var dropdownHtml = '<div class="mt-2"><select class="dropdown_value" style="width: 97%;" id="dropdown_value'+repCount+'" multiple="multiple"></select></div>';
            $(this).closest('.input-group').after(dropdownHtml);
        } else {
            $(this).closest('.input-group').next('.mt-2').remove();
        }

        $("#dropdown_value"+repCount).select2({
            placeholder: "Write down options.",
            tags: true,
            tokenSeparators: [',', ' '],
            width: 'resolve'
        })
    });

    function updateDropdown() {
        var paramName = $('.paramName').map(function() {
          return $(this).val();
        }).get();
        var dropdown = document.getElementById("myDropdown");
        dropdown.innerHTML = '';
        for (var i = 0; i < paramName.length; i++) {
          var lastItem = paramName[i];
          lastItem = lastItem.replace(/\s+/g, '-');
          var link = document.createElement("a");
          link.setAttribute("class", "dropdown-item");
          link.innerHTML = lastItem;
          dropdown.append(link);
        }
      }

    $(document).on('blur', '.paramName', function(event) {
    updateDropdown();
    });

    $(document).on('click', '#add-item', function(event) {
      repCount++;
    }); 

    $(document).on('click', '.delete-item', function(event) {
      repCount--;
        setTimeout(updateDropdown,500);
    }); 
    

    $('.dropdown-menu').on('click', '.dropdown-item', function(event) {
        const selectedText = event.target.innerText;
        const textArea = $('#about_text');
      
        // Check if the selected text is a non-empty string
        const selectedTextTrimmed = selectedText.trim();
        if (selectedTextTrimmed !== '') {
          // Get the current text area content
          let textAreaTxt = textArea.val();
      
          // Get the current caret position
          const caretPosition = textArea.prop('selectionStart');
      
          // Insert the selected text with hashtags at the current caret position
          const txtToAdd = `{{${selectedTextTrimmed}}} `;
          const newTextAreaTxt = textAreaTxt.slice(0, caretPosition) + txtToAdd + textAreaTxt.slice(caretPosition);
          textArea.val(newTextAreaTxt);
      
          // Move the caret position to the end of the inserted text
          const newCaretPosition = caretPosition + txtToAdd.length;
          textArea.prop('selectionStart', newCaretPosition);
          textArea.prop('selectionEnd', newCaretPosition);
      
          // Trigger the change and input events to ensure changes are registered
          textArea.trigger('change');
          textArea.trigger('input');
        }
      });
          

    if(table10==''){
        table10= $("#mytable10").DataTable({
            fixedHeader: false,
            colReorder: true,
            serverSide: true,
            processing:true,
            bFilter: true,
            order: [[ 2, "asc" ]],
            pageLength: 10,
            ajax:
                {
                    "url": template_manager_url_template_data,
                    "type": 'POST',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                    },
                    data: function ( d )
                    {
                    }
                },
            language:
                {
                    url: global_url_datatable_language
                },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
                {
                    targets: [0,1],
                    visible: false
                },
                {
                    targets: [6,7],
                    className: 'text-center'
                },
                {
                    targets: [7],
                    sortable: false
                }

            ],
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                if(areWeUsingScroll)
                {
                    if (perscroll10) perscroll10.destroy();
                    perscroll10 = new PerfectScrollbar('#mytable10_wrapper .dataTables_scrollBody');
                }
                var $searchInput = $('#mytable10_filter input');
                $searchInput.unbind();
                $searchInput.bind('keyup', function(e) {
                    if(this.value.length > 2 || this.value.length==0) {
                        table10.search( this.value ).draw();
                    }
                });
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
                if(areWeUsingScroll)
                {
                    if (perscroll10) perscroll10.destroy();
                    perscroll10 = new PerfectScrollbar('#mytable10_wrapper .dataTables_scrollBody');
                }
            }
        });
    }
    else table10.draw();

    if(table11==''){
        table11 = $("#mytable11").DataTable({
            fixedHeader: false,
            colReorder: true,
            serverSide: true,
            processing:true,
            bFilter: true,
            order: [[ 2, "asc" ]],
            pageLength: 10,
            ajax:
                {
                    "url": template_manager_url_template_group_data,
                    "type": 'POST',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                    },
                    data: function ( d )
                    {
                    }
                },
            language:
                {
                    url: global_url_datatable_language
                },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
                {
                    targets: [0,1,2],
                    visible: false
                },
                {
                    targets: [5,6],
                    className: 'text-center'
                },
                {
                    targets: [0,3,6],
                    sortable: false
                }

            ],
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                if(areWeUsingScroll)
                {
                    if (perscroll11) perscroll11.destroy();
                    perscroll11 = new PerfectScrollbar('#mytable11_wrapper .dataTables_scrollBody');
                }
                var $searchInput = $('#mytable11_filter input');
                $searchInput.unbind();
                $searchInput.bind('keyup', function(e) {
                    if(this.value.length > 2 || this.value.length==0) {
                        table11.search( this.value ).draw();
                    }
                });
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
                if(areWeUsingScroll)
                {
                    if (perscroll11) perscroll11.destroy();
                    perscroll11 = new PerfectScrollbar('#mytable11_wrapper .dataTables_scrollBody');
                }
            }
        });
    }
    else table11.draw();

    $(document).on('click', '#new_template_field', function(event) {
        event.preventDefault();
        $("#add_template_field").modal('show');
        setTimeout(function() { $("#api_type").trigger('change'); }, 500)
    });

    $(document).on('click', '.edit-template', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        $("#hidden-template-id").val(id);

        $.ajax({
            method: 'post',
            dataType: 'JSON',
            data: {id},
            url: template_manager_url_template_edit,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success: function (response) {
                if (response.status=='1') {
                    var template_name = response.data.template_name;
                    var about_text = response.data.about_text;
                    var template_description = response.data.template_description;
                    var ai_template_group_id = response.data.ai_template_group_id;
                    var api_group = capitalizeFirstLetter(response.data.api_group);
                    var api_type = response.data.api_type;
                    var output_display = response.data.output_display;
                    var model = response.data.model;
                    var template_thumb = response.data.template_thumb;
                    var default_tokens = response.data.default_tokens;
                    var prompt_fields = JSON.parse(response.data.prompt_fields);
                    if (response.data.paramType_drop_down_values === null || response.data.paramType_drop_down_values =='' ) {
                      var paramType_drop_down_values = [];
                    } 
                    else {
                      var paramType_drop_down_values = JSON.parse(response.data.paramType_drop_down_values);
                    }
                    var i=0;
                    $.each(prompt_fields, function(key, value) {
                      if(i>0) $("#add-item").trigger('click');  
                      $(".paramName").eq(i).val(key);
                      $(".paramType").eq(i).val(value).trigger('change');
                      i++;
                    });

                    i=0;
                    $.each(paramType_drop_down_values, function(key, value) {
                      $.each(value, function(key2, value2) {
                        var newOption = new Option(value2,value2, true, true);
                        $('.dropdown_value').eq(i).append(newOption);
                      });
                      $(".dropdown_value").eq(i).val(value).trigger('change');
                      i++;
                    });

                    const selectContainer = document.querySelector('.select2-container');
                    selectContainer.style.display = 'block';
                    $("#template_name").val(template_name);
                    $("#about_text").val(about_text);
                    $("#template_description").val(template_description);
                    $('#api_type optgroup[data-group="'+api_group+'"] option[value="' + api_type +'"]').prop('selected', true);
                    $("#api_type").trigger('change');
                    $("#output_display").val(output_display).trigger('change');
                    $("#model").val(model).trigger('change');
                    $("#template_thumb").val(template_thumb);
                    $("#default_tokens").val(default_tokens);
                    $("#ai_template_group_id").val(ai_template_group_id).trigger('change');
                    $("#add_template_field").modal('show');
                }
                else Swal.fire({icon: 'error',title: global_lang_error,html: response.message});
                
            },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            },
        });
    });

    $(document).on('change', '#api_type', function(event) {
        event.preventDefault();
        var api_type = $(this).val();
        if(api_type=='') return false;
        var api_group = $(this).find('option:selected').parent().data('group');
        var models = global_var_openai_endpoint_list[api_group][api_type].models
        var output = global_var_openai_endpoint_list[api_group][api_type].output
        var icon = global_var_openai_endpoint_list[api_group][api_type].icon

        $("#template_thumb").val(icon)

        if(api_group!='Text' && api_group!='Code' && api_group!='Chat')
        $("#default_tokens_container").addClass('d-none');        
        else $("#default_tokens_container").removeClass('d-none');
        if(api_group=='Audio' || api_type=='images/variations')            
        $(".prompt_related_container").addClass('d-none');        
        else $(".prompt_related_container").removeClass('d-none');

        $("#output_display").html('').trigger('change')
        if(typeof(output) !== undefined && Object.keys(output).length>0) $("#output_display").html(generate_options(output)).trigger('change')

        $("#model").html('<option value="">'+global_lang_default+'</option>').trigger('change')
        if(typeof(models) !== undefined && Object.keys(models).length>0) {
            $("#model").html(generate_options(models)).trigger('change')
        }
        
    });

    $(document).on('click', '#create_template', function(event) {
        event.preventDefault();
        var paramType_drop_down_values =[];
        for(var i=1;i<=repCount;i++){
            paramType_drop_down_values[i-1] = $('#dropdown_value'+i).val();     
        }
        $("#template_name_err").text("");
        $("#about_text_err").text("");
        $("#template_description_err").text("");
        $("#ai_template_group_id_err").text("");

        var id = $("#hidden-template-id").val();
        var template_name = $("#template_name").val();
        var about_text = $("#about_text").val();
        var template_description = $("#template_description").val();
        var model = $("#model").val();
        var ai_template_group_id = $("#ai_template_group_id").val();
        var api_type = $("#api_type").val();
        var output_display = $("#output_display").val();
        var default_tokens = $("#default_tokens").val();
        var api_group = $("#api_type").find('option:selected').parent().data('group');

        var template_thumb = $("#template_thumb").val();

        if(template_name == '') {
            $("#template_name_err").text(global_lang_required);
            return false;
        }        
        if(ai_template_group_id == '') {
            $("#ai_template_group_id_err").text(global_lang_required);
            return false;
        }

        if(api_group!='Audio' && api_type!='images/variations' && about_text == '') {
            $("#about_text_err").text(global_lang_required);
            return false;
        }

        var paramName = $('.paramName').map(function() {
          return $(this).val();
        }).get();

        var paramType = $('.paramType').map(function() {
          return $(this).val();
        }).get();

        

        $(this).addClass('btn-progress');
        var that = $(this);
        $.ajax({
            url: template_manager_url_template_save,
            type: 'POST',
            dataType: 'json',
            data: {id,template_name,about_text,template_description,model,paramType_drop_down_values,ai_template_group_id,template_thumb,paramName,paramType,api_type,api_group,output_display,default_tokens},
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success: function(response) {
                if(response.status=='1') {
                    toastr.success(response.message, global_lang_success,{'positionClass':'toast-bottom-right'});
                    $("#add_template_field").modal('hide');
                }
                else
                {
                    var errorMessage = response.message;
                    Swal.fire({icon: 'error',title: global_lang_error,html: errorMessage});
                }
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

    $('#add_template_field').on('hidden.bs.modal', function() {
        location.reload();
    });

    $(document).on('click', '#new_group_field', function(event) {
        event.preventDefault();
        $("#hidden-group-id").val('');        
        $("#group_name").val('');
        $("#group_serial").val('');
        $("#icon_class").val('');
        $("#group_name_err").text("");
        $("#group_serial_err").text("");
        $("#icon_class_err").text("");
        $("#add_group_field").modal('show');
    });

    $(document).on('click', '.edit-template-group', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('group-name');
        var icon = $(this).data('icon-class');
        var serial = $(this).data('serial');
        $("#hidden-group-id").val(id);
        $("#group_name").val(name);
        $("#icon_class").val(icon);
        $("#group_serial").val(serial);
        $("#group_name_err").text("");
        $("#group_serial_err").text("");
        $("#icon_class_err").text("");;
        $("#add_group_field").modal('show');
    });

    $(document).on('click', '#create_template_group', function(event) {
        event.preventDefault();

        $("#group_name_err").text("");
        $("#group_serial_err").text("");
        $("#icon_class_err").text("");

        var id = $("#hidden-group-id").val();
        var serial = $("#group_serial").val();
        var group_name = $("#group_name").val();
        var icon_class = $("#icon_class").val();

        if(group_name == '') {
            $("#group_name_err").text(global_lang_required);
            return false;
        }

        $(this).addClass('btn-progress');
        var that = $(this);

        $.ajax({
            url: template_manager_url_template_group_save,
            type: 'POST',
            dataType: 'json',
            data: {id,group_name,serial,icon_class},
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success: function(response) {
                if(response.status=='1') {
                    toastr.success(response.message, global_lang_success,{'positionClass':'toast-bottom-right'});
                    $("#add_group_field").modal('hide');
                }
                else
                {
                    var errorMessage = response.message;
                    Swal.fire({icon: 'error',title: global_lang_error,html: errorMessage});
                }
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

    $('#add_group_field').on('hidden.bs.modal', function() {
        table11.draw();
    });
});


(function($) {
  'use strict';
  $(function() {
    $('.repeater').repeater({
      // (Optional)
      // "defaultValues" sets the values of added items.  The keys of
      // defaultValues refer to the value of the input's name attribute.
      // If a default value is not specified for an input, then it will
      // have its value cleared.
      defaultValues: {
        'text-input': ''
      },
      // (Optional)
      // "show" is called just after an item is added.  The item is hidden
      // at this point.  If a show callback is not given the item will
      // have $(this).show() called on it.
      show: function() {
        $(this).slideDown();
      },
      // (Optional)
      // "hide" is called when a user clicks on a data-repeater-delete
      // element.  The item is still visible.  "hide" is passed a function
      // as its first argument which will properly remove the item.
      // "hide" allows for a confirmation step, to send a delete request
      // to the server, etc.  If a hide callback is not given the item
      // will be deleted.
      hide: function(deleteElement) {
        $(this).slideUp(deleteElement);        
      },
      // (Optional)
      // Removes the delete button from the first list item,
      // defaults to false.
      isFirstItemUndeletable: false
    })
  });
})(jQuery);


function generate_options(myArray){
   var options = '';
   $.each(myArray, function(index, value) {
        options += '<option value="' + index + '">' + value + '</option>';
   });
   return options;
}

function capitalizeFirstLetter(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}