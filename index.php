<?php
/**
 * Zionite Charity - Home Page
 * Main landing page with hero, mission, services, testimonials, and donation sections
 */

require_once 'includes/config.php';

$pageTitle = t('nav_home');
$pageDescription = 'Zionite Charity - Bringing hope and support to those suffering silently through emotional support, food assistance, clothing support, hospital visits, orphanage support, elderly home support, prayer, and humanitarian activities.';

// Fetch featured projects
try {
    $stmt = $pdo->query("SELECT * FROM projects WHERE featured = 1 AND status = 'ongoing' LIMIT 3");
    $featuredProjects = $stmt->fetchAll();
} catch (PDOException $e) {
    $featuredProjects = [];
}

// Fetch testimonials
try {
    $stmt = $pdo->query("SELECT * FROM testimonials WHERE status = 'active' ORDER BY created_at DESC LIMIT 3");
    $testimonials = $stmt->fetchAll();
} catch (PDOException $e) {
    $testimonials = [];
}

// Fetch services
try {
    $stmt = $pdo->query("SELECT * FROM services WHERE status = 'active' ORDER BY order_index ASC LIMIT 4");
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    $services = [];
}

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero hero--photo" style="background-image: url('<?php echo SITE_URL; ?>/assets/images/Community Food Drive.jpg');">
    <div class="container">
        <div class="hero-content">
            <h1><?php echo t('hero_title'); ?></h1>
            <p><?php echo t('hero_subtitle'); ?></p>
            <div class="hero-buttons">
                <a href="donation.php" class="btn btn-donate">
                    <i class="fas fa-heart"></i> <?php echo t('cta_donate'); ?>
                </a>
                <a href="volunteer.php" class="btn btn-white">
                    <i class="fas fa-user-plus"></i> <?php echo t('cta_volunteer'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="section mission">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('mission_title'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('mission_text'); ?></p>
        </div>
        
        <div class="mission-grid">
            <div class="mission-card animate-on-scroll">
                <i class="fas fa-heart"></i>
                <h3>Compassion</h3>
                <p>We approach every individual with genuine care and understanding, recognizing the unique challenges they face.</p>
            </div>
            
            <div class="mission-card animate-on-scroll">
                <i class="fas fa-hand-holding-heart"></i>
                <h3>Support</h3>
                <p>Providing comprehensive support through emotional care, food, clothing, medical visits, and humanitarian aid.</p>
            </div>
            
            <div class="mission-card animate-on-scroll">
                <i class="fas fa-users"></i>
                <h3>Community</h3>
                <p>Building a community of volunteers and donors united in the mission to help those in need.</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('services_title'); ?></h2>
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
            <div class="service-card animate-on-scroll">
                <div class="service-image">
                    <img src="<?php echo SITE_URL; ?>/assets/images/Emotional Support.jpg" alt="Emotional Support">
                </div>
                <div class="service-content">
                    <h3>Emotional Support</h3>
                    <p>Providing counseling and emotional support to those suffering silently in our communities.</p>
                </div>
            </div>
            
            <div class="service-card animate-on-scroll">
                <div class="service-image">
                    <img src="<?php echo SITE_URL; ?>/assets/images/Food Assistance.jpg" alt="Food Assistance">
                </div>
                <div class="service-content">
                    <h3>Food Assistance</h3>
                    <p>Distributing food packages and meals to families and individuals in need.</p>
                </div>
            </div>
            
            <div class="service-card animate-on-scroll">
                <div class="service-image">
                    <img src="<?php echo SITE_URL; ?>/assets/images/Clothing Support.jpg" alt="Clothing Support">
                </div>
                <div class="service-content">
                    <h3>Clothing Support</h3>
                    <p>Collecting and distributing clothing to help those with limited resources.</p>
                </div>
            </div>
            
            <div class="service-card animate-on-scroll">
                <div class="service-image">
                    <img src="<?php echo SITE_URL; ?>/assets/images/Hospital Visits.jpg" alt="Hospital Visits">
                </div>
                <div class="service-content">
                    <h3>Hospital Visits</h3>
                    <p>Regular visits to hospitals to bring comfort and support to patients.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="services.php" class="btn btn-secondary">
                <?php echo t('services_title'); ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Featured Projects Section -->
<section class="section" style="background: var(--off-white);">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('projects_title'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('projects_subtitle'); ?></p>
        </div>
        
        <div class="projects-grid">
            <?php foreach ($featuredProjects as $project): ?>
            <div class="project-card animate-on-scroll">
                <?php renderProjectImage($project); ?>
                <div class="project-content">
                    <span class="project-category"><?php echo htmlspecialchars($project['category']); ?></span>
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($project['description'], 0, 150)) . '...'; ?></p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($project['location']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo formatDate($project['start_date']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($featuredProjects)): ?>
            <div class="project-card animate-on-scroll">
                <?php renderProjectImage(['title' => 'Community Food Drive', 'category' => 'Food Assistance']); ?>
                <div class="project-content">
                    <span class="project-category">Food Assistance</span>
                    <h3>Community Food Drive</h3>
                    <p>Monthly food distribution program serving over 500 families in need across the city.</p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Multiple Locations</span>
                        <span><i class="fas fa-calendar"></i> Ongoing</span>
                    </div>
                </div>
            </div>
            
            <div class="project-card animate-on-scroll">
                <?php renderProjectImage(['title' => 'Hospital Visit Program', 'category' => 'Hospital Visits']); ?>
                <div class="project-content">
                    <span class="project-category">Hospital Visits</span>
                    <h3>Hospital Visit Program</h3>
                    <p>Weekly visits to local hospitals bringing comfort, gifts, and support to patients.</p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> City Hospital</span>
                        <span><i class="fas fa-calendar"></i> Ongoing</span>
                    </div>
                </div>
            </div>
            
            <div class="project-card animate-on-scroll">
                <?php renderProjectImage(['title' => 'Orphanage Renovation', 'category' => 'Orphanage Support']); ?>
                <div class="project-content">
                    <span class="project-category">Orphanage Support</span>
                    <h3>Orphanage Renovation</h3>
                    <p>Complete renovation of the Sunshine Orphanage including new facilities and equipment.</p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Sunshine Orphanage</span>
                        <span><i class="fas fa-calendar"></i> Ongoing</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="projects.php" class="btn btn-secondary">
                <?php echo t('projects_title'); ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section testimonials">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('testimonials_title'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('testimonials_subtitle'); ?></p>
        </div>
        
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $testimonial): ?>
            <div class="testimonial-card animate-on-scroll">
                <i class="fas fa-quote-left"></i>
                <p class="testimonial-text"><?php echo htmlspecialchars($testimonial['testimonial']); ?></p>
                <div class="testimonial-author">
                    <div class="testimonial-author-avatar">
                        <?php echo strtoupper(substr(htmlspecialchars($testimonial['name']), 0, 1)); ?>
                    </div>
                    <div class="testimonial-author-info">
                        <h4><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                        <p><?php echo htmlspecialchars($testimonial['role']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($testimonials)): ?>
            <div class="testimonial-card animate-on-scroll">
                <i class="fas fa-quote-left"></i>
                <p class="testimonial-text">Zionite Charity helped me when I was at my lowest. Their emotional support program gave me hope and strength to move forward.</p>
                <div class="testimonial-author">
                    <div class="testimonial-author-avatar">S</div>
                    <div class="testimonial-author-info">
                        <h4>Sarah Johnson</h4>
                        <p>Beneficiary</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card animate-on-scroll">
                <i class="fas fa-quote-left"></i>
                <p class="testimonial-text">Volunteering with Zionite Charity has been a life-changing experience. The impact we make in people's lives is immeasurable.</p>
                <div class="testimonial-author">
                    <div class="testimonial-author-avatar">M</div>
                    <div class="testimonial-author-info">
                        <h4>Michael Thompson</h4>
                        <p>Volunteer</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card animate-on-scroll">
                <i class="fas fa-quote-left"></i>
                <p class="testimonial-text">I've been supporting Zionite Charity for years. Their transparency and dedication to helping those in need is truly inspiring.</p>
                <div class="testimonial-author">
                    <div class="testimonial-author-avatar">E</div>
                    <div class="testimonial-author-info">
                        <h4>Emily Davis</h4>
                        <p>Donor</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
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

<!-- Donation Section -->
<section class="section donation">
    <div class="container">
        <div class="section-header">
            <h2>Quick Donation</h2>
            <div class="divider"></div>
            <p>Choose an amount to make an instant donation and help those in need.</p>
        </div>
        
        <div class="donation-options">
            <div class="donation-option" onclick="selectDonation(25)">
                <h3>$25</h3>
                <span>Provides meals for a family</span>
            </div>
            <div class="donation-option" onclick="selectDonation(50)">
                <h3>$50</h3>
                <span>Supports hospital visits</span>
            </div>
            <div class="donation-option" onclick="selectDonation(100)">
                <h3>$100</h3>
                <span>Helps orphanage support</span>
            </div>
            <div class="donation-option" onclick="selectDonation(250)">
                <h3>$250</h3>
                <span>Sponsors a project</span>
            </div>
        </div>
        
        <div style="text-align: center;">
            <a href="donation.php" class="btn btn-donate">
                <i class="fas fa-heart"></i> Donate Now
            </a>
        </div>
    </div>
</section>

<script>
function selectDonation(amount) {
    document.querySelectorAll('.donation-option').forEach(opt => opt.classList.remove('active'));
    event.currentTarget.classList.add('active');
    window.location.href = 'donation.php?amount=' + amount;
}
</script>

<?php include 'includes/footer.php'; ?>
