<?php

return [
    'default_locale' => 'en',

    'sources' => [
        'countries' => [
            'model' => \App\Models\CountryModel::class,
            'label' => ['column' => 'name', 'json_locale' => false],
            'value' => ['column' => 'key'],
            'order' => ['column' => 'name->en', 'direction' => 'asc'],
            'active' => ['column' => null],
        ],
    ],
];

