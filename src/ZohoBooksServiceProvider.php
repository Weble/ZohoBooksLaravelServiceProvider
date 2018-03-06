<?php

namespace Webleit\ZohoBooksLaravelServiceProvider;

use Webleit\ZohoBooksApi\ZohoBooks;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Webleit\ZohoBooksLaravelServiceProvider\Events\LocalInvoiceCreated;
use Webleit\ZohoBooksLaravelServiceProvider\Listeners\UpdateZohoBooksContact;

/**
 * Class ZohoBooksServiceProvider
 * @package App\Providers
 */
class ZohoBooksServiceProvider extends ServiceProvider
{
    protected $listen = [
        'Laravel\Spark\Events\PaymentMethod\VatIdUpdated' => [
            'Laravel\Spark\Listeners\Subscription\UpdateTaxPercentageOnStripe',
            UpdateZohoBooksContact::class
        ],

        'Laravel\Spark\Events\PaymentMethod\BillingAddressUpdated' => [
            'Laravel\Spark\Listeners\Subscription\UpdateTaxPercentageOnStripe',
            UpdateZohoBooksContact::class
        ],

        // Custom Events
        LocalInvoiceCreated::class => [
           \Webleit\ZohoBooksLaravelServiceProvider\Listeners\CreateZohoBooksInvoice::class,
        ],
    ];

    public function boot()
    {
        $this->publishMigrations();
        $this->publishConfig();
        $this->registerRoutes();

        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }

    public function register ()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/zohobooks.php', 'zohobooks');

        $this->app->singleton(ZohoBooks::class, function ($app) {
            return new ZohoBooks(
                config('zohobooks.authtoken'),
                config('zohobooks.organization_id')
            );
        });
    }

    protected function publishMigrations()
    {
        if (! class_exists('AddZohoIdToUsersAndInvoices')) {
            $this->publishes([
                __DIR__.'/../database/add_zoho_id_to_users_and_invoices.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_add_zoho_id_to_users_and_invoices.php'),
            ], 'migrations');
        }
    }

    protected function publishConfig (): void
    {
        // Publish a config file
        $this->publishes([
            __DIR__ . '/../config/zohobooks.php' => config_path('zohobooks.php'),
        ], 'config');
    }

    /**
     * Register predefined routes used for Spark.
     */
    protected function registerRoutes()
    {
        if (class_exists('Laravel\Spark\Providers\AppServiceProvider')) {
            include __DIR__.'/../routes/web.php';
        }
    }
}
