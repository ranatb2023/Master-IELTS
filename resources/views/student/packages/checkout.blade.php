<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Checkout
            </h2>
            <a href="{{ route('student.packages.show', $package) }}"
                class="text-sm text-indigo-600 hover:text-indigo-900">
                ← Back to Package
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order Summary (Right - shown first on mobile) -->
                <div class="lg:col-span-1 order-first lg:order-last">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-6">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Order Summary</h3>
                        </div>
                        <div class="p-6">
                            <!-- Package Info -->
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">{{ $package->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $package->courses->count() }} courses included</p>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="border-t border-gray-200 pt-4 space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Package Price</span>
                                    <span
                                        class="font-medium text-gray-900">${{ number_format($package->price, 2) }}</span>
                                </div>

                                @if(!$package->is_lifetime && $package->duration_days)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Duration</span>
                                        <span class="font-medium text-gray-900">{{ $package->duration_days }} days</span>
                                    </div>
                                @elseif($package->is_lifetime)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Access</span>
                                        <span class="font-medium text-green-600">Lifetime</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Total -->
                            <div class="border-t border-gray-200 mt-4 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span
                                        class="text-2xl font-bold text-gray-900">${{ number_format($package->price, 2) }}</span>
                                </div>
                            </div>

                            <!-- Security Note -->
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <div class="text-xs text-blue-800">
                                        <p class="font-semibold mb-1">Secure Payment</p>
                                        <p>Your payment is processed securely through Stripe. We never store your card
                                            details.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form (Left) -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Payment Information</h3>
                        </div>
                        <div class="p-6">
                            <!-- Payment Form -->
                            <form id="payment-form">
                                @csrf

                                <!-- Card Element Container -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Card Details
                                    </label>
                                    <div id="card-element" class="p-3 border border-gray-300 rounded-md bg-white">
                                        <!-- Stripe Card Element will be inserted here -->
                                    </div>
                                    <div id="card-errors" class="mt-2 text-sm text-red-600" role="alert"></div>
                                </div>

                                <!-- Billing Name -->
                                <div class="mb-6">
                                    <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Name on Card
                                    </label>
                                    <input type="text" id="billing_name" name="billing_name"
                                        value="{{ Auth::user()->name }}" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <!-- Terms Agreement -->
                                <div class="mb-6">
                                    <label class="flex items-start">
                                        <input type="checkbox" id="terms_agreed" name="terms_agreed" required
                                            class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-600">
                                            I agree to the <a href="#"
                                                class="text-indigo-600 hover:text-indigo-800">Terms of Service</a> and
                                            <a href="#" class="text-indigo-600 hover:text-indigo-800">Privacy Policy</a>
                                        </span>
                                    </label>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" id="submit-button"
                                    class="w-full px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span id="button-text">Complete Purchase -
                                        ${{ number_format($package->price, 2) }}</span>
                                    <span id="spinner" class="hidden">
                                        <svg class="animate-spin inline h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Processing...
                                    </span>
                                </button>

                                <!-- Error Display -->
                                <div id="payment-error"
                                    class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p id="payment-error-message" class="text-sm text-red-800"></p>
                                    </div>
                                </div>
                            </form>

                            <!-- Payment Methods -->
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <p class="text-xs text-gray-500 text-center mb-3">We accept</p>
                                <div class="flex justify-center items-center gap-3 flex-wrap">
                                    <!-- Visa -->
                                    <div class="bg-white border border-gray-200 rounded px-3 py-2 shadow-sm">
                                        <svg class="h-6 w-10" viewBox="0 0 48 32" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect width="48" height="32" rx="4" fill="#1434CB" />
                                            <path
                                                d="M21.3 21.8l1.9-11.7h3l-1.9 11.7h-3zm13.6-11.4c-.6-.2-1.5-.5-2.6-.5-2.9 0-4.9 1.5-4.9 3.7 0 1.6 1.5 2.5 2.6 3 1.1.6 1.5.9 1.5 1.4 0 .8-.9 1.1-1.8 1.1-1.2 0-1.8-.2-2.8-.6l-.4-.2-.4 2.4c.7.3 2 .6 3.3.6 3.1 0 5.1-1.5 5.1-3.8 0-1.3-.8-2.2-2.5-3-1-.5-1.6-.9-1.6-1.4 0-.5.5-1 1.6-1 .9 0 1.6.2 2.1.4l.3.1.4-2.2zm5.8-1.3h-2.3c-.7 0-1.3.2-1.6.9L32 21.8h3.1s.5-1.4.6-1.7h3.7c.1.4.4 1.7.4 1.7H43l-2.3-11.7zm-3.5 7.6c.2-.6 1.2-3.2 1.2-3.2s.3-.7.4-1.1l.2 1.1s.6 2.8.7 3.4h-2.5v-.2zm-17.7-7.6L16.7 18l-.3-1.6c-.5-1.7-2.1-3.6-3.9-4.5l2.7 10h3.1l4.6-11.7h-3.1z"
                                                fill="white" />
                                            <path
                                                d="M13.2 10.1H8.9l-.1.4c3.7.9 6.1 3.1 7.1 5.8l-1-4.9c-.2-.7-.7-1.2-1.4-1.3z"
                                                fill="#F7B600" />
                                        </svg>
                                    </div>

                                    <!-- Mastercard -->
                                    <div class="bg-white border border-gray-200 rounded px-3 py-2 shadow-sm">
                                        <svg class="h-6 w-10" viewBox="0 0 48 32" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect width="48" height="32" rx="4" fill="#000000" />
                                            <circle cx="18" cy="16" r="8" fill="#EB001B" />
                                            <circle cx="30" cy="16" r="8" fill="#F79E1B" />
                                            <path
                                                d="M24 9.6c-1.7 1.4-2.8 3.6-2.8 6s1.1 4.6 2.8 6c1.7-1.4 2.8-3.6 2.8-6s-1.1-4.6-2.8-6z"
                                                fill="#FF5F00" />
                                        </svg>
                                    </div>

                                    <!-- American Express -->
                                    <div class="bg-white border border-gray-200 rounded px-3 py-2 shadow-sm">
                                        <svg class="h-6 w-10" viewBox="0 0 48 32" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect width="48" height="32" rx="4" fill="#006FCF" />
                                            <path
                                                d="M10.5 13.2h2.7l-.7-1.6-.6-1.5h-.1l-.6 1.5-.7 1.6zm17.1-1.4v-.7h-2.4v.7h.8v3.3h-2.3v-2.8c0-.3.2-.5.5-.5h.3v-.7h-2.9v.7h.3c.3 0 .5.2.5.5v2.8h-2.3v-3.3h.8v-.7h-2.5v.7h.3c.3 0 .5.2.5.5v3.5h6.4v-3.3h.8zm10.9-.7h-3.1l-.4 1-.4-1h-2v.7h.3c.3 0 .5.2.5.5v2.8c0 .3-.2.5-.5.5h-.3v.7h3.1v-.7h-.3c-.3 0-.5-.2-.5-.5v-2.6l1.2 3.8h1.1l1.2-3.8v2.6c0 .3-.2.5-.5.5h-.3v.7h2.4v-.7h-.3c-.3 0-.5-.2-.5-.5v-2.8c0-.3.2-.5.5-.5h.3v-.7zM9 11.1h3.1l.7 1.6h1.1l-2.3-5.2h-1.1l-2.3 5.2h1.1l.7-1.6zm10.5 4.1h-2.4v.7h3.1v-.7h-.3c-.3 0-.5-.2-.5-.5v-2.3l2.2 3.5h.7v-4.3c0-.3.2-.5.5-.5h.3v-.7h-2.4v.7h.3c.3 0 .5.2.5.5v2.3l-2.2-3.5h-1.8v.7h.3c.3 0 .5.2.5.5v2.8c0 .3-.2.5-.5.5zm7.1 0h-.3c-.3 0-.5-.2-.5-.5v-2.8c0-.3.2-.5.5-.5h.3v-.7h-3.1v.7h.3c.3 0 .5.2.5.5v2.8c0 .3-.2.5-.5.5h-.3v.7h6.4v-1.4h-.8v.7h-2.5z"
                                                fill="white" />
                                        </svg>
                                    </div>

                                    <!-- Discover -->
                                    <div class="bg-white border border-gray-200 rounded px-3 py-2 shadow-sm">
                                        <svg class="h-6 w-10" viewBox="0 0 48 32" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect width="48" height="32" rx="4" fill="#FF6000" />
                                            <path d="M32 6h16v20c-5.3 0-10.7-6.7-16-20z" fill="#F7981D" />
                                            <path
                                                d="M11.2 13.8c0-1.1-.8-1.8-2-1.8h-2v7.2h2c1.2 0 2-.7 2-1.8v-3.6zm-.8.1v3.4c0 .7-.4 1.1-1.2 1.1h-1.1v-5.6h1.1c.8 0 1.2.4 1.2 1.1zm3.9-1.9h-.8v7.2h.8v-7.2zm3.5 5.1c0-.4-.2-.7-.5-.9-.2-.1-.5-.2-1-.3-.9-.2-1.5-.6-1.5-1.4 0-.8.7-1.4 1.7-1.4.5 0 .9.1 1.2.3l-.2.7c-.3-.2-.6-.3-1-.3-.6 0-.9.3-.9.7 0 .4.2.6.9.8.9.2 1.6.6 1.6 1.5 0 .8-.6 1.5-1.8 1.5-.5 0-1-.1-1.4-.3l.2-.7c.3.2.8.3 1.2.3.6 0 1.1-.3 1.1-.8zm4.9-1.4c-.4-.4-1.1-.6-1.8-.6-.4 0-.8 0-1.1.1v5.6c.3.1.6.1 1.1.1.7 0 1.4-.2 1.8-.6.5-.5.8-1.2.8-2.2s-.3-1.7-.8-2.4zm.1 4.4c-.3.3-.7.5-1.4.5-.3 0-.6 0-.8-.1v-4.9c.2 0 .5-.1.8-.1.7 0 1.1.2 1.4.5.3.3.5.9.5 1.6s-.2 1.2-.5 1.5zm4.8-4.9h-2.7v2.4h2.5v.7h-2.5v3h-.8v-6.8h3.5v.7zm3.7-.7h-2.7v6.8h2.7v-.7h-1.9v-2.4h1.8v-.7h-1.8v-2.2h1.9v-.8zm4 6.8l-1.1-2.3c-.2-.4-.3-.7-.5-1.1h0c-.1.4-.3.7-.5 1.1l-1.1 2.3h-.9l1.9-3.5-1.8-3.3h.9l1 2.2c.2.4.3.8.5 1.2h0c.1-.4.3-.8.5-1.2l1-2.2h.9l-1.9 3.3 2 3.5h-.9z"
                                                fill="white" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex justify-center mt-4">
                                    <div class="flex items-center gap-2 px-3 py-1.5 bg-indigo-50 rounded-full">
                                        <svg class="h-4" viewBox="0 0 60 25" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M59.2 13.2v-1.5c0-.3-.2-.5-.5-.5h-1.3c-.3 0-.5.2-.5.5v1.5c0 .3.2.5.5.5h1.3c.3 0 .5-.2.5-.5z"
                                                fill="#635BFF" />
                                            <path
                                                d="M54.4 13.2v-1.5c0-.3-.2-.5-.5-.5h-1.3c-.3 0-.5.2-.5.5v1.5c0 .3.2.5.5.5h1.3c.3 0 .5-.2.5-.5z"
                                                fill="#635BFF" />
                                            <path
                                                d="M3.7 8.5c0-1.2.9-2.1 2.1-2.1.7 0 1.3.3 1.7.7l1-1c-.7-.7-1.6-1.1-2.7-1.1-2 0-3.6 1.6-3.6 3.6s1.6 3.6 3.6 3.6c1 0 2-.4 2.7-1.1l-1-1c-.4.4-1 .7-1.7.7-1.2-.1-2.1-1-2.1-2.3zm8.8 3.2c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3zm0-4.5c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5-.7-1.5-1.5-1.5zm8.3 4.5c-1 0-1.8-.5-2.3-1.3l1.3-.8c.3.5.7.7 1.1.7.7 0 1.2-.5 1.2-1.2V5.9h1.5v3.2c0 1.6-1.2 2.6-2.8 2.6zm9.4 0c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3zm0-4.5c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5-.7-1.5-1.5-1.5zm7.3-1.3v6h-1.5v-6h-1.7V5.9h4.9v1.5h-1.7zm8.9 5.8h-1.5V8.9c0-.6-.4-1-1-1s-1 .4-1 1v2.8h-1.5V5.9h1.5v.5c.4-.4.9-.6 1.5-.6 1.2 0 2 .8 2 2v3.9zm7 0h-1.5V8.9c0-.6-.4-1-1-1s-1 .4-1 1v2.8h-1.5V5.9h1.5v.5c.4-.4.9-.6 1.5-.6 1.2 0 2 .8 2 2v3.9z"
                                                fill="#635BFF" />
                                        </svg>
                                        <span class="text-xs font-medium text-indigo-700">Powered by Stripe</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            // Initialize Stripe
            const stripe = Stripe('{{ config("services.stripe.key") }}');
            const elements = stripe.elements();

            // Create card element
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

            // Handle card errors
            cardElement.on('change', function (event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            // Handle form submission
            const form = document.getElementById('payment-form');
            const submitButton = document.getElementById('submit-button');
            const buttonText = document.getElementById('button-text');
            const spinner = document.getElementById('spinner');
            const errorDisplay = document.getElementById('payment-error');
            const errorMessage = document.getElementById('payment-error-message');

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                // Disable submit button
                submitButton.disabled = true;
                buttonText.classList.add('hidden');
                spinner.classList.remove('hidden');
                errorDisplay.classList.add('hidden');

                try {
                    // Create payment intent on the server
                    const response = await fetch('{{ route("student.packages.create-payment-intent", $package) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            amount: {{ $package->price * 100 }}, // Convert to cents
                            package_id: {{ $package->id }}
                        })
                    });

                    const { clientSecret, error: serverError } = await response.json();

                    if (serverError) {
                        throw new Error(serverError);
                    }

                    // Confirm payment with Stripe
                    const { error: stripeError, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
                        payment_method: {
                            card: cardElement,
                            billing_details: {
                                name: document.getElementById('billing_name').value
                            }
                        }
                    });

                    if (stripeError) {
                        throw new Error(stripeError.message);
                    }

                    if (paymentIntent.status === 'succeeded') {
                        // Process purchase on server
                        const purchaseResponse = await fetch('{{ route("student.packages.process-purchase", $package) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                payment_method: 'stripe',
                                payment_intent_id: paymentIntent.id,
                                amount_paid: {{ $package->price }}
                            })
                        });

                        const purchaseResult = await purchaseResponse.json();

                        if (purchaseResult.success) {
                            // Redirect to success page
                            window.location.href = purchaseResult.redirect_url;
                        } else {
                            throw new Error(purchaseResult.message || 'Purchase processing failed');
                        }
                    }
                } catch (error) {
                    // Show error
                    errorMessage.textContent = error.message;
                    errorDisplay.classList.remove('hidden');

                    // Re-enable submit button
                    submitButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    spinner.classList.add('hidden');
                }
            });
        </script>
    @endpush
</x-app-layout>