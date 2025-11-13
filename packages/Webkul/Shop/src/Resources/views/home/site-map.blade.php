<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Site Map
    </x-slot>

    <div class="container mt-8 max-1180:px-5 max-md:mt-6 max-md:px-4">
        <!-- Form Container -->
        <div
            class="m-auto w-full max-w-[870px] rounded-xl border border-zinc-200 p-16 px-[90px] max-md:px-8 max-md:py-8 max-sm:border-none max-sm:p-0">
            <div class ="container">
                <h1 class="font-dmserif text-4xl max-md:text-3xl max-sm:text-xl">
                    <!-- Site Map heading -->
                    Company Information
                </h1>

                <p class="mt-4 text-xl text-zinc-500 max-sm:mt-1 max-sm:text-sm">
                    <!-- Site Map Links -->
                <ul>
                    <li><a href="{{ route('shop.home.index') }}">Home</a></li>
                    <li><a href="{{ route('shop.cms.page', 'about-us') }}">About Us</a></li>
                    <li><a href="{{ route('shop.cms.page', 'contact-us') }}">Contact</a></li>
                    <li><a href="{{ route('shop.customer.session.index') }}">My Account</a></li>
                    <li><a href="{{ route('shop.customers.account.orders.index') }}">My Orders</a></li>
                    <li><a href="{{ route('shop.checkout.cart.index') }}">Cart</a></li>
                </ul>
                </p>
            </div>

            <div class ="container">
                <!-- Account -->
                <h1 class="font-dmserif text-4xl max-md:text-3xl max-sm:text-xl">
                    Account
                </h1>

                <p class="mt-4 text-xl text-zinc-500 max-sm:mt-1 max-sm:text-sm">
                <ul>
                    <li><a href="{{ route('shop.customers.account.profile.index') }}">My Account</a></li>
                    <li><a href="{{ route('shop.customers.account.orders.index') }}">My Orders</a></li>
                    <li><a href="{{ route('shop.customers.account.wishlist.index') }}">My Wishlist</a></li>
                    <li><a href="{{ route('shop.checkout.cart.index') }}">My Cart</a></li>
                    <li><a href="{{ route('shop.customer.session.create') }}">Sign In</a></li>
                    <li><a href="{{ route('shop.customers.register.index') }}">Register</a></li>

                </ul>
                </p>
            </div>

            <div class ="container">
                <!-- Shop Categories -->
                <h1 class="font-dmserif text-4xl max-md:text-3xl max-sm:text-xl">
                    Shop Categories
                </h1>

                <p class="mt-4 text-xl text-zinc-500 max-sm:mt-1 max-sm:text-sm">
                <ul>
                    @foreach (core()->getCurrentChannel()->root_category->children as $category)
                        @if ($category->status)
                            <li><a href="{{ url('/') . '/' . $category->slug }}">{{ $category->name }}</a></li>
                        @endif
                    @endforeach

                </ul>
                </p>
            </div>

            <div class ="container">
                <!-- Policies -->
                <h1 class="font-dmserif text-4xl max-md:text-3xl max-sm:text-xl">
                    Policies
                </h1>

                <p class="mt-4 text-xl text-zinc-500 max-sm:mt-1 max-sm:text-sm">
                <ul>
                    <li><a href="{{ route('shop.cms.page', 'privacy-policy') }}">Privacy Policy</a></li>
                </ul>
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
        {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>
