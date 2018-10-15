<?php

namespace Tohidplus\Zarrinpal;

use Illuminate\Support\ServiceProvider;
use SoapClient;

class ZarrinpalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/zarrinpal.php', 'zarrinpal'
        );
        $this->publishes([
            __DIR__.'/config/zarrinpal.php' => config_path('zarrinpal.php'),
        ]);
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('zarrinpal',function (){
            $client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
            return new Zarrinpal($client);
        });
    }
}
