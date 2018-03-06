<?php

namespace Webleit\ZohoBooksLaravelServiceProvider\Listeners;

use Webleit\ZohoBooksLaravelServiceProvider\Contracts\ZohoBooksRepositoryContract;
use Webleit\ZohoBooksLaravelServiceProvider\Events\LocalInvoiceCreated;
use Laravel\Spark\LocalInvoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Webleit\ZohoBooksApi\ZohoBooks;

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
     * @param ZohoBooksRepositoryContract $zohoBooks
     */
    public function __construct (ZohoBooksRepositoryContract $zohoBooks)
    {
        $this->zohoBooks = $zohoBooks;
    }

    /**
     * @param LocalInvoiceCreated $event
     */
    public function handle(LocalInvoiceCreated $event)
    {
        $this->zohoBooks->createOnZohoBooks($event->billable, $event->localInvoice);
    }
}
