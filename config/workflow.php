<?php

return [
    'email_evidence' => [
        'mode' => env('EMAIL_EVIDENCE_MODE', 'both'), // link_only | snapshot | both
        'max_attachment_bytes' => (int) env('EMAIL_EVIDENCE_MAX_ATTACHMENT', 25 * 1024 * 1024), // 25 MB
    ],
];

