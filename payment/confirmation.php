<?php
/**
 * Bank transfer & cash — instructions after pledge recorded
 */

require_once dirname(__DIR__) . '/includes/config.php';

$donationId = (int) ($_GET['donation_id'] ?? 0);
$method = $_GET['method'] ?? 'bank-transfer';

if (!$donationId) {
    header('Location: ' . SITE_URL . '/donation.php');
    exit;
}

$donation = getDonationById($pdo, $donationId);
if (!$donation) {
    header('Location: ' . SITE_URL . '/donation.php');
    exit;
}

$pageTitle = 'Complete Your Donation';
$isBank = $method === 'bank-transfer';
$refCode = 'ZC-' . str_pad($donation['id'], 6, '0', STR_PAD_LEFT);

include dirname(__DIR__) . '/includes/header.php';
?>

<section class="section" style="padding-top: 120px;">
    <div class="container" style="max-width: 800px; margin: 0 auto;">
        <div class="payment-result payment-result--pending">
            <div class="payment-result__icon"><i class="fas fa-info-circle"></i></div>
            <h1><?php echo $isBank ? 'Bank Transfer Instructions' : 'Cash Donation Instructions'; ?></h1>
            <p>Your donation pledge of <strong><?php echo formatCurrency($donation['amount']); ?></strong> has been recorded. Please complete your payment using the details below.</p>

            <div class="payment-receipt">
                <p><strong>Donation reference (required):</strong> <span class="payment-ref"><?php echo htmlspecialchars($refCode); ?></span></p>
                <p><strong>Amount:</strong> <?php echo formatCurrency($donation['amount']); ?></p>
                <p><strong>Status:</strong> Pending — awaiting payment</p>
            </div>

            <?php if ($isBank): ?>
            <div class="payment-instructions">
                <h2><i class="fas fa-university"></i> Bank Details</h2>
                <ul class="payment-bank-list">
                    <li><strong>Bank name:</strong> <?php echo htmlspecialchars(BANK_NAME); ?></li>
                    <li><strong>Account name:</strong> <?php echo htmlspecialchars(BANK_ACCOUNT_NAME); ?></li>
                    <li><strong>Account number:</strong> <?php echo htmlspecialchars(BANK_ACCOUNT_NUMBER); ?></li>
                    <li><strong>Routing / sort code:</strong> <?php echo htmlspecialchars(BANK_ROUTING); ?></li>
                    <?php if (BANK_IBAN !== ''): ?>
                    <li><strong>IBAN:</strong> <?php echo htmlspecialchars(BANK_IBAN); ?></li>
                    <?php endif; ?>
                    <?php if (BANK_SWIFT !== ''): ?>
                    <li><strong>SWIFT / BIC:</strong> <?php echo htmlspecialchars(BANK_SWIFT); ?></li>
                    <?php endif; ?>
                </ul>
                <p class="payment-instructions__note">Include reference <strong><?php echo htmlspecialchars($refCode); ?></strong> in the transfer description so we can match your donation. We will mark it as completed once received (usually within 2–5 business days).</p>
            </div>
            <?php else: ?>
            <div class="payment-instructions">
                <h2><i class="fas fa-money-bill-wave"></i> Cash Donation</h2>
                <p><?php echo nl2br(htmlspecialchars(CASH_INSTRUCTIONS)); ?></p>
                <p class="payment-instructions__note">Please mention reference <strong><?php echo htmlspecialchars($refCode); ?></strong> when you visit so we can record your gift.</p>
            </div>
            <?php endif; ?>

            <div class="payment-result__actions">
                <a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-donate">Back to Home</a>
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-secondary">Questions? Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
