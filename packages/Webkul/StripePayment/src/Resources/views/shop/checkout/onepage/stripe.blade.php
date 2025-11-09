@if (request()->routeIs('shop.checkout.onepage.index') &&
        (bool) core()->getConfigData('sales.payment_methods.stripepayment.active'))
    @php
        $publishableKey = core()->getConfigData('sales.payment_methods.stripepayment.publishable_key');
        $acceptedCurrency = core()->getConfigData('sales.payment_methods.stripepayment.accepted_currencies');
        $currentCurrency = core()->getCurrentCurrencyCode();
        $acceptedCurrenciesArray = array_map('trim', explode(',', $acceptedCurrency));
        $currencyToUse = in_array($currentCurrency, $acceptedCurrenciesArray)
            ? $currentCurrency
            : $acceptedCurrenciesArray[0] ?? $currentCurrency;
    @endphp

    @pushOnce('scripts')
        <script src="https://js.stripe.com/v3/"></script>

        {{-- Stripe Smart Button Template --}}
        <script type="text/x-template" id="v-stripe-smart-button-template">
            <div class="w-full">
                <!-- Express Checkout (Apple Pay / Google Pay / Link) -->
                <div v-if="paymentRequestAvailable" class="mb-4">
                    <div ref="paymentRequestButton" class="payment-request-button"></div>
                </div>

                <!-- Card Element Fallback -->
                <div v-else>
                    <div ref="cardElement" id="card-element" class="p-4 border rounded-md bg-white shadow-sm"></div>
                    <div v-if="cardError" class="text-red-600 mt-2 text-sm">@{{ cardError }}</div>

                    <div class="mt-4">
                        <button
                            type="button"
                            class="block bg-navyBlue text-white px-6 py-3 rounded-2xl font-medium hover:opacity-90 transition"
                            :disabled="isProcessing"
                            @click="payWithCard"
                        >
                            <span v-if="isProcessing">Processing…</span>
                            <span v-else>Pay with Card</span>
                        </button>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-stripe-smart-button', {
                template: '#v-stripe-smart-button-template',

                data() {
                    return {
                        stripe: null,
                        elements: null,
                        card: null,
                        clientSecret: null,
                        isProcessing: false,
                        cardError: null,
                        paymentRequest: null,
                        paymentRequestAvailable: false,
                        currency: '{{ $currencyToUse }}',
                        publishableKey: '{{ $publishableKey }}',
                    };
                },

                mounted() {
                    this.initStripe();
                },

                methods: {
                    async initStripe() {
                        if (typeof Stripe === 'undefined') {
                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: 'Stripe.js failed to load.'
                            });
                            return;
                        }

                        this.stripe = Stripe(this.publishableKey, {
                            locale: 'auto'
                        });
                        this.elements = this.stripe.elements();

                        // Initialize card element
                        const style = {
                            base: {
                                fontSize: '16px',
                                color: '#32325d',
                                '::placeholder': {
                                    color: '#a0aec0'
                                },
                            },
                            invalid: {
                                color: '#fa755a'
                            },
                        };

                        this.card = this.elements.create('card', {
                            style
                        });
                        this.card.mount(this.$refs.cardElement);

                        this.card.on('change', (event) => {
                            this.cardError = event.error ? event.error.message : null;
                        });

                        await this.setupPaymentRequest();
                    },

                    async setupPaymentRequest() {
                        try {
                            const resp = await this.$axios.get("{{ route('stripe.cart-amount') }}");
                            const amount = resp.data.amount || 0;

                            this.paymentRequest = this.stripe.paymentRequest({
                                country: "IN",
                                currency: this.currency.toLowerCase(),
                                total: {
                                    label: "{{ config('app.name') }} Order",
                                    amount: amount,
                                },
                                requestPayerName: true,
                                requestPayerEmail: true,
                            });

                            const result = await this.paymentRequest.canMakePayment();

                            if (result && (result.googlePay || result.applePay || result.link)) {
                                const prButton = this.elements.create('paymentRequestButton', {
                                    paymentRequest: this.paymentRequest,
                                    style: {
                                        paymentRequestButton: {
                                            type: 'default',
                                            theme: 'dark',
                                            height: '45px'
                                        }
                                    },
                                });

                                this.paymentRequestAvailable = true;
                                await this.$nextTick();

                                if (this.$refs.paymentRequestButton) {
                                    prButton.mount(this.$refs.paymentRequestButton);
                                }

                                this.paymentRequest.on('paymentmethod', async (ev) => {
                                    const clientSecret = await this.fetchClientSecret();
                                    if (!clientSecret) {
                                        ev.complete('fail');
                                        return;
                                    }

                                    const {
                                        error,
                                        paymentIntent
                                    } = await this.stripe.confirmCardPayment(
                                        clientSecret, {
                                            payment_method: ev.paymentMethod.id
                                        }, {
                                            handleActions: false
                                        }
                                    );

                                    if (error) {
                                        ev.complete('fail');
                                        this.$emitter.emit('add-flash', {
                                            type: 'error',
                                            message: error.message,
                                        });
                                    } else {
                                        ev.complete('success');
                                        await this.postPaymentSuccess(paymentIntent);
                                    }
                                });
                            } else {
                                console.warn('Wallets not available; showing card input fallback.');
                            }
                        } catch (err) {
                            console.error('Payment Request setup failed:', err);
                        }
                    },

                    async fetchClientSecret() {
                        try {
                            const resp = await this.$axios.post("{{ route('stripe.create-payment-intent') }}", {
                                _token: "{{ csrf_token() }}"
                            });
                            if (resp.data.success && resp.data.client_secret) {
                                this.clientSecret = resp.data.client_secret;
                                return this.clientSecret;
                            }
                        } catch (e) {
                            console.error(e);
                        }
                        return null;
                    },

                    async payWithCard() {
                        this.isProcessing = true;
                        this.cardError = null;

                        const clientSecret = await this.fetchClientSecret();

                        if (!clientSecret) {
                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: 'Unable to create payment. Try again.'
                            });
                            this.isProcessing = false;
                            return;
                        }

                        const result = await this.stripe.confirmCardPayment(clientSecret, {
                            payment_method: {
                                card: this.card
                            },
                        });

                        if (result.error) {
                            this.cardError = result.error.message;
                            this.isProcessing = false;
                        } else if (result.paymentIntent.status === 'succeeded') {
                            await this.postPaymentSuccess(result.paymentIntent);
                        }
                    },

                    async postPaymentSuccess(paymentIntent) {
                        try {
                            const resp = await this.$axios.post("{{ route('stripe.confirm-payment') }}", {
                                _token: "{{ csrf_token() }}",
                                payment_intent_id: paymentIntent.id
                            });

                            if (resp.data.redirect_url) {
                                window.location.href = resp.data.redirect_url;
                            } else {
                                window.location.href = "{{ route('shop.checkout.onepage.success') }}";
                            }
                        } catch (e) {
                            console.error(e);
                            window.location.href = "{{ route('shop.checkout.cart.index') }}";
                        }
                    }
                }
            });
        </script>
    @endPushOnce
@endif
