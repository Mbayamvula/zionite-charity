<?php
/**
 * Zionite Charity - Footer Include
 * Reusable footer component for all pages
 */
?>
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-brand">
                        <i class="fas fa-hands-holding-heart"></i>
                        <h3>Zionite Charity</h3>
                        <p>Bringing hope and support to those suffering silently in our communities.</p>
                    </div>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4><?php echo t('footer_quick_links'); ?></h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/index.php"><?php echo t('nav_home'); ?></a></li>
                        <li><a href="<?php echo SITE_URL; ?>/about.php"><?php echo t('nav_about'); ?></a></li>
                        <li><a href="<?php echo SITE_URL; ?>/services.php"><?php echo t('nav_services'); ?></a></li>
                        <li><a href="<?php echo SITE_URL; ?>/projects.php"><?php echo t('nav_projects'); ?></a></li>
                        <li><a href="<?php echo SITE_URL; ?>/reports.php"><?php echo t('nav_reports'); ?></a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4><?php echo t('footer_get_involved'); ?></h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/donation.php"><?php echo t('nav_donate'); ?></a></li>
                        <li><a href="<?php echo SITE_URL; ?>/volunteer.php"><?php echo t('nav_volunteer'); ?></a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact.php"><?php echo t('nav_contact'); ?></a></li>
                        <li><a href="<?php echo SITE_URL; ?>/reports.php"><?php echo t('nav_reports'); ?></a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4><?php echo t('footer_contact'); ?></h4>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars(SITE_CONTACT_ADDRESS); ?></span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?php echo preg_replace('/\s+/', '', SITE_CONTACT_PHONE); ?>"><?php echo htmlspecialchars(SITE_CONTACT_PHONE); ?></a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo htmlspecialchars(SITE_CONTACT_EMAIL); ?>"><?php echo htmlspecialchars(SITE_CONTACT_EMAIL); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <p>&copy; <?php echo date('Y'); ?> Zionite Charity. <?php echo t('footer_copyright'); ?></p>
                </div>
                <div class="footer-legal">
                    <a href="#"><?php echo t('footer_privacy'); ?></a>
                    <a href="#"><?php echo t('footer_terms'); ?></a>
                    <a href="#"><?php echo t('footer_cookies'); ?></a>
                    <a href="<?php echo SITE_URL; ?>/admin/login.php" style="color: var(--accent-gold); font-weight: 600;"><i class="fas fa-lock"></i> <?php echo t('footer_admin'); ?></a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- JavaScript -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
