<?php
/**
 * Payment success — Stripe Checkout return or PayPal capture
 */

require_once dirname(__DIR__) . '/includes/config.php';

$pageTitle = 'Donation Successful';
$donation = null;
$error = '';

$donationId = (int) ($_GET['donation_id'] ?? $_SESSION['pending_donation_id'] ?? 0);
$provider = $_GET['provider'] ?? '';

if ($provider === 'stripe' && !empty($_GET['session_id']) && $donationId) {
    $result = completeStripeDonation($pdo, $_GET['session_id'], $donationId);
    if ($result['success']) {
        $donation = $result['donation'];
        unset($_SESSION['pending_donation_id']);
    } else {
        $error = $result['error'] ?? 'Could not verify your payment.';
    }
} elseif ($provider === 'paypal' && $donationId) {
    $orderId = $_GET['token'] ?? $_SESSION['paypal_order_id'] ?? '';
    if ($orderId) {
        $result = capturePayPalDonation($pdo, $orderId, $donationId);
        if ($result['success']) {
            $donation = $result['donation'];
            unset($_SESSION['pending_donation_id'], $_SESSION['paypal_order_id']);
        } else {
            $error = $result['error'] ?? 'Could not complete PayPal payment.';
        }
    } else {
        $error = 'Missing PayPal payment reference.';
    }
} else {
    $donation = $donationId ? getDonationById($pdo, $donationId) : null;
    if ($donation && $donation['status'] === 'completed') {
        // Already completed (page refresh)
    } elseif (!$donation) {
        $error = 'Donation not found.';
    }
}

include dirname(__DIR__) . '/includes/header.php';
?>

<section class="section" style="padding-top: 120px;">
    <div class="container" style="max-width: 720px; margin: 0 auto;">
        <?php if ($donation && $donation['status'] === 'completed'): ?>
        <div class="payment-result payment-result--success">
            <div class="payment-result__icon"><i class="fas fa-check-circle"></i></div>
            <h1>Thank You for Your Donation!</h1>
            <p>Your generous gift of <strong><?php echo formatCurrency($donation['amount']); ?></strong> has been received successfully.</p>
            <div class="payment-receipt">
                <p><strong>Reference:</strong> #<?php echo str_pad($donation['id'], 6, '0', STR_PAD_LEFT); ?></p>
                <p><strong>Payment method:</strong> <?php echo htmlspecialchars(getPaymentMethodLabel($donation['payment_method'])); ?></p>
                <?php if (!empty($donation['transaction_id'])): ?>
                <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($donation['transaction_id']); ?></p>
                <?php endif; ?>
                <p><strong>Date:</strong> <?php echo formatDate($donation['created_at']); ?></p>
            </div>
            <p class="payment-result__note">A confirmation may be sent to your email if you provided one. Your support helps us continue our humanitarian work.</p>
            <div class="payment-result__actions">
                <a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-donate">Back to Home</a>
                <a href="<?php echo SITE_URL; ?>/projects.php" class="btn btn-secondary">See Our Projects</a>
            </div>
        </div>
        <?php else: ?>
        <div class="payment-result payment-result--error">
            <div class="payment-result__icon"><i class="fas fa-exclamation-circle"></i></div>
            <h1>Payment Could Not Be Confirmed</h1>
            <p><?php echo htmlspecialchars($error ?: 'Something went wrong while processing your payment.'); ?></p>
            <div class="payment-result__actions">
                <a href="<?php echo SITE_URL; ?>/donation.php" class="btn btn-donate">Try Again</a>
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-secondary">Contact Us</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
