<?php
$api_type_list = [];
foreach ($openai_endpoint_list as $type=>$endpoints){
    foreach ($endpoints as $api_title=>$endpoint){
        $api_type_list[$type][$api_title] = $endpoint['title'];
    }
}
$input_types = [''=>__('Select Type'),'text'=>__('Text'),'textarea'=>__('Textarea'),'number'=>__('Number'),'date'=>__('Date'),'color'=>__('Color'),'dropdown'=>__('Dropdown')];
$template_list_drop_down = $template_list;
$template_list_drop_down[''] = __('Select');
?>
<div class="modal fade" id="add_template_field" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Template')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="">
                <input type="hidden" id="hidden-template-id" value="">


                <div class="alert alert-warning">{{__("Your provided content, such as template name or description, must be in English. If you wish to localize your content, you can add it and translate it using a multilingual editor.")}}</div>

                <div class="row">
                    <div class="col-12 col-xl-6 pe-xl-1">
                        <div class="form-group">
                            <label><?php echo __('Template Title'); ?> *</label>
                            <input type="text" name="template_name" id="template_name" class="form-control form-control-lg">
                            <span id="template_name_err" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6 ps-xl-1">
                        <div class="form-group">
                            <label><?php echo __('Template Group'); ?> *</label>
                            <?php  echo Form::select('ai_template_group_id',$template_list_drop_down,'',['class'=>'form-control form-control-lg select2 w-100','id'=>'ai_template_group_id']);?>
                            <span id="ai_template_group_id_err" class="text-danger"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label><?php echo __('Template Description'); ?></label>
                            <textarea name="template_description" id="template_description" required class="form-control form-control-lg"></textarea>
                            <span id="template_description_err" class="text-danger"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-4 pe-xl-1">
                        <div class="form-group">
                            <label><?php echo __('API Type'); ?></label>
                            <select name="api_type" id="api_type" class="form-control form-control-lg w-100 select2">
                                @foreach ($api_type_list as $opt_group=>$options)
                                 <?php
                                    $label = $opt_group=='audio' ? __("Speech to Text") : __(ucfirst($opt_group));
                                 ?>
                                 <optgroup label="{{$label}}" data-group="{{$opt_group}}">
                                     @foreach ($options as $op_key=>$op_val)
                                         <option value="{{$op_key}}">{{$op_val}}</option>
                                     @endforeach
                                 </optgroup>
                                @endforeach
                            </select>
                            <span id="api_type_err" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="col-12 col-xl-4 ps-xl-0 pe-xl-1">
                        <div class="form-group">
                            <label><?php echo __('Prompt Model'); ?></label>
                            <?php  echo Form::select('model',[],'',['class'=>'form-control form-control-lg select2 w-100','id'=>'model']);?>
                            <span id="model_err" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="col-12 col-xl-4 ps-xl-0">
                        <div class="form-group">
                            <label><?php echo __('Output Type'); ?></label>
                            <?php  echo Form::select('output_display',[],'',['class'=>'form-control form-control-lg select2 w-100','id'=>'output_display']);?>
                            <span id="output_display_err" class="text-danger"></span>
                        </div>
                    </div>
                </div>

                <div class="col-12" id="default_tokens_container">
                    <div class="form-group">
                        <label><?php echo __('Default Token'); ?></label>
                        <input name="default_tokens" id="default_tokens" type="number" class="form-control form-control-lg" min="1" value="1500">
                        <span id="default_tokens_err" class="text-danger"></span>
                    </div>
                </div>

               <div class="col-12 prompt_related_container">
                 <div class="form-group repeater">
                   <label><?php echo __('Custom Input Parameters'); ?></label>
                  <div data-repeater-list="group-a">
                    <div data-repeater-item class="mb-2">
                      <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                        <input type="text" class="form-control form-control-lg paramName" id="paramName" placeholder="{{__('Parameter Name')}}">
                        <?php  echo Form::select('paramType',$input_types,'',['class'=>'form-control form-control-lg paramType','id'=>'paramType']);?>

                        <a data-repeater-delete class="delete-item ms-2 mt-3 text-decoration-none text-danger cursor-pointer" title="{{__('Delete')}}">
                          <i class="ti-trash"></i>
                      </div>
                      </a>
                    </div>
                  </div>
                   <button data-repeater-create id="add-item" type="button" class="btn btn-primary btn-sm mb-2 mt-2 no-radius">
                     <i class="ti-plus"></i> {{__('Add Field')}}
                   </button>
                 </div>
               </div>

               <div class="col-12 prompt_related_container">
                    <div class="form-group">
                        <div class="row">
                            <div class="col d-flex"> <label class="d-inline-flex p-2 mb-0"><?php echo __('Prompt Intro'); ?>*</label>
                                <div class="dropdown">
                                    <button class="btn btn-white btn-sm dropdown-toggle rounded-1" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php echo __('Variables'); ?> 
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1" id="myDropdown">
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <textarea name="about_text" id="about_text" required class="form-control form-control-lg"></textarea>
                        <span id="about_text_err" class="text-danger"></span>
                        <small style="font-size: 10px"><?php echo __('Example: Write a blog on {{about}} by targeting {{target-keyword}} keyword.'); ?></small>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label><?php echo __('Icon Class'); ?> (<a target="_BLANK" class="text-decoration-none fw-bld" href="https://pictogrammers.com/library/mdi/">{{__('Find Icon')}}</a>) </label>
                        <input type="text" id="template_thumb" class="form-control form-control-lg" placeholder="mdi mdi-robot">
                    </div>
                </div>

            </div>
            <div class="modal-footer d-block">
                <button type="button" id="create_template" class="btn btn-sm btn-success float-start"><i class="fas fa-check-circle"></i> {{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_group_field" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Template Group')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="">
                <input type="hidden" id="hidden-group-id" value="">
                <div class="alert alert-warning">{{__("Group name must be in English. If you wish to localize your content, you can add it and translate it using a multilingual editor.")}}</div>
                <div class="col-12">
                    <div class="form-group">
                        <label><?php echo __('Group Name'); ?> *</label>
                        <input type="text" name="group_name" id="group_name" class="form-control form-control-lg">
                        <span id="group_name_err" class="text-danger"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo __('Serial'); ?></label>
                            <input type="number" name="group_serial" id="group_serial" class="form-control form-control-lg">
                            <span id="group_serial_err" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo __('Icon Class'); ?> (<a target="_BLANK" class="text-decoration-none fw-bld" href="https://pictogrammers.com/library/mdi/">{{__('Find Icon')}}</a>) </label>
                            <input type="text" name="icon_class" id="icon_class" class="form-control form-control-lg" placeholder="mdi mdi-heart">
                            <span id="icon_class_err" class="text-danger"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-block">
                <button type="button" id="create_template_group" class="btn btn-sm btn-success float-start"><i class="fas fa-check-circle"></i> {{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>
