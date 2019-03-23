<?php
/**
 * Created by PhpStorm.
 * User: icharle
 * Date: 2019/3/10
 * Time: 下午2:17
 */

namespace Icharle\Alipaytool;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;

class AlipaytoolServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/alipaytool.php' => config_path('alipaytool.php'),
            __DIR__ . '/../storage/private.txt' => storage_path('pem/private.txt'),
            __DIR__ . '/../storage/public.txt' => storage_path('pem/public.txt')
        ], 'alipaytool');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('alipaytool', function () {
            return new Alipaytool();
        });
        $this->app->alias('alipaytool', Wxtool::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['alipaytool'];
    }
}