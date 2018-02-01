<?php

namespace Webleit\ZohoBooksLaravelServiceProvider\Listeners;

use Webleit\ZohoBooksLaravelServiceProvider\Repositories\ZohoBooksInvoiceRepository;

/**
 * Class UpdateZohoBooksContact
 * @package App\Listeners
 */
class UpdateZohoBooksContact
{
    /**
     * @var ZohoBooks
     */
    protected $zohoBooks;

    /**
     * CreateZohoBooksInvoice constructor.
     * @param ZohoBooksInvoiceRepository $zohoBooks
     */
    public function __construct (ZohoBooksInvoiceRepository $zohoBooks)
    {
        $this->zohoBooks = $zohoBooks;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->zohoBooks->updateZohoContact($event->billable);
    }
}
