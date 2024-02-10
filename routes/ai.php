<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home;
use App\Http\Controllers\Template;

$auth_or_guest =  env('APP_ENV')=='local' ? 'guest' : 'auth';

Route::get('template/manager',[Template::class,'template_manager'])->middleware(['auth'])->name('template-manager');
Route::post('template/manager/template/list',[Template::class,'list_template_data'])->middleware(['auth'])->name('list-template-data');
Route::post('template/manager/save',[Template::class,'save_template'])->middleware(['auth','XssSanitizer'])->name('save-template');
Route::post('template/manager/template/delete',[Template::class,'delete_template'])->middleware(['auth'])->name('delete-template');
Route::post('template/manager/template/edit',[Template::class,'edit_template'])->middleware(['auth','XssSanitizer'])->name('edit-template');
Route::post('template/manager/template/update-status',[Template::class,'update_template_status'])->middleware(['auth'])->name('update-template-status');

Route::post('template/manager/template/group/list',[Template::class,'list_template_group_data'])->middleware(['auth'])->name('list-template-group-data');
Route::post('template/manager/template/group/save',[Template::class,'save_template_group'])->middleware(['auth','XssSanitizer'])->name('save-template-group');
Route::post('template/manager/template/group/delete',[Template::class,'delete_template_group'])->middleware(['auth'])->name('delete-template-group');
Route::post('template/manager/template/group/update-status',[Template::class,'update_template_group_status'])->middleware(['auth'])->name('update-template-group-status');

Route::post('tools/action',[Template::class,'tools_action'])->middleware(['auth'])->name('tools-action');
Route::post('tools/input/upload',[Template::class,'upload_input_media'])->middleware(['auth'])->name('tools-upload-input-media');
Route::post('tools/input/download/text',[Template::class,'download_text'])->middleware(['auth'])->name('tools-download-text');
Route::post('tools/input/download/file',[Template::class,'download_file'])->middleware(['auth'])->name('tools-download-file');
Route::get('tools/search/history',[Template::class,'search_history'])->middleware(['auth'])->name('tools-search-history');
Route::post('tools/search/history',[Template::class,'search_history_data'])->middleware(['auth'])->name('tools-search-history-data');
Route::post('tools/search/delete/{api_group}',[Template::class,'delete_search'])->middleware(['auth'])->name('tools-delete-search');
//This route needed to be bottom
Route::get('tools/{group_slug}/{template_slug}/{search_id?}',[Template::class,'tools'])->middleware(['auth'])->name('tools');
Route::any('generate/lang',[Template::class,'generate_dynamic_lang'])->middleware(['auth'])->name('generate-lang');
