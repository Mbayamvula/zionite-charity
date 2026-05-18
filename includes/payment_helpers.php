<?php
/**
 * Payment helpers — Stripe Checkout, PayPal, bank transfer, cash
 */

/**
 * Ensure donations table has payment tracking columns
 */
function ensureDonationPaymentColumns(PDO $pdo) {
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $columns = [
        'payment_reference' => "ALTER TABLE donations ADD COLUMN payment_reference VARCHAR(255) NULL AFTER status",
        'transaction_id' => "ALTER TABLE donations ADD COLUMN transaction_id VARCHAR(255) NULL AFTER payment_reference",
    ];

    foreach ($columns as $column => $sql) {
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM donations LIKE " . $pdo->quote($column));
            if ($stmt->rowCount() === 0) {
                $pdo->exec($sql);
            }
        } catch (PDOException $e) {
            // Column may already exist or table missing
        }
    }
}

/**
 * @param PDO $pdo
 * @param array $data
 * @return int|false Donation ID
 */
function createPendingDonation(PDO $pdo, array $data) {
    ensureDonationPaymentColumns($pdo);

    $stmt = $pdo->prepare(
        "INSERT INTO donations (donor_name, email, phone, amount, donation_type, payment_method, purpose, is_anonymous, message, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
    );

    $ok = $stmt->execute([
        $data['donor_name'],
        $data['email'] ?? '',
        $data['phone'] ?? '',
        $data['amount'],
        $data['donation_type'] ?? 'one-time',
        $data['payment_method'],
        $data['purpose'] ?? '',
        $data['is_anonymous'] ?? 0,
        $data['message'] ?? '',
    ]);

    return $ok ? (int) $pdo->lastInsertId() : false;
}

/**
 * @param PDO $pdo
 * @param int $id
 * @return array|null
 */
function getDonationById(PDO $pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM donations WHERE id = ?");
    $stmt->execute([(int) $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * @param PDO $pdo
 * @param int $id
 * @param string $status
 * @param string|null $paymentReference
 * @param string|null $transactionId
 */
function updateDonationPayment(PDO $pdo, $id, $status, $paymentReference = null, $transactionId = null) {
    ensureDonationPaymentColumns($pdo);

    $stmt = $pdo->prepare(
        "UPDATE donations SET status = ?, payment_reference = COALESCE(?, payment_reference), transaction_id = COALESCE(?, transaction_id) WHERE id = ?"
    );
    $stmt->execute([$status, $paymentReference, $transactionId, (int) $id]);
}

function isStripeConfigured() {
    return STRIPE_SECRET_KEY !== '' && STRIPE_PUBLISHABLE_KEY !== '';
}

function isPayPalConfigured() {
    return PAYPAL_CLIENT_ID !== '' && PAYPAL_CLIENT_SECRET !== '';
}

function getPaymentBaseUrl() {
    return rtrim(SITE_URL, '/') . '/payment';
}

/**
 * Create Stripe Checkout Session and return checkout URL
 * @return array{url: string, session_id: string}|array{error: string}
 */
function createStripeCheckoutSession(PDO $pdo, array $donation) {
    if (!isStripeConfigured()) {
        return ['error' => 'Credit card payments are not configured yet. Please use PayPal, bank transfer, or cash, or contact the administrator.'];
    }

    $amountCents = (int) round((float) $donation['amount'] * 100);
    if ($amountCents < 50) {
        return ['error' => 'Minimum donation amount is $0.50.'];
    }

    $donationId = (int) $donation['id'];
    $purposeLabel = $donation['purpose'] ? str_replace('-', ' ', ucfirst($donation['purpose'])) : 'General Fund';

    $params = [
        'mode' => 'payment',
        'success_url' => getPaymentBaseUrl() . '/success.php?provider=stripe&donation_id=' . $donationId . '&session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => getPaymentBaseUrl() . '/cancel.php?donation_id=' . $donationId,
        'client_reference_id' => (string) $donationId,
        'customer_email' => $donation['email'] ?: null,
        'line_items[0][price_data][currency]' => DONATION_CURRENCY,
        'line_items[0][price_data][unit_amount]' => $amountCents,
        'line_items[0][price_data][product_data][name]' => SITE_NAME . ' — ' . $purposeLabel,
        'line_items[0][price_data][product_data][description]' => 'Donation #' . $donationId,
        'line_items[0][quantity]' => 1,
        'payment_method_types[0]' => 'card',
        'metadata[donation_id]' => (string) $donationId,
    ];

    if (empty($params['customer_email'])) {
        unset($params['customer_email']);
    }

    $response = stripeApiRequest('POST', 'checkout/sessions', $params);
    if (isset($response['error'])) {
        return ['error' => $response['error']];
    }

    if (empty($response['url']) || empty($response['id'])) {
        return ['error' => 'Unable to start card payment. Please try again.'];
    }

    updateDonationPayment($pdo, $donationId, 'pending', $response['id'], null);

    return ['url' => $response['url'], 'session_id' => $response['id']];
}

/**
 * Verify Stripe session and mark donation completed
 */
function completeStripeDonation(PDO $pdo, $sessionId, $donationId) {
    $response = stripeApiRequest('GET', 'checkout/sessions/' . urlencode($sessionId));
    if (isset($response['error'])) {
        return ['success' => false, 'error' => $response['error']];
    }

    $refDonationId = (int) ($response['metadata']['donation_id'] ?? $response['client_reference_id'] ?? 0);
    if ($refDonationId !== (int) $donationId) {
        return ['success' => false, 'error' => 'Donation reference mismatch.'];
    }

    if (($response['payment_status'] ?? '') === 'paid') {
        $txn = $response['payment_intent'] ?? $sessionId;
        updateDonationPayment($pdo, $donationId, 'completed', $sessionId, $txn);
        return ['success' => true, 'donation' => getDonationById($pdo, $donationId)];
    }

    return ['success' => false, 'error' => 'Payment was not completed.'];
}

/**
 * @return array|array{error: string}
 */
function createPayPalOrder(PDO $pdo, array $donation) {
    if (!isPayPalConfigured()) {
        return ['error' => 'PayPal is not configured yet. Please use another payment method or contact the administrator.'];
    }

    $amount = number_format((float) $donation['amount'], 2, '.', '');
    $donationId = (int) $donation['id'];
    $base = getPaymentBaseUrl();

    $body = [
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'reference_id' => 'donation_' . $donationId,
            'description' => SITE_NAME . ' donation #' . $donationId,
            'amount' => [
                'currency_code' => strtoupper(DONATION_CURRENCY),
                'value' => $amount,
            ],
        ]],
        'application_context' => [
            'brand_name' => SITE_NAME,
            'landing_page' => 'NO_PREFERENCE',
            'user_action' => 'PAY_NOW',
            'return_url' => $base . '/success.php?provider=paypal&donation_id=' . $donationId,
            'cancel_url' => $base . '/cancel.php?donation_id=' . $donationId,
        ],
    ];

    $response = paypalApiRequest('POST', '/v2/checkout/orders', $body);
    if (isset($response['error'])) {
        return ['error' => $response['error']];
    }

    $approveUrl = null;
    foreach ($response['links'] ?? [] as $link) {
        if (($link['rel'] ?? '') === 'approve') {
            $approveUrl = $link['href'];
            break;
        }
    }

    if (!$approveUrl || empty($response['id'])) {
        return ['error' => 'Unable to start PayPal payment.'];
    }

    updateDonationPayment($pdo, $donationId, 'pending', $response['id'], null);

    return ['url' => $approveUrl, 'order_id' => $response['id']];
}

/**
 * Capture PayPal order after donor approval
 */
function capturePayPalDonation(PDO $pdo, $orderId, $donationId) {
    $response = paypalApiRequest('POST', '/v2/checkout/orders/' . urlencode($orderId) . '/capture', []);
    if (isset($response['error'])) {
        return ['success' => false, 'error' => $response['error']];
    }

    if (($response['status'] ?? '') === 'COMPLETED') {
        $captureId = $response['purchase_units'][0]['payments']['captures'][0]['id'] ?? $orderId;
        updateDonationPayment($pdo, $donationId, 'completed', $orderId, $captureId);
        return ['success' => true, 'donation' => getDonationById($pdo, $donationId)];
    }

    return ['success' => false, 'error' => 'PayPal payment was not completed.'];
}

function stripeApiRequest($method, $endpoint, $params = []) {
    $url = 'https://api.stripe.com/v1/' . ltrim($endpoint, '/');

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $method === 'GET' && $params ? $url . '?' . http_build_query($params) : $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => STRIPE_SECRET_KEY . ':',
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    ]);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }

    $raw = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($raw, true);
    if ($code >= 400 || isset($data['error'])) {
        return ['error' => $data['error']['message'] ?? 'Stripe payment error.'];
    }

    return $data;
}

function getPayPalAccessToken() {
    static $token = null;
    static $expires = 0;

    if ($token && time() < $expires) {
        return $token;
    }

    $base = PAYPAL_MODE === 'live'
        ? 'https://api-m.paypal.com'
        : 'https://api-m.sandbox.paypal.com';

    $ch = curl_init($base . '/v1/oauth2/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_USERPWD => PAYPAL_CLIENT_ID . ':' . PAYPAL_CLIENT_SECRET,
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
        CURLOPT_HTTPHEADER => ['Accept: application/json', 'Accept-Language: en_US'],
    ]);

    $raw = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($raw, true);

    if (empty($data['access_token'])) {
        return null;
    }

    $token = $data['access_token'];
    $expires = time() + (int) ($data['expires_in'] ?? 3000) - 60;
    return $token;
}

function paypalApiRequest($method, $path, $body = null) {
    $token = getPayPalAccessToken();
    if (!$token) {
        return ['error' => 'PayPal authentication failed. Check your API credentials.'];
    }

    $base = PAYPAL_MODE === 'live'
        ? 'https://api-m.paypal.com'
        : 'https://api-m.sandbox.paypal.com';

    $ch = curl_init($base . $path);
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token,
    ];

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
    ]);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body === [] ? '{}' : json_encode($body));
    }

    $raw = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($raw, true);
    if ($code >= 400) {
        $msg = $data['message'] ?? ($data['details'][0]['description'] ?? 'PayPal payment error.');
        return ['error' => $msg];
    }

    return $data ?: [];
}

function getPaymentMethodLabel($method) {
    $labels = [
        'credit-card' => 'Credit / Debit Card',
        'paypal' => 'PayPal',
        'bank-transfer' => 'Bank Transfer',
        'cash' => 'Cash',
    ];
    return $labels[$method] ?? ucfirst(str_replace('-', ' ', $method));
}
