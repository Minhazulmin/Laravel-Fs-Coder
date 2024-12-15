<?php

namespace Minhazulmin\Fscoder;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class FscoderServiceProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register(): void {
        $this->loadRoutesFrom( __DIR__ . '/routes/web.php' );
        $this->loadViewsFrom( __DIR__ . '/views', 'fsCoderView' );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {
        Session::put( '_token', bin2hex( random_bytes( 32 ) ) );
    }
}