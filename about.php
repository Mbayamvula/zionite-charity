<?php
/**
 * Zionite Charity - About Page
 * Information about vision, compassion, transparency, and humanitarian mission
 */

require_once 'includes/config.php';

$pageTitle = t('nav_about');
$pageDescription = 'Learn about Zionite Charity\'s vision, mission, values of compassion and transparency, and our commitment to humanitarian activities.';

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="hero hero--photo" style="padding: 120px 0 80px; background-image: url('<?php echo SITE_URL; ?>/assets/images/Community Garden Project.jpg');">
    <div class="container">
        <div class="hero-content">
            <h1><?php echo t('about_title'); ?></h1>
            <p><?php echo t('about_subtitle'); ?></p>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('about_title'); ?></h2>
            <div class="divider"></div>
        </div>
        
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <p style="font-size: 1.125rem; line-height: 1.8; color: var(--gray-600); margin-bottom: 20px;">
                Zionite Charity was founded with a simple yet powerful vision: to reach out to those who are suffering silently in our communities. We recognized that many individuals face emotional, physical, and financial challenges without anyone to turn to for support.
            </p>
            <p style="font-size: 1.125rem; line-height: 1.8; color: var(--gray-600); margin-bottom: 20px;">
                Our organization began as a small group of compassionate individuals driven by the desire to make a difference. Today, we have grown into a comprehensive humanitarian organization providing emotional support, food assistance, clothing support, hospital visits, orphanage support, elderly care, prayer, and various humanitarian activities.
            </p>
            <p style="font-size: 1.125rem; line-height: 1.8; color: var(--gray-600);">
                We believe that everyone deserves compassion, dignity, and support regardless of their circumstances. Our commitment to transparency ensures that every donation and effort directly benefits those in need.
            </p>
        </div>
    </div>
</section>

<!-- Vision & Mission Section -->
<section class="section" style="background: var(--off-white);">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('about_subtitle'); ?></h2>
            <div class="divider"></div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 50px;">
            <div class="animate-on-scroll">
                <div style="background: var(--primary-blue); color: var(--white); padding: 40px; border-radius: 10px; text-align: center;">
                    <img src="<?php echo SITE_URL; ?>/assets/images/Christmas Party for Elderly.jpg" alt="Vision" style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px; margin-bottom: 20px;">
                    <h3 style="color: var(--white); margin-bottom: 20px;"><?php echo t('about_vision'); ?></h3>
                    <p style="line-height: 1.8; color: var(--gray-200);">
                        To create a world where no one suffers silently, where every individual has access to emotional support, basic necessities, and the compassion they deserve. We envision communities united in care and support for one another.
                    </p>
                </div>
            </div>
            
            <div class="animate-on-scroll">
                <div style="background: var(--white); padding: 40px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center;">
                    <img src="<?php echo SITE_URL; ?>/assets/images/Elderly Christmas Party.jpg" alt="Mission" style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px; margin-bottom: 20px;">
                    <h3 style="margin-bottom: 20px;"><?php echo t('about_mission'); ?></h3>
                    <p style="line-height: 1.8; color: var(--gray-600);">
                        To provide comprehensive humanitarian support to those suffering silently through emotional counseling, food assistance, clothing distribution, hospital visits, orphanage support, elderly care, prayer, and community service. We are committed to transparency, accountability, and making a tangible difference in people's lives.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Values Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('about_values'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('about_values'); ?></p>
        </div>
        
        <div class="mission-grid">
            <div class="mission-card animate-on-scroll">
                <img src="<?php echo SITE_URL; ?>/assets/images/Emotional Support.jpg" alt="Compassion" style="width: 100%; height: 150px; object-fit: cover; border-radius: 10px; margin-bottom: 15px;">
                <h3>Compassion</h3>
                <p>We approach every individual with genuine empathy and care, recognizing the dignity and worth of every person we serve.</p>
            </div>
            
            <div class="mission-card animate-on-scroll">
                <img src="<?php echo SITE_URL; ?>/assets/images/Winter Clothing Collection.jpg" alt="Transparency" style="width: 100%; height: 150px; object-fit: cover; border-radius: 10px; margin-bottom: 15px;">
                <h3>Transparency</h3>
                <p>We maintain complete transparency in our operations, finances, and decision-making processes to build trust with our donors and beneficiaries.</p>
            </div>
            
            <div class="mission-card animate-on-scroll">
                <img src="<?php echo SITE_URL; ?>/assets/images/Food Assistance.jpg" alt="Integrity" style="width: 100%; height: 150px; object-fit: cover; border-radius: 10px; margin-bottom: 15px;">
                <h3>Integrity</h3>
                <p>We uphold the highest ethical standards in all our activities, ensuring that resources are used effectively and responsibly.</p>
            </div>
            
            <div class="mission-card animate-on-scroll">
                <img src="<?php echo SITE_URL; ?>/assets/images/Community Food Drive.jpg" alt="Community" style="width: 100%; height: 150px; object-fit: cover; border-radius: 10px; margin-bottom: 15px;">
                <h3>Community</h3>
                <p>We believe in the power of community and work to build networks of support that extend beyond our immediate efforts.</p>
            </div>
            
            <div class="mission-card animate-on-scroll">
                <img src="<?php echo SITE_URL; ?>/assets/images/eldest visit.jpg" alt="Spiritual Support" style="width: 100%; height: 150px; object-fit: cover; border-radius: 10px; margin-bottom: 15px;">
                <h3>Spiritual Support</h3>
                <p>We offer prayer and spiritual support to those who seek it, respecting all faiths and beliefs while providing comfort and hope.</p>
            </div>
            
            <div class="mission-card animate-on-scroll">
                <img src="<?php echo SITE_URL; ?>/assets/images/visiting eldest.jpg" alt="Humanitarian Action" style="width: 100%; height: 150px; object-fit: cover; border-radius: 10px; margin-bottom: 15px;">
                <h3>Humanitarian Action</h3>
                <p>We are driven by humanitarian principles and take action to alleviate suffering wherever we find it.</p>
            </div>
        </div>
    </div>
</section>

<!-- What We Do Section -->
<section class="section" style="background: var(--off-white);">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('services_title'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('services_subtitle'); ?></p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md);">
                <i class="fas fa-heart" style="font-size: 2rem; color: var(--accent-gold); margin-bottom: 15px;"></i>
                <h4 style="margin-bottom: 10px;">Emotional Support</h4>
                <p style="color: var(--gray-600);">Providing counseling and emotional support to individuals and families facing difficult times.</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md);">
                <i class="fas fa-utensils" style="font-size: 2rem; color: var(--accent-gold); margin-bottom: 15px;"></i>
                <h4 style="margin-bottom: 10px;">Food Assistance</h4>
                <p style="color: var(--gray-600);">Distributing food packages and organizing meal programs for those in need.</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md);">
                <i class="fas fa-tshirt" style="font-size: 2rem; color: var(--accent-gold); margin-bottom: 15px;"></i>
                <h4 style="margin-bottom: 10px;">Clothing Support</h4>
                <p style="color: var(--gray-600);">Collecting and distributing clothing to individuals and families with limited resources.</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md);">
                <i class="fas fa-hospital" style="font-size: 2rem; color: var(--accent-gold); margin-bottom: 15px;"></i>
                <h4 style="margin-bottom: 10px;">Hospital Visits</h4>
                <p style="color: var(--gray-600);">Regular visits to hospitals to provide comfort, companionship, and support to patients.</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md);">
                <i class="fas fa-child" style="font-size: 2rem; color: var(--accent-gold); margin-bottom: 15px;"></i>
                <h4 style="margin-bottom: 10px;">Orphanage Support</h4>
                <p style="color: var(--gray-600);">Supporting orphanages with supplies, educational materials, and care for children.</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md);">
                <i class="fas fa-user" style="font-size: 2rem; color: var(--accent-gold); margin-bottom: 15px;"></i>
                <h4 style="margin-bottom: 10px;">Elderly Care</h4>
                <p style="color: var(--gray-600);">Providing companionship and assistance to elderly residents in care homes.</p>
            </div>
        </div>
    </div>
</section>

<!-- Transparency Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Our Commitment to Transparency</h2>
            <div class="divider"></div>
            <p>We believe in complete transparency to maintain trust with our donors and beneficiaries.</p>
        </div>
        
        <div style="max-width: 900px; margin: 0 auto;">
            <div style="background: var(--off-white); padding: 40px; border-radius: 10px; margin-bottom: 30px;">
                <h3 style="margin-bottom: 20px;"><i class="fas fa-file-alt" style="color: var(--accent-gold); margin-right: 10px;"></i> Financial Reporting</h3>
                <p style="color: var(--gray-600); line-height: 1.8;">We publish detailed annual and quarterly financial reports showing exactly how donations are used. Our financial statements are audited regularly to ensure accuracy and accountability.</p>
            </div>
            
            <div style="background: var(--off-white); padding: 40px; border-radius: 10px; margin-bottom: 30px;">
                <h3 style="margin-bottom: 20px;"><i class="fas fa-chart-line" style="color: var(--accent-gold); margin-right: 10px;"></i> Impact Reporting</h3>
                <p style="color: var(--gray-600); line-height: 1.8;">We regularly report on the impact of our programs, including the number of people served, projects completed, and outcomes achieved. This helps our donors understand the real difference their contributions make.</p>
            </div>
            
            <div style="background: var(--off-white); padding: 40px; border-radius: 10px; margin-bottom: 30px;">
                <h3 style="margin-bottom: 20px;"><i class="fas fa-users-cog" style="color: var(--accent-gold); margin-right: 10px;"></i> Governance</h3>
                <p style="color: var(--gray-600); line-height: 1.8;">Our organization is governed by a dedicated board of directors who oversee our operations and ensure we adhere to our mission and values. We maintain clear policies and procedures for all activities.</p>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="reports.php" class="btn btn-secondary">
                    <i class="fas fa-download"></i> View Our Reports
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta">
    <div class="container">
        <h2>Join Our Mission</h2>
        <p>Together, we can make a real difference in the lives of those suffering silently. Your support matters.</p>
        <div class="cta-buttons">
            <a href="donation.php" class="btn btn-donate">
                <i class="fas fa-heart"></i> Donate Now
            </a>
            <a href="volunteer.php" class="btn btn-volunteer">
                <i class="fas fa-user-plus"></i> Volunteer
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
