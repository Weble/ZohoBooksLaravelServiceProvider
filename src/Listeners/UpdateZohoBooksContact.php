<?php

namespace Webleit\ZohoBooksLaravelServiceProvider\Listeners;

use Webleit\ZohoBooksLaravelServiceProvider\Contracts\ZohoBooksRepositoryContract;

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
     * @param ZohoBooksRepositoryContract $zohoBooks
     */
    public function __construct (ZohoBooksRepositoryContract $zohoBooks)
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
