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
        'specialities' => [
            'model' => \App\Models\SpecialityModel::class,
            'label' => ['column' => 'name', 'json_locale' => false],
            'value' => ['column' => 'id'],
            'order' => ['column' => 'name->en', 'direction' => 'asc'],
            'active' => ['column' => null],
        ],
    ],
];

