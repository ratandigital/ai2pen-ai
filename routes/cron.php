<?php
use App\Http\Controllers\Cron;

Route::get('cron/clean-junk-data/'.ENV('CRON_TOKEN'),[Cron::class,'clean_junk_data'])->name('cron-clean-junk-data');
Route::any('cron/paypal/transaction/'.ENV('CRON_TOKEN'),[Cron::class,'get_paypal_subscriber_transaction'])->name('get-paypal-subscriber-transaction');
