<?php

namespace Webleit\ZohoBooksLaravelServiceProvider\Contracts;

use Illuminate\Http\Response;
use Laravel\Cashier\Invoice;
use Webleit\ZohoBooksApi\Models\Contact;
use Laravel\Cashier\Billable;
use Laravel\Spark\LocalInvoice;
use Laravel\Spark\Repositories\StripeLocalInvoiceRepository;

interface ZohoBooksRepositoryContract
{
    public function createOnZohoBooks ($billable, $localInvoice);

    /**
     * @param Billable $billable
     * @return \Webleit\ZohoBooksApi\Models\Contact
     */
    public function getOrCreateZohoContact ($billable);

    /**
     * @param Billable $billable
     */
    public function updateZohoContact ($billable);

    /**
     * @param int $invoiceId
     * @return null|\Webleit\ZohoBooksApi\Models\Model
     */
    public function getZohoInvoice($invoiceId);

    /**
     * @param $localInvoice
     * @return mixed
     */
    public function storeAndGetZohoInvoicePdf(LocalInvoice $localInvoice, $storagePath = null);

    /**
     * @param LocalInvoice $localInvoice
     * @param null $storagePath
     * @return Response
     */
    public function downloadZohoInvoicePdf(LocalInvoice $localInvoice, $storagePath = null);

    /**
     * @param Invoice $invoice
     * @param Contact $zohoContact
     * @return \Webleit\ZohoBooksApi\Models\Invoice
     */
    public function getOrCreateZohoInvoice (LocalInvoice $localInvoice, Invoice $invoice, Contact $zohoContact);
}