<?php

return [
    'sheet_json_url' => env('GOOGLE_SHEETS_FEEDBACK_JSON_URL'),
    'form_url' => env('GOOGLE_FEEDBACK_FORM_URL'),
    'qr_image' => env('GOOGLE_FEEDBACK_QR_IMAGE', 'images/feedback-qr-placeholder.svg'),
];
