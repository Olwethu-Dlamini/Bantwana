<?php
// Initialize SettingModel to get hero data
require_once BASE_PATH . '/application/models/SettingModel.php';
$settingModel = new SettingModel();

// Get hero data from SettingModel instead of controller
$heroImageFilename = $settingModel->get('gallery_hero_image', 'bg_6.jpg');
$heroTitle = $settingModel->get('gallery_hero_title', 'Our Photo Gallery');
$heroSubtitle = $settingModel->get('gallery_hero_subtitle', 'Capturing moments of hope, action, and impact.');

// Check if the custom image exists, otherwise use default
$heroImagePath = BASE_PATH . '/public_html/images/' . $heroImageFilename;
if (!empty($heroImageFilename) && file_exists($heroImagePath)) {
    $heroImageUrl = BASE_URL . '/images/' . htmlspecialchars($heroImageFilename);
} else {
    $heroImageUrl = BASE_URL . '/images/bg_6.jpg';
    $heroImageFilename = 'bg_6.jpg'; // Reset to default for display
}

// Pagination setup
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 3; // Show 6 galleries per page
$totalGalleries = count($data['galleries'] ?? []);
$totalPages = ceil($totalGalleries / $itemsPerPage);
$offset = ($currentPage - 1) * $itemsPerPage;

// Get paginated galleries
$paginatedGalleries = array_slice($data['galleries'] ?? [], $offset, $itemsPerPage);

// SEO Meta Data
$pageTitle = ($currentPage > 1) ? $heroTitle . ' - Page ' . $currentPage : $heroTitle;
$pageDescription = 'Browse through our photo galleries showcasing Bantwana Initiative Eswatini\'s impact, community programs, and transformative stories.';
$pageKeywords = 'photo gallery, Bantwana Initiative, Eswatini, community photos, impact stories, visual documentation';
$canonicalUrl = BASE_URL . '/gallery' . ($currentPage > 1 ? '?page=' . $currentPage : '');
?>

<!-- SEO Meta Tags -->
<meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords); ?>">
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">

<!-- Open Graph Tags -->
<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle . ' - Bantwana Initiative Eswatini'); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
<meta property="og:image" content="<?php echo $heroImageUrl; ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Bantwana Initiative Eswatini">

<!-- Twitter Card Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
<meta name="twitter:image" content="<?php echo $heroImageUrl; ?>">

<!-- Pagination Meta Tags -->
<?php if ($currentPage > 1): ?>
<link rel="prev" href="<?php echo BASE_URL; ?>/gallery<?php echo ($currentPage > 2) ? '?page=' . ($currentPage - 1) : ''; ?>">
<?php endif; ?>
<?php if ($currentPage < $totalPages): ?>
<link rel="next" href="<?php echo BASE_URL; ?>/gallery?page=<?php echo ($currentPage + 1); ?>">
<?php endif; ?>

<!-- Schema.org Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ImageGallery",
    "name": "<?php echo htmlspecialchars($heroTitle); ?>",
    "description": "<?php echo htmlspecialchars($pageDescription); ?>",
    "url": "<?php echo htmlspecialchars($canonicalUrl); ?>",
    "publisher": {
        "@type": "Organization",
        "name": "Bantwana Initiative Eswatini",
        "url": "<?php echo BASE_URL; ?>",
        "logo": {
            "@type": "ImageObject",
            "url": "<?php echo BASE_URL; ?>/images/logo.png"
        }
    },
    "mainEntity": [
        <?php foreach ($paginatedGalleries as $index => $gallery): ?>
        {
            "@type": "ImageObject",
            "name": "<?php echo htmlspecialchars($gallery['name']); ?>",
            "description": "<?php echo htmlspecialchars($gallery['description'] ?: 'Photo gallery from Bantwana Initiative Eswatini'); ?>",
            "contentUrl": "<?php echo !empty($gallery['images']) ? BASE_URL . '/images/gallery/' . htmlspecialchars($gallery['images'][0]['filename']) : BASE_URL . '/images/image_placeholder.jpg'; ?>",
            "thumbnailUrl": "<?php echo !empty($gallery['images']) ? BASE_URL . '/images/gallery/' . htmlspecialchars($gallery['images'][0]['filename']) : BASE_URL . '/images/image_placeholder.jpg'; ?>"
        }<?php echo ($index < count($paginatedGalleries) - 1) ? ',' : ''; ?>
        <?php endforeach; ?>
    ]
}
</script>

<!-- Hero Section -->
<div class="hero-wrap" style="background-image: url('<?php echo $heroImageUrl; ?>');" data-stellar-background-ratio="1">
    <div class="hero-gradient"></div> <!-- Gradient overlay -->
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center" data-scrollax-parent="true">
            <div class="col-md-9 ftco-animate pb-5 text-center" data-scrollax=" properties: { translateY: '70%' }">
                <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <span class="mr-2"><a href="<?php echo BASE_URL; ?>/">Home</a></span> 
                    <span>Gallery</span>
                    <?php if ($currentPage > 1): ?>
                        <span class="mr-2"> - Page <?php echo $currentPage; ?></span>
                    <?php endif; ?>
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

<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center pb-5">
            <div class="col-md-12 text-center heading-section ftco-animate">
                <span class="subheading">Visual Stories</span>
                <h2 class="mb-4">Explore Our Moments</h2>
                <p>Browse through collections of photos showcasing our work, the people we serve, and the communities we impact.</p>
                <?php if ($totalPages > 1): ?>
                    <p class="text-muted">Showing page <?php echo $currentPage; ?> of <?php echo $totalPages; ?> (<?php echo $totalGalleries; ?> total galleries)</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($paginatedGalleries)): ?>
            <div class="row">
                <div class="col-12 text-center">
                    <div class="empty-state py-5">
                        <i class="fas fa-images fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No galleries available</h4>
                        <p class="text-muted">Please check back later for new photo galleries.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Gallery Grid -->
            <div class="row gallery-grid" id="gallery-container">
                <?php foreach ($paginatedGalleries as $gallery): ?>
                    <div class="col-md-6 col-lg-4 ftco-animate d-flex align-items-stretch mb-4 gallery-item">
                        <div class="blog-entry d-flex flex-column w-100 shadow-sm">
                            <?php
                                // Use first image for thumbnail or a placeholder if the gallery is empty.
                                $thumbnailUrl = !empty($gallery['images'])
                                    ? BASE_URL . '/images/gallery/' . htmlspecialchars($gallery['images'][0]['filename'])
                                    : BASE_URL . '/images/image_placeholder.jpg';
                                $imageCount = count($gallery['images']);
                            ?>
                            <a href="#galleryModal<?php echo $gallery['id']; ?>" class="img-link" data-toggle="modal" aria-label="Open <?php echo htmlspecialchars($gallery['name']); ?> gallery">
                                <div class="image-container">
                                    <img src="<?php echo $thumbnailUrl; ?>" 
                                         class="img-fluid gallery-image" 
                                         alt="Thumbnail for <?php echo htmlspecialchars($gallery['name']); ?>"
                                         width="400" 
                                         height="300"
                                         loading="lazy">
                                    <div class="image-overlay">
                                        <div class="overlay-content">
                                            <i class="fas fa-search-plus fa-2x"></i>
                                            <p class="mt-2">View Gallery</p>
                                        </div>
                                    </div>
                                    <div class="image-count-badge">
                                        <i class="fas fa-images"></i>
                                        <span><?php echo $imageCount; ?></span>
                                    </div>
                                </div>
                            </a>
                            <div class="text p-4 d-flex flex-column flex-grow-1">
                                <h3 class="heading">
                                    <a href="#galleryModal<?php echo $gallery['id']; ?>" data-toggle="modal">
                                        <?php echo htmlspecialchars($gallery['name']); ?>
                                    </a>
                                </h3>
                                <p class="gallery-description">
                                    <?php echo htmlspecialchars(strlen($gallery['description'] ?: '') > 100 
                                        ? substr($gallery['description'], 0, 100) . '...' 
                                        : ($gallery['description'] ?: 'A collection of moments from this initiative.')); ?>
                                </p>
                                <div class="mt-auto">
                                    <p class="meta d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-images text-primary mr-1"></i> <?php echo $imageCount; ?> Photos</span>
                                        <?php if (isset($gallery['created_at'])): ?>
                                            <span class="text-muted small">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                <?php echo date('M j, Y', strtotime($gallery['created_at'])); ?>
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                    <p>
                                        <a href="#galleryModal<?php echo $gallery['id']; ?>" 
                                           class="btn btn-primary btn-block" 
                                           data-toggle="modal"
                                           aria-label="View <?php echo htmlspecialchars($gallery['name']); ?> gallery">
                                            View Gallery <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Modal with Lazy Loading -->
                    <div class="modal fade" id="galleryModal<?php echo $gallery['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="galleryModalLabel<?php echo $gallery['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="galleryModalLabel<?php echo $gallery['id']; ?>">
                                        <i class="fas fa-images mr-2"></i><?php echo htmlspecialchars($gallery['name']); ?>
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php if (!empty($gallery['description'])): ?>
                                        <div class="gallery-description-full mb-4 p-3 bg-light rounded">
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($gallery['description'])); ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (empty($gallery['images'])): ?>
                                        <div class="text-center py-5">
                                            <i class="fas fa-image fa-4x text-muted mb-3"></i>
                                            <p class="text-muted">This gallery is currently empty.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="row gallery-images-grid">
                                            <?php foreach ($gallery['images'] as $index => $image): ?>
                                            <div class="col-md-4 col-sm-6 mb-4 gallery-image-item">
                                                <div class="gallery-image-container">
                                                    <a href="<?php echo BASE_URL; ?>/images/gallery/<?php echo htmlspecialchars($image['filename']); ?>" 
                                                       class="image-popup gal-item d-block"
                                                       data-index="<?php echo $index; ?>"
                                                       aria-label="View image <?php echo ($index + 1); ?> of <?php echo count($gallery['images']); ?>">
                                                        <div class="image-wrapper">
                                                            <img src="<?php echo BASE_URL; ?>/images/gallery/<?php echo htmlspecialchars($image['filename']); ?>" 
                                                                 class="img-fluid rounded modal-image" 
                                                                 alt="<?php echo htmlspecialchars($image['alt_text'] ?: 'Image ' . ($index + 1) . ' from ' . $gallery['name']); ?>"
                                                                 width="300" 
                                                                 height="225"
                                                                 loading="lazy">
                                                            <div class="image-overlay-small">
                                                                <i class="fas fa-search-plus"></i>
                                                            </div>
                                                        </div>
                                                        <?php if (!empty($image['caption'])): ?>
                                                            <div class="image-caption mt-2 small text-muted">
                                                                <?php echo htmlspecialchars($image['caption']); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer d-flex justify-content-between">
                                    <div>
                                        <?php if (!empty($gallery['images'])): ?>
                                            <span class="text-muted">
                                                <i class="fas fa-images mr-1"></i>
                                                <?php echo count($gallery['images']); ?> images
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fas fa-times mr-1"></i>Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Enhanced Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="row mt-5">
                    <div class="col-12">
                        <nav aria-label="Gallery pagination" class="d-flex justify-content-center">
                            <ul class="pagination pagination-lg">
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo BASE_URL; ?>/gallery" aria-label="First page">
                                            <i class="fas fa-angle-double-left"></i>
                                        </a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo BASE_URL; ?>/gallery<?php echo ($currentPage > 2) ? '?page=' . ($currentPage - 1) : ''; ?>" aria-label="Previous page">
                                            <i class="fas fa-angle-left"></i> Previous
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                // Show page numbers with ellipsis for large page counts
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                
                                if ($startPage > 1): ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo BASE_URL; ?>/gallery">1</a></li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif;
                                endif;

                                for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo BASE_URL; ?>/gallery<?php echo ($i > 1) ? '?page=' . $i : ''; ?>">
                                            <?php echo $i; ?>
                                            <?php if ($i == $currentPage): ?>
                                                <span class="sr-only">(current)</span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endfor;

                                if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo BASE_URL; ?>/gallery?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a></li>
                                <?php endif; ?>

                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo BASE_URL; ?>/gallery?page=<?php echo ($currentPage + 1); ?>" aria-label="Next page">
                                            Next <i class="fas fa-angle-right"></i>
                                        </a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo BASE_URL; ?>/gallery?page=<?php echo $totalPages; ?>" aria-label="Last page">
                                            <i class="fas fa-angle-double-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Call to Action Section -->
<section class="ftco-section-3 img" style="background-image: url(<?php echo BASE_URL; ?>/images/bg_5.jpg);">
    <div class="overlay"></div>
    <div class="container">
        <div class="row d-md-flex align-items-center justify-content-center text-center">
            <div class="col-md-8 ftco-animate">
                <h2 class="mb-3">Support Our Mission</h2>
                <p>Your contribution helps us capture and share more stories of hope and transformation.</p>
                <p><a href="<?php echo BASE_URL; ?>/donate" class="btn btn-white btn-outline-white px-4 py-3">Donate Now</a></p>
            </div>
        </div>
    </div>
</section>

<style>
/* Enhanced Gallery Styles */
.gallery-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gallery-item:hover {
    transform: translateY(-5px);
}

.blog-entry {
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.blog-entry:hover {
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

/* Uniform Image Container */
.img-link {
    display: block;
    overflow: hidden;
    height: 250px;
    position: relative;
}

.image-container {
    position: relative;
    height: 100%;
    overflow: hidden;
}

.gallery-image, .modal-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease, filter 0.3s ease;
}

/* Image Overlay Effects */
.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
    text-align: center;
}

.img-link:hover .image-overlay {
    opacity: 1;
}

.img-link:hover .lazy-image {
    transform: scale(1.1);
}

/* Image Count Badge */
.image-count-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Modal Gallery Images */
.gallery-images-grid {
    max-height: 600px;
    overflow-y: auto;
}

.gallery-image-container {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
}

.image-wrapper {
    position: relative;
    height: 200px;
    overflow: hidden;
    border-radius: 10px;
}

.modal-image {
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.image-overlay-small {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
    font-size: 1.2rem;
}

.gallery-image-container:hover .image-overlay-small {
    opacity: 1;
}

.gallery-image-container:hover .modal-image {
    transform: scale(1.05);
}

/* Empty State */
.empty-state {
    opacity: 0.7;
}

/* Pagination Styling */
.pagination-lg .page-link {
    padding: 12px 20px;
    font-size: 1.1rem;
    border-radius: 8px;
    margin: 0 3px;
    border: 1px solid #dee2e6;
    color: #4CAF50;
}

.pagination-lg .page-item.active .page-link {
    background-color: #4CAF50;
    border-color: #4CAF50;
}

.pagination-lg .page-link:hover {
    background-color: #f8f9fa;
    border-color: #4CAF50;
}

/* Loading Animation */
.lazy-image[data-src] {
    background: linear-gradient(90deg, #f0f0f0 25%, transparent 37%, #f0f0f0 63%);
    background-size: 400% 100%;
    animation: loading 1.5s ease-in-out infinite;
}

@keyframes loading {
    0% { background-position: 100% 50%; }
    100% { background-position: -100% 50%; }
}

/* Responsive Design */
@media (max-width: 768px) {
    .img-link {
        height: 200px;
    }
    
    .image-wrapper {
        height: 150px;
    }
    
    .pagination-lg .page-link {
        padding: 8px 12px;
        font-size: 1rem;
    }
    
    .gallery-images-grid {
        max-height: 400px;
    }
}

/* Accessibility Improvements */
.page-link:focus,
.image-popup:focus,
.btn:focus {
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.25);
    outline: none;
}

/* Print Styles */
@media print {
    .modal, .pagination, .image-overlay, .image-overlay-small {
        display: none !important;
    }
}
</style>

<script>
$(document).ready(function() {
    // Initialize Magnific Popup when modal is shown
    $(document).on('shown.bs.modal', '.modal', function () {
        const modal = $(this);
        const galleryContainer = modal.find('.gallery-images-grid');

        // Initialize Magnific Popup with enhanced options
        galleryContainer.magnificPopup({
            delegate: 'a.gal-item',
            type: 'image',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1]
            },
            image: {
                titleSrc: function(item) {
                    const caption = item.el.find('.image-caption').text();
                    const index = parseInt(item.el.data('index')) + 1;
                    const total = item.el.closest('.gallery-images-grid').find('.gal-item').length;
                    return caption ? `${caption} <small>(${index} of ${total})</small>` : `Image ${index} of ${total}`;
                },
                verticalFit: true
            },
            zoom: {
                enabled: true,
                duration: 300,
                easing: 'ease-in-out',
                opener: function(openerElement) {
                    return openerElement.find('img');
                }
            },
            removalDelay: 300,
            mainClass: 'mfp-with-zoom',
            callbacks: {
                beforeOpen: function() {
                    this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
                    this.st.mainClass = this.st.el.attr('data-effect');
                }
            }
        });
    });

    // Enhanced keyboard navigation
    $(document).on('keydown', function(e) {
        if ($('.modal.show').length > 0) {
            // ESC to close modal
            if (e.keyCode === 27) {
                $('.modal.show').modal('hide');
            }
        }
    });

    // Smooth scroll for pagination
    $('.pagination a').on('click', function(e) {
        $('html, body').animate({
            scrollTop: $('.ftco-section').offset().top - 100
        }, 500);
    });

    // Analytics tracking for gallery interactions
    $('.img-link, .gal-item').on('click', function() {
        const galleryName = $(this).closest('.gallery-item, .modal').find('.modal-title, .heading a').text().trim();
        console.log('Gallery interaction:', galleryName);
        
        // Add your analytics code here
        if (typeof gtag !== 'undefined') {
            gtag('event', 'gallery_view', {
                'event_category': 'Gallery',
                'event_label': galleryName
            });
        }
    });

    // Performance optimization - preload next page images
    if ('requestIdleCallback' in window) {
        requestIdleCallback(function() {
            const nextPageLink = $('.pagination .page-item:not(.active) .page-link[href*="page="]').first();
            if (nextPageLink.length && nextPageLink.attr('href')) {
                const link = document.createElement('link');
                link.rel = 'prefetch';
                link.href = nextPageLink.attr('href');
                document.head.appendChild(link);
            }
        });
    }

    // Image error handling
    $('img').on('error', function() {
        if (!$(this).hasClass('error-handled')) {
            $(this).addClass('error-handled');
            $(this).attr('src', '<?php echo BASE_URL; ?>/images/image_placeholder.jpg');
            $(this).attr('alt', 'Image not available');
        }
    });

    // Loading states for modals
    $('.modal').on('show.bs.modal', function() {
        $(this).find('.modal-body').addClass('loading');
    });

    $('.modal').on('shown.bs.modal', function() {
        setTimeout(() => {
            $(this).find('.modal-body').removeClass('loading');
        }, 300);
    });
});

// Service Worker for caching (if available)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').then(function(registration) {
            console.log('SW registered: ', registration);
        }).catch(function(registrationError) {
            console.log('SW registration failed: ', registrationError);
        });
    });
}

// Performance monitoring
window.addEventListener('load', function() {
    if ('PerformanceObserver' in window) {
        const observer = new PerformanceObserver(function(list) {
            const entries = list.getEntries();
            entries.forEach(function(entry) {
                if (entry.entryType === 'largest-contentful-paint') {
                    console.log('LCP:', entry.startTime);
                }
            });
        });
        observer.observe({entryTypes: ['largest-contentful-paint']});
    }
});
</script>