<?php

namespace Webleit\ZohoBooksLaravelServiceProvider\Events;

use Laravel\Cashier\Invoice;
use Laravel\Cashier\Billable;
use Laravel\Spark\LocalInvoice;

/**
 * Class LocalInvoiceCreated
 * @package Webleit\ZohoBooksLaravelServiceProvider\Events
 */
class LocalInvoiceCreated
{
    /**
     * @var Billable
     */
    public $billable;

    /**
     * @var LocalInvoice
     */
    public $localInvoice;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($billable,  LocalInvoice $localInvoice)
    {
        $this->billable = $billable;
        $this->localInvoice = $localInvoice;
    }
}
