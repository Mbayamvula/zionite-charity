<?php
/**
 * Zionite Charity - Reports Page
 * Reports page for transparency and downloadable PDF reports
 */

require_once 'includes/config.php';

$pageTitle = t('nav_reports');
$pageDescription = 'Access Zionite Charity\'s annual reports, financial statements, and project reports. We maintain complete transparency in our operations and finances.';

// Fetch all published reports
try {
    $stmt = $pdo->query("SELECT * FROM reports WHERE status = 'published' ORDER BY published_date DESC, created_at DESC");
    $reports = $stmt->fetchAll();
} catch (PDOException $e) {
    $reports = [];
}

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="hero hero--photo" style="padding: 120px 0 80px; background-image: url('<?php echo SITE_URL; ?>/assets/images/Clean Water Initiative.jpg');">
    <div class="container">
        <div class="hero-content">
            <h1><?php echo t('reports_title'); ?></h1>
            <p><?php echo t('reports_subtitle'); ?></p>
        </div>
    </div>
</section>

<!-- Transparency Statement Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('reports_commitment'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('reports_subtitle'); ?></p>
        </div>
        
        <div style="max-width: 900px; margin: 0 auto;">
            <div style="background: var(--off-white); padding: 40px; border-radius: 10px; margin-bottom: 30px;">
                <h3 style="margin-bottom: 20px;"><i class="fas fa-file-invoice-dollar" style="color: var(--accent-gold); margin-right: 10px;"></i> Financial Transparency</h3>
                <p style="color: var(--gray-600); line-height: 1.8;">We publish detailed financial reports showing exactly how donations and funds are allocated. Our financial statements are prepared according to accounting standards and are available for public review. We ensure that the maximum percentage of funds goes directly to our programs and services.</p>
            </div>
            
            <div style="background: var(--off-white); padding: 40px; border-radius: 10px; margin-bottom: 30px;">
                <h3 style="margin-bottom: 20px;"><i class="fas fa-chart-bar" style="color: var(--accent-gold); margin-right: 10px;"></i> Impact Reporting</h3>
                <p style="color: var(--gray-600); line-height: 1.8;">We regularly report on the impact of our programs, including metrics such as number of people served, projects completed, volunteers engaged, and outcomes achieved. This helps our donors and stakeholders understand the real difference their contributions make in communities.</p>
            </div>
            
            <div style="background: var(--off-white); padding: 40px; border-radius: 10px; margin-bottom: 30px;">
                <h3 style="margin-bottom: 20px;"><i class="fas fa-balance-scale" style="color: var(--accent-gold); margin-right: 10px;"></i> Independent Audits</h3>
                <p style="color: var(--gray-600); line-height: 1.8;">Our financial records undergo regular independent audits by reputable accounting firms to ensure accuracy, compliance, and accountability. Audit reports are made available to the public upon request.</p>
            </div>
            
            <div style="background: var(--off-white); padding: 40px; border-radius: 10px;">
                <h3 style="margin-bottom: 20px;"><i class="fas fa-users-cog" style="color: var(--accent-gold); margin-right: 10px;"></i> Governance</h3>
                <p style="color: var(--gray-600); line-height: 1.8;">Our organization is governed by a dedicated board of directors who provide oversight and ensure we adhere to our mission, values, and legal obligations. We maintain clear policies and procedures for all organizational activities.</p>
            </div>
        </div>
    </div>
</section>

<!-- Reports Section -->
<section class="section" style="background: var(--off-white);">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('reports_available'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('reports_subtitle'); ?></p>
        </div>
        
        <?php if (!empty($reports)): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Report Title</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Size</th>
                        <th>Downloads</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($report['title']); ?></strong>
                            <?php if ($report['description']): ?>
                            <br><small style="color: var(--gray-500);"><?php echo htmlspecialchars(substr($report['description'], 0, 100)); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="background: var(--accent-gold); color: var(--primary-blue); padding: 3px 10px; border-radius: 15px; font-size: 0.875rem; font-weight: 600;">
                                <?php echo htmlspecialchars(ucfirst($report['report_type'])); ?>
                            </span>
                        </td>
                        <td><?php echo $report['published_date'] ? formatDate($report['published_date']) : formatDate($report['created_at']); ?></td>
                        <td><?php echo $report['file_size'] ? number_format($report['file_size'], 2) . ' MB' : 'N/A'; ?></td>
                        <td><?php echo number_format($report['download_count']); ?></td>
                        <td>
                            <?php if ($report['file_path']): ?>
                            <a href="<?php echo SITE_URL; ?>/uploads/reports/<?php echo htmlspecialchars($report['file_path']); ?>" download class="btn btn-secondary" style="padding: 8px 15px; font-size: 0.875rem;">
                                <i class="fas fa-download"></i> Download
                            </a>
                            <?php else: ?>
                            <span style="color: var(--gray-500);">Not Available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <!-- Sample reports when database is empty -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Report Title</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Size</th>
                        <th>Downloads</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>Annual Report 2023</strong>
                            <br><small style="color: var(--gray-500);">Comprehensive overview of our activities, financials, and impact for 2023.</small>
                        </td>
                        <td>
                            <span style="background: var(--accent-gold); color: var(--primary-blue); padding: 3px 10px; border-radius: 15px; font-size: 0.875rem; font-weight: 600;">Annual</span>
                        </td>
                        <td>January 15, 2024</td>
                        <td>2.5 MB</td>
                        <td>1,234</td>
                        <td>
                            <a href="#" class="btn btn-secondary" style="padding: 8px 15px; font-size: 0.875rem;">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Financial Statement Q4 2023</strong>
                            <br><small style="color: var(--gray-500);">Quarterly financial statement for the fourth quarter of 2023.</small>
                        </td>
                        <td>
                            <span style="background: var(--accent-gold); color: var(--primary-blue); padding: 3px 10px; border-radius: 15px; font-size: 0.875rem; font-weight: 600;">Quarterly</span>
                        </td>
                        <td>January 10, 2024</td>
                        <td>1.2 MB</td>
                        <td>567</td>
                        <td>
                            <a href="#" class="btn btn-secondary" style="padding: 8px 15px; font-size: 0.875rem;">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Community Food Drive Impact Report</strong>
                            <br><small style="color: var(--gray-500);">Detailed report on our food distribution program and its impact.</small>
                        </td>
                        <td>
                            <span style="background: var(--accent-gold); color: var(--primary-blue); padding: 3px 10px; border-radius: 15px; font-size: 0.875rem; font-weight: 600;">Project</span>
                        </td>
                        <td>December 20, 2023</td>
                        <td>3.1 MB</td>
                        <td>892</td>
                        <td>
                            <a href="#" class="btn btn-secondary" style="padding: 8px 15px; font-size: 0.875rem;">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Annual Report 2022</strong>
                            <br><small style="color: var(--gray-500);">Comprehensive overview of our activities, financials, and impact for 2022.</small>
                        </td>
                        <td>
                            <span style="background: var(--accent-gold); color: var(--primary-blue); padding: 3px 10px; border-radius: 15px; font-size: 0.875rem; font-weight: 600;">Annual</span>
                        </td>
                        <td>January 18, 2023</td>
                        <td>2.3 MB</td>
                        <td>2,456</td>
                        <td>
                            <a href="#" class="btn btn-secondary" style="padding: 8px 15px; font-size: 0.875rem;">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Key Statistics Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Key Statistics 2023</h2>
            <div class="divider"></div>
            <p>Highlights of our impact and achievements in 2023.</p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px;">
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center;">
                <div class="counter" data-target="15420" data-duration="2000" style="font-size: 2.5rem; font-weight: 700; color: var(--accent-gold); margin-bottom: 10px;">0</div>
                <h4 style="margin-bottom: 5px;">People Served</h4>
                <p style="color: var(--gray-600);">Individuals and families supported through our programs</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center;">
                <div class="counter" data-target="850" data-duration="2000" style="font-size: 2.5rem; font-weight: 700; color: var(--accent-gold); margin-bottom: 10px;">0</div>
                <h4 style="margin-bottom: 5px;">Volunteers</h4>
                <p style="color: var(--gray-600);">Active volunteers contributing their time and skills</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center;">
                <div class="counter" data-target="45" data-duration="2000" style="font-size: 2.5rem; font-weight: 700; color: var(--accent-gold); margin-bottom: 10px;">0</div>
                <h4 style="margin-bottom: 5px;">Projects Completed</h4>
                <p style="color: var(--gray-600);">Humanitarian projects successfully completed</p>
            </div>
            
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center;">
                <div class="counter" data-target="92" data-duration="2000" style="font-size: 2.5rem; font-weight: 700; color: var(--accent-gold); margin-bottom: 10px;">0</div>
                <h4 style="margin-bottom: 5px;">%</h4>
                <p style="color: var(--gray-600);">Of donations go directly to programs</p>
            </div>
        </div>
    </div>
</section>

<!-- Request Report Section -->
<section class="section" style="background: var(--off-white);">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('reports_request'); ?></h2>
            <div class="divider"></div>
            <p><?php echo t('reports_subtitle'); ?></p>
        </div>
        
        <div style="max-width: 600px; margin: 0 auto;">
            <form method="POST" action="contact.php" style="background: var(--white); padding: 40px; border-radius: 10px; box-shadow: var(--shadow-md);">
                <div class="form-group">
                    <label for="name">Your Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" value="Report Request">
                </div>
                
                <div class="form-group">
                    <label for="message">Your Request *</label>
                    <textarea id="message" name="message" class="form-control" rows="4" required placeholder="Please specify which report or information you need..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-donate" style="width: 100%;">
                    <i class="fas fa-paper-plane"></i> Submit Request
                </button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
