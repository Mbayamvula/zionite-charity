<?php
/**
 * Zionite Charity - Volunteer Registration Page
 * Volunteer registration form connected to MySQL database
 */

require_once 'includes/config.php';

$pageTitle = t('nav_volunteer');
$pageDescription = 'Join Zionite Charity as a volunteer and make a difference in the lives of those suffering silently. Register today to start your humanitarian journey.';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    securityRequireCsrf();
    securityCheckHoneypot();

    if (!securityRateLimit('volunteer_form', 5, 3600)) {
        $message = 'Too many registration attempts. Please try again later.';
        $messageType = 'error';
    } else {
    $firstName = cleanInput($_POST['first_name'] ?? '');
    $lastName = cleanInput($_POST['last_name'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $phone = cleanInput($_POST['phone'] ?? '');
    $address = cleanInput($_POST['address'] ?? '');
    $city = cleanInput($_POST['city'] ?? '');
    $country = cleanInput($_POST['country'] ?? '');
    $skills = cleanInput($_POST['skills'] ?? '');
    $availability = cleanInput($_POST['availability'] ?? '');
    $motivation = cleanInput($_POST['motivation'] ?? '');
    
    // Basic validation
    if (empty($firstName) || empty($lastName) || empty($email)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM volunteers WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $message = 'This email is already registered. Please use a different email or contact us.';
                $messageType = 'error';
            } else {
                // Insert volunteer into database
                $stmt = $pdo->prepare("INSERT INTO volunteers (first_name, last_name, email, phone, address, city, country, skills, availability, motivation, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
                $result = $stmt->execute([$firstName, $lastName, $email, $phone, $address, $city, $country, $skills, $availability, $motivation]);
                
                if ($result) {
                    $message = 'Thank you for registering as a volunteer! We will review your application and contact you soon.';
                    $messageType = 'success';
                } else {
                    $message = 'There was an error submitting your application. Please try again.';
                    $messageType = 'error';
                }
            }
        } catch (PDOException $e) {
            $message = APP_ENV === 'production' ? 'Unable to submit your application. Please try again later.' : 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
    }
}

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="hero hero--photo" style="padding: 120px 0 80px; background-image: url('<?php echo SITE_URL; ?>/assets/images/Youth Mentorship Program.jpg');">
    <div class="container">
        <div class="hero-content">
            <h1><?php echo t('volunteer_title'); ?></h1>
            <p><?php echo t('volunteer_subtitle'); ?></p>
        </div>
    </div>
</section>

<!-- Volunteer Form Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('volunteer_form_title'); ?></h2>
            <div class="divider"></div>
            <p>Fill out the form below to register as a volunteer. We'll review your application and get in touch with you.</p>
        </div>
        
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <div style="max-width: 800px; margin: 0 auto;">
            <form method="POST" action="" style="background: var(--white); padding: 40px; border-radius: 10px; box-shadow: var(--shadow-md);">
                <?php echo csrfField(); ?>
                <?php echo securityHoneypotField(); ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                    </div>
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
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" class="form-control" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" class="form-control" value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="skills">Skills & Experience</label>
                    <textarea id="skills" name="skills" class="form-control" rows="3" placeholder="Tell us about your skills, experience, or areas where you'd like to volunteer..."><?php echo isset($_POST['skills']) ? htmlspecialchars($_POST['skills']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="availability">Availability</label>
                    <textarea id="availability" name="availability" class="form-control" rows="2" placeholder="When are you available to volunteer? (e.g., weekends, evenings, specific days)"><?php echo isset($_POST['availability']) ? htmlspecialchars($_POST['availability']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="motivation">Why do you want to volunteer?</label>
                    <textarea id="motivation" name="motivation" class="form-control" rows="4" required placeholder="Share your motivation for volunteering with Zionite Charity..."><?php echo isset($_POST['motivation']) ? htmlspecialchars($_POST['motivation']) : ''; ?></textarea>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn btn-donate">
                        <i class="fas fa-paper-plane"></i> Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Volunteer Benefits Section -->
<section class="section" style="background: var(--off-white);">
    <div class="container">
        <div class="section-header">
            <h2>Why Volunteer With Us?</h2>
            <div class="divider"></div>
            <p>Discover the benefits of joining our volunteer team.</p>
        </div>
        
        <div class="mission-grid">
            <div class="mission-card animate-on-scroll">
                <i class="fas fa-hand-holding-heart"></i>
                <h3>Make a Difference</h3>
                <p>Directly impact the lives of those suffering silently and bring hope to your community.</p>
            </div>
            
            <div class="mission-card animate-on-scroll">
                <i class="fas fa-users"></i>
                <h3>Build Community</h3>
                <p>Connect with like-minded individuals who share your passion for humanitarian work.</p>
            </div>
            
            <div class="mission-card animate-on-scroll">
                <i class="fas fa-graduation-cap"></i>
                <h3>Gain Experience</h3>
                <p>Develop valuable skills in counseling, event organization, and community service.</p>
            </div>
            
            <div class="mission-card animate-on-scroll">
                <i class="fas fa-smile"></i>
                <h3>Personal Growth</h3>
                <p>Experience personal fulfillment and growth through helping others in need.</p>
            </div>
            
            <div class="mission-card animate-on-scroll">
                <i class="fas fa-certificate"></i>
                <h3>Certification</h3>
                <p>Receive volunteer certificates and recognition for your dedicated service.</p>
            </div>
            
            <div class="mission-card animate-on-scroll">
                <i class="fas fa-network-wired"></i>
                <h3>Networking</h3>
                <p>Build professional networks and connections within the humanitarian sector.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta">
    <div class="container">
        <h2>Ready to Make a Difference?</h2>
        <p>Join our volunteer team today and start making a positive impact in your community.</p>
        <div style="text-align: center;">
            <a href="#volunteer-form" class="btn btn-donate">
                <i class="fas fa-user-plus"></i> Register Now
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
