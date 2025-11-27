<?php
// Initialize SettingModel to get hero data
require_once BASE_PATH . '/application/models/SettingModel.php';
$settingModel = new SettingModel();

// Get hero data from SettingModel instead of controller
$heroImageFilename = $settingModel->get('blog_hero_image', 'bg_2.jpg');
$heroTitle = $settingModel->get('blog_hero_title', 'Blog');
$heroSubtitle = $settingModel->get('blog_hero_subtitle', 'Stay updated with our latest news and stories.');

// Check if the custom image exists, otherwise use default
$heroImagePath = BASE_PATH . '/public_html/images/' . $heroImageFilename;
if (!empty($heroImageFilename) && file_exists($heroImagePath)) {
    $heroImageUrl = BASE_URL . '/images/' . htmlspecialchars($heroImageFilename);
} else {
    $heroImageUrl = BASE_URL . '/images/bg_2.jpg';
    $heroImageFilename = 'bg_2.jpg'; // Reset to default for display
}
?>

<!-- Hero Section -->
<div class="hero-wrap" style="background-image: url('<?php echo $heroImageUrl; ?>');" data-stellar-background-ratio="1">
    <div class="hero-gradient"></div> <!-- Gradient overlay -->
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center" data-scrollax-parent="true">
            <div class="col-md-9 ftco-animate pb-5 text-center" data-scrollax="properties: { translateY: '70%' }">
                <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <span class="mr-2"><a href="<?php echo BASE_URL; ?>/">Home</a></span> <span>Blog</span>
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

<!-- Blog Content Section -->
<section class="ftco-section bg-light">
    <div class="container">
        <div class="row">
            <!-- Main Facebook Feed Column -->
            <div class="col-lg-8 ftco-animate">
                <!-- Facebook Feed Section (from original blog page, without loading indicator) -->
                <div id="dynamic-content" class="fb-feed-container">
                    <!-- Facebook Page Plugin (will be populated via AJAX or directly) -->
                    <div id="fb-root"></div>
                    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v23.0&appId=395042390636886"></script>
                    <div class="fb-page" data-href="https://www.facebook.com/bantwanainitiativeeswatini" data-tabs="timeline" data-width="800" data-height="1200" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true">
                        <blockquote cite="https://www.facebook.com/bantwanainitiativeeswatini" class="fb-xfbml-parse-ignore">
                            <a href="https://www.facebook.com/bantwanainitiativeeswatini">Bantwana Initiative Eswatini</a>
                        </blockquote>
                    </div>
                </div>
            </div>

            <!-- Sidebar with Social Media Links -->
            <div class="col-lg-4 sidebar ftco-animate">
                <!-- Social Media Section -->
                <div class="sidebar-box bg-white p-4 shadow-sm rounded mb-4">
                    <h3 class="heading-2 mb-4 text-center">Follow Us</h3>
                    <p class="text-center text-muted mb-4">Stay connected with Bantwana Initiative Eswatini on our social media channels</p>
                    
                    <div class="social-media-links">
                        <!-- Facebook -->
                        <div class="social-link-item mb-3 p-3 border rounded">
                            <div class="d-flex align-items-center">
                                <div class="social-icon facebook-icon me-3">
                                    <i class="fab fa-facebook-f"></i>
                                </div>
                                <div class="social-content">
                                    <h6 class="mb-1">Facebook</h6>
                                    <p class="text-muted small mb-2">Follow our page for daily updates</p>
                                    <a href="https://www.facebook.com/bantwanainitiativeeswatini" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                        <i class="fab fa-facebook-f mr-1"></i> Follow
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- LinkedIn -->
                        <div class="social-link-item mb-3 p-3 border rounded">
                            <div class="d-flex align-items-center">
                                <div class="social-icon linkedin-icon me-3">
                                    <i class="fab fa-linkedin-in"></i>
                                </div>
                                <div class="social-content">
                                    <h6 class="mb-1">LinkedIn</h6>
                                    <p class="text-muted small mb-2">Professional updates and partnerships</p>
                                    <a href="https://www.linkedin.com/company/bantwana-initiative-eswatini" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                        <i class="fab fa-linkedin-in mr-1"></i> Connect
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Instagram -->
                        <div class="social-link-item mb-3 p-3 border rounded">
                            <div class="d-flex align-items-center">
                                <div class="social-icon instagram-icon me-3">
                                    <i class="fab fa-instagram"></i>
                                </div>
                                <div class="social-content">
                                    <h6 class="mb-1">Instagram</h6>
                                    <p class="text-muted small mb-2">Behind the scenes and community stories</p>
                                    <a href="https://www.instagram.com/bantwana_initiative_eswatini" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                        <i class="fab fa-instagram mr-1"></i> Follow
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="sidebar-box bg-white p-4 shadow-sm rounded">
                    <h3 class="heading-2 mb-4 text-center">Contact Us</h3>
                    <div class="contact-info">
                        <div class="contact-item mb-3">
                            <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                            <span class="small">Bantwana Offices, Lot 482, Corner of Mimosa and Syringa Road, Courts Valley, Manzini, Eswatini</span>
                        </div>
                        <div class="contact-item mb-3">
                            <i class="fas fa-phone text-primary mr-2"></i>
                            <a href="tel:+26825052848" class="text-decoration-none">+268 2505 2848</a>
                        </div>
                        <div class="contact-item mb-3">
                            <i class="fas fa-envelope text-primary mr-2"></i>
                            <a href="mailto:info@bantwana.co.sz" class="text-decoration-none">info@bantwana.co.sz</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Container for the Facebook feed (from original blog page) */
.fb-feed-container {
    max-width: 800px; /* Increased width for a larger feed */
    margin: 0 auto; /* Center the feed */
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Ensure the feed is responsive */
@media (max-width: 850px) {
    .fb-feed-container {
        max-width: 100%;
        padding: 10px;
    }
}

/* Adjust the Facebook iframe for larger display */
.fb-page {
    width: 100%;
    min-height: 1200px; /* Increased height for larger feed */
}

/* Ensure the feed scales properly on smaller screens */
@media (max-width: 600px) {
    .fb-page {
        min-height: 800px; /* Adjust height for smaller screens */
    }
}

/* Social Media Icons */
.social-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin-right: 15px;
    flex-shrink: 0;
}

.facebook-icon {
    background: linear-gradient(135deg, #3b5998, #8b9dc3);
}

.linkedin-icon {
    background: linear-gradient(135deg, #0077b5, #00a0dc);
}

.instagram-icon {
    background: linear-gradient(135deg, #405de6, #5851db, #833ab4, #c13584, #e1306c, #fd1d1d, #f56040, #f77737, #fcaf45, #ffdc80);
}

.social-link-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.social-link-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Contact info styling */
.contact-item {
    display: flex;
    align-items: flex-start;
}

.contact-item i {
    margin-top: 2px;
    flex-shrink: 0;
}

/* Ensure responsive behavior */
@media (max-width: 991px) {
    .fb-feed-container {
        margin-bottom: 30px;
    }
}

/* Button styling */
.btn-outline-primary {
    border-color: #4CAF50;
    color: #4CAF50;
}

.btn-outline-primary:hover {
    background-color: #4CAF50;
    border-color: #4CAF50;
}

.btn-primary {
    background-color: #4CAF50;
    border-color: #4CAF50;
}

.btn-primary:hover {
    background-color: #45a049;
    border-color: #45a049;
}
</style>

<script>
$(document).ready(function() {
    // Function to load Facebook feed content (from original blog page)
    function loadFacebookFeed() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/home/fetchPosts', // Adjust to your controller/method
            type: 'GET',
            data: { source: 'facebook' }, // Request Facebook feed
            dataType: 'html',
            success: function(response) {
                $('#dynamic-content').html(response); // Inject the response
                // Trigger FB SDK to re-parse the fb-page element
                if (typeof FB !== 'undefined') {
                    FB.XFBML.parse();
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " - " + error);
                $('#dynamic-content').html('<p class="text-center text-danger">Failed to load Facebook feed. Please try again later.</p>');
            }
        });
    }

    // Auto-load the Facebook feed on page load
    loadFacebookFeed();

    // Add smooth hover effects for social media links
    $('.social-link-item').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );

    // Track social media clicks for analytics (optional)
    $('.social-link-item a').click(function() {
        var platform = $(this).find('i').hasClass('fa-facebook-f') ? 'Facebook' : 
                      $(this).find('i').hasClass('fa-linkedin-in') ? 'LinkedIn' : 
                      $(this).find('i').hasClass('fa-instagram') ? 'Instagram' : 'Unknown';
        
        console.log('Social media click:', platform);
        // Add your analytics tracking code here if needed
    });
});
</script>