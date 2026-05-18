<?php
/**
 * Zionite Charity - Projects/Activities Page
 * Displaying all projects and activities with images and descriptions
 */

require_once 'includes/config.php';

$pageTitle = t('nav_projects');
$pageDescription = 'Explore Zionite Charity\'s ongoing and completed projects including food drives, hospital visits, orphanage support, elderly care, and humanitarian activities.';

// Fetch all projects
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY featured DESC, created_at DESC");
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $projects = [];
}

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="hero hero--photo" style="padding: 120px 0 80px; background-image: url('<?php echo SITE_URL; ?>/assets/images/Orphanage Renovation.jpg');">
    <div class="container">
        <div class="hero-content">
            <h1><?php echo t('projects_page_title'); ?></h1>
            <p><?php echo t('projects_page_subtitle'); ?></p>
        </div>
    </div>
</section>

<!-- Projects Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('projects_ongoing'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('projects_subtitle'); ?></p>
        </div>
        
        <div class="projects-grid">
            <?php 
            $ongoingProjects = array_filter($projects, function($p) { return $p['status'] == 'ongoing'; });
            foreach ($ongoingProjects as $project): 
            ?>
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
                    <?php if ($project['budget']): ?>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--gray-200);">
                        <span style="font-weight: 600; color: var(--accent-gold);">Budget: <?php echo formatCurrency($project['budget']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($ongoingProjects)): ?>
            <div class="project-card animate-on-scroll">
                <div class="project-image">
                    <img src="<?php echo SITE_URL; ?>/assets/images/Community Food Drive.jpg" alt="Community Food Drive" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="project-content">
                    <span class="project-category">Food Assistance</span>
                    <h3>Community Food Drive</h3>
                    <p>Monthly food distribution program serving over 500 families in need across the city. We collect food donations and distribute packages to vulnerable families.</p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Multiple Locations</span>
                        <span><i class="fas fa-calendar"></i> Ongoing</span>
                    </div>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--gray-200);">
                        <span style="font-weight: 600; color: var(--accent-gold);">Budget: $50,000</span>
                    </div>
                </div>
            </div>
            
            <div class="project-card animate-on-scroll">
                <div class="project-image">
                    <img src="<?php echo SITE_URL; ?>/assets/images/Hospital Visit Program.jpg" alt="Hospital Visit Program" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="project-content">
                    <span class="project-category">Hospital Visits</span>
                    <h3>Hospital Visit Program</h3>
                    <p>Weekly visits to local hospitals bringing comfort, gifts, and support to patients who have no family visitors. Our volunteers provide companionship and prayer.</p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> City Hospital</span>
                        <span><i class="fas fa-calendar"></i> Ongoing</span>
                    </div>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--gray-200);">
                        <span style="font-weight: 600; color: var(--accent-gold);">Budget: $25,000</span>
                    </div>
                </div>
            </div>
            
            <div class="project-card animate-on-scroll">
                <div class="project-image">
                    <img src="<?php echo SITE_URL; ?>/assets/images/Orphanage Renovation.jpg" alt="Orphanage Renovation" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="project-content">
                    <span class="project-category">Orphanage Support</span>
                    <h3>Orphanage Renovation</h3>
                    <p>Complete renovation of the Sunshine Orphanage including new facilities, equipment, and improved living conditions for 50 children.</p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Sunshine Orphanage</span>
                        <span><i class="fas fa-calendar"></i> Ongoing</span>
                    </div>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--gray-200);">
                        <span style="font-weight: 600; color: var(--accent-gold);">Budget: $75,000</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Completed Projects Section -->
<section class="section" style="background: var(--off-white);">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('projects_completed'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('projects_subtitle'); ?></p>
        </div>
        
        <div class="projects-grid">
            <?php 
            $completedProjects = array_filter($projects, function($p) { return $p['status'] == 'completed'; });
            foreach ($completedProjects as $project): 
            ?>
            <div class="project-card animate-on-scroll">
                <?php renderProjectImage($project, ['opacity' => 0.7, 'icon' => 'fa-check-circle']); ?>
                <div class="project-content">
                    <span class="project-category" style="background: var(--gray-300); color: var(--gray-700);"><?php echo htmlspecialchars($project['category']); ?></span>
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($project['description'], 0, 150)) . '...'; ?></p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($project['location']); ?></span>
                        <span><i class="fas fa-check"></i> Completed: <?php echo formatDate($project['end_date']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($completedProjects)): ?>
            <div class="project-card animate-on-scroll">
                <div class="project-image" style="opacity: 0.7;">
                    <img src="<?php echo SITE_URL; ?>/assets/images/Winter Clothing Collection.jpg" alt="Winter Clothing Collection" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="project-content">
                    <span class="project-category" style="background: var(--gray-300); color: var(--gray-700);">Clothing Drive</span>
                    <h3>Winter Clothing Collection</h3>
                    <p>Successfully collected and distributed over 2,000 winter coats and clothing items to homeless shelters and families in need during the winter season.</p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> City Wide</span>
                        <span><i class="fas fa-check"></i> Completed: March 2024</span>
                    </div>
                </div>
            </div>
            
            <div class="project-card animate-on-scroll">
                <div class="project-image" style="opacity: 0.7;">
                    <img src="<?php echo SITE_URL; ?>/assets/images/Christmas Party for Elderly.jpg" alt="Christmas Party for Elderly" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="project-content">
                    <span class="project-category" style="background: var(--gray-300); color: var(--gray-700);">Elderly Care</span>
                    <h3>Christmas Party for Elderly</h3>
                    <p>Organized a special Christmas celebration for 200 elderly residents with gifts, meals, entertainment, and companionship during the holiday season.</p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Elderly Home</span>
                        <span><i class="fas fa-check"></i> Completed: December 2023</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Upcoming Projects Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('projects_upcoming'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('projects_subtitle'); ?></p>
        </div>
        
        <div class="projects-grid">
            <?php 
            $upcomingProjects = array_filter($projects, function($p) { return $p['status'] == 'upcoming'; });
            foreach ($upcomingProjects as $project): 
            ?>
            <div class="project-card animate-on-scroll">
                <?php renderProjectImage($project, ['icon' => 'fa-clock', 'imgOpacity' => 0.85]); ?>
                <div class="project-content">
                    <span class="project-category" style="background: var(--gray-400); color: var(--white);">Upcoming</span>
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($project['description'], 0, 150)) . '...'; ?></p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($project['location']); ?></span>
                        <span><i class="fas fa-calendar"></i> Starting: <?php echo formatDate($project['start_date']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($upcomingProjects)): ?>
            <div class="project-card animate-on-scroll">
                <?php renderProjectImage(['title' => 'Mobile Health Clinic', 'category' => 'Healthcare'], ['icon' => 'fa-clock']); ?>
                <div class="project-content">
                    <span class="project-category" style="background: var(--gray-400); color: var(--white);">Upcoming</span>
                    <h3>Mobile Health Clinic</h3>
                    <p>Launching a mobile health clinic to provide basic medical services to underserved communities and rural areas with limited healthcare access.</p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Rural Areas</span>
                        <span><i class="fas fa-calendar"></i> Starting: June 2024</span>
                    </div>
                </div>
            </div>
            
            <div class="project-card animate-on-scroll">
                <?php renderProjectImage(['title' => 'Youth Mentorship Program', 'category' => 'Education'], ['icon' => 'fa-clock']); ?>
                <div class="project-content">
                    <span class="project-category" style="background: var(--gray-400); color: var(--white);">Upcoming</span>
                    <h3>Youth Mentorship Program</h3>
                    <p>A comprehensive mentorship program pairing at-risk youth with positive role models to provide guidance, support, and opportunities for personal growth.</p>
                    <div class="project-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Community Centers</span>
                        <span><i class="fas fa-calendar"></i> Starting: July 2024</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
