<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use AMohamed\OfflineCashier\Services\InvoiceService;
use AMohamed\OfflineCashier\Models\Payment;
use AMohamed\OfflineCashier\Models\Invoice;

class InvoiceComponent extends Component
{
    public $subscriptions;
    protected $invoiceService;

    public function __construct()
    {
        $this->invoiceService = new InvoiceService();
    }

    public function mount()
    {
        $this->subscriptions = Auth::user()->subscriptions;
    }

    public function downloadInvoice($subscriptionId)
    {
        $subscription = $this->subscriptions->find($subscriptionId);
        if (!$subscription) {
            session()->flash('error', 'Subscription not found.');
            return;
        }

        $payment = Payment::where('subscription_id', $subscription->id)->first();
        if (!$payment) {
            session()->flash('error', 'Payment not found for this subscription.');
            return;
        }

        // Check if invoice already exists
        $invoice = Invoice::where('payment_id', $payment->id)->first();
        if (!$invoice) {
            // Only generate new invoice if one doesn't exist
            $invoice = $this->invoiceService->generate($payment);
        }

        // Generate PDF content
        $pdfContent = $this->invoiceService->generatePdf($invoice);
        $fileName = 'invoice_' . $invoice->number . '.pdf';

        return response()->streamDownload(function() use ($pdfContent) {
            echo $pdfContent;
        }, $fileName);
    }

    public function render()
    {
        $invoices = Invoice::whereIn('payment_id', Payment::whereIn('subscription_id', $this->subscriptions->pluck('id'))->pluck('id'))->get();
        // dd($invoices, Payment::whereIn('subscription_id', $this->subscriptions->pluck('id'))->pluck('id'));
        return view('livewire.subscription.invoice-component', [
            'subscriptions' => $this->subscriptions,
            'invoices' => $invoices
        ]);
    }
}
