<?php

namespace Webleit\ZohoBooksLaravelServiceProvider;

use Illuminate\Http\Response;
use Webleit\ZohoBooksLaravelServiceProvider\Repositories\ZohoBooksInvoiceRepository;

/**
 * Trait HasZohoBooksInvoices
 * @package Webleit\ZohoBooksLaravelServiceProvider
 */
trait HasZohoBooksInvoices
{
    /**
     * @param string $id
     * @param array $data
     * @param null $storagePath
     * @return Response
     */
    public function downloadInvoice ($id, array $data, $storagePath = null)
    {
        $localInvoice = $this->localInvoices()
            ->where('provider_id', $id)
            ->firstOrFail();

        return app(ZohoBooksInvoiceRepository::class)->downloadZohoInvoicePdf($localInvoice, $storagePath);
    }
}