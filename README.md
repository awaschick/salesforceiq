# RelateIQ Client - Beta

[![Latest Stable Version](https://poser.pugx.org/torann/relateiq/v/stable.png)](https://packagist.org/packages/torann/relateiq) [![Total Downloads](https://poser.pugx.org/torann/relateiq/downloads.png)](https://packagist.org/packages/torann/relateiq)

----------

## Installation

- [RelateIQ Client on Packagist](https://packagist.org/packages/torann/relateiq)
- [RelateIQ Client on GitHub](https://github.com/torann/relateiq)

To get the latest version of RelateIQ Client simply require it in your `composer.json` file.

~~~
"torann/relateiq": "dev-master"
~~~

You'll then need to run `composer install` to download it and have the autoloader updated.

Once RelateIQ Client is installed you need to register the service provider with the application. Open up `app/config/app.php` and find the `providers` key.


```php
'Torann\RelateIQ\ServiceProvider'
```

> There is no need to add the Facade, the package will add it for you.


### Publish the config

Run this on the command line from the root of your project:

	$ php artisan config:publish torann/relateiq

This will publish RelateIQ Client' config to ``app/config/packages/torann/relateiq/``.
