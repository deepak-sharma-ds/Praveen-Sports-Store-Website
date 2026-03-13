<x-admin::layouts>

    <x-slot:title>
        Google Merchant Center
    </x-slot>

    <div class="page-content">

        <div class="page-header">
            <div class="page-title">
                <h1>Google Merchant Center</h1>
            </div>
        </div>

        <div class="box" style="padding: 24px;">
            <p style="margin-bottom: 16px;">
                Click the button below to sync your active products to Google Merchant Center.
                Make sure you have configured your <strong>Merchant ID</strong> and
                <strong>Service Account JSON</strong> under
                <em>Admin → Configure → Google Merchant → Settings</em>.
            </p>

            <a
                href="{{ route('admin.googlemerchant.sync') }}"
                class="btn btn-primary btn-lg"
                id="google-merchant-sync-btn"
            >
                Sync Products to Google Merchant
            </a>
        </div>

    </div>

</x-admin::layouts>