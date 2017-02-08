<?php namespace SalesforceIQ;

use Illuminate\Foundation\AliasLoader;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Add 'Assets' facade alias
        AliasLoader::getInstance()->alias('SalesforceIQ', 'Bubba\Support\SalesforceIQ\Facade');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Bind 'hashids' shared component to the IoC container
        $this->app->singleton('salesforceiq', function($app)
        {
            $config = $app['config']['services.salesforceiq'];

            return new Client($config['key'], $config['secret']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
