<?php
/**
 * Project image helpers — maps projects to images in assets/images/
 */

/**
 * @return array<string, string> Title to image filename
 */
function getProjectImageMapByTitle() {
    return [
        'Community Food Drive' => 'Community Food Drive.jpg',
        'Hospital Visit Program' => 'Hospital Visit Program.jpg',
        'Orphanage Renovation' => 'Orphanage Renovation.jpg',
        'Winter Clothing Collection' => 'Winter Clothing Collection.jpg',
        'Christmas Party for Elderly' => 'Christmas Party for Elderly.jpg',
        'Elderly Christmas Party' => 'Elderly Christmas Party.jpg',
        'Mobile Health Clinic' => 'Mobile Health Clinic.jpg',
        'Youth Mentorship Program' => 'Youth Mentorship Program.jpg',
        'Clean Water Initiative' => 'Clean Water Initiative.jpg',
        'Skills Training Workshop' => 'Skills Training Workshop.jpg',
        'Disaster Relief Fund' => 'Disaster Relief Fund.jpg',
        'Community Garden Project' => 'Community Garden Project.jpg',
    ];
}

/**
 * @return array<string, string> Category to image filename
 */
function getProjectImageMapByCategory() {
    return [
        'Food Assistance' => 'Food Assistance.jpg',
        'Hospital Visits' => 'Hospital Visits.jpg',
        'Orphanage Support' => 'Orphanage Renovation.jpg',
        'Elderly Care' => 'visiting eldest.jpg',
        'Clothing Drive' => 'Clothing Support.jpg',
        'Healthcare' => 'Mobile Health Clinic.jpg',
        'Education' => 'Skills Training Workshop.jpg',
        'Emergency' => 'Disaster Relief Fund.jpg',
        'Infrastructure' => 'Clean Water Initiative.jpg',
    ];
}

/**
 * @param string $filename
 * @return string|null Full URL if file exists
 */
function getAssetImageUrl($filename) {
    $path = dirname(__DIR__) . '/assets/images/' . $filename;
    if (!file_exists($path)) {
        return null;
    }
    return SITE_URL . '/assets/images/' . rawurlencode($filename);
}

/**
 * @param array $project
 * @return string|null Full image URL for a project
 */
function getProjectImageUrl($project) {
    if (!empty($project['image'])) {
        $uploadPath = dirname(__DIR__) . '/uploads/projects/' . $project['image'];
        if (file_exists($uploadPath)) {
            return SITE_URL . '/uploads/projects/' . rawurlencode($project['image']);
        }
    }

    $title = $project['title'] ?? '';
    $category = $project['category'] ?? '';

    $filename = getProjectImageMapByTitle()[$title]
        ?? getProjectImageMapByCategory()[$category]
        ?? null;

    return $filename ? getAssetImageUrl($filename) : null;
}

/**
 * @param array $project
 * @param array{opacity?: float, imgOpacity?: float, icon?: string} $options
 */
function renderProjectImage($project, $options = []) {
    $wrapperOpacity = $options['opacity'] ?? 1;
    $imgOpacity = $options['imgOpacity'] ?? null;
    $icon = $options['icon'] ?? 'fa-project-diagram';
    $alt = htmlspecialchars($project['title'] ?? 'Project');
    $url = getProjectImageUrl($project);

    $wrapperStyle = $wrapperOpacity < 1 ? ' style="opacity: ' . (float) $wrapperOpacity . ';"' : '';
    echo '<div class="project-image"' . $wrapperStyle . '>';
    if ($url) {
        $imgStyle = 'width: 100%; height: 100%; object-fit: cover;';
        if ($imgOpacity !== null) {
            $imgStyle .= ' opacity: ' . (float) $imgOpacity . ';';
        }
        echo '<img src="' . htmlspecialchars($url) . '" alt="' . $alt . '" style="' . $imgStyle . '">';
    } else {
        echo '<i class="fas ' . htmlspecialchars($icon) . '"></i>';
    }
    echo '</div>';
}
