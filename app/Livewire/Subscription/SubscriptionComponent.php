<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use AMohamed\OfflineCashier\Models\Plan;
use AMohamed\OfflineCashier\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use AMohamed\OfflineCashier\Services\InvoiceService;
use Illuminate\Support\Facades\Log;
use AMohamed\OfflineCashier\Services\SubscriptionService;

class SubscriptionComponent extends Component
{
    public $plans;
    public $selectedPlan;
    public $paymentMethod = 'cash';
    public $referenceNumber;
    protected $invoiceService;
    protected $subscriptionService;

    public function __construct()
    {
        $this->invoiceService = new InvoiceService();
        $this->subscriptionService = new SubscriptionService();
    }

    public function mount()
    {
        $this->plans = Plan::with('features')->get();
    }

    public function selectPlan($planId)
    {
        $this->selectedPlan = Plan::find($planId);
    }

    public function subscribe()
    {
        $this->validate([
            'selectedPlan' => 'required',
            'paymentMethod' => 'required|in:cash,check,bank_transfer,stripe',
            'referenceNumber' => 'required_if:paymentMethod,cash,check,bank_transfer'
        ]);

        $subscription = $this->subscriptionService->create(Auth::user(), $this->selectedPlan, $this->paymentMethod);

        $payment = $subscription->payments()->create([
            'amount' => $this->selectedPlan->price,
            'payment_method' => $this->paymentMethod,
            'status' => 'completed',
            'reference_number' => $this->referenceNumber,
            'paid_at' => now(),
        ]);

        $this->invoiceService->generate($payment);

        session()->flash('message', 'Successfully subscribed to ' . $this->selectedPlan->name);

        $this->reset(['selectedPlan', 'paymentMethod', 'referenceNumber']);
    }

    public function render()
    {
        return view('livewire.subscription.subscription-component');
    }
}
