<?php

return [
    [
        'key'    => 'sales.payment_methods.customstripepayment',
        'name'   => 'CustomStripePayment', // use translation
        'info'   => 'Information about CustomStripePayment', // use translation
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'Title', // use translation
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ],
            [
                'name'          => 'description',
                'title'         => 'Description', // use translation
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ],
            [
                'name'          => 'active',
                'title'         => 'Status', // use translation
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ],
            [
                'name'          => 'sandbox',
                'title'         => 'Sandbox', // use translation
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ],
            [
                'name' => 'publishable_key',
                'title' => 'Publishable Key',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => false,
            ],
            [
                'name' => 'secret_key',
                'title' => 'Secret Key',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => false,
            ],
            [
                'name' => 'enable_google_pay',
                'title' => 'Enable Google Pay',
                'type' => 'boolean',
                'default_value' => true,
            ],
            [
                'name' => 'enable_apple_pay',
                'title' => 'Enable Apple Pay',
                'type' => 'boolean',
                'default_value' => true,
            ]
        ]
    ]
];
