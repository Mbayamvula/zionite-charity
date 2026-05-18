<?php
/**
 * Process donation and redirect to the selected payment provider
 */

require_once dirname(__DIR__) . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/donation.php');
    exit;
}

securityRequireCsrf();
securityCheckHoneypot();

if (!securityRateLimit('donation_form', 15, 3600)) {
    $_SESSION['donation_errors'] = ['Too many donation attempts. Please try again later.'];
    header('Location: ' . SITE_URL . '/donation.php');
    exit;
}

$donorName = trim($_POST['donor_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$amount = (float) ($_POST['amount'] ?? 0);
$donationType = $_POST['donation_type'] ?? 'one-time';
$paymentMethod = $_POST['payment_method'] ?? 'credit-card';
$purpose = trim($_POST['purpose'] ?? '');
$isAnonymous = isset($_POST['is_anonymous']) ? 1 : 0;
$messageText = trim($_POST['message'] ?? '');

$allowedMethods = ['credit-card', 'paypal', 'bank-transfer', 'cash'];
$allowedTypes = ['one-time', 'monthly'];

if (!in_array($paymentMethod, $allowedMethods, true)) {
    $paymentMethod = 'credit-card';
}
if (!in_array($donationType, $allowedTypes, true)) {
    $donationType = 'one-time';
}

$errors = [];
if ($donorName === '') {
    $errors[] = 'Full name is required.';
}
if ($amount < 1) {
    $errors[] = 'Please enter a donation amount of at least ' . DONATION_CURRENCY_SYMBOL . '1.';
}
if ($paymentMethod === 'credit-card' && !isStripeConfigured()) {
    $errors[] = 'Card payments are temporarily unavailable. Please choose PayPal, bank transfer, or cash.';
}
if ($paymentMethod === 'paypal' && !isPayPalConfigured()) {
    $errors[] = 'PayPal is temporarily unavailable. Please choose another payment method.';
}

if ($errors) {
    $_SESSION['donation_errors'] = $errors;
    $_SESSION['donation_form'] = $_POST;
    header('Location: ' . SITE_URL . '/donation.php');
    exit;
}

$donationId = createPendingDonation($pdo, [
    'donor_name' => $donorName,
    'email' => filter_var($email, FILTER_SANITIZE_EMAIL),
    'phone' => $phone,
    'amount' => $amount,
    'donation_type' => $donationType,
    'payment_method' => $paymentMethod,
    'purpose' => $purpose,
    'is_anonymous' => $isAnonymous,
    'message' => $messageText,
]);

if (!$donationId) {
    $_SESSION['donation_errors'] = ['Could not save your donation. Please try again.'];
    $_SESSION['donation_form'] = $_POST;
    header('Location: ' . SITE_URL . '/donation.php');
    exit;
}

$donation = getDonationById($pdo, $donationId);
$_SESSION['pending_donation_id'] = $donationId;

switch ($paymentMethod) {
    case 'credit-card':
        $result = createStripeCheckoutSession($pdo, $donation);
        if (isset($result['error'])) {
            updateDonationPayment($pdo, $donationId, 'failed');
            $_SESSION['donation_errors'] = [$result['error']];
            $_SESSION['donation_form'] = $_POST;
            header('Location: ' . SITE_URL . '/donation.php');
            exit;
        }
        header('Location: ' . $result['url']);
        exit;

    case 'paypal':
        $result = createPayPalOrder($pdo, $donation);
        if (isset($result['error'])) {
            updateDonationPayment($pdo, $donationId, 'failed');
            $_SESSION['donation_errors'] = [$result['error']];
            $_SESSION['donation_form'] = $_POST;
            header('Location: ' . SITE_URL . '/donation.php');
            exit;
        }
        $_SESSION['paypal_order_id'] = $result['order_id'];
        header('Location: ' . $result['url']);
        exit;

    case 'bank-transfer':
    case 'cash':
        header('Location: ' . getPaymentBaseUrl() . '/confirmation.php?donation_id=' . $donationId . '&method=' . urlencode($paymentMethod));
        exit;

    default:
        header('Location: ' . SITE_URL . '/donation.php');
        exit;
}
