<?php

namespace Webleit\ZohoBooksLaravelServiceProvider\Http\Controllers;

use Laravel\Spark\LocalInvoice;
use Webleit\ZohoBooksLaravelServiceProvider\Notifications\InvoicePaid;
use Webleit\ZohoBooksLaravelServiceProvider\Repositories\ZohoBooksInvoiceRepository;
use Illuminate\Http\Response;
use \Laravel\Spark\Http\Controllers\Settings\Billing\StripeWebhookController as SparkStripeWebhookController;

/**
 * Class StripeWebhookController
 * @package App\Http\Controllers
 */
class StripeWebhookController extends SparkStripeWebhookController
{
    /**
     * Handle a successful invoice payment from a Stripe subscription.
     *
     * By default, this e-mails a copy of the invoice to the customer.
     *
     * @param  array  $payload
     * @return Response
     */
    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        $user = $this->getUserByStripeId(
            $payload['data']['object']['customer']
        );

        if (is_null($user)) {
            return $this->teamInvoicePaymentSucceeded($payload);
        }

        $invoice = $user->findInvoice($payload['data']['object']['id']);

        $localInvoice = app(ZohoBooksInvoiceRepository::class)->createForUser($user, $invoice);

        $this->sendInvoiceNotification(
            $user, $invoice, $localInvoice
        );

        return new Response('Webhook Handled', 200);
    }

    /**
     * Send an invoice notification e-mail.
     *
     * @param  mixed $billable
     * @param  \Laravel\Cashier\Invoice
     * @param  LocalInvoice
     * @return void
     */
    protected function sendInvoiceNotification($billable, $invoice, $localInvoice = null)
    {
        $billable->notify(new InvoicePaid($billable, $invoice, $localInvoice));
    }
}