<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'webhook/*',
        'script/*',
        'cron/*',
        '/accept-cookie'
    ];

    function __construct(Application $app, Encrypter $encrypter)
    {
        parent::__construct($app, $encrypter);
        if(env('APP_ENV')=='local') {
            array_push($this->except,'common/*');
        }
    }
}
