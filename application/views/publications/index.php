<?php
// Guard
if (!defined('BASE_URL')) {
    die('BASE_URL not defined.');
}

// Ensure hero array exists and set defaults
$publicationsHero = isset($publicationsHero) && is_array($publicationsHero) ? $publicationsHero : [];
$publicationsHero['publications_hero_image'] = $publicationsHero['publications_hero_image'] ?? 'bg_5.jpg';
$publicationsHero['publications_hero_title'] = $publicationsHero['publications_hero_title'] ?? 'Our Publications';
$publicationsHero['publications_hero_subtitle'] = $publicationsHero['publications_hero_subtitle'] ?? 'Access our reports, manuals, and resources.';

// Prepare hero variables
$heroImageFilename = $publicationsHero['publications_hero_image'];
$heroImageUrl = rtrim(BASE_URL, '/') . '/images/' . htmlspecialchars($heroImageFilename);
$heroTitle = $publicationsHero['publications_hero_title'];
$heroSubtitle = $publicationsHero['publications_hero_subtitle'];

// Ensure categorizedPublications exists
$categorizedPublications = isset($categorizedPublications) && is_array($categorizedPublications)
    ? $categorizedPublications
    : [
        'manuals_handbooks' => [],
        'annual_reports' => [],
        'other' => []
    ];

// Fallback helper for file size formatting (only if not defined)
if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes): string {
        if ($bytes === null || $bytes === '') {
            return '—';
        }
        $bytes = (float) $bytes;
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = min(floor(log($bytes) / log(1024)), count($units) - 1);
        $bytes /= pow(1024, $i);
        return ($i === 0) ? intval($bytes) . ' ' . $units[$i] : round($bytes, 1) . ' ' . $units[$i];
    }
}

// Define base path for logging (optional)
define('PUBLIC_PATH', __DIR__ . '/../../public_html'); // For logging only
?>

<!-- Hero Section -->
<div class="hero-wrap" style="background-image: url('<?php echo htmlspecialchars($heroImageUrl); ?>');" data-stellar-background-ratio="1">
    <div class="hero-gradient"></div>
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center" data-scrollax-parent="true">
            <div class="col-md-9 ftco-animate pb-5 text-center" data-scrollax="properties: { translateY: '70%' }">
                <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <span class="mr-2"><a href="<?php echo htmlspecialchars(rtrim(BASE_URL, '/')); ?>/">Home</a></span>
                    <span>Publications</span>
                </p>
                <h1 class="mb-3 bread" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <?php echo htmlspecialchars($heroTitle); ?>
                </h1>
                <p class="mb-0" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <?php echo htmlspecialchars($heroSubtitle); ?>
                </p>
            </div>
        </div>
    </div>
</div>
<!-- End Hero Section -->

<section class="ftco-section bg-light">
    <div class="container">
        <div class="row justify-content-center pb-5">
            <div class="col-md-12 text-center heading-section ftco-animate">
                <span class="subheading">Resources & Reports</span>
                <h2 class="mb-4">Download Our Publications</h2>
                <p>Access our latest reports, manuals, guides, and other resources to learn more about our work and impact.</p>
            </div>
        </div>

        <?php
        // Check if there are any publications
        $hasAnyPublications = false;
        foreach ($categorizedPublications as $publications) {
            if (!empty($publications)) {
                $hasAnyPublications = true;
                break;
            }
        }
        ?>

        <?php if (!$hasAnyPublications): ?>
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted">
                        No publications available at the moment. 
                        <a href="<?php echo htmlspecialchars(rtrim(BASE_URL, '/')); ?>/contact">Contact us</a> for more information.
                    </p>
                </div>
            </div>
        <?php else: ?>
            <?php
            // Define category metadata
            $categoryMetadata = [
                'manuals_handbooks' => ['title' => 'Manuals & Handbooks', 'icon' => 'fas fa-book'],
                'annual_reports' => ['title' => 'Annual Reports', 'icon' => 'fas fa-file-contract'],
                'other' => ['title' => 'Other Publications', 'icon' => 'fas fa-file'],
            ];
            $categoryCount = count(array_filter($categorizedPublications, fn($pubs) => !empty($pubs)));
            $currentCategoryIndex = 0;
            ?>

            <!-- Loop through each category -->
            <?php foreach ($categorizedPublications as $categoryKey => $publicationsInCategory): ?>
                <?php if (!empty($publicationsInCategory)): ?>
                    <?php
                    $categoryInfo = $categoryMetadata[$categoryKey] ?? [
                        'title' => ucwords(str_replace('_', ' ', $categoryKey)),
                        'icon' => 'fas fa-folder'
                    ];
                    ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <h3 class="mb-3">
                                <i class="<?php echo htmlspecialchars($categoryInfo['icon']); ?> mr-2" aria-hidden="true"></i>
                                <?php echo htmlspecialchars($categoryInfo['title']); ?>
                            </h3>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <?php foreach ($publicationsInCategory as $publication): ?>
                            <?php
                            // Safely get publication data with defaults
                            $id = $publication['id'] ?? 0;
                            $title = $publication['title'] ?? 'Untitled Publication';
                            $filename = $publication['filename'] ?? '';
                            $description = $publication['description'] ?? '';
                            $fileSizeBytes = $publication['file_size'] ?? null;
                            $uploadedAt = $publication['uploaded_at'] ?? null;
                            $sortOrder = $publication['sort_order'] ?? 0;

                            // Sanitize filename
                            $sanitizedFilename = basename($filename); // Prevent path traversal
                            // Use direct file URL since .htaccess allows /images/
                            $fileUrl = rtrim(BASE_URL, '/') . '/images/publications/' . rawurlencode($sanitizedFilename);
                            // Log for debugging (optional)
                            $filePath = PUBLIC_PATH . '/images/publications/' . $sanitizedFilename;
                            if ($sanitizedFilename && !file_exists($filePath)) {
                                error_log("index.php - File not found: $filePath (category: $categoryKey, filename: $sanitizedFilename)");
                            }

                            // Determine icon based on file extension
                            $fileExtension = $sanitizedFilename ? strtolower(pathinfo($sanitizedFilename, PATHINFO_EXTENSION)) : '';
                            $iconClass = match ($fileExtension) {
                                'pdf' => 'fas fa-file-pdf text-danger',
                                'doc', 'docx' => 'fas fa-file-word text-primary',
                                'xls', 'xlsx' => 'fas fa-file-excel text-success',
                                'ppt', 'pptx' => 'fas fa-file-powerpoint text-warning',
                                'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-info',
                                'zip', 'rar' => 'fas fa-file-archive text-secondary',
                                'txt' => 'fas fa-file-alt text-muted',
                                default => 'fas fa-file'
                            };

                            // Format file size and date
                            $fileSizeFormatted = formatFileSize($fileSizeBytes);
                            $modDateFormatted = $uploadedAt ? date('F j, Y', strtotime($uploadedAt)) : '—';
                            ?>
                            <div class="col-md-6 col-lg-4 d-flex ftco-animate">
                                <div class="blog-entry align-self-stretch w-100">
                                    <a href="<?php echo htmlspecialchars($fileUrl); ?>" 
                                       target="_blank" rel="noopener noreferrer"
                                       class="block-20 d-flex align-items-center justify-content-center" style="background-color: #f8f9fa; min-height: 200px;">
                                        <div class="text-center">
                                            <span class="<?php echo htmlspecialchars($iconClass); ?> fa-3x mb-2" aria-hidden="true"></span>
                                            <h4 class="heading mb-0"><?php echo htmlspecialchars($title); ?></h4>
                                            <p class="mb-0 text-muted small"><?php echo htmlspecialchars(strtoupper($fileExtension ?: 'N/A')); ?> | <?php echo htmlspecialchars($fileSizeFormatted); ?></p>
                                            <p class="mb-0 text-muted small"><?php echo htmlspecialchars($modDateFormatted); ?></p>
                                        </div>
                                    </a>
                                    <div class="text p-4 d-block">
                                        <?php if (!empty($description)): ?>
                                            <p class="mb-3"><?php echo htmlspecialchars($description); ?></p>
                                        <?php else: ?>
                                            <p class="mb-3 text-muted">No description available.</p>
                                        <?php endif; ?>
                                        <p class="text-center">
                                            <a href="<?php echo htmlspecialchars($fileUrl); ?>" 
                                               class="btn btn-primary btn-outline-primary py-2 px-3"
                                               target="_blank" rel="noopener noreferrer">
                                               Download <i class="fas fa-download ml-1" aria-hidden="true"></i>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (++$currentCategoryIndex < $categoryCount): ?>
                        <hr class="my-5">
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

