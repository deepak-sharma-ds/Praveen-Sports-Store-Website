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

        <script type="text/x-template" id="v-stripe-smart-button-template">
            <div class="w-full">
                <div class="mb-4">
                    <div v-show="paymentRequestAvailable" ref="paymentRequestButton" class="payment-request-button"></div>
                </div>

                <div class="card-element-wrapper">
                    <div ref="cardElement" id="card-element" class="p-4 border rounded"></div>
                    <div v-if="cardError" class="text-red-600 mt-2">@{{ cardError }}</div>
                </div>

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
        </script>

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
                        currency: '{{ $currencyToUse }}',
                        publishableKey: '{{ $publishableKey }}',
                    };
                },

                mounted() {
                    this.register();
                },

                methods: {
                    async register() {
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

                        this.card.on('change', (event) => {
                            if (event.error) this.cardError = event.error.message;
                            else this.cardError = null;
                        });

                        // Payment Request setup
                        this.paymentRequest = this.stripe.paymentRequest({
                            country: '{{ core()->getConfigData('sales.general.country') ?? 'US' }}',
                            currency: this.currency.toLowerCase(),
                            total: {
                                label: '{{ config('app.name') }} Order',
                                amount: await this.getCartAmountInMinorUnits()
                            },
                            requestPayerName: true,
                            requestPayerEmail: true
                        });

                        const result = await this.paymentRequest.canMakePayment();
                        if (result) {
                            const prButton = this.elements.create('paymentRequestButton', {
                                paymentRequest: this.paymentRequest,
                            });

                            prButton.mount(this.$refs.paymentRequestButton);
                            this.paymentRequestAvailable = true;

                            this.paymentRequest.on('paymentmethod', async (ev) => {
                                const clientSecret = await this.fetchClientSecret();
                                if (!clientSecret) {
                                    ev.complete('fail');
                                    return;
                                }

                                const confirmResult = await this.stripe.confirmCardPayment(
                                    clientSecret, {
                                        payment_method: ev.paymentMethod.id
                                    }, {
                                        handleActions: false
                                    });

                                if (confirmResult.error) {
                                    ev.complete('fail');
                                    this.$emitter.emit('add-flash', {
                                        type: 'error',
                                        message: confirmResult.error.message
                                    });
                                } else {
                                    if (confirmResult.paymentIntent && confirmResult.paymentIntent
                                        .status === 'requires_action') {
                                        const actionResult = await this.stripe.confirmCardPayment(
                                            clientSecret);
                                        if (actionResult.error) {
                                            ev.complete('fail');
                                            this.$emitter.emit('add-flash', {
                                                type: 'error',
                                                message: actionResult.error.message
                                            });
                                            return;
                                        }
                                    }

                                    ev.complete('success');
                                    await this.postPaymentSuccess(confirmResult.paymentIntent || {});
                                }
                            });
                        }
                    },

                    async getCartAmountInMinorUnits() {
                        try {
                            const resp = await this.$axios.get("{{ route('stripe.cart-amount') }}");
                            if (resp.data && resp.data.amount) return resp.data.amount;
                        } catch (e) {}
                        return 0;
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
                            } else if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
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
                            if (resp.data.redirect_url) window.location.href = resp.data.redirect_url;
                            else window.location.href = "{{ route('shop.checkout.onepage.success') }}";
                        } catch (e) {
                            window.location.href = "{{ route('shop.checkout.cart.index') }}";
                        }
                    }
                }
            });
        </script>
    @endPushOnce
@endif
