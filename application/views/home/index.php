<?php
// HERO IMAGE
$heroImageFilename = $data['homeContent']['home_hero_image'] ?? 'bg_7.jpg';
$heroImageUrl = BASE_URL . '/images/' . htmlspecialchars($heroImageFilename);

// HERO TITLE + SUBTITLE
$heroTitle = $data['homeContent']['home_hero_title'] ?? 'Bantwana Initiative Eswatini';
$heroSubtitle = $data['homeContent']['home_hero_subtitle'] ?? 'To enhance the well-being and resilience of vulnerable children, youth, and their families affected by HIV & AIDS and poverty through holistic care, protection, and empowerment.';
?>

<!-- Hero Section with Gradient Overlay -->
<div class="hero-wrap gradient-hero" style="background-image: url('<?php echo $heroImageUrl; ?>');" data-stellar-background-ratio="1">
    <div class="hero-gradient"></div>
    <div class="hero-content">
        <h1 class="hero-title"><?php echo htmlspecialchars($heroTitle); ?></h1>
        <p class="hero-subtitle"><?php echo htmlspecialchars($heroSubtitle); ?></p>
    </div>
</div>

<!-- Bantwana's Mission Section -->
<section class="mission-section ftco-section bg-light">
    <div class="container">
        <div class="row justify-content-center mb-5 pb-3">
            <div class="col-md-8 heading-section ftco-animate text-center">
                <h1 class="mb-4">Bantwana's Vision</h1>
                <p><?php echo htmlspecialchars($data['homeContent']['mission_text'] ?? 'We envision a society where every child is healthy, safe, and empowered to realize their full potential in a nurturing and equitable environment.'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Social Media Sticky Buttons -->
<div class="social-sticky-wrapper">
    <div class="fb-sticky-circle" onclick="toggleFacebookFeed()">
        <i class="fab fa-facebook-f"></i>
    </div>
    <div class="ig-sticky-circle" onclick="toggleInstagramFeed()">
        <i class="fab fa-instagram"></i>
    </div>
</div>

<!-- Facebook Feed -->
<div class="fb-feed" id="fbFeed">
    <div class="close-btn-fb" onclick="toggleFacebookFeed()">&times;</div>
    <iframe 
        src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fbantwanainitiativeeswatini&tabs=timeline&width=340&height=900&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId=395042390636886" 
        width="340" 
        height="900" 
        style="border:none;overflow:hidden" 
        scrolling="yes" 
        frameborder="0" 
        allowfullscreen="true" 
        allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share">
    </iframe>
</div>

<!-- Instagram Feed -->
<div class="ig-feed" id="igFeed">
    <div class="close-btn-ig" onclick="toggleInstagramFeed()">&times;</div>
    <iframe 
        src="https://www.instagram.com/bantwana_initiative_eswatini/embed" 
        width="340" 
        height="900" 
        frameborder="0" 
        scrolling="yes" 
        allowtransparency="true">
    </iframe>
</div>

<!-- Counter Section -->
<section class="ftco-counter ftco-intro" id="section-counter">
    <div class="container">
        <div class="row no-gutters">
            <div class="col-md-5 d-flex justify-content-center counter-wrap ftco-animate">
                <div class="block-18 color-1 align-items-stretch">
                    <div class="text">
                        <span><?php echo htmlspecialchars($data['homeContent']['home_counter_main_text'] ?? 'Served Over'); ?></span>
                        <strong style="color: #ffffffff;" class="number" data-number="<?php echo (int)str_replace(',', '', $data['homeContent']['home_counter_number'] ?? '0'); ?>">
                            <?php echo htmlspecialchars($data['homeContent']['home_counter_number'] ?? '0'); ?>
                        </strong>
                        <span><?php echo htmlspecialchars($data['homeContent']['home_counter_unit'] ?? 'Children in 4 countries in Africa'); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md d-flex justify-content-center counter-wrap ftco-animate">
                <div class="block-18 color-2 align-items-stretch">
                    <div class="text">
                        <h3 class="mb-4"><?php echo htmlspecialchars($data['homeContent']['home_counter_donate_title'] ?? 'Support Our Work'); ?></h3>
                        <p><?php echo htmlspecialchars($data['homeContent']['home_counter_donate_text'] ?? 'Your contribution makes a direct impact. Help us continue our vital programs.'); ?></p>
                        <p><a href="<?php echo BASE_URL; ?>/donate" class="btn btn-white px-3 py-2 mt-2">Donate Now</a></p>
                    </div>
                </div>
            </div>
            <div class="col-md d-flex justify-content-center counter-wrap ftco-animate">
                <div class="block-18 color-3 align-items-stretch">
                    <div class="text">
                        <h3 class="mb-4"><?php echo htmlspecialchars($data['homeContent']['home_counter_volunteer_title'] ?? 'Be a Volunteer'); ?></h3>
                        <p><?php echo htmlspecialchars($data['homeContent']['home_counter_volunteer_text'] ?? 'Give your time and skills to make a difference in our communities.'); ?></p>
                        <p><a href="<?php echo BASE_URL; ?>/volunteer" class="btn btn-white px-3 py-2 mt-2">Volunteer</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="ftco-section">
    <div class="container">
        <div class="row">
            <div class="col-md-6 d-flex align-self-stretch ftco-animate">
                <div class="media block-6 d-flex services p-3 py-4 d-block w-100">
                    <div class="icon d-flex mb-3"><span class="flaticon-donation-1"></span></div>
                    <div class="media-body pl-4">
                        <h3 class="heading">Make a Donation</h3>
                        <p>Provide essential resources like food, education, and healthcare to children and families in need.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-flex align-self-stretch ftco-animate">
                <div class="media block-6 d-flex services p-3 py-4 d-block w-100">
                    <div class="icon d-flex mb-3"><span class="flaticon-charity"></span></div>
                    <div class="media-body pl-4">
                        <h3 class="heading">Become A Volunteer</h3>
                        <p>Join our team of dedicated volunteers and contribute your time and expertise to our programs.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New "Our Latest Programs" Section -->
<?php if (!empty($data['latestPrograms'])): ?>
<section class="ftco-section bg-light">
    <div class="container">
        <div class="row justify-content-center mb-5 pb-3">
            <div class="col-md-7 heading-section ftco-animate text-center">
                <span class="subheading">Our Programs</span>
                <h2 class="mb-4">Our Latest Initiatives</h2>
                <p>Discover the impactful programs we run to support children, youth, and families.</p>
            </div>
        </div>
        <div class="row">
            <?php foreach ($data['latestPrograms'] as $program): ?>
            <?php
            $iconClass = 'flaticon-charity';
            switch (strtolower(substr($program['title'], 0, 5))) {
                case 'healt': $iconClass = 'flaticon-medical'; break;
                case 'econo': case 'incom': $iconClass = 'flaticon-donation'; break;
                case 'educa': $iconClass = 'flaticon-reading'; break;
                case 'socia': case 'child': $iconClass = 'flaticon-shield'; break;
                case 'adole': case 'youth': $iconClass = 'flaticon-user'; break;
            }
            $defaultImage = BASE_URL . '/images/image_placeholder.jpg';
            $programImage = !empty($program['image_filename']) ? BASE_URL . '/images/programs/' . htmlspecialchars($program['image_filename']) : $defaultImage;
            ?>
            <div class="col-md-4 d-flex ftco-animate">
                <div class="blog-entry align-self-stretch">
                    <a href="<?php echo BASE_URL; ?>/programs#program-<?php echo $program['id']; ?>" class="block-20" style="background-image: url('<?php echo $programImage; ?>');">
                    </a>
                    <div class="text p-4 d-block">
                        <div class="meta mb-3">
                            <div><a href="#"><span class="icon-calendar"></span> Ongoing</a></div>
                            <div><a href="#"><span class="icon-map-marker"></span> <?php echo htmlspecialchars($program['location'] ?? 'Nationwide'); ?></a></div>
                        </div>
                        <h3 class="heading mb-3"><a href="<?php echo BASE_URL; ?>/programs#program-<?php echo $program['id']; ?>"><?php echo htmlspecialchars($program['title']); ?></a></h3>
                        <p><?php echo htmlspecialchars(substr(strip_tags($program['content']), 0, 100) . (strlen(strip_tags($program['content'])) > 100 ? '...' : '')); ?></p>
                        <p><a href="<?php echo BASE_URL; ?>/programs#program-<?php echo $program['id']; ?>" class="btn btn-outline-primary py-2 px-3">Learn More</a></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
         <div class="row mt-4">
            <div class="col text-center">
                <p><a href="<?php echo BASE_URL; ?>/programs" class="btn btn-primary">View All Programs</a></p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
<!-- End New "Our Latest Programs" Section -->

<style>
/* Hero Section Styles */
.gradient-hero {
    position: relative;
    height: clamp(400px, 65vh, 800px);
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.hero-gradient {
    position: absolute;
    inset: 0;
    z-index: 1;
    background: linear-gradient(
        to bottom,
        rgba(0, 0, 0, 0.5) 0%,
        rgba(0, 0, 0, 0.5) 50%,
        rgba(0, 0, 0, 0.5) 100%
    );
    pointer-events: none;
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 1000px;
    text-align: center;
}

.hero-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    color: #ffffff;
    text-shadow: 0 3px 15px rgba(0, 0, 0, 0.7);
}

.hero-subtitle {
    font-size: clamp(1rem, 2vw, 1.5rem);
    color: #f8f8f8;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
}

@media (max-width: 768px) {
    .gradient-hero {
        height: clamp(350px, 60vh, 600px);
        padding: 1.5rem;
    }
    .hero-title {
        font-size: clamp(2rem, 4.5vw, 3rem);
    }
    .hero-subtitle {
        font-size: clamp(1rem, 2vw, 1.5rem);
    }
}

@media (max-width: 480px) {
    .gradient-hero {
        padding: 1rem;
    }
    .hero-title {
        font-size: clamp(1.75rem, 4vw, 2.5rem);
    }
    .hero-subtitle {
        font-size: clamp(0.9rem, 1.8vw, 1.25rem);
    }
}

/* Social Media Sticky Buttons */
.social-sticky-wrapper {
    position: fixed;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 9998;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.fb-sticky-circle,
.ig-sticky-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.fb-sticky-circle {
    background: linear-gradient(45deg, #4267B2 0%, #365899 25%, #1877F2 50%, #166FE5 75%, #0E5A8A 100%);
}

.ig-sticky-circle {
    background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
}

.fb-sticky-circle:hover,
.ig-sticky-circle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
}

.fb-sticky-circle i,
.ig-sticky-circle i {
    color: white;
    font-size: 24px;
}

/* Facebook & Instagram Feed Containers */
.fb-feed,
.ig-feed {
    position: fixed;
    right: 0;
    top: 0;
    height: 100%;
    width: 340px;
    background-color: white;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    z-index: 9997;
    display: flex;
    flex-direction: column;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.fb-feed iframe,
.ig-feed iframe {
    width: 100% !important;
    height: 100%;
    border: none;
    margin: 0;
    padding: 0;
}

.fb-feed.open,
.ig-feed.open {
    transform: translateX(0);
}

/* Close Buttons */
.close-btn-fb,
.close-btn-ig {
    background-color: #1877F2;
    color: white;
    text-align: center;
    cursor: pointer;
    font-size: 24px;
    padding: 5px 10px;
    font-weight: bold;
    align-self: flex-end;
    z-index: 10000;
    margin: 0;
}

.close-btn-ig {
    background-color: #E1306C;
}

/* Mission Section Styling */
.mission-section {
    padding: 4rem 0;
}

.mission-section .heading-section h2 {
    font-size: clamp(2rem, 4vw, 2.5rem);
    font-weight: 700;
    color: #333;
}

.mission-section .heading-section p {
    font-size: clamp(1rem, 2vw, 1.25rem);
    font-weight: 400;
    color: #666;
    max-width: 800px;
    margin: 0 auto;
}
</style>

<script>
function toggleFacebookFeed() {
    const fbFeed = document.getElementById('fbFeed');
    const igFeed = document.getElementById('igFeed');
    const fbBtn = document.querySelector('.fb-sticky-circle');
    const igBtn = document.querySelector('.ig-sticky-circle');

    // Close Instagram if open
    if (igFeed.classList.contains('open')) {
        igFeed.classList.remove('open');
        igBtn.style.display = 'flex';
    }

    // Toggle Facebook
    fbFeed.classList.toggle('open');
    fbBtn.style.display = fbFeed.classList.contains('open') ? 'none' : 'flex';
}

function toggleInstagramFeed() {
    const fbFeed = document.getElementById('fbFeed');
    const igFeed = document.getElementById('igFeed');
    const fbBtn = document.querySelector('.fb-sticky-circle');
    const igBtn = document.querySelector('.ig-sticky-circle');

    // Close Facebook if open
    if (fbFeed.classList.contains('open')) {
        fbFeed.classList.remove('open');
        fbBtn.style.display = 'flex';
    }

    // Toggle Instagram
    igFeed.classList.toggle('open');
    igBtn.style.display = igFeed.classList.contains('open') ? 'none' : 'flex';
}
</script>

<!-- Structured Data (Schema.org) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NGO",
  "name": "Bantwana Initiative Eswatini",
  "url": "<?php echo BASE_URL; ?>/",
  "logo": "<?php echo BASE_URL; ?>/images/bantwanalogo.jpg",
  "description": "To enhance the well-being and resilience of vulnerable children, youth, and their families affected by HIV & AIDS and poverty through holistic care, protection, and empowerment.",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Coates Valley",
    "addressLocality": "Manzini",
    "addressCountry": "SZ"
  },
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+268 2505 2848",
    "contactType": "customer service"
  },
  "sameAs": [
    "https://www.facebook.com/bantwanaeswatini"
  ]
}
</script>