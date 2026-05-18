/**
 * Donation form — amount selection, payment method UI
 */
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('donation-form');
    if (!form) return;

    const amountInput = document.getElementById('custom-amount');
    const options = document.querySelectorAll('.donation-option');
    const paymentRadios = form.querySelectorAll('input[name="payment_method"]');
    const infoBox = document.getElementById('payment-method-info');
    const summaryAmount = document.getElementById('summary-amount');
    const submitText = document.getElementById('submit-btn-text');
    const cfg = window.DONATION_CONFIG || { currencySymbol: '$' };

    function setAmount(amount) {
        amountInput.value = amount;
        options.forEach(function (opt) {
            opt.classList.toggle('active', parseFloat(opt.dataset.amount) === parseFloat(amount));
        });
        updateSummary();
    }

    function updateSummary() {
        const val = parseFloat(amountInput.value) || 0;
        summaryAmount.textContent = cfg.currencySymbol + val.toFixed(2);
    }

    function getSelectedPayment() {
        const checked = form.querySelector('input[name="payment_method"]:checked');
        return checked ? checked.value : 'credit-card';
    }

    function updatePaymentUI() {
        const method = getSelectedPayment();
        document.querySelectorAll('.payment-method-card').forEach(function (card) {
            const input = card.querySelector('input[type="radio"]');
            card.classList.toggle('payment-method-card--selected', input && input.checked);
        });

        const labels = {
            'credit-card': 'Continue to Secure Card Payment',
            paypal: 'Continue to PayPal',
            'bank-transfer': 'Get Bank Transfer Instructions',
            cash: 'Get Cash Donation Instructions'
        };
        submitText.textContent = labels[method] || 'Continue to Payment';

        if (!infoBox) return;

        if (method === 'bank-transfer') {
            infoBox.hidden = false;
            infoBox.innerHTML = '<p><i class="fas fa-info-circle"></i> After submitting, you will receive bank details and a unique reference code to include with your transfer to <strong>' + escapeHtml(cfg.bankName || 'our bank') + '</strong>.</p>';
        } else if (method === 'cash') {
            infoBox.hidden = false;
            infoBox.innerHTML = '<p><i class="fas fa-info-circle"></i> After submitting, you will receive instructions for making a cash donation in person, including your reference number.</p>';
        } else {
            infoBox.hidden = true;
            infoBox.innerHTML = '';
        }
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    options.forEach(function (opt) {
        opt.addEventListener('click', function () {
            setAmount(opt.dataset.amount);
        });
        opt.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                setAmount(opt.dataset.amount);
            }
        });
    });

    amountInput.addEventListener('input', function () {
        options.forEach(function (opt) {
            opt.classList.remove('active');
        });
        updateSummary();
    });

    paymentRadios.forEach(function (radio) {
        radio.addEventListener('change', updatePaymentUI);
    });

    const initial = parseFloat(amountInput.value);
    if (initial) {
        options.forEach(function (opt) {
            if (parseFloat(opt.dataset.amount) === initial) {
                opt.classList.add('active');
            }
        });
    }

    updateSummary();
    updatePaymentUI();
});
