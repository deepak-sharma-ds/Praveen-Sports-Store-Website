{{-- <div id="stripe-payment-section">

    <div id="stripe-card-element" style="display:block;margin-left:20px;">
        <label>Card Payment</label>
        <div id="card-element" style="padding:10px;border:1px solid #ccc;border-radius:8px;"></div>
        <div id="card-errors" style="color:red;"></div>
    </div>

    <div id="google-pay-button" style="display:none;margin-left:20px;"></div>
    <div id="apple-pay-button" style="display:none;margin-left:20px;"></div>

    <input type="hidden" id="stripe-payment-type" value="card">

</div>

<input type="hidden" id="selected-payment-method" value="card">


<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener("DOMContentLoaded", async function() {
        const stripe = Stripe(
            "{{ core()->getConfigData('sales.payment_methods.customstripepayment.publishable_key') }}");
        const elements = stripe.elements();

        // Card Element
        const cardElement = elements.create("card");
        cardElement.mount("#card-element");

        const cardDiv = document.getElementById("stripe-card-element");
        const googleDiv = document.getElementById("google-pay-button");
        const appleDiv = document.getElementById("apple-pay-button");
        const typeInput = document.getElementById("stripe-payment-type");

        const cartAmount = Math.round(
            {{ \Webkul\Checkout\Facades\Cart::getCart()->base_grand_total ?? 0 }} * 100);

        // Payment Request API (Google Pay & Apple Pay)
        const paymentRequest = stripe.paymentRequest({
            country: "IN",
            currency: "inr",
            total: {
                label: "Order Total",
                amount: cartAmount,
            },
            requestPayerName: true,
            requestPayerEmail: true,
        });

        const prButton = elements.create("paymentRequestButton", {
            paymentRequest: paymentRequest,
            style: {
                paymentRequestButton: {
                    theme: "dark",
                    height: "45px",
                },
            },
        });

        // Detect Wallet Availability
        paymentRequest.canMakePayment().then(function(result) {
            if (result) {
                if (result.googlePay) {
                    googleDiv.style.display = "block";
                    prButton.mount("#google-pay-button");
                }
                if (result.applePay) {
                    appleDiv.style.display = "block";
                    prButton.mount("#apple-pay-button");
                }
            }
        });

        // Radio Toggle Logic
        document.querySelectorAll("input[name='payment_type']").forEach(option => {
            option.addEventListener("change", function() {

                typeInput.value = this.value;

                if (this.value === "card") {
                    cardDiv.style.display = "block";
                    googleDiv.style.display = "none";
                    appleDiv.style.display = "none";
                }

                if (this.value === "google") {
                    googleDiv.style.display = "block";
                    cardDiv.style.display = "none";
                    appleDiv.style.display = "none";
                }

                if (this.value === "apple") {
                    appleDiv.style.display = "block";
                    cardDiv.style.display = "none";
                    googleDiv.style.display = "none";
                }

                if (window.eventBus?.$emit) {
                    window.eventBus.$emit("after-payment-selected");
                }
            });
        });

    });
</script> --}}






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
        <button id="pay-btn">Pay Now</button>
    </div>

    <div id="google-pay-button" style="display:none;margin-left:20px;"></div>
    <div id="apple-pay-button" style="display:none;margin-left:20px;"></div>
</div>

<input type="hidden" id="stripe-payment-type" value="card">
<input type="hidden" id="payment-intent-id">

<div id="payment-loader" style="display:none;margin-left:20px;color:green;">
    Processing... Please wait.
</div>


<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener("DOMContentLoaded", async function() {

        const stripe = Stripe(
            "{{ core()->getConfigData('sales.payment_methods.customstripepayment.publishable_key') }}");
        const elements = stripe.elements();

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
            currency: "inr",
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
