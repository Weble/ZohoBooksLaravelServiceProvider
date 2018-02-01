<?php

namespace Webleit\ZohoBooksLaravelServiceProvider\Listeners;

use Webleit\ZohoBooksLaravelServiceProvider\Events\LocalInvoiceCreated;
use Laravel\Spark\LocalInvoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Webleit\ZohoBooksApi\ZohoBooks;
use Webleit\ZohoBooksLaravelServiceProvider\Repositories\ZohoBooksInvoiceRepository;

/**
 * Class CreateZohoBooksInvoice
 * @package App\Listeners
 */
class CreateZohoBooksInvoice implements ShouldQueue
{
    /**
     * @var ZohoBooks
     */
    protected $zohoBooks;

    /**
     * CreateZohoBooksInvoice constructor.
     * @param ZohoBooks $zohoBooks
     */
    public function __construct (ZohoBooksInvoiceRepository $zohoBooks)
    {
        $this->zohoBooks = $zohoBooks;
    }

    /**
     * @param LocalInvoiceCreated $event
     */
    public function handle(LocalInvoiceCreated $event)
    {
        $this->zohoBooks->createOnZohoBooks($event->billable, $event->invoice, $event->localInvoice);
    }
}
