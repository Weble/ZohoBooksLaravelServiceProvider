<?php

namespace Webleit\ZohoBooksLaravelServiceProvider;
use Illuminate\Http\Response;
use Webleit\ZohoBooksLaravelServiceProvider\Repositories\ZohoBooksInvoiceRepository;
use Laravel\Cashier\Invoice;

/**
 * Trait UseZohoBooksInvoice
 * @package Webleit\ZohoBooksLaravelServiceProvider
 */
trait UseZohoBooksInvoice
{
    /**
     * @var Invoice
     */
    protected $invoice;

    /**
     * @return Invoice|null
     */
    public function asInvoice()
    {
        if (!$this->invoice) {
            $this->invoice = $this->user->findInvoice($this->provider_id);
        }

        return $this->invoice;
    }

    /**
     * @return null|\Webleit\ZohoBooksApi\Models\Invoice
     */
    public function asZohoBooksInvoice ()
    {
        if (!$this->zohobooks_id) {
            return null;
        }

        return app(ZohoBooksInvoiceRepository::class)->getZohoInvoice($this->zohobooks_id);
    }

    /**
     * @return string
     */
    public function pdf($storagePath = null)
    {
        return app(ZohoBooksInvoiceRepository::class)->storeAndGetZohoInvoicePdf($this, $storagePath);
    }

    /**
     * Create an invoice download response.
     *
     * @return Response
     */
    public function downloadPdf($storagePath = null)
    {
        return app(ZohoBooksInvoiceRepository::class)->downloadZohoInvoicePdf($this, $storagePath);
    }
}