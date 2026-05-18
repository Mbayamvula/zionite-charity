<?php
/**
 * Zionite Charity - Services Page
 * Listing all charity services offered
 */

require_once 'includes/config.php';

$pageTitle = t('nav_services');
$pageDescription = 'Discover all services offered by Zionite Charity including emotional support, food assistance, clothing support, hospital visits, orphanage support, elderly care, prayer, and humanitarian activities.';

// Fetch all services
try {
    $stmt = $pdo->query("SELECT * FROM services WHERE status = 'active' ORDER BY order_index ASC");
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    $services = [];
}

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="hero hero--photo" style="padding: 120px 0 80px; background-image: url('<?php echo SITE_URL; ?>/assets/images/Skills Training Workshop.jpg');">
    <div class="container">
        <div class="hero-content">
            <h1><?php echo t('services_page_title'); ?></h1>
            <p><?php echo t('services_page_subtitle'); ?></p>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('services_what_we_offer'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('services_subtitle'); ?></p>
        </div>
        
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
            <div class="service-card animate-on-scroll">
                <div class="service-icon">
                    <i class="fas fa-<?php echo htmlspecialchars($service['icon']); ?>"></i>
                </div>
                <div class="service-content">
                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($services)): ?>
            <!-- Default services if database is empty -->
            <div class="service-card animate-on-scroll">
                <div class="service-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="service-content">
                    <h3>Emotional Support</h3>
                    <p>Providing counseling and emotional support to those suffering silently in our communities. We offer one-on-one counseling sessions, support groups, and crisis intervention services.</p>
                </div>
            </div>
            
            <div class="service-card animate-on-scroll">
                <div class="service-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="service-content">
                    <h3>Food Assistance</h3>
                    <p>Distributing food packages and meals to families and individuals in need. Our food bank operates weekly and we organize community meal programs for the homeless and vulnerable.</p>
                </div>
            </div>
            
            <div class="service-card animate-on-scroll">
                <div class="service-icon">
                    <i class="fas fa-tshirt"></i>
                </div>
                <div class="service-content">
                    <h3>Clothing Support</h3>
                    <p>Collecting and distributing clothing to help those with limited resources. We accept donations of gently used clothing and distribute them to families, children, and individuals in need.</p>
                </div>
            </div>
            
            <div class="service-card animate-on-scroll">
                <div class="service-icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <div class="service-content">
                    <h3>Hospital Visits</h3>
                    <p>Regular visits to hospitals to bring comfort and support to patients. Our volunteers visit patients who have no family support, providing companionship, prayer, and encouragement.</p>
                </div>
            </div>
            
            <div class="service-card animate-on-scroll">
                <div class="service-icon">
                    <i class="fas fa-child"></i>
                </div>
                <div class="service-content">
                    <h3>Orphanage Support</h3>
                    <p>Supporting orphanages with supplies, education, and care for children. We provide educational materials, clothing, food supplies, and organize recreational activities for orphaned children.</p>
                </div>
            </div>
            
            <div class="service-card animate-on-scroll">
                <div class="service-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="service-content">
                    <h3>Elderly Care</h3>
                    <p>Providing companionship and assistance to elderly residents in care homes. Our volunteers spend time with elderly individuals, helping with errands, providing companionship, and offering emotional support.</p>
                </div>
            </div>
            
            <div class="service-card animate-on-scroll">
                <div class="service-icon">
                    <i class="fas fa-praying-hands"></i>
                </div>
                <div class="service-content">
                    <h3>Prayer Support</h3>
                    <p>Offering spiritual support and prayer for those in need of comfort. We have prayer teams available to pray with individuals, families, and for specific situations and needs.</p>
                </div>
            </div>
            
            <div class="service-card animate-on-scroll">
                <div class="service-icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <div class="service-content">
                    <h3>Humanitarian Activities</h3>
                    <p>Organizing and participating in various humanitarian aid activities including disaster relief, community clean-up drives, health camps, and educational support programs.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- How to Access Services Section -->
<section class="section" style="background: var(--off-white);">
    <div class="container">
        <div class="section-header">
            <h2>How to Access Our Services</h2>
            <div class="divider"></div>
            <p>Getting help from Zionite Charity is simple and confidential.</p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
            <div style="background: var(--white); padding: 40px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center;">
                <div style="background: var(--accent-gold); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <span style="font-size: 1.5rem; font-weight: 700; color: var(--primary-blue);">1</span>
                </div>
                <h3 style="margin-bottom: 15px;">Contact Us</h3>
                <p style="color: var(--gray-600);">Reach out to us through our contact form, phone, or email. All inquiries are treated with complete confidentiality.</p>
            </div>
            
            <div style="background: var(--white); padding: 40px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center;">
                <div style="background: var(--accent-gold); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <span style="font-size: 1.5rem; font-weight: 700; color: var(--primary-blue);">2</span>
                </div>
                <h3 style="margin-bottom: 15px;">Assessment</h3>
                <p style="color: var(--gray-600);">Our team will assess your needs and determine which services can best support you or your family.</p>
            </div>
            
            <div style="background: var(--white); padding: 40px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center;">
                <div style="background: var(--accent-gold); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <span style="font-size: 1.5rem; font-weight: 700; color: var(--primary-blue);">3</span>
                </div>
                <h3 style="margin-bottom: 15px;">Receive Support</h3>
                <p style="color: var(--gray-600);">Receive the support and services you need with dignity and respect, completely free of charge.</p>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 50px;">
            <a href="contact.php" class="btn btn-donate">
                <i class="fas fa-envelope"></i> Contact Us for Help
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta">
    <div class="container">
        <h2><?php echo t('cta_title'); ?></h2>
        <p><?php echo t('cta_subtitle'); ?></p>
        <div class="cta-buttons">
            <a href="donation.php" class="btn btn-donate">
                <i class="fas fa-heart"></i> <?php echo t('cta_donate'); ?>
            </a>
            <a href="volunteer.php" class="btn btn-volunteer">
                <i class="fas fa-user-plus"></i> <?php echo t('cta_volunteer'); ?>
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
