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
     * @var Invoice
     */
    public $invoice;

    /**
     * @var LocalInvoice
     */
    public $localInvoice;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Billable $billable, Invoice $invoice, LocalInvoice $localInvoice)
    {
        $this->billable = $billable;
        $this->invoice = $invoice;
        $this->localInvoice = $localInvoice;
    }
}
