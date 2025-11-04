<?php

return [
    [
        'key'    => 'sales.payment_methods.stripepayment',
        'name'   => 'StripePayment', // use translation
        'info'   => 'Information about StripePayment', // use translation
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
                'name' => 'secret_key',
                'title' => 'Secret Key',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => false,
            ],
            [
                'name' => 'publishable_key',
                'title' => 'Publishable Key',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => false,
            ],
            [
                'name' => 'accepted_currencies',
                'title' => 'Accepted Currencies (comma separated)',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => false,
            ],
        ]
    ]
];
