<?php

namespace Webleit\ZohoBooksLaravelServiceProvider\Events;

use Laravel\Cashier\Invoice;
use Laravel\Cashier\Billable;

/**
 * Class InvoiceCreated
 * @package App\Events
 */
class InvoiceCreated
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
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($billable, Invoice $invoice)
    {
        $this->billable = $billable;
        $this->invoice = $invoice;
    }
}
