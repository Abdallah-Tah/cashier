<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use AMohamed\OfflineCashier\Models\Plan;
use AMohamed\OfflineCashier\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionComponent extends Component
{
    public $plans;
    public $selectedPlan;
    public $paymentMethod = 'cash';
    public $referenceNumber;

    public function mount()
    {
        $this->plans = Plan::all();
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

        $subscription = Auth::user()->subscriptions()->create([
            'plan_id' => $this->selectedPlan->id,
            'status' => 'active',
            'payment_method' => $this->paymentMethod,
        ]);

        $payment = $subscription->payments()->create([
            'amount' => $this->selectedPlan->price,
            'payment_method' => $this->paymentMethod,
            'status' => 'completed',
            'reference_number' => $this->referenceNumber,
            'paid_at' => now(),
        ]);

        session()->flash('message', 'Successfully subscribed to ' . $this->selectedPlan->name);

        $this->reset(['selectedPlan', 'paymentMethod', 'referenceNumber']);
    }

    public function render()
    {
        return view('livewire.subscription.subscription-component');
    }
}
