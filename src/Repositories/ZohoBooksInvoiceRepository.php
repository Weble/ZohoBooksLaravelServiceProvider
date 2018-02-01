<?php

namespace Webleit\ZohoBooksLaravelServiceProvider\Repositories;

use Webleit\ZohoBooksApi\ZohoBooks;
use Webleit\ZohoBooksLaravelServiceProvider\Events\InvoiceCreated;
use Webleit\ZohoBooksLaravelServiceProvider\Events\LocalInvoiceCreated;
use Laravel\Cashier\Billable;
use Laravel\Spark\LocalInvoice;
use Laravel\Spark\Repositories\StripeLocalInvoiceRepository;

/**
 * Class ZohoBooksInvoiceRepository
 * @package App\Repositories
 */
class ZohoBooksInvoiceRepository extends StripeLocalInvoiceRepository
{
    /**
     * CreateZohoBooksInvoice constructor.
     * @param ZohoBooks $zohoBooks
     */
    public function __construct (ZohoBooks $zohoBooks)
    {
        $this->zohoBooks = $zohoBooks;
    }

    /**
     * @param mixed $billable
     * @param \Laravel\Cashier\Invoice $invoice
     */
    protected function createForBillable ($billable, $invoice)
    {
        event(new InvoiceCreated($billable, $invoice));

        /** @var LocalInvoice $localInvoice */
        $localInvoice = parent::createForBillable($billable, $invoice);

        event(new LocalInvoiceCreated($billable, $invoice, $localInvoice));

        return $localInvoice;
    }

    /**
     *
     * @param Billable $billable
     * @param Invoice $invoice
     * @param LocalInvoice $localInvoice
     */
    public function createOnZohoBooks ($billable, $invoice, $localInvoice)
    {
        $zohoContact = $this->getOrCreateZohoContact($billable);

        $billable->zohobooks_id = $zohoContact->getId();
        $billable->save();

        $zohoInvoice = $this->getOrCreateZohoInvoice($invoice, $zohoContact);

        $localInvoice->zohobooks_id = $zohoInvoice->getId();
        $localInvoice->save();
    }

    /**
     * @param Billable $billable
     * @return \Webleit\ZohoBooksApi\Models\Contact
     */
    protected function getOrCreateZohoContact ($billable)
    {
        $zohoContactsModule = $this->zohoBooks->contacts;

        if ($billable->zohobooks_id) {
            return $zohoContactsModule->get($billable->zohobooks_id);
        }

        $data = $this->getContactDataFromBillable($billable);

        return $zohoContactsModule->create($data);
    }

    /**
     * @param Billable $billable
     */
    public function updateZohoContact ($billable)
    {
        $zohoContactsModule = $this->zohoBooks->contacts;

        if ($billable->zohobooks_id) {
            $data = $this->getContactDataFromBillable($billable);
            $zohoContactsModule->update($billable->zohobooks_id, $data);
        }
    }

    /**
     * @param int $invoiceId
     * @return null|\Webleit\ZohoBooksApi\Models\Model
     */
    public function getZohoInvoice($invoiceId)
    {
        $invoicesModule = $this->zohoBooks->invoices;
        return $invoicesModule->get($invoiceId);
    }

    /**
     * @param Invoice $invoice
     * @param Contact $zohoContact
     * @return \Webleit\ZohoBooksApi\Models\Invoice
     */
    protected function getOrCreateZohoInvoice (Invoice $invoice, Contact $zohoContact)
    {
        $invoicesModule = $this->zohoBooks->invoices;

        if ($invoice->zohobooks_id) {
            return $invoicesModule->get($invoice->zohobook_id);
        }

        $zohoTax = $this->getOrCreateZohoTax($invoice->tax_percent);

        $lineItems = [];

        /** @var InvoiceItem $item */
        foreach ($invoice->invoiceItems() as $item) {
            $amount = $item->amount / 100;

            $lineItems[] = [
                'name' => $item->description,
                'quantity' => $item->quantity,
                'tax_id' => $zohoTax->getId(),
                'rate' => $amount / ($item->quantity ?: 1),
                'item_total' => $amount + $amount * $invoice->tax_percent / 100
            ];
        }

        /** @var InvoiceItem $item */
        foreach ($invoice->subscriptions() as $item) {
            $amount = $item->amount / 100;

            $lineItems[] = [
                'name' => $item->description ?: 'Abbonamento',
                'description' => $item->startDateAsCarbon()->format('d/m/Y') . ' - ' . $item->endDateAsCarbon()->format('d/m/Y'),
                'quantity' => $item->quantity,
                'tax_id' => $zohoTax->getId(),
                'rate' => $amount / ($item->quantity ?: 1),
                'item_total' => $amount + $amount * $invoice->tax_percent / 100
            ];
        }

        $data = [
            'customer_id' => $zohoContact->getId(),
            'reference_number' => $invoice->id,
            'date' => $invoice->date()->format('Y-m-d'),
            'line_items' => $lineItems,
        ];

        $zohoInvoice = $invoicesModule->create($data, [
            'send' => 'false'
        ]);

        if ($zohoInvoice->status != 'paid') {
            $amount = $invoice->rawTotal() / 100;

            $data = [
                'customer_id' => $zohoContact->getId(),
                'payment_mode' => 'creditcard',
                'amount' => $amount,
                'date' => $invoice->date()->format('Y-m-d'),
                'reference_number' => $invoice->asStripeInvoice()->id,
                'invoices' => [
                    [
                        'invoice_id' => $zohoInvoice->getId(),
                        'amount_applied' => $amount,
                    ]
                ]
            ];

            $this->zohoBooks->customerpayments->create($data);
        }

        return $zohoInvoice;
    }

    /**
     * @param $percentage
     * @return mixed|\Webleit\ZohoBooksApi\Models\Model
     */
    protected function getOrCreateZohoTax ($percentage)
    {
        $zohoTaxes = $this->zohoBooks->settings->taxes->getList();

        $tax = null;
        foreach ($zohoTaxes as $zohoTax) {
            if ($zohoTax->percentage - $percentage < 0.01) {
                $tax = $zohoTax;
            }
        }

        if ($tax) {
            return $tax;
        }

        return $this->zohoBooks->settings->taxes->create([
            'tax_name' => 'VAT - ' . $percentage . '%',
            'tax_percentage' => $percentage,
            'tax_type' => 'tax'
        ]);
    }

    /**
     * @param $billable
     * @return array
     */
    protected function getContactDataFromBillable ($billable): array
    {
        $data = [
            'contact_name' => $billable->name,
            'company_name' => $billable->company_name,
            'billing_address' => [
                'address' => $billable->billing_address,
                'street2' => $billable->billing_address_line_2,
                'city' => $billable->billing_city,
                'state' => $billable->billing_state,
                'zip' => $billable->billing_zip,
                'country' => $billable->billing_contry,
            ],
            'custom_fields' => [
                [
                    'index' => 1,
                    'value' => $billable->vat_id
                ],
                [
                    'index' => 2,
                    'value' => $billable->personal_id
                ]
            ]
        ];
        return $data;
    }
}