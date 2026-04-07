<?php

return [
    [
        'key'  => 'other',
        'name' => 'Other Settings',
        'info' => 'Other Settings Info',
        // 'icon' => 'settings/settings.svg',
        'sort' => 11,
    ],
    [
        'key'  => 'other.brochure',
        'name' => 'brochure::app.admin.system.title',
        'info' => 'brochure::app.admin.system.title-info',
        'icon' => 'settings/settings.svg',
        'sort' => 11,
    ],
    [
        'key'    => 'other.brochure.credentials',
        'name'   => 'brochure::app.admin.system.brochure-credentials', // use translation
        'info'   => 'brochure::app.admin.system.brochure-credentials-info', // use translation
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'brochure_flipbook.pdf.file',
                'title'         => 'Brochure PDF File', // use translation
                'type'          => 'file',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ]
        ]
    ]
];
