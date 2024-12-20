<div class="py-12 bg-gray-100">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Choose Your Insurance Plan</h1>
            <p class="text-lg text-gray-600">Protect your vehicle with our comprehensive insurance plans</p>
        </div>

        <!-- Insurance Plans Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @foreach($plans as $plan)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-200 hover:scale-105">
                    <div class="p-8">
                        <div class="text-center">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                            <p class="text-gray-600 mb-6">{{ $plan->description }}</p>
                            <div class="mb-6">
                                <span class="text-4xl font-bold text-blue-600">${{ number_format($plan->price, 2) }}</span>
                                <span class="text-gray-500">/{{ $plan->billing_interval }}</span>
                            </div>
                        </div>

                        <ul class="space-y-4 mb-8">
                            @foreach($plan->features as $feature)
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>

                        <button wire:click="selectPlan({{ $plan->id }})"
                                class="w-full py-4 px-6 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition duration-200 transform hover:-translate-y-1">
                            Get Started
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Application Form -->
        @if($selectedPlan)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-8">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Complete Your Insurance Application</h2>
                    <p class="text-gray-600 mb-8">You've selected the {{ $selectedPlan->name }} plan at ${{ number_format($selectedPlan->price, 2) }}/{{ $selectedPlan->billing_interval }}</p>

                    <form wire:submit.prevent="subscribe" class="space-y-6">
                        <!-- Vehicle Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Vehicle Type</label>
                                <select wire:model="vehicleType" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <option value="">Select Vehicle Type</option>
                                    <option value="sedan">Sedan</option>
                                    <option value="suv">SUV</option>
                                    <option value="truck">Truck</option>
                                    <option value="van">Van</option>
                                    <option value="sports">Sports Car</option>
                                </select>
                                @error('vehicleType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Vehicle Model</label>
                                <input type="text" wire:model="vehicleModel" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="e.g., Toyota Camry">
                                @error('vehicleModel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Vehicle Year</label>
                                <input type="number" wire:model="vehicleYear" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="e.g., 2020">
                                @error('vehicleYear') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Driver's License Number</label>
                                <input type="text" wire:model="driverLicense" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Enter your driver's license number">
                                @error('driverLicense') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Accident History (Last 3 Years)</label>
                            <textarea wire:model="accidentHistory" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Please describe any accidents or claims in the last 3 years"></textarea>
                            @error('accidentHistory') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Payment Information -->
                        <div class="border-t pt-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Payment Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Payment Method</label>
                                    <select wire:model="paymentMethod" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                        <option value="">Select Payment Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="check">Check</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="stripe">Credit Card (Stripe)</option>
                                    </select>
                                    @error('paymentMethod') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @if(in_array($paymentMethod, ['cash', 'check', 'bank_transfer']))
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-2">Reference Number</label>
                                        <input type="text" wire:model="referenceNumber" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Enter payment reference number">
                                        @error('referenceNumber') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" wire:click="$set('selectedPlan', null)"
                                    class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                                Back to Plans
                            </button>
                            <button type="submit"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
