# Zoho Books Laravel Service Provider for Cashier and Spark

This package provides Zoho Books integration for Laravel Spark and Laravel Cashier

## Features

This package automatically integrates zoho books with Laravel Spark and Laravel Cashier.
Specifically:

- It creates a new `Zoho Contact` for each new paying subscriber you have, linking it to you `User` class
- It creates a new `Zoho Invoice` for each new payment received (just stripe at the moment) and links it to the `Invoice` from Cashier or the `LocalInvoice` from Spark
- It closes the invoice registering the payment linking it to stripe in zoho books
- It triggers new events that Spark doesn't trigger, specifically `Invoice Created` and `LocalInvoiceCreated` when a new Cashier invoice and a new Spark LocalInvoice are created
- It overrides the default Spark notification to send the Zoho Books Invoice instead of the Spark invoice as the attachment
- It overrides the downloading of the pdf serving the zoho books pdf instead of the Spark pdf in the user settings page
- It works for both subscriptions and single charges
- It automatically generates the required tax lines on Zoho Books (for us Europeans)

## Install

Via Composer

``` bash
$ composer require webleit/zohobookslaravelserviceprovider
```

The service provider gets autodiscovered by Laravel new autodiscover feature.
After that you can publish configuration and migrations using

```php artisan vendor:publish```

And then running the migrations:

```php artisan migrate```

## Usage

This is how you would you this service provider in a Laravel Spark installation.
Laravel Cashier uses less features than Laravel Spark (for example, it doesn't deal with storing locally the invoices)
therefore you will need to remove the parts that do not make sense for Laravel Cashier.

#### Route

Register the new route for the Stripe Webhook controller. This needs manually because the spark service provider is
registered by the application, and therefore is always loaded after any package.

In your `RouteSeviceProvider`, under the `map` method, add:

```$router->post('webhook/stripe', 'Webleit\ZohoBooksLaravelServiceProvider\Http\Controllers\StripeWebhookController@handleWebhook');```

And that's it!

If you have overridden the ```LocalInvoice``` class for spark, you can add to it the handy trait

```UseZohoBooksInvoice```

to add some nice methods to that class that helps you dealing with the zoho invoice.

#### Repository

All the interactions that you might need are stored in the  ```ZohoBooksInvoiceRepository``` class.

####

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email daniele@weble.it instead of using the issue tracker.

## Credits

- [Daniele Rosario][https://www.weble.it]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
