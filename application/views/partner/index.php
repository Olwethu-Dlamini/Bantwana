<?php
// Initialize SettingModel to get hero data
require_once BASE_PATH . '/application/models/SettingModel.php';
$settingModel = new SettingModel();

// Get hero data from SettingModel instead of controller
$heroImageFilename = $settingModel->get('partner_hero_image', 'partner_hero_68ba9e176080f.jpg');
$heroImageUrl = BASE_URL . '/images/' . htmlspecialchars($heroImageFilename);
$heroTitle = $settingModel->get('partner_hero_title', 'Partner With Us');
$heroSubtitle = $settingModel->get('partner_hero_subtitle', 'Join forces to create sustainable change for vulnerable children and families.');

// SEO Meta Data for head section
$pageTitle = 'Partner With Us - ' . ($heroTitle ?? 'Bantwana Initiative Eswatini');
$pageDescription = 'Join Bantwana Initiative Eswatini in creating lasting change. Explore funding, in-kind, awareness, and implementation partnership opportunities.';
$pageKeywords = 'partnership, collaboration, NGO Eswatini, children welfare, community development, funding partners, implementation partners';
?>

<!-- SEO Meta Tags (to be added to head section of layout) -->
<meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords); ?>">
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?php echo BASE_URL; ?>/partner-with-us">

<!-- Open Graph Tags -->
<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
<meta property="og:image" content="<?php echo $heroImageUrl; ?>">
<meta property="og:url" content="<?php echo BASE_URL; ?>/partner-with-us">
<meta property="og:type" content="website">

<!-- Schema.org Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Bantwana Initiative Eswatini",
    "url": "<?php echo BASE_URL; ?>",
    "logo": "<?php echo BASE_URL; ?>/images/logo.png",
    "description": "<?php echo htmlspecialchars($pageDescription); ?>",
    "address": {
        "@type": "PostalAddress",
        "addressCountry": "SZ"
    },
    "contactPoint": {
        "@type": "ContactPoint",
        "email": "partnerships@bantwana.co.sz",
        "contactType": "Partnership Inquiries"
    }
}
</script>

<!-- Additional CSS for enhancements -->
<style>
/* Partnership Icons Styles */
.partnership-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #ffa200ff, #e29521c6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin-bottom: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.partnership-icon:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(255, 174, 54, 0.88);
}

/* Partners Carousel Styles */
.partners-carousel {
    overflow: hidden;
    position: relative;
    background: #f8f9fa;
    padding: 40px 0;
    border-radius: 10px;
    margin: 20px 0;
}

.partners-track {
    display: flex;
    animation: scroll 25s linear infinite;
    gap: 60px;
    align-items: center;
}

.partner-logo {
    flex-shrink: 0;
    padding: 0 20px;
}

.partner-logo img {
    max-height: 80px;
    max-width: 150px;
    object-fit: contain;
    filter: grayscale(70%);
    transition: filter 0.3s ease, transform 0.3s ease;
}

.partner-logo img:hover {
    filter: grayscale(0%);
    transform: scale(1.1);
}

@keyframes scroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

.partners-carousel:hover .partners-track {
    animation-play-state: paused;
}

/* Enhanced Service Cards */
.service-card-enhanced {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    border: 1px solid #e9ecef;
}

.service-card-enhanced:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

/* Benefits list styling */
.benefits-list {
    list-style: none;
    padding: 0;
}

.benefits-list li {
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
    position: relative;
    padding-left: 30px;
}

.benefits-list li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #28a745;
    font-weight: bold;
    font-size: 18px;
}

.benefits-list li:last-child {
    border-bottom: none;
}

/* Performance optimizations */
img {
    loading: lazy;
}
</style>

<!-- Hero Section -->
<div class="hero-wrap" style="background-image: url('<?php echo $heroImageUrl; ?>');" data-stellar-background-ratio="0.5">
    <div class="hero-gradient"></div>
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center" data-scrollax-parent="true">
            <div class="col-md-9 ftco-animate pb-5 text-center" data-scrollax=" properties: { translateY: '70%' }">
                <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <span class="mr-2"><a href="<?php echo BASE_URL; ?>/">Home</a></span> <span>Partner With Us</span>
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
                <span class="subheading">Strategic Alliances</span>
                <h2 class="mb-4">Why Partner With Bantwana?</h2>
                <p>We believe that collaboration amplifies our impact. By partnering with Bantwana Initiative Eswatini, you join a network of organizations and individuals committed to transforming lives and strengthening communities across the region.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 ftco-animate">
                <h3 class="mb-4">Types of Partnerships</h3>
                <p>We welcome a variety of partnership models tailored to your organization's strengths and our shared goals:</p>

                <div class="row mb-4">
                    <div class="col-md-6 mb-4">
                        <div class="media block-6 services d-flex p-3 py-4 d-block w-100 service-card-enhanced">
                            <div class="partnership-icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div class="media-body pl-4">
                                <h4 class="heading">Funding Partners</h4>
                                <p>Provide financial resources to support our core programs, specific initiatives, or emergency responses. Your investment directly improves outcomes for children and families.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="media block-6 services d-flex p-3 py-4 d-block w-100 service-card-enhanced">
                            <div class="partnership-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <div class="media-body pl-4">
                                <h4 class="heading">In-Kind Partners</h4>
                                <p>Donate goods, services, or expertise (e.g., office supplies, transportation, pro-bono professional services) to help us operate more efficiently and effectively.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="media block-6 services d-flex p-3 py-4 d-block w-100 service-card-enhanced">
                            <div class="partnership-icon">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <div class="media-body pl-4">
                                <h4 class="heading">Awareness Partners</h4>
                                <p>Help us raise awareness about our mission and the challenges faced by vulnerable populations through joint campaigns, events, or media outreach.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="media block-6 services d-flex p-3 py-4 d-block w-100 service-card-enhanced">
                            <div class="partnership-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div class="media-body pl-4">
                                <h4 class="heading">Implementation Partners</h4>
                                <p>Collaborate with us on the ground to design, implement, and evaluate programs, leveraging our combined expertise and networks for greater reach and effectiveness.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="mt-5 mb-4">Benefits of Partnership</h3>
                <ul class="benefits-list">
                    <li><strong>Direct Impact:</strong> See the tangible results of your contribution on the ground.</li>
                    <li><strong>Local Expertise:</strong> Benefit from our deep understanding of the local context and established community relationships.</li>
                    <li><strong>Transparency & Accountability:</strong> Receive regular updates and reports on how your partnership is making a difference.</li>
                    <li><strong>Co-Creation:</strong> Work with us to develop innovative solutions tailored to specific needs.</li>
                    <li><strong>Network Access:</strong> Connect with other like-minded organizations and stakeholders in the development sector.</li>
                    <li><strong>Brand Alignment:</strong> Associate your brand with a respected and impactful organization.</li>
                </ul>

                <h3 class="mt-5 mb-4">Our Successful Partnerships</h3>
                <p>We are proud to collaborate with a diverse range of partners, including:</p>
                
                <!-- Enhanced Partners Display with Carousel -->
                <div class="partners-carousel">
                    <div class="partners-track">
                        <!-- First set of partners -->
                        <div class="partner-logo">
                            <img src="<?php echo BASE_URL; ?>/images/PEPFAR.jpg" alt="PEPFAR - President's Emergency Plan for AIDS Relief" class="img-fluid">
                        </div>
                        <div class="partner-logo">
                            <img src="<?php echo BASE_URL; ?>/images/USAID.jpg" alt="USAID - United States Agency for International Development" class="img-fluid">
                        </div>
                        <div class="partner-logo">
                            <img src="<?php echo BASE_URL; ?>/images/eswatini.png" alt="Government of Eswatini" class="img-fluid">
                        </div>
                        <div class="partner-logo">
                            <img src="<?php echo BASE_URL; ?>/images/GlobalFund.jpg" alt="The Global Fund to Fight AIDS, Tuberculosis and Malaria" class="img-fluid">
                        </div>
                        <div class="partner-logo">
                            <img src="<?php echo BASE_URL; ?>/images/UNICEF.jpg" alt="UNICEF - United Nations Children's Fund" class="img-fluid">
                        </div>
                        <div class="partner-logo">
                            <img src="<?php echo BASE_URL; ?>/images/world bank.jpg" alt="World Bank Group" class="img-fluid">
                        </div>
                        <!-- Duplicate set for smooth infinite scroll -->
                        <div class="partner-logo">
                            <img src="<?php echo BASE_URL; ?>/images/PEPFAR.jpg" alt="PEPFAR - President's Emergency Plan for AIDS Relief" class="img-fluid">
                        </div>
                        <div class="partner-logo">
                            <img src="<?php echo BASE_URL; ?>/images/USAID.jpg" alt="USAID - United States Agency for International Development" class="img-fluid">
                        </div>
                        <div class="partner-logo">
                            <img src="<?php echo BASE_URL; ?>/images/eswatini.png" alt="Government of Eswatini" class="img-fluid">
                        </div>
                        <div class="partner-logo">
                            <img src="<?php echo BASE_URL; ?>/images/GlobalFund.jpg" alt="The Global Fund to Fight AIDS, Tuberculosis and Malaria" class="img-fluid">
                        </div>
                        <div class="partner-logo">
                            <img src="<?php echo BASE_URL; ?>/images/UNICEF.jpg" alt="UNICEF - United Nations Children's Fund" class="img-fluid">
                        </div>
                        <div class="partner-logo">
                            <img src="<?php echo BASE_URL; ?>/images/world bank.jpg" alt="World Bank Group" class="img-fluid">
                        </div>
                    </div>
                </div>
                
            </div> <!-- .col-lg-8 -->

            <div class="col-lg-4 sidebar ftco-animate">
                <div class="sidebar-box ftco-animate bg-light p-4">
                    <h3 class="heading-2 mb-4">Get In Touch</h3>
                    <p>Interested in exploring a partnership opportunity with Bantwana Initiative Eswatini? We'd love to hear from you!</p>
                    <p>Please contact our Partnerships Team to discuss how we can work together.</p>
                    <p><a href="<?php echo BASE_URL; ?>/contact" class=" btn-outline- px-4 py-3">Contact Our Partnerships Team</a></p>
                </div>
            </div> <!-- .col-lg-4 -->
        </div> <!-- .row -->
    </div> <!-- .container -->
</section>

<!-- Optional: Call to Action -->
<section class="ftco-section-3 img" style="background-image: url(<?php echo BASE_URL; ?>/images/bg_6.jpg);">
    <div class="overlay"></div>
    <div class="container">
        <div class="row d-md-flex align-items-center justify-content-center text-center">
            <div class="col-md-8 ftco-animate">
                <h2 class="mb-3">Ready to Partner?</h2>
                <p>Let's start a conversation about how we can achieve more together.</p>
                <p><a href="<?php echo BASE_URL; ?>/contact" class="btn btn-white btn-outline-white px-4 py-3">Contact Our Partnerships Team</a></p>
            </div>
        </div>
    </div>
</section>
<!-- End Call to Action -->