<?php

use App\Http\Controllers\Affiliate;
use App\Http\Controllers\Subscription;

// Settings
Route::get('setting/affiliate',[Subscription::class,'affiliate_settings'])->middleware(['auth'])->name('affiliate-settings');
Route::post('affiliate/user/list',[Subscription::class,'affiliate_list_user_data'])->middleware(['auth'])->name('affiliate-list-user-data');
Route::post('affiliate/commission',[Subscription::class,'affiliate_commission_settings_set'])->middleware(['auth','XssSanitizer'])->name('affiliate-commission-settings-set');
Route::post('affiliate/user/create',[Subscription::class,'affiliate_user_form_submission'])->middleware(['auth','XssSanitizer'])->name('affiliate-user-form-submission');
Route::post('affiliate/user/update-info',[Subscription::class,'affiliate_user_get_info'])->middleware(['auth'])->name('affiliate-user-get-info');
Route::post("user/affiliate/request/delete",[Subscription::class,'delete_user_affiliate_request'])->middleware(['auth'])->name('delete-user-affiliate-request');
Route::post("affiliate/send/whatsapp/otp",[Subscription::class,'affiliate_send_whatsapp_otp'])->middleware(['auth'])->name('affiliate-send-whatsapp-otp');
Route::post("affiliate/withdrawal-requests",[Subscription::class,'affiliate_withdrawal_requests_admin'])->middleware(['auth'])->name('affiliate-withdrawal-request-list-admin');
Route::post("affiliate/withdrawal-requests-delete",[Subscription::class,'affiliate_withdrawal_requests_delete_admin'])->middleware(['auth'])->name('affiliate-withdrawal-request-delete-admin');
Route::any("affiliate/withdrawal-request/change",[Subscription::class,'affiliate_withdrawal_request_status_change'])->middleware(['auth'])->name('affiliate-withdrawal-request-status-change');
Route::post("affiliate/user-request/list",[Subscription::class,'affiliate_user_request_list'])->middleware(['auth'])->name('affiliate-user-request-list');
Route::post("affiliate/user-request/status/change",[Subscription::class,'affiliate_request_status_change'])->middleware(['auth'])->name('affiliate-request-status-change');
Route::post('affiliate/send-email',[Subscription::class,'affiliate_send_email'])->middleware(['auth'])->name('affiliate-send-email');

Route::get("affiliate/request",[Affiliate::class,'affiliate_request'])->middleware(['auth'])->name('affiliate-program');
Route::any("affiliate/request/action",[Affiliate::class,'affiliate_request_action'])->middleware(['auth'])->name('affiliate-request-action');
Route::any("dashboard/affiliate",[Affiliate::class,'index'])->middleware(['auth'])->name('affiliate-dashboard');
Route::get("affiliate/account",[Affiliate::class,'index'])->middleware(['auth'])->name('affiliate-account');
Route::get("affiliate/settings",[Affiliate::class,'settings'])->middleware(['auth'])->name('affiliate-user-self-settings');


/* Withdrawal Methods */
Route::get("withdrawals/methods",[Affiliate::class,'withdrawal_methods'])->middleware(['auth'])->name('affiliate-withdrawal-methods');
Route::post("withdrawals/methods",[Affiliate::class,'withdrawal_methods_data'])->middleware(['auth'])->name('affiliate-withdrawal-methods-data');
Route::post("withdrawals/methods/create",[Affiliate::class,'new_method'])->middleware(['auth','XssSanitizer'])->name('affiliate-create-withdrawal-method');
Route::post("withdrawals/methods/info",[Affiliate::class,'get_method_info'])->middleware(['auth'])->name('affiliate-get-withdrawal-method-info');
Route::post("withdrawals/methods/update",[Affiliate::class,'update_method_info'])->middleware(['auth','XssSanitizer'])->name('affiliate-update-withdrawal-method-info');
Route::post("withdrawals/methods/delete",[Affiliate::class,'delete_withdrawal_method'])->middleware(['auth'])->name('affiliate-withdrawal-method-delete');


/* Withdrawal Requests */
Route::get("withdrawals/requests",[Affiliate::class,'withdrawal_requests'])->middleware(['auth'])->name('affiliate-withdrawal-requests');
Route::post("withdrawals/requests/search",[Affiliate::class,'withdrawal_requests'])->middleware(['auth'])->name('affiliate-withdrawal-requests-search');
Route::post("withdrawals/requests/delete",[Affiliate::class,'delete_withdrawal_request'])->middleware(['auth'])->name('delete-withdrawal-request');
Route::post("affiliate_system/get_requests_info",[Affiliate::class,'get_requests_info'])->middleware(['auth'])->name('affiliate_system-get-requests-info');
Route::post("affiliate/system/issue/new_request",[Affiliate::class,'issue_new_request'])->middleware(['auth'])->name('affiliate-system-issue-new-request');
