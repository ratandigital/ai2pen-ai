<?php

use App\Http\Controllers\Home;
use App\Http\Controllers\Member;
use App\Http\Controllers\Landing;
use App\Http\Controllers\Livechat;
use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Env;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Subscription;
use App\Http\Controllers\UpdateSystem;
$auth_or_guest =  env('APP_ENV')=='local' ? 'guest' : 'auth';


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if(!file_exists(public_path("install.txt"))) {
    Route::get('/', [Landing::class,'index'])->name('home');
	Route::get('/dashboard', [Dashboard::class,'index'])->middleware(['auth'])->name('dashboard');
}
else {
	Route::get('/', [Landing::class,'install'])->name('home');
	Route::get('/dashboard', [Landing::class,'install'])->middleware(['auth'])->name('dashboard');
}


Route::get('/dashboard/user', [Dashboard::class,'index'])->middleware(['auth'])->name('dashboard-user');

Route::get('settings/account',[Member::class,'account'])->middleware(['auth'])->name('account');
Route::post('settings/account',[Member::class,'account_action'])->middleware(['auth','XssSanitizer'])->name('account-action');
Route::get('settings',[Member::class,'general_settings'])->middleware(['auth'])->name('general-settings');
Route::post('settings',[Member::class,'general_settings_action'])->middleware(['auth','XssSanitizer'])->name('general-settings-action');
Route::post('settings/set-session-active-tab',[Member::class,'set_session_active_tab'])->middleware(['auth'])->name('general-settings-set-session-active-tab');
Route::get('settings/payment',[Member::class,'payment_settings'])->middleware(['auth'])->name('payment-settings');
Route::post('settings/payment',[Member::class,'payment_settings_action'])->middleware(['auth','XssSanitizerExcludable:manual_payment_instruction'])->name('payment-settings-action');
Route::post('settings/sms-email-api/list',[Member::class,'api_settings_data'])->middleware(['auth'])->name('api-settings-data');
Route::post('settings/sms-email-api/update-data',[Member::class,'update_api_settings'])->middleware(['auth'])->name('update-api-settings');
Route::post('settings/sms-email-api/save',[Member::class,'save_api_settings'])->middleware(['auth','XssSanitizer'])->name('save-api-settings');
Route::post('settings/sms-email-api/delete',[Member::class,'delete_api_settings_action'])->middleware(['auth'])->name('delete-api-settings-action');
Route::post('settings/thirdparty-api/delete',[Member::class,'delete_thirdparty_api_settings_action'])->middleware(['auth'])->name('delete-thirdparty-api-settings-action');
Route::post('settings/email-autoresponder/delete',[Member::class,'delete_email_auto_settings_action'])->middleware(['auth'])->name('delete-email-auto-settings-action');

Route::get('usage-log',[Member::class,'usage_log'])->middleware(['auth'])->name('usage-log');
Route::post('notification/mark-seen',[Member::class,'notification_mark_seen'])->middleware(['auth'])->name('notification-mark-seen');

Route::get('payment/transaction-log',[Member::class,'transaction_log'])->middleware(['auth'])->name('transaction-log');
Route::post('payment/transaction-log',[Member::class,'transaction_log_data'])->middleware(['auth'])->name('transaction-log-data');
Route::post('payment/api-log',[Member::class,'list_payment_api_log_data'])->middleware(['auth'])->name('list-payment-api-log-data');
Route::get('payment/transaction-log/manual',[Member::class,'manual_transaction_log'])->middleware(['auth'])->name('transaction-log-manual');
Route::post('payment/transaction-log/manual',[Member::class,'manual_transaction_log_data'])->middleware(['auth'])->name('transaction-log-manual-data');
Route::get('payment/select-package',[Member::class,'select_package'])->middleware(['auth'])->name('select-package');
Route::get('payment/buy-package/{id}',[Member::class,'buy_package'])->middleware(['auth'])->name('buy-package');
Route::post('payment/manual/file/upload',[Member::class,'manual_payment_upload_file'])->name('Manual-payment-upload-file');
Route::post('payment/manual/file/delete',[Member::class,'manual_payment_delete_file'])->name('Manual-payment-uploaded-file-delete');
Route::post('payment/manual/submission',[Member::class,'manual_payment'])->middleware(['XssSanitizer'])->name('Manual-payment-submission');
Route::post('payment/manual/actions',[Member::class,'manual_payment_handle_actions'])->name('Manual-payment-handle-action');

Route::get('package/list',[Subscription::class,'list_package'])->middleware(['auth'])->name('list-package');
Route::post('package/list',[Subscription::class,'list_package_data'])->middleware(['auth'])->name('list-package-data');
Route::get('package/create',[Subscription::class,'create_package'])->middleware(['auth'])->name('create-package');
Route::post('package/create',[Subscription::class,'save_package'])->middleware(['auth','XssSanitizer'])->name('create-package-action');
Route::get('package/update/{id}',[Subscription::class,'update_package'])->middleware(['auth'])->name('update-package');
Route::post('package/update',[Subscription::class,'save_package'])->middleware(['auth','XssSanitizer'])->name('update-package-action');
Route::post('package/delete',[Subscription::class,'delete_package'])->middleware(['auth'])->name('delete-package');

Route::get('user/list',[Subscription::class,'list_user'])->middleware(['auth'])->name('list-user');
Route::post('user/list',[Subscription::class,'list_user_data'])->middleware(['auth'])->name('list-user-data');
Route::get('user/create',[Subscription::class,'create_user'])->middleware(['auth'])->name('create-user');
Route::post('user/create',[Subscription::class,'save_user'])->middleware(['auth','XssSanitizer'])->name('create-user-action');
Route::post('user/update-status',[Subscription::class,'update_user_status'])->middleware(['auth'])->name('update-user-status');
Route::get('user/update/{id}',[Subscription::class,'update_user'])->middleware(['auth'])->name('update-user');
Route::post('user/update',[Subscription::class,'save_user'])->middleware(['auth','XssSanitizer'])->name('update-user-action');
Route::post('user/delete',[Subscription::class,'delete_user'])->middleware(['auth'])->name('delete-user');
Route::post('user/send-email',[Subscription::class,'user_send_email'])->middleware(['auth'])->name('user-send-email');

Route::post('common/get-thirdparty-api-profile-dropdown',[Home::class,'get_thirdparty_api_profile_dropdown'])->middleware(['auth'])->name('common-get-thirdparty-api-profile-dropdown');
Route::post('common/get-email-profile-dropdown',[Home::class,'get_email_profile_dropdown'])->middleware(['auth'])->name('common-get-email-profile-dropdown');
Route::post('common/get-sms-profile-dropdown',[Home::class,'get_sms_profile_dropdown'])->middleware(['auth'])->name('common-get-sms-profile-dropdown');


Route::get('restricted', [Home::class,'restricted_access'])->name('restricted-access');
Route::get('credential/check', [Home::class,'credential_check'])->middleware(['auth'])->name('credential-check');
Route::post('credential/check', [Home::class,'credential_check_action'])->middleware(['auth'])->name('credential-check-action');
Route::get('check/update', [UpdateSystem::class,'update_list'])->middleware(['auth'])->name('update-list');
Route::post('initiate/update', [UpdateSystem::class,'initialize_update'])->middleware(['auth'])->name('update-initiate');



Route::get('livechat',[Livechat::class,'load_livechat'])->middleware(['auth'])->name('livechat.load');
Route::get('livechat/custom_prompt/{id}',[Livechat::class,'custom_livechat'])->middleware(['auth'])->name('livechat.loadcustom');
Route::post('livechat_conversation',[Livechat::class,'livechat_conversation'])->middleware(['auth'])->name('livechat_conversation');
Route::post('livechat_side_conversation',[Livechat::class,'livechat_side_conversation'])->middleware(['auth'])->name('livechat_side_conversation');
Route::post('livechat_side_conversation_delete',[Livechat::class,'livechat_side_conversation_delete'])->middleware(['auth'])->name('livechat_side_conversation_delete');
Route::post('user_choice_system_prompt',[Livechat::class,'user_choice_system_prompt'])->middleware(['auth'])->name('user_choice_system_prompt');
Route::post('livechat_sidechat_edit',[Livechat::class,'livechat_sidechat_edit'])->middleware(['auth'])->name('livechat_sidechat_edit');
Route::post('livechat_conversation_download',[Livechat::class,'livechat_conversation_download'])->middleware(['auth'])->name('livechat_conversation_download');

Route::get('ai_chat_settings',[Member::class,'ai_chat_settings'])->middleware(['auth'])->name('ai-chat-settings');
Route::post('ai_chat_settings_action',[Member::class,'ai_chat_settings_action'])->middleware(['auth'])->name('ai-chat-settings-action');
Route::post('ai_chat_settings_data',[Member::class,'ai_chat_settings_data'])->middleware(['auth'])->name('ai-chat-settings-data');
Route::post('delete_ai_chat_settings_action',[Member::class,'delete_ai_chat_settings_action'])->middleware(['auth'])->name('delete-ai-chat-settings-action');
Route::post('edit_ai_chat_settings_action',[Member::class,'edit_ai_chat_settings_action'])->middleware(['auth'])->name('edit-ai-chat-settings-action');
Route::post('ai_chat_profile_dropdown',[Member::class,'ai_chat_profile_dropdown'])->middleware(['auth'])->name('ai-chat-profile-dropdown');


Route::get('/storage/{extra}', function ($extra) {
return redirect("/public/storage/$extra");
})->where('extra', '.*');

require __DIR__.'/api.php';
require __DIR__.'/auth.php';
require __DIR__.'/landing.php';
require __DIR__.'/ai.php';
require __DIR__.'/cron.php';
require __DIR__.'/agency.php';
if(check_build_version() == 'double'){
	require __DIR__.'/webhook.php';
	require __DIR__.'/affiliate.php';
}
require __DIR__.'/docs.php';

