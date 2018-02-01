<?php

namespace Webleit\ZohoBooksLaravelServiceProvider\Notifications;

use Laravel\Spark\LocalInvoice;
use Laravel\Spark\Billable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Laravel\Cashier\Invoice;

/**
 * Class InvoicePaid
 * @package App\Notifications
 */
class InvoicePaid extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Billable
     */
    protected $billable;

    /**
     * @var Invoice
     */
    protected $invoice;

    /**
     * @var LocalInvoice
     */
    protected $localInvoice;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct (Billable $billable, Invoice $invoice, LocalInvoice $localInvoice)
    {
        $this->localInvoice = $localInvoice;
        $this->invoice = $invoice;
        $this->billable = $billable;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via ($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail ($notifiable)
    {
        $invoiceData = \Spark::invoiceDataFor($this->billable);

        $mailMessage = (new MailMessage)->subject($invoiceData['product'] . ' Invoice')
            ->greeting('Hi ' . explode(' ', $this->billable->name)[0] . '!')
            ->line('Thanks for your continued support. We\'ve attached a copy of your invoice for your records. Please let us know if you have any questions or concerns!')
            ->attachData($this->localInvoice->pdf(), 'invoice.pdf');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray ($notifiable)
    {
        return [
            //
        ];
    }
}
