<?php

return [
    'url'              => env('SAP_URL', 'https://bomba-food-sas-6lz9tg16.it-cpi034-rt.cfapps.us10-002.hana.ondemand.com/http/Purchase_Order_test/PurchaseOrder'),
    'token'            => env('SAP_TOKEN', 'c2ItZmM5ODQzZTctYjZjMS00ZTJhLWFjMzQtOGYyZGM3ZWZjYTYzIWI0MzIxNTJ8aXQtcnQtYm9tYmEtZm9vZC1zYXMtNmx6OXRnMTYhYjQxMDMzNDpkNWM0NzcwYS00MTRmLTRkYWQtYjZiZi0yZmZlMmE4M2Q2MTIkSzNBa3pOS0F6N19YeUh5ei14blhzeG9ZRTNjQjhqM2tYY3BxbndTU193QT0='),
    'purchasing_org'   => env('SAP_PURCHASING_ORG', '4810'),
    'purchasing_group' => env('SAP_PURCHASING_GROUP', '001'),
    'currency'         => env('SAP_CURRENCY', 'COP'),
    'payment_terms'    => env('SAP_PAYMENT_TERMS', 'NT00'),
    'company_code'     => env('SAP_COMPANY_CODE', '4810'),
    'cost_center'      => env('SAP_COST_CENTER', '4810V00055'),
];
