<?php

return [
    [
        'key'  => 'googlemerchant',
        'name' => 'googlemerchant::app.admin.system.title',
        'info' => 'googlemerchant::app.admin.system.title-info',
        'icon' => 'icon-settings',
        'sort' => 10,
    ], [
        'key'  => 'googlemerchant.settings',
        'name' => 'googlemerchant::app.admin.system.settings',
        'info' => 'googlemerchant::app.admin.system.settings-info',
        'sort' => 0,
        'icon' => '',
    ], [
        'key'    => 'googlemerchant.settings.api_credentials',
        'name'   => 'googlemerchant::app.admin.system.api_credentials',
        'info'   => 'googlemerchant::app.admin.system.api_credentials-info',
        'sort'   => 0,
        'fields' => [
            [
                'name'          => 'merchant_id',
                'title'         => 'googlemerchant::app.admin.system.merchant_id',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'service_account_json',
                'title'         => 'googlemerchant::app.admin.system.service_account_json',
                'type'          => 'textarea',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
            ],
        ],
    ],
];
