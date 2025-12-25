@extends('layouts.student')

@section('title', 'Payment Method')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Payment Method</h1>
                <p class="mt-2 text-gray-600">Manage your default payment method for subscriptions</p>
            </div>

            <!-- Current Payment Method -->
            @if(auth()->user()->hasDefaultPaymentMethod())
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Current Payment Method</h2>
                        @if(!auth()->user()->subscribed('default'))
                            <form action="{{ route('student.subscriptions.delete-payment-method') }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this payment method? You won\'t be able to subscribe until you add a new one.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Remove Card
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center">
                            <!-- Card Icon -->
                            @if(strtolower(auth()->user()->pm_type) == 'visa')
                                <div class="w-14 h-10 mr-4 bg-blue-600 rounded flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">VISA</span>
                                </div>
                            @elseif(strtolower(auth()->user()->pm_type) == 'mastercard')
                                <div class="w-14 h-10 mr-4 bg-red-600 rounded flex items-center justify-center">
                                    <div class="flex">
                                        <div class="w-4 h-4 rounded-full bg-red-400 -mr-2"></div>
                                        <div class="w-4 h-4 rounded-full bg-yellow-400"></div>
                                    </div>
                                </div>
                            @else
                                <svg class="w-12 h-12 text-gray-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            @endif

                            <div>
                                <p class="text-lg font-medium text-gray-900">
                                    {{ ucfirst(auth()->user()->pm_type) }} •••• {{ auth()->user()->pm_last_four }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    @if(auth()->user()->subscribed('default'))
                                        <span class="inline-flex items-center text-green-600">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Active subscription
                                        </span>
                                    @else
                                        Default payment method
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if(auth()->user()->subscribed('default'))
                            <span class="text-xs text-gray-500 italic px-2 py-1 bg-gray-100 rounded">
                                Required for subscription
                            </span>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">No payment method on file</p>
                            <p class="text-sm text-yellow-700 mt-1">Add a payment method below to subscribe to plans.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Update/Add Payment Method Form -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ auth()->user()->hasDefaultPaymentMethod() ? 'Update' : 'Add' }} Payment Method
                </h2>

                <form id="payment-method-form" action="{{ route('student.subscriptions.update-payment-method') }}"
                    method="POST">
                    @csrf

                    <!-- Card Element Container -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Card Information</label>
                        <div id="card-element" class="p-3 border border-gray-300 rounded-md bg-white">
                            <!-- Stripe Card Element will be inserted here -->
                        </div>
                        <div id="card-errors" class="mt-2 text-sm text-red-600"></div>
                    </div>

                    <!-- Hidden Payment Method ID -->
                    <input type="hidden" name="payment_method" id="payment-method">

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between">
                        <a href="{{ route('student.subscriptions.manage') }}"
                            class="text-gray-600 hover:text-gray-800 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Subscription
                        </a>
                        <button type="submit" id="submit-button"
                            class="inline-flex items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                            <span id="button-text">{{ auth()->user()->hasDefaultPaymentMethod() ? 'Update' : 'Add' }}
                                Payment Method</span>
                            <svg id="spinner" class="hidden animate-spin ml-2 h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Note -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-gray-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Secure Payment Processing</p>
                        <p class="text-sm text-gray-600 mt-1">
                            Your payment information is encrypted and processed securely by Stripe. We never store your full
                            card number.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ $stripeKey }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            }
        });

        cardElement.mount('#card-element');

        // Handle real-time validation errors
        cardElement.on('change', function (event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission
        const form = document.getElementById('payment-method-form');
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const spinner = document.getElementById('spinner');

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            // Disable submit button
            submitButton.disabled = true;
            buttonText.classList.add('hidden');
            spinner.classList.remove('hidden');

            // Create payment method
            const { setupIntent, error } = await stripe.confirmCardSetup(
                '{{ $intent->client_secret }}',
                {
                    payment_method: {
                        card: cardElement,
                    }
                }
            );

            if (error) {
                // Show error
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;

                // Re-enable submit button
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                spinner.classList.add('hidden');
            } else {
                // Set payment method ID and submit form
                document.getElementById('payment-method').value = setupIntent.payment_method;
                form.submit();
            }
        });
    </script>
@endpush