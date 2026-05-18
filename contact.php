<?php
/**
 * Zionite Charity - Contact Page
 * Contact form using PHP and MySQL
 */

require_once 'includes/config.php';

$pageTitle = t('nav_contact');
$pageDescription = 'Get in touch with Zionite Charity. Contact us for support, volunteering, donations, or any inquiries about our humanitarian work.';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    securityRequireCsrf();
    securityCheckHoneypot();

    if (!securityRateLimit('contact_form', 10, 3600)) {
        $message = 'Too many messages sent. Please try again later.';
        $messageType = 'error';
    } else {
    $name = cleanInput($_POST['name'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $phone = cleanInput($_POST['phone'] ?? '');
    $subject = cleanInput($_POST['subject'] ?? '');
    $messageText = cleanInput($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($messageText)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } else {
        try {
            // Insert contact message into database
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, 'unread')");
            $result = $stmt->execute([$name, $email, $phone, $subject, $messageText]);
            
            if ($result) {
                $message = 'Thank you for contacting us! We have received your message and will get back to you soon.';
                $messageType = 'success';
            } else {
                $message = 'There was an error sending your message. Please try again.';
                $messageType = 'error';
            }
        } catch (PDOException $e) {
            $message = APP_ENV === 'production' ? 'Unable to send your message. Please try again later.' : 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
    }
}

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="hero hero--photo" style="padding: 120px 0 80px; background-image: url('<?php echo SITE_URL; ?>/assets/images/Mobile Health Clinic.jpg');">
    <div class="container">
        <div class="hero-content">
            <h1><?php echo t('contact_title'); ?></h1>
            <p><?php echo t('contact_subtitle'); ?></p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('contact_form_title'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('contact_subtitle'); ?></p>
        </div>
        
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 50px;">
            <!-- Contact Form -->
            <div>
                <form method="POST" action="" style="background: var(--white); padding: 40px; border-radius: 10px; box-shadow: var(--shadow-md);">
                    <?php echo csrfField(); ?>
                    <?php echo securityHoneypotField(); ?>
                    <div class="form-group">
                        <label for="name">Your Name *</label>
                        <input type="text" id="name" name="name" class="form-control" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Your Message *</label>
                        <textarea id="message" name="message" class="form-control" rows="5" required placeholder="How can we help you?"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-donate" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
            
            <!-- Contact Information -->
            <div>
                <div style="background: var(--primary-blue); color: var(--white); padding: 40px; border-radius: 10px; margin-bottom: 30px;">
                    <h3 style="color: var(--white); margin-bottom: 30px;">Contact Information</h3>
                    
                    <div style="margin-bottom: 25px;">
                        <div style="display: flex; gap: 15px; align-items: flex-start;">
                            <i class="fas fa-map-marker-alt" style="color: var(--accent-gold); font-size: 1.25rem; margin-top: 5px;"></i>
                            <div>
                                <h4 style="color: var(--white); margin-bottom: 5px;">Address</h4>
                                <p style="color: var(--gray-200);">123 Charity Street<br>Humanitarian City, HC 12345</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <div style="display: flex; gap: 15px; align-items: flex-start;">
                            <i class="fas fa-phone" style="color: var(--accent-gold); font-size: 1.25rem; margin-top: 5px;"></i>
                            <div>
                                <h4 style="color: var(--white); margin-bottom: 5px;">Phone</h4>
                                <p style="color: var(--gray-200);">+1 (555) 123-4567</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <div style="display: flex; gap: 15px; align-items: flex-start;">
                            <i class="fas fa-envelope" style="color: var(--accent-gold); font-size: 1.25rem; margin-top: 5px;"></i>
                            <div>
                                <h4 style="color: var(--white); margin-bottom: 5px;">Email</h4>
                                <p style="color: var(--gray-200);">info@zionitecharity.org</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div style="display: flex; gap: 15px; align-items: flex-start;">
                            <i class="fas fa-clock" style="color: var(--accent-gold); font-size: 1.25rem; margin-top: 5px;"></i>
                            <div>
                                <h4 style="color: var(--white); margin-bottom: 5px;">Office Hours</h4>
                                <p style="color: var(--gray-200);">Monday - Friday: 9:00 AM - 5:00 PM<br>Saturday: 10:00 AM - 2:00 PM<br>Sunday: Closed</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Social Media -->
                <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md);">
                    <h3 style="margin-bottom: 20px;"><?php echo t('contact_info'); ?></h3>
                    <div class="footer-social" style="justify-content: flex-start;">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section" style="background: var(--off-white);">
    <div class="container">
        <div class="section-header">
            <h2>Frequently Asked Questions</h2>
            <div class="divider"></div>
            <p>Find answers to common questions about Zionite Charity.</p>
        </div>
        
        <div style="max-width: 800px; margin: 0 auto;">
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); margin-bottom: 20px;">
                <h4 style="margin-bottom: 10px;">How can I volunteer with Zionite Charity?</h4>
                <p style="color: var(--gray-600);">You can volunteer by filling out our volunteer registration form on the website. We review all applications and will contact you with available opportunities that match your skills and availability.</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); margin-bottom: 20px;">
                <h4 style="margin-bottom: 10px;">How do I make a donation?</h4>
                <p style="color: var(--gray-600);">You can make a donation through our secure online donation form. We accept various payment methods including credit cards, PayPal, and bank transfers. All donations are tax-deductible.</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); margin-bottom: 20px;">
                <h4 style="margin-bottom: 10px;">How can I request assistance from Zionite Charity?</h4>
                <p style="color: var(--gray-600);">If you or someone you know needs assistance, please contact us through our contact form or call our office. All requests are treated with confidentiality and compassion.</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); margin-bottom: 20px;">
                <h4 style="margin-bottom: 10px;">Can I donate goods instead of money?</h4>
                <p style="color: var(--gray-600);">Yes! We accept donations of gently used clothing, non-perishable food items, toys, and other essential goods. Please contact us to arrange a donation drop-off or pickup.</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md);">
                <h4 style="margin-bottom: 10px;">How is my donation used?</h4>
                <p style="color: var(--gray-600);">We maintain complete transparency about how donations are used. You can view our annual reports on the Reports page. A high percentage of donations go directly to programs and services for those in need.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
