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

        {{-- 💡 Blade sometimes breaks Vue templates. This prevents it. --}}
        @verbatim
            <script type="text/x-template" id="v-stripe-smart-button-template">
            <div class="w-full">
                <!-- Stripe Express Checkout (Apple Pay / Google Pay / Browser Pay) -->
                <div v-if="paymentRequestAvailable" class="mb-4">
                    <div ref="paymentRequestButton" class="payment-request-button"></div>
                </div>

                <!-- Fallback: Card Element -->
                <div v-else>
                    <div ref="cardElement" id="card-element" class="p-4 border rounded"></div>
                    <div v-if="cardError" class="text-red-600 mt-2">@{{ cardError }}</div>

                    <div class="mt-4">
                        <button
                            type="button"
                            class="primary-button w-max rounded-2xl bg-navyBlue px-11 py-3"
                            :disabled="isProcessing"
                            @click="payWithCard"
                        >
                            <span v-if="isProcessing">Processing…</span>
                            <span v-else>Pay with card</span>
                        </button>
                    </div>
                </div>
            </div>
        </script>
        @endverbatim

        <script type="module">
            console.log('Initializing Stripe Smart Button component');

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
                        currency: @json($currencyToUse),
                        publishableKey: @json($publishableKey),
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
                                message: 'Stripe.js not loaded.'
                            });
                            return;
                        }

                        this.stripe = Stripe(this.publishableKey, {
                            locale: 'auto'
                        });
                        this.elements = this.stripe.elements();
                        console.log('✅ Stripe.js initialized.');

                        // Card setup (fallback)
                        const style = {
                            base: {
                                fontSize: '16px',
                                color: '#32325d',
                                '::placeholder': {
                                    color: '#a0aec0'
                                }
                            }
                        };

                        this.card = this.elements.create('card', {
                            style
                        });
                        this.card.mount(this.$refs.cardElement);
                        this.card.on('change', e => this.cardError = e.error ? e.error.message : null);

                        // Initialize Payment Request Button
                        console.log("Initialize Payment Request Button");
                        await this.setupPaymentRequest();
                    },

                    async setupPaymentRequest() {
                        try {
                            const amount = await this.getCartAmountInMinorUnits();

                            this.paymentRequest = this.stripe.paymentRequest({
                                country: 'IN', // ✅ Country code, not currency
                                currency: this.currency.toLowerCase(),
                                total: {
                                    label: 'ANA Sports Order',
                                    amount: amount,
                                },
                                requestPayerName: true,
                                requestPayerEmail: true,
                            });

                            const result = await this.paymentRequest.canMakePayment();
                            console.log('canMakePayment result:', result);

                            if (result) {
                                this.paymentRequestAvailable = true;
                                await this.$nextTick();

                                const prButton = this.elements.create('paymentRequestButton', {
                                    paymentRequest: this.paymentRequest,
                                    style: {
                                        paymentRequestButton: {
                                            type: 'buy',
                                            theme: 'dark',
                                            height: '45px'
                                        }
                                    }
                                });

                                if (this.$refs.paymentRequestButton) {
                                    prButton.mount(this.$refs.paymentRequestButton);
                                    console.log('✅ Stripe Express Button mounted.');
                                } else {
                                    console.error('❌ paymentRequestButton ref missing.');
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
                                            message: error.message
                                        });
                                        return;
                                    }

                                    ev.complete('success');
                                    await this.postPaymentSuccess(paymentIntent);
                                });
                            } else {
                                console.warn('Stripe Express Checkout not available in this browser.');
                            }
                        } catch (error) {
                            console.error('Payment Request setup failed:', error);
                        }
                    },

                    async getCartAmountInMinorUnits() {
                        try {
                            const resp = await this.$axios.get("{{ route('stripe.cart-amount') }}");
                            return resp.data?.amount || 0;
                        } catch {
                            return 0;
                        }
                    },

                    async fetchClientSecret() {
                        try {
                            const resp = await this.$axios.post("{{ route('stripe.create-payment-intent') }}", {
                                _token: "{{ csrf_token() }}"
                            });
                            if (resp.data.success) {
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

                        try {
                            const clientSecret = await this.fetchClientSecret();
                            if (!clientSecret) {
                                this.$emitter.emit('add-flash', {
                                    type: 'error',
                                    message: 'Could not create payment.'
                                });
                                this.isProcessing = false;
                                return;
                            }

                            const result = await this.stripe.confirmCardPayment(clientSecret, {
                                payment_method: {
                                    card: this.card
                                }
                            });

                            if (result.error) {
                                this.cardError = result.error.message;
                                this.isProcessing = false;
                            } else if (result.paymentIntent.status === 'succeeded') {
                                window.location.href = "{{ route('shop.checkout.onepage.success') }}";
                            }
                        } catch (err) {
                            console.error(err);
                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: 'Payment failed.'
                            });
                            this.isProcessing = false;
                        }
                    },

                    async postPaymentSuccess(paymentIntent) {
                        try {
                            const resp = await this.$axios.post("{{ route('stripe.confirm-payment') }}", {
                                _token: "{{ csrf_token() }}",
                                payment_intent_id: paymentIntent.id
                            });

                            const redirectUrl = resp.data.redirect_url ||
                                "{{ route('shop.checkout.onepage.success') }}";
                            window.location.href = redirectUrl;
                        } catch {
                            window.location.href = "{{ route('shop.checkout.cart.index') }}";
                        }
                    }
                }
            });
        </script>
    @endPushOnce
@endif
