<?php
    if(!isset($is_admin)) $is_admin = '0';
    if(!isset($is_member)) $is_member = '0';
    if(!isset($is_manager)) $is_manager = '0';
    if(!isset($is_trial)) $is_trial = '0';
    if(!isset($user_module_ids)) $user_module_ids = [];
    if(!isset($team_access)) $team_access = [];
    $language = config('app.locale');
    $language_exp = explode('-', $language);
    $language_code = $language_exp[0] ?? 'en';
    $datatable_lang_file_path = get_public_path('assets').DIRECTORY_SEPARATOR.'vendors'.DIRECTORY_SEPARATOR.'datatables'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$language_code.'.json';
    if(file_exists($datatable_lang_file_path))
    $datatable_lang_file = asset('assets/vendors/datatables/language/'.$language_code.'.json');
    else $datatable_lang_file = asset('assets/vendors/datatables/language/en.json');
?>
<script type="text/javascript">
    "use strict";
    var base_url = '{{url('/')}}';
    var site_url = base_url;
    var temp_route_variable = 1;
    var csrf_token = '{{ csrf_token() }}';
    var today = '{{ date("Y-m-d") }}';
    var is_admin = '{{(int)$is_admin}}';
    var is_member = '{{(int)$is_member}}';
    var is_manager = '{{(int)$is_manager}}';
    var route_name = '{{isset($route_name) && !empty($route_name) ? $route_name : ""}}';
    var language = '{{$language}}';
    var auth_user_id = '{{Auth::user()->id ?? ''}}';
    var auth_user_name = '{{Auth::user()->name ?? ''}}';
    var auth_user_email = '{{Auth::user()->email ?? ''}}';
    var auth_user_type = '{{Auth::user()->user_type ?? ''}}';

    var user_module_ids = '{{json_encode($user_module_ids)}}';
    var module_id_token = '{{$module_id_token}}';
    var module_id_image = '{{$module_id_image}}';
    var module_id_audio = '{{$module_id_audio}}';
    var module_id_team_member = '{{$module_id_team_member}}';
    var module_id_settings = '{{$module_id_settings}}';
    var module_id_template_manager = '{{$module_id_template_manager}}';

    var global_url_login = '{{ route('login') }}';
    var global_url_register = '{{ route('register') }}';
    var global_url_dashboard = '{{ route('dashboard') }}';
    var global_url_datatable_language = '{{$datatable_lang_file}}';
    var global_url_payment_success = '{{ route('transaction-log') }}'+'?action=success';
    var global_url_payment_cancel = '{{ route('transaction-log') }}'+'?action=cancel';
    var global_url_notification_mark_seen = '{{ route('notification-mark-seen') }}'

    var global_lang_copied_to_clipboard = '{{ __('Copied to clipboard') }}';
    var global_lang_default = '{{ __('Default') }}';
    var global_lang_loading = '{{ __('Loading') }}';
    var global_lang_save = '{{ __('Save') }}';
    var global_lang_saving = '{{ __('Saving') }}';
    var global_lang_sent = '{{ __('Sent') }}';
    var global_lang_required = '{{ __('Required') }}';
    var global_lang_ok = '{{ __('OK') }}';
    var global_lang_procced = '{{ __('Proceed') }}';
    var global_lang_success = '{{ __('Success') }}';
    var global_lang_warning = '{{ __('Warning') }}';
    var global_lang_error = '{{ __('Error') }}';
    var global_lang_confirm = '{{ __('Confirm') }}';
    var global_lang_create = '{{ __('Create') }}';
    var global_lang_create_default = '{{ __('Create Default') }}';
    var global_lang_edit = '{{ __('Edit') }}';
    var global_lang_delete = '{{ __('Delete') }}';
    var global_lang_clear_log = '{{ __('Clear Log') }}';
    var global_lang_cancel = '{{ __('Cancel') }}';
    var global_lang_apply = '{{ __('Apply') }}';
    var global_lang_understand = '{{ __('I Understand') }}';
    var global_lang_download = '{{ __('Download') }}';
    var global_lang_from = '{{ __('From') }}';
    var global_lang_to = '{{ __('To') }}';
    var global_lang_custom = '{{ __('Custom') }}';
    var global_lang_choose_data = '{{ __('Date') }}';
    var global_lang_last_30_days = '{{ __('Last 30 Days') }}';
    var global_lang_this_month = '{{ __('This Month') }}';
    var global_lang_last_month = '{{ __('Last Month') }}';
    var global_lang_something_wrong = '{{ __('Something went wrong.') }}';
    var global_lang_confirmation = '{{ __('Are you sure?') }}';
    var global_lang_delete_confirmation = '{{ __('Do you really want to delete this record? This action cannot be undone and will delete any other related data if needed.') }}';
    var global_lang_submitted_successfully = '{{ __('Data has been submitted successfully.') }}';
    var global_lang_saved_successfully = '{{ __('Data has been saved successfully.') }}';
    var global_lang_deleted_successfully = '{{ __('Data has been deleted successfully.') }}';
    var global_lang_fill_required_fields = '{{ __('Please complete all the required fields.') }}';
    var global_lang_bot_unsubscribed = '{{ __('Unsubscribed') }}';
    var global_all_fields_are_required = '{{ __('All fields are required.') }}';

    var common_function_url_get_email_profile_dropdown = '{{route('common-get-email-profile-dropdown')}}';
    var common_function_url_get_thirdparty_api_profile_dropdown = '{{route('common-get-thirdparty-api-profile-dropdown')}}';

    var subscription_list_package_url_data = '{{route('list-package-data')}}';
    var subscription_list_package_url_update = '{{route('update-package',':id')}}';
    var subscription_list_package_url_delete = '{{route('delete-package')}}';
    var subscription_list_user_url_data = '{{route('list-user-data')}}';
    var subscription_list_user_url_update = '{{route('update-user',':id')}}';
    var subscription_list_user_url_delete = '{{route('delete-user')}}';
    var subscription_list_user_url_send_email = '{{route('user-send-email')}}';
    var subscription_list_user_lang_send_email = '{{__('Send Email')}}';
    var subscription_list_user_lang_email = '{{__('Email')}}';
    var subscription_list_user_lang_warning_select_user = '{{__('You have to select users to send email.')}}';

    var member_transaction_log_url_data= '{{route('transaction-log-data')}}';
    var member_transaction_log_manual_url_data= '{{route('transaction-log-manual-data')}}';
    var member_payment_buy_package_url = '{{route('buy-package',':id')}}';
    var member_payment_select_package_lang_already_subscribed = '{{__('Already Subscribed')}}';
    var member_payment_select_package_lang_already_subscribed_lang = '{{__('You already have a subscription set up. If you want to switch to a new payment method or subscription, please sure to cancel your current one first.')}}';
    var member_payment_buy_package_package_id = '{{$buy_package_package_id ?? '0'}}';
    var member_payment_buy_package_has_recurring_flag = '{{$has_reccuring ?? '0'}}';
    var member_settings_list_api_settings_url_data = '{{route('api-settings-data')}}';
    var ai_chat_settings_action = '{{route('ai-chat-settings-action')}}';
    var ai_chat_settings_data = '{{route('ai-chat-settings-data')}}';
    var edit_ai_chat_settings_action = '{{route('edit-ai-chat-settings-action')}}';
    var ai_chat_profile_dropdown = '{{route('ai-chat-profile-dropdown')}}';
    var member_settings_list_api_settings_url_update_data = '{{route('update-api-settings')}}';
    var member_settings_list_api_settings_url_save = '{{route('save-api-settings')}}';
    var member_settings_list_api_log_url_data = '{{route('list-payment-api-log-data')}}';

    var manual_payment_upload_file_route = '{{ route("Manual-payment-upload-file") }}';
    var manual_payment_submission_route = '{{ route("Manual-payment-submission") }}';
    var manual_payment_upload_file_delete_route = '{{ route("Manual-payment-uploaded-file-delete") }}';
    var manual_payment_handle_action_route = '{{ route("Manual-payment-handle-action") }}';

    var template_manager_url_template_data = '{{route('list-template-data')}}';
    var template_manager_url_template_edit = '{{route('edit-template')}}';
    var template_manager_url_template_save = '{{route('save-template')}}';
    var template_manager_url_template_group_data = '{{route('list-template-group-data')}}';
    var template_manager_url_template_group_save = '{{route('save-template-group')}}'

    var tools_url_input_media_upload = '{{route('tools-upload-input-media')}}'
    var tools_url_generate_action = '{{route('tools-action')}}'
    var tools_var_api_group = '{{$template_data->api_group??''}}'
    var tools_list_search_url_data = '{{route('tools-search-history-data')}}';

    // Affiliate global lang
    var global_lang_affiliate_withdrawal_response = '{{ __('Do you really want to change the affliate withdrawal status.') }}';
    var global_lang_affiliate_user_response = '{{ __('Do you really want to change the  status.') }}';
    var affliate_edit_request = '{{ __('Edit Request') }}'
    var requested_amount_error = '{{ __('Please provide a valid amount. You are allowed to withdraw minimum $50') }}'

    @if(check_build_version()=='double')
        var affiliate_subscription_list_user_url_data = '{{route('affiliate-list-user-data')}}';
        var affiliate_common_commision_set = '{{route('affiliate-commission-settings-set')}}';
        var affiliate_user_form_submission = '{{route('affiliate-user-form-submission')}}';
        var affiliate_user_get_info = '{{route('affiliate-user-get-info')}}';
        var affiliate_withdrawal_methods_data = '{{route('affiliate-withdrawal-methods-data')}}';
        var affiliate_create_withdrawal_method = '{{route('affiliate-create-withdrawal-method')}}';
        var affiliate_get_withdrawal_method_info = '{{route('affiliate-get-withdrawal-method-info')}}';
        var affiliate_update_withdrawal_method_info = '{{route('affiliate-update-withdrawal-method-info')}}';
        var affiliate_withdrawal_method_delete = '{{route('affiliate-withdrawal-method-delete')}}';
        var affiliate_user_request_list = '{{ route('affiliate-user-request-list') }}';
        var affiliate_request_status_change = '{{ route('affiliate-request-status-change') }}';
        var affiliate_send_whatsapp_otp = '{{ route('affiliate-send-whatsapp-otp') }}';
        var affiliate_withdrawal_requests_admin = '{{ route('affiliate-withdrawal-request-list-admin') }}';
        var affiliate_withdrawal_requests_delete_admin = '{{ route('affiliate-withdrawal-request-delete-admin') }}';
        var affiliate_withdrawal_requests_status_change = '{{ route('affiliate-withdrawal-request-status-change') }}';
        var affiliate_system_get_requests_info = '{{ route('affiliate_system-get-requests-info') }}';
        var affiliate_system_issue_new_request = '{{ route('affiliate-system-issue-new-request') }}';
        var affiliate_withdrawal_requests = '{{ route('affiliate-withdrawal-requests') }}';
        var affiliate_delete_withdrawal_requests = '{{ route('delete-withdrawal-request') }}';
        var affiliate_list_user_url_send_email = '{{route('affiliate-send-email')}}';
    @endif

    var purchase_code_active = '{{ route("credential-check-action") }}';

    <?php if(check_is_mobile_view()) echo 'var areWeUsingScroll = false;';
    else echo 'var areWeUsingScroll = true;';?>

    var global_var_openai_endpoint_list = JSON.parse('<?php echo json_encode($openai_endpoint_list);?>')
</script>
