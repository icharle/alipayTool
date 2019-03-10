<?php
/**
 * Created by PhpStorm.
 * User: icharle
 * Date: 2019/3/10
 * Time: 下午2:17
 */

namespace Icharle\Alipaytool;

use Illuminate\Support\ServiceProvider;

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
            __DIR__ . '/../config/alipaytool.php' => config_path('alipaytool.php')
        ], 'alipaytool');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('alipaytool', function ($app) {

            $config = $app->make('config');

            $appPrivateKey = $config->get('alipaytool.APP_PRIVATE_KEY');
            $alipayPulicKey = $config->get('alipaytool.ALIPAY_PUBLIC_KEY');
            $alipayAppId = $config->get('alipaytool.ALIPAY_APP_ID');


            return new Alipaytool($alipayAppId, $appPrivateKey, $alipayPulicKey);
        });
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