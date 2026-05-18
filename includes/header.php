<?php
/**
 * Zionite Charity - Header Include
 * Reusable header component for all pages
 */
if (!isset($pageTitle)) {
    $pageTitle = 'Home';
}
if (!isset($pageDescription)) {
    $pageDescription = 'Zionite Charity - Helping people suffering silently through emotional support, food assistance, clothing support, hospital visits, orphanage support, elderly home support, prayer, and humanitarian activities.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <title><?php echo htmlspecialchars($pageTitle); ?> | Zionite Charity</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="<?php echo SITE_URL; ?>/index.php">
                    <img src="<?php echo SITE_URL; ?>/assets/images/logo zionite charity.jpg" alt="Zionite Charity Logo" style="height: 60px; width: auto;">
                    <span class="nav-brand-name">
                        <span class="nav-brand-line">Zionite</span>
                        <span class="nav-brand-line">Charity</span>
                    </span>
                </a>
            </div>
            
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="<?php echo SITE_URL; ?>/index.php" class="<?php echo $pageTitle == t('nav_home') ? 'active' : ''; ?>"><?php echo t('nav_home'); ?></a></li>
                <li><a href="<?php echo SITE_URL; ?>/about.php" class="<?php echo $pageTitle == t('nav_about') ? 'active' : ''; ?>"><?php echo t('nav_about'); ?></a></li>
                <li><a href="<?php echo SITE_URL; ?>/services.php" class="<?php echo $pageTitle == t('nav_services') ? 'active' : ''; ?>"><?php echo t('nav_services'); ?></a></li>
                <li><a href="<?php echo SITE_URL; ?>/projects.php" class="<?php echo $pageTitle == t('nav_projects') ? 'active' : ''; ?>"><?php echo t('nav_projects'); ?></a></li>
                <li><a href="<?php echo SITE_URL; ?>/reports.php" class="<?php echo $pageTitle == t('nav_reports') ? 'active' : ''; ?>"><?php echo t('nav_reports'); ?></a></li>
                <li><a href="<?php echo SITE_URL; ?>/contact.php" class="<?php echo $pageTitle == t('nav_contact') ? 'active' : ''; ?>"><?php echo t('nav_contact'); ?></a></li>
                <li class="nav-cta">
                    <a href="<?php echo SITE_URL; ?>/donation.php" class="btn btn-donate">
                        <i class="fas fa-heart"></i> <?php echo t('nav_donate'); ?>
                    </a>
                </li>
                <li class="nav-cta">
                    <a href="<?php echo SITE_URL; ?>/volunteer.php" class="btn btn-volunteer">
                        <i class="fas fa-user-plus"></i> <?php echo t('nav_volunteer'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
