<?php
/* ======= loaded in public/index.php ====== */


if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){ // only overwrite on windows platform

    /*
        overwrittong e() of vendor/laravel/framework/src/Illuminate/Foundation/helper.php
        ==============================================================================
        this function is resposible for rendering trans() or __() output
        we are not using namespaced translation like __('filename.translation') that loads from resources/lang/en/filename.php
        we are using non-namespaced translation like __('translation') that loads form resources/lang/en.json file directly
        if any translation like __('filename') matches any file name inside resources/lang/en folder
        system renders the whole resources/lang/en/filename.php array
    */
    function __($key = null, $replace = [], $locale = null)
    {
        if (is_null($key)) {
            return $key;
        }
        $langFiles = array ('auth','dash-board','docs','guest','landing','member','misc','pagination','passwords','subscription','system','validation','dynamic');
        if(in_array(strtolower($key), $langFiles)) {
            $namespaced_str = strtolower($key).'.'.ucfirst($key);
            if(__($namespaced_str) != $namespaced_str) return $key;
            else return __($namespaced_str);
        }
        return trans($key, $replace, $locale);
    }
}

