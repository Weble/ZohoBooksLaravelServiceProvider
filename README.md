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

### 1. Install via Composer

``` bash
$ composer require webleit/zohobookslaravelserviceprovider
```

The service provider gets autodiscovered by Laravel new autodiscover feature.

### 2. Publish vendor config, routes and migrations

You can publish configuration and migrations using

```php artisan vendor:publish```

### 3. Run the migrations

Run the migrations to add the `zohobooks_id` fields to the users and invoices tables.

```php artisan migrate```

### 4. Override the Stripe webhook route

Register the new route for the Stripe Webhook controller. 
This needs manually because the spark service provider is registered by the application, and therefore is always loaded after any package.

In your `RouteSeviceProvider`, inside the `map` method, add:            

```$router->post('webhook/stripe', 'Webleit\ZohoBooksLaravelServiceProvider\Http\Controllers\StripeWebhookController@handleWebhook');```

so that your final code looks like this:

```
public function map(Router $router)
{
    $this->mapWebRoutes($router);
    $this->mapApiRoutes($router);
    
    $router->post('webhook/stripe', 'Webleit\ZohoBooksLaravelServiceProvider\Http\Controllers\StripeWebhookController@handleWebhook');
}
```

### 5. (Optional) Download the Zoho Books Invoice instead of the Spark one

If you want the user to download the zoho invoice instead of the spark invoice from his settings page, 
add the `HasZohoBooksInvoices` to the `App\User` class.

### 6. (Optional) Add Zoho Books methods to the LocalInvoice class

If you have overridden the ```LocalInvoice``` class for spark, you can add to it the handy trait ```UseZohoBooksInvoice```
which adds a lots of useful methods to it:

- `asInvoice()` to get the Cashier Invoice
- `asZohoBooksInvoice()` to get the ZohoBooks Invoice
- `pdf($storagePath = null)` to get the PDF content of the invoice
- `downloadPdf($storagePath = null)` to download the PDF content of the invoice

## Configuration

When you publish the config file, you can add 3 parameters to it:

- `authtoken` (set through the env variable `ZOHOBOOKS_AUTHTOKEN`), which is the zoho auth token to be used, which you can get [here](https://accounts.zoho.com/apiauthtoken/create?SCOPE=ZohoBooks%2Fbooksapi)
- `organization_id` (set through the env variable `ZOHOBOOKS_ORGANIZATION_ID`), which is the organization ID you want to work on.If the account you provided has just one organization, this can be null. 
- `invoice_storage_path` When you need the zoho books invoice PDF, it gets stored locally for cache. This is the path in the storage dir where it gets stored as a pdf file. Defaults to `invoices` 

## The Repository

All the interactions that you might need are stored in the  ```ZohoBooksInvoiceRepository``` class:

- `createOnZohoBooks (App\User $billable, Laravel\Cashier\Invoice $invoice, Laravel\Spark\LocalInvoice $localInvoice)` creates both contact and invoice on ZohoBooks, checking if this wasn't already done.
- `updateZohoContact (App\User $billable)` updates an existing contact on zohobooks
- `getZohoInvoice($invoiceId)` get the zoho books invoice using the zoho's invoice id
- `storeAndGetZohoInvoicePdf(Laravel\Spark\LocalInvoice $localInvoice, $storagePath = null)` get the pdf content of the zoho books invoice, storing it locally for cache
- `downloadZohoInvoicePdf(LocalInvoice $localInvoice, $storagePath = null)` download the pdf of the zoho invoice, storing it locally for cache

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
