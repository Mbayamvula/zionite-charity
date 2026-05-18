<?php
/**
 * Payment cancelled by donor
 */

require_once dirname(__DIR__) . '/includes/config.php';

$donationId = (int) ($_GET['donation_id'] ?? $_SESSION['pending_donation_id'] ?? 0);

if ($donationId) {
    $donation = getDonationById($pdo, $donationId);
    if ($donation && $donation['status'] === 'pending') {
        updateDonationPayment($pdo, $donationId, 'failed');
    }
}

unset($_SESSION['pending_donation_id'], $_SESSION['paypal_order_id']);

$pageTitle = 'Payment Cancelled';
include dirname(__DIR__) . '/includes/header.php';
?>

<section class="section" style="padding-top: 120px;">
    <div class="container" style="max-width: 720px; margin: 0 auto;">
        <div class="payment-result payment-result--cancel">
            <div class="payment-result__icon"><i class="fas fa-times-circle"></i></div>
            <h1>Payment Cancelled</h1>
            <p>Your donation was not completed. No charge has been made.</p>
            <div class="payment-result__actions">
                <a href="<?php echo SITE_URL; ?>/donation.php" class="btn btn-donate">Return to Donation Page</a>
                <a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>
</section>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
