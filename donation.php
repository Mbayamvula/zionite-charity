<?php
/**
 * Zionite Charity - Donation Page
 * Donation form with Stripe, PayPal, bank transfer, and cash
 */

require_once 'includes/config.php';

$pageTitle = t('nav_donate');
$pageDescription = 'Support Zionite Charity\'s humanitarian work by making a donation. Your contribution helps us provide emotional support, food assistance, clothing, hospital visits, and more to those in need.';

$selectedAmount = isset($_GET['amount']) ? $_GET['amount'] : '';
$formData = $_SESSION['donation_form'] ?? [];
$errors = $_SESSION['donation_errors'] ?? [];
unset($_SESSION['donation_form'], $_SESSION['donation_errors']);

$defaultPayment = isStripeConfigured() ? 'credit-card' : (isPayPalConfigured() ? 'paypal' : 'bank-transfer');
$selectedPayment = $formData['payment_method'] ?? $defaultPayment;

$fd = function ($key, $default = '') use ($formData, $selectedAmount) {
    if ($key === 'amount' && isset($formData['amount'])) {
        return htmlspecialchars($formData['amount']);
    }
    if ($key === 'amount' && $selectedAmount !== '') {
        return htmlspecialchars($selectedAmount);
    }
    return htmlspecialchars($formData[$key] ?? $default);
};

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="hero hero--photo" style="padding: 120px 0 80px; background-image: url('<?php echo SITE_URL; ?>/assets/images/Disaster Relief Fund.jpg');">
    <div class="container">
        <div class="hero-content">
            <h1><?php echo t('donation_title'); ?></h1>
            <p><?php echo t('donation_subtitle'); ?></p>
        </div>
    </div>
</section>

<!-- Donation Form Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('cta_donate'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('donation_subtitle'); ?></p>
        </div>

        <?php if ($errors): ?>
        <div class="alert alert-error" style="max-width: 800px; margin: 0 auto 25px;">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($errors as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div style="max-width: 800px; margin: 0 auto;">
            <form id="donation-form" method="POST" action="<?php echo SITE_URL; ?>/payment/process.php" class="donation-form-card">
                <?php echo csrfField(); ?>
                <?php echo securityHoneypotField(); ?>
                <!-- Donation Amount -->
                <div class="form-block">
                    <label class="form-block__label">Select Donation Amount</label>
                    <div class="donation-options">
                        <div class="donation-option" data-amount="25" role="button" tabindex="0">
                            <h3>$25</h3>
                            <span>Provides meals for a family</span>
                        </div>
                        <div class="donation-option" data-amount="50" role="button" tabindex="0">
                            <h3>$50</h3>
                            <span>Supports hospital visits</span>
                        </div>
                        <div class="donation-option" data-amount="100" role="button" tabindex="0">
                            <h3>$100</h3>
                            <span>Helps orphanage support</span>
                        </div>
                        <div class="donation-option" data-amount="250" role="button" tabindex="0">
                            <h3>$250</h3>
                            <span>Sponsors a project</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="custom-amount">Or Enter Custom Amount (<?php echo DONATION_CURRENCY_SYMBOL; ?>) *</label>
                        <input type="number" id="custom-amount" name="amount" class="form-control" min="1" step="0.01" required value="<?php echo $fd('amount'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="donation_type">Donation Type</label>
                    <select id="donation_type" name="donation_type" class="form-control">
                        <option value="one-time" <?php echo ($formData['donation_type'] ?? '') === 'monthly' ? '' : 'selected'; ?>>One-Time Donation</option>
                        <option value="monthly" <?php echo ($formData['donation_type'] ?? '') === 'monthly' ? 'selected' : ''; ?>>Monthly Donation</option>
                    </select>
                    <small class="form-hint">Monthly gifts are recorded as recurring pledges; online card billing is processed per payment.</small>
                </div>

                <h3 class="form-section-title">Personal Information</h3>

                <div class="form-group">
                    <label for="donor_name">Full Name *</label>
                    <input type="text" id="donor_name" name="donor_name" class="form-control" required value="<?php echo $fd('donor_name'); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $fd('email'); ?>" placeholder="For receipt & confirmation">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo $fd('phone'); ?>">
                    </div>
                </div>

                <!-- Payment Method -->
                <h3 class="form-section-title">Payment Method *</h3>
                <div class="payment-methods">
                    <label class="payment-method-card <?php echo !isStripeConfigured() ? 'payment-method-card--disabled' : ''; ?>">
                        <input type="radio" name="payment_method" value="credit-card" <?php echo $selectedPayment === 'credit-card' ? 'checked' : ''; ?> <?php echo !isStripeConfigured() ? 'disabled' : ''; ?>>
                        <span class="payment-method-card__icon"><i class="fas fa-credit-card"></i></span>
                        <span class="payment-method-card__title">Credit / Debit Card</span>
                        <span class="payment-method-card__desc">Secure payment via Stripe</span>
                        <?php if (!isStripeConfigured()): ?>
                        <span class="payment-method-card__badge">Setup required</span>
                        <?php endif; ?>
                    </label>

                    <label class="payment-method-card <?php echo !isPayPalConfigured() ? 'payment-method-card--disabled' : ''; ?>">
                        <input type="radio" name="payment_method" value="paypal" <?php echo $selectedPayment === 'paypal' ? 'checked' : ''; ?> <?php echo !isPayPalConfigured() ? 'disabled' : ''; ?>>
                        <span class="payment-method-card__icon"><i class="fab fa-paypal"></i></span>
                        <span class="payment-method-card__title">PayPal</span>
                        <span class="payment-method-card__desc">Pay with your PayPal account</span>
                        <?php if (!isPayPalConfigured()): ?>
                        <span class="payment-method-card__badge">Setup required</span>
                        <?php endif; ?>
                    </label>

                    <label class="payment-method-card">
                        <input type="radio" name="payment_method" value="bank-transfer" <?php echo $selectedPayment === 'bank-transfer' ? 'checked' : ''; ?>>
                        <span class="payment-method-card__icon"><i class="fas fa-university"></i></span>
                        <span class="payment-method-card__title">Bank Transfer</span>
                        <span class="payment-method-card__desc">Wire or direct deposit</span>
                    </label>

                    <label class="payment-method-card">
                        <input type="radio" name="payment_method" value="cash" <?php echo $selectedPayment === 'cash' ? 'checked' : ''; ?>>
                        <span class="payment-method-card__icon"><i class="fas fa-money-bill-wave"></i></span>
                        <span class="payment-method-card__title">Cash</span>
                        <span class="payment-method-card__desc">In-person donation</span>
                    </label>
                </div>

                <div id="payment-method-info" class="payment-method-info" hidden></div>

                <div class="form-group">
                    <label for="purpose">Donation Purpose (Optional)</label>
                    <select id="purpose" name="purpose" class="form-control">
                        <?php
                        $purposes = [
                            '' => 'General Fund - Where Needed Most',
                            'food-assistance' => 'Food Assistance',
                            'hospital-visits' => 'Hospital Visits',
                            'orphanage-support' => 'Orphanage Support',
                            'elderly-care' => 'Elderly Care',
                            'clothing' => 'Clothing Support',
                            'emotional-support' => 'Emotional Support',
                        ];
                        $selPurpose = $formData['purpose'] ?? '';
                        foreach ($purposes as $val => $label):
                        ?>
                        <option value="<?php echo htmlspecialchars($val); ?>" <?php echo $selPurpose === $val ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_anonymous" value="1" <?php echo !empty($formData['is_anonymous']) ? 'checked' : ''; ?>>
                        <span>Make this donation anonymous</span>
                    </label>
                </div>

                <div class="form-group">
                    <label for="message">Personal Message (Optional)</label>
                    <textarea id="message" name="message" class="form-control" rows="3" placeholder="Add a personal message with your donation..."><?php echo $fd('message'); ?></textarea>
                </div>

                <div class="donation-summary" id="donation-summary">
                    <p>Total: <strong id="summary-amount"><?php echo DONATION_CURRENCY_SYMBOL; ?>0.00</strong></p>
                </div>

                <div class="donation-submit">
                    <button type="submit" class="btn btn-donate btn-donate--lg" id="donate-submit-btn">
                        <i class="fas fa-heart"></i> <span id="submit-btn-text">Continue to Payment</span>
                    </button>
                </div>

                <p class="donation-secure-note">
                    <i class="fas fa-lock"></i> Card and PayPal payments are processed securely. We never store your card details on our servers.
                </p>
            </form>

            <?php if (!isStripeConfigured() || !isPayPalConfigured()): ?>
            <div class="payment-setup-notice">
                <h4><i class="fas fa-cog"></i> Administrator: Enable online payments</h4>
                <p>Add your API keys in <code>includes/payment_config.php</code> to activate card (Stripe) and PayPal. Bank transfer and cash work without API keys.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Donation Impact Section -->
<section class="section" style="background: var(--off-white);">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('donation_impact'); ?></h2>
            <div class="divider"></div>
            <p>See how your contribution makes a real difference in people's lives.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center;">
                <div style="font-size: 3rem; font-weight: 700; color: var(--accent-gold); margin-bottom: 10px;">$25</div>
                <h4 style="margin-bottom: 10px;">Provides Meals</h4>
                <p style="color: var(--gray-600);">Feeds a family of four for one week with essential food supplies.</p>
            </div>
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center;">
                <div style="font-size: 3rem; font-weight: 700; color: var(--accent-gold); margin-bottom: 10px;">$50</div>
                <h4 style="margin-bottom: 10px;">Hospital Visits</h4>
                <p style="color: var(--gray-600);">Supports hospital visit program including gifts and comfort items for patients.</p>
            </div>
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center;">
                <div style="font-size: 3rem; font-weight: 700; color: var(--accent-gold); margin-bottom: 10px;">$100</div>
                <h4 style="margin-bottom: 10px;">Orphanage Support</h4>
                <p style="color: var(--gray-600);">Provides educational materials and supplies for orphanage children.</p>
            </div>
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center;">
                <div style="font-size: 3rem; font-weight: 700; color: var(--accent-gold); margin-bottom: 10px;">$250</div>
                <h4 style="margin-bottom: 10px;">Project Sponsorship</h4>
                <p style="color: var(--gray-600);">Helps fund community projects that benefit hundreds of people.</p>
            </div>
        </div>
    </div>
</section>

<!-- Other Ways to Give Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('donation_other_ways'); ?></h2>
            <div class="divider"></div>
            <p>Beyond monetary donations, there are many ways to support our mission.</p>
        </div>

        <div class="mission-grid">
            <div class="mission-card animate-on-scroll">
                <i class="fas fa-clock"></i>
                <h3>Volunteer Your Time</h3>
                <p>Share your time and skills by volunteering with our programs and activities.</p>
                <a href="volunteer.php" class="btn btn-secondary" style="margin-top: 15px;">Learn More</a>
            </div>
            <div class="mission-card animate-on-scroll">
                <i class="fas fa-box"></i>
                <h3>Donate Goods</h3>
                <p>Contribute clothing, food, toys, and other essential items for those in need.</p>
                <a href="contact.php" class="btn btn-secondary" style="margin-top: 15px;">Contact Us</a>
            </div>
            <div class="mission-card animate-on-scroll">
                <i class="fas fa-building"></i>
                <h3>Corporate Partnership</h3>
                <p>Partner with us as a corporate sponsor and make a lasting community impact.</p>
                <a href="contact.php" class="btn btn-secondary" style="margin-top: 15px;">Partner With Us</a>
            </div>
        </div>
    </div>
</section>

<script>
window.DONATION_CONFIG = {
    currencySymbol: <?php echo json_encode(DONATION_CURRENCY_SYMBOL); ?>,
    bankName: <?php echo json_encode(BANK_NAME); ?>,
    cashNote: <?php echo json_encode(CASH_INSTRUCTIONS); ?>
};
</script>
<script src="<?php echo SITE_URL; ?>/assets/js/donation.js"></script>

<?php include 'includes/footer.php'; ?>
