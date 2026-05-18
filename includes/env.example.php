<?php
/**
 * Environment configuration — PRODUCTION TEMPLATE
 *
 * 1. Copy this file to env.php in the same folder
 * 2. Fill in your real values
 * 3. Never commit env.php to a public repository
 */
return [
    // local | production
    'app_env' => 'production',

    // Database (create a dedicated user on your host, not root)
    'db_host' => 'localhost',
    'db_name' => 'your_database_name',
    'db_user' => 'your_database_user',
    'db_pass' => 'your_strong_database_password',
    'db_charset' => 'utf8mb4',

    // Site — must match your live URL with https://
    'site_name' => 'Zionite Charity',
    'site_url' => 'https://www.yourdomain.com',
    'admin_email' => 'Charityzionite@gmail.com',
    'contact_address' => 'Morphou, Guzerlyurt, North Cyprus',
    'contact_phone' => '+905338326112',
    'contact_email' => 'Charityzionite@gmail.com',

    // Security — generate a long random string
    'security_maintenance_token' => 'CHANGE-TO-RANDOM-64-CHAR-STRING',

    // Stripe (https://dashboard.stripe.com/apikeys) — use live keys in production
    'stripe_secret_key' => '',
    'stripe_publishable_key' => '',

    // PayPal (https://developer.paypal.com) — set mode to live in production
    'paypal_client_id' => '',
    'paypal_client_secret' => '',
    'paypal_mode' => 'live',

    // Bank transfer (shown after donation)
    'bank_name' => 'Your Bank Name',
    'bank_account_name' => 'Zionite Charity',
    'bank_account_number' => '',
    'bank_routing' => '',
    'bank_iban' => '',
    'bank_swift' => '',

    'cash_instructions' => 'Cash donations can be made in person at Morphou, Guzerlyurt, North Cyprus. Office hours: Monday–Friday, 9:00 AM – 5:00 PM.',

    'donation_currency' => 'usd',
    'donation_currency_symbol' => '$',
];
