<?php

return [
    'client_id'     => env('GMAIL_CLIENT_ID'),
    'client_secret' => env('GMAIL_CLIENT_SECRET'),
    'redirect_uri'  => env('GMAIL_REDIRECT_URI', 'https://sap.sibco.info/gmail/callback'),
];
