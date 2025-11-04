<div style="margin-left:20px; margin-bottom:10px;">
    <label>
        <input type="radio" name="payment_type" value="card" checked> Card Payment
    </label>
    <label style="display:none;">
        <input type="radio" name="payment_type" value="google"> Google Pay
    </label>
    <label style="display:none;">
        <input type="radio" name="payment_type" value="apple"> Apple Pay
    </label>
</div>

<div id="stripe-payment-section">
    <div id="stripe-card-element" style="display:block;margin-left:20px;">
        <label>Card Payment</label>
        <div id="card-element" style="padding:10px;border:1px solid #ccc;border-radius:8px;"></div>
        <span id="card-errors" style="color:red;font-size:13px;"></span>
    </div>

    <div id="google-pay-button" style="display:none;margin-left:20px;"></div>
    <div id="apple-pay-button" style="display:none;margin-left:20px;"></div>
</div>
<div id="express-checkout-element"></div>

<input type="hidden" id="stripe-payment-type" value="card">
<input type="hidden" id="payment-intent-id">

<div id="payment-loader" style="display:none;margin-left:20px;color:green;">
    Processing... Please wait.
</div>


{{-- <script src="https://js.stripe.com/v3/"></script> --}}
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js" type="text/javascript"></script>
<script src="https://js.stripe.com/clover/stripe.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", async function() {

        const stripe = Stripe(
            "{{ core()->getConfigData('sales.payment_methods.customstripepayment.publishable_key') }}");
        const elements = stripe.elements();
        console.log(elements);


        const cardElement = elements.create("card");
        cardElement.mount("#card-element");

        const cardDiv = document.getElementById("stripe-card-element");
        const googleDiv = document.getElementById("google-pay-button");
        const appleDiv = document.getElementById("apple-pay-button");
        const loader = document.getElementById("payment-loader");
        const typeInput = document.getElementById("stripe-payment-type");

        const cartAmount = Math.round(
            {{ \Webkul\Checkout\Facades\Cart::getCart()->base_grand_total ?? 0 }} * 100);

        const paymentRequest = stripe.paymentRequest({
            country: "IN",
            currency: "usd",
            total: {
                label: "Order Total",
                amount: cartAmount
            },
            requestPayerName: true,
            requestPayerEmail: true,
        });

        const prButton = elements.create("paymentRequestButton", {
            paymentRequest,
            style: {
                paymentRequestButton: {
                    theme: "dark",
                    height: "45px"
                }
            },
        });

        paymentRequest.canMakePayment().then(function(result) {
            console.log(result);

            if (result) {
                if (result.googlePay) {
                    document.querySelector("input[value='google']").parentElement.style.display =
                        "inline-block";
                    prButton.on("click", () => startPayment("google"));
                    prButton.mount("#google-pay-button");
                }
                if (result.applePay) {
                    document.querySelector("input[value='apple']").parentElement.style.display =
                        "inline-block";
                    prButton.on("click", () => startPayment("apple"));
                    prButton.mount("#apple-pay-button");
                }
            }
        });

        document.querySelectorAll("input[name='payment_type']").forEach(option => {
            option.addEventListener("change", function() {
                typeInput.value = this.value;

                cardDiv.style.display = (this.value === "card") ? "block" : "none";
                googleDiv.style.display = (this.value === "google") ? "block" : "none";
                appleDiv.style.display = (this.value === "apple") ? "block" : "none";

                window.eventBus?.$emit("after-payment-selected");
            });
        });

        window.addEventListener("submit-order", () => {
            startPayment(typeInput.value);
        });

        async function startPayment(type) {
            loader.style.display = "block";

            // Create PaymentIntent
            const response = await fetch("{{ route('stripe.payment.intent') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                }
            });

            const {
                client_secret
            } = await response.json();

            const confirmParams = {
                payment_method: {
                    card: (type === "card") ? cardElement : null,
                }
            };

            const result = await stripe.confirmCardPayment(client_secret, confirmParams);

            if (result.error) {
                document.getElementById("card-errors").innerText = result.error.message;
                loader.style.display = "none";
            } else {
                finalizeOrder(result.paymentIntent.id);
            }
        }

        async function finalizeOrder(paymentIntentId) {
            await fetch("{{ route('stripe.order.complete') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    payment_intent_id: paymentIntentId
                })
            });

            loader.innerText = "Payment Successful ✅ Redirecting...";
            location.reload();
        }
    });
</script>

<script>
    setTimeout(function() {
        const stripe = Stripe(
            "{{ core()->getConfigData('sales.payment_methods.customstripepayment.publishable_key') }}");
        var total_price2 = Math.round(
            {{ \Webkul\Checkout\Facades\Cart::getCart()->base_grand_total ?? 0 }} * 100);
        console.log(total_price2);

        var shipping_price = $("#shipping_price").val();
        const options = {
            mode: 'payment',
            amount: parseInt(100),
            currency: 'usd',
            // Customizable with appearance API.
            appearance: {},
            // shippingAddressRequired: true,
        };

        // Set up Stripe.js and Elements to use in checkout form
        const elements = stripe.elements(options);
        // Create and mount the Express Checkout Element
        const expressCheckoutElement = elements.create('expressCheckout');
        expressCheckoutElement.mount('#express-checkout-element');



        // ✅ Handle element load errors
        expressCheckoutElement.on('loaderror', (event) => {
            console.error("Express Checkout load error:", event.error);
            alert("Payment UI failed to load: " + event.error.message);
        });

        const handleError = (error) => {
            const messageContainer = document.querySelector('#error-message');
            messageContainer.textContent = error.message;
            alert("Payment Error: " + error.message);
        }


        expressCheckoutElement.on('click', (event) => {
            // Handle click event
            const options = {
                emailRequired: true,
                phoneNumberRequired: true,
                shippingAddressRequired: true,
                shippingRates: [{
                    'id': '1',
                    'amount': parseInt(shipping_price),
                    'displayName': 'Test Shipping'
                }]
            };
            // return options1;
            return event.resolve(options);
        });

        let isProcessing = false; // Prevent multiple submissions
        expressCheckoutElement.on('confirm', async (event) => {
            if (isProcessing) return; // Stop duplicate processing
            isProcessing = true;
            let addgift_express = '';
            if ($("#addgift_express").is(":checked")) {
                addgift_express = $("#addgift_express").val();
            } else {
                addgift_express = $('input[type="hidden"]#addgift_express').val();
            }
            const {
                error: submitError
            } = await elements.submit();
            if (submitError) {
                handleError(submitError);
                return;
            }
            // Create the PaymentIntent and obtain clientSecret
            const res = await fetch("{{ route('stripe.payment.intent') }}", {
                method: 'POST',
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    addgift_express: addgift_express // replace with your value
                })
            });

            const {
                client_secret: clientSecret
            } = await res.json();
            if (clientSecret != '') {
                $('.loading').show();
                const {
                    error
                } = await stripe.confirmPayment({
                    // `elements` instance used to create the Express Checkout Element
                    elements,
                    // `clientSecret` from the created PaymentIntent
                    clientSecret,
                    confirmParams: {
                        return_url: "{{ route('stripe.order.complete') }}",
                    },
                });
            } else {
                alert("Due to an issue with your payment, please try again.");
                //  window.location.href="{{ url('/') }}"

                window.location.reload();
            }

            if (error) {
                handleError(error);
            } else {
                // The payment UI automatically closes with a success animation.
                // Your customer is redirected to your `return_url`.
            }
        });
    });
</script>
