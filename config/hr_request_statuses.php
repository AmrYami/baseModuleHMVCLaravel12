<?php

/**
 * Request statuses: string key => ['code' => int, 'label' => string]
 * Codes are stored in the DB; labels are for display.
 */
return [
    'draft' => [
        'code'  => 0,
        'label' => 'Draft',
    ],
    'submitted' => [
        'code'  => 1,
        'label' => 'Submitted',
    ],
    'approved' => [
        'code'  => 2,
        'label' => 'Approved',
    ],
    'partial_approved' => [
        'code'  => 3,
        'label' => 'Partially Approved',
    ],
    'rejected' => [
        'code'  => 4,
        'label' => 'Rejected',
    ],
];
