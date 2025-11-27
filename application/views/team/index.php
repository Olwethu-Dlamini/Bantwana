<!-- application/views/team/index.php -->

<?php
// Ensure BASE_URL is defined
if (!defined('BASE_URL')) {
    die('BASE_URL not defined.');
}

// Switch theme: modern | elegant | minimal
$theme = $data['theme'] ?? 'minimal';

// Hero data
$heroImageFilename = $data['teamHero']['team_hero_image'] ?? 'bg_team.jpg';
$heroImageUrl = BASE_URL . '/images/' . htmlspecialchars($heroImageFilename);
$heroTitle = $data['teamHero']['team_hero_title'] ?? 'Our Team';
$heroSubtitle = $data['teamHero']['team_hero_subtitle'] ?? 'Meet the dedicated individuals driving our mission.';

// Thulani image
$thulaniImageFilename = $data['thulaniImage'] ?? 'thulani_earnshaw.jpg';
$thulaniImageUrl = BASE_URL . '/images/' . htmlspecialchars($thulaniImageFilename);
?>

<!-- Hero Section -->
<div class="hero-wrap" style="background-image: url('<?php echo $heroImageUrl; ?>');">
    <div class="hero-gradient"></div> <!-- Gradient overlay -->
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center">
            <div class="col-md-9 ftco-animate pb-5 text-center">
                <p class="breadcrumbs">
                    <span class="mr-2"><a href="<?php echo BASE_URL; ?>/">Home</a></span> <span>Our Team</span>
                </p>
                <h1 class="mb-3 bread"><?php echo htmlspecialchars($heroTitle); ?></h1>
                <p class="mb-0"><?php echo htmlspecialchars($heroSubtitle); ?></p>
            </div>
        </div>
    </div>
</div>
<!-- End Hero Section -->

<section class="ftco-section bg-light about-section">
    <div class="container">
        <!-- Board of Directors -->
        <div class="row justify-content-center mb-5 pb-3">
            <div class="col-md-10 heading-section ftco-animate text-center">
                <h2 class="mb-4">Board of Directors</h2>
                <p>Bantwana Initiative Eswatini (BIE) is proudly governed by a committed Board of Directors
                    whose extensive multi-sectoral expertise and experience form the backbone of our leadership.
                    This distinguished team carries the vital responsibility of overseeing governance, providing
                    strategic direction, and upholding the highest standards of accountability. Their stewardship
                    guarantees that every program and initiative we undertake is fully aligned with—and actively
                    advances—our mission to improve the well-being, growth, and resilience of vulnerable
                    children, youth, and families. Through their guidance, we are empowered to create
                    meaningful, lasting change in the communities we serve.
                </p>
            </div>
        </div>

        <!-- Country Director -->
        <div class="row justify-content-center mb-5 pb-3">
            <div class="col-md-10 ftco-animate">
                <h3 class="mb-4 text-center">Country Director</h3>
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="<?php echo $thulaniImageUrl; ?>" alt="Thulani Earnshaw" class="img-fluid rounded mb-3" style="max-width: 200px;">
                    </div>
                    <div class="col-md-8">
                        <h4>Thulani Earnshaw</h4>
                    <p>Thulani Earnshaw brings over 20 years of distinguished leadership in livelihoods, youth
                        development, and economic empowerment across Eswatini. Since 2007, he has excelled as
                        the Executive Director of Bantwana Initiative Eswatini, passionately driving programs that
                        serve vulnerable children and households. Under his visionary leadership, Bantwana manages
                        significant funding from USAID/PEPFAR, Global Fund, UN Trust Fund, OSISA, and
                        UNICEF, delivering impactful, sustainable services to those most in need.
                        Thulani&#39;s strong commitment to social justice is reflected in his collaborative approach,
                        forging effective partnerships with government, civil society, and the private sector to
                        advance support for orphans, vulnerable children, families, and to champion gender equity
                        and human rights. Holding a Master’s degree in Sustainable International Development from
                        Brandeis University, he combines deep technical expertise with exceptional communication
                        and cultural competency.
                        Renowned for his inclusive leadership and a winning mentality, Thulani consistently
                        strengthens organizational governance and fosters lasting partnerships, driving significant
                        positive change in vulnerable communities throughout Eswatini.
                    </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Programs Staff -->
        <div class="row justify-content-center mb-5 pb-3">
            <div class="col-md-10 heading-section ftco-animate text-center">
                <h3 class="mb-4">Programs Staff</h3>
                <p>Our dedicated and highly skilled programs team spearheads a diverse and robust portfolio of
                    strategic initiatives, meticulously aligned with our core thematic priorities. We are deeply
                    committed to fostering a results-driven culture that emphasizes measurable impact,
                    underpinned by a team comprised of professionally qualified individuals equipped with
                    extensive training and real-world experience. This foundation ensures our operational
                    excellence and unwavering ability to deliver sustainable, transformative outcomes in the
                    communities we serve.
                    Throughout implementation, we engage proactively with community cadres and leadership
                    structures, ensuring grassroots involvement and local ownership. Our approach is
                    characterized by building and sustaining strategic partnerships across sectors, leveraging
                    collaborative synergies with a broad network of organizations to maximize efficiency,
                    innovation, and reach. This multi-stakeholder coordination enhances the quality and
                    accessibility of services, ultimately contributing to improved well-being and lasting
                    empowerment within our target populations.
                    Our track record of success, driven by a hunger for excellence and continuous improvement,
                    reflects our organizational capacity to navigate complex challenges and exceed
                    expectations—making us a reliable and impactful partner for donors committed to
                    meaningful change.
                </p>
            </div>
        </div>

        <!-- Community Cadres -->
        <div class="row justify-content-center">
            <div class="col-md-10 heading-section ftco-animate text-center">
                <h3 class="mb-4">Community Cadres</h3>
                <p>The organization implements through community cadres who are the cornerstone of our
                    grassroots initiatives, playing a critical role in directly engaging beneficiaries and ensuring
                    timely achievement of project objectives. Recruited from within the local areas they serve,
                    through them we do not only reinforce community ownership of development efforts but also
                    create meaningful employment opportunities for rural youth and young women. Their
                    dedication and deep community ties have made them indispensable members of our
                    organization, driving both impact and sustainability in our programs.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="ftco-section-3 img" style="background-image: url(<?php echo BASE_URL; ?>/images/bg_6.jpg);">
    <div class="overlay"></div>
    <div class="container">
        <div class="row d-md-flex align-items-center justify-content-center text-center">
            <div class="col-md-8 ftco-animate">
                <h2 class="mb-3">Support Our Mission</h2>
                <p>Your contribution makes a direct impact. Learn how you can help sustain and expand our vital work.</p>
                <p>
                    <a href="<?php echo BASE_URL; ?>/donate" class="btn btn-white btn-outline-white px-4 py-3">Donate Now</a>
                    <a href="<?php echo BASE_URL; ?>/volunteer" class="btn btn-white btn-outline-white px-4 py-3">Volunteer</a>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ================= THEMES ================= -->
<style>
/* Common base styling */
.hero-wrap {
    position: relative;
    background-size: cover;
    background-position: center;
}
.hero-wrap .overlay {
    position: absolute;
    inset: 0;
}
.hero-wrap .container {
    position: relative;
    z-index: 2;
}
.hero-wrap .breadcrumbs {
    font-size: 1rem;
}
.hero-wrap .bread {
    font-weight: 800;
}
.about-section {
    padding: 4rem 0;
}

/* --- Theme 1: Modern Gradient --- */
<?php if ($theme === 'modern'): ?>
.hero-wrap { height: 65vh; display: flex; align-items: center; }
.hero-wrap .overlay { background: linear-gradient(to bottom right, rgba(0,0,0,0.8), rgba(0,0,0,0.2)); }
.hero-wrap .bread { font-size: 3rem; color: #fff; }
.hero-wrap p.mb-0 { color: #f0f0f0; font-size: 1.25rem; }
.about-section h2, .about-section h3 { font-weight: 700; }
.about-section img { border-radius: 12px; box-shadow: 0 5px 25px rgba(0,0,0,0.15); }

/* --- Theme 2: Elegant Soft --- */
<?php elseif ($theme === 'elegant'): ?>
.hero-wrap { height: 70vh; }
.hero-wrap .overlay { background: rgba(255,255,255,0.25); backdrop-filter: blur(3px); }
.hero-wrap .bread { font-size: 2.8rem; font-family: 'Playfair Display', serif; color: #fff; }
.hero-wrap p.mb-0 { font-family: 'Lato', sans-serif; color: #f9f9f9; }
.about-section { background: linear-gradient(to bottom, #f9f9f9, #ffffff); }
.about-section h2, .about-section h3 { font-family: 'Playfair Display', serif; }
.about-section h4 { font-family: 'Lato', sans-serif; }
.about-section img { border-radius: 50%; border: 5px solid #eee; box-shadow: 0 3px 15px rgba(0,0,0,0.1); }

/* --- Theme 3: Minimal Corporate --- */
<?php elseif ($theme === 'minimal'): ?>
.hero-wrap { height: 60vh; display: flex; align-items: center; }
.hero-wrap .overlay { background: rgba(0,0,0,0.5); }
.hero-wrap .bread { font-size: 2.5rem; color: #fff; letter-spacing: 1px; }
.hero-wrap p.mb-0 { color: #eaeaea; font-size: 1.1rem; }
.about-section { background: #f7f9fc; }
.about-section img { border-radius: 8px; border: 1px solid #ddd; padding: 5px; }
<?php endif; ?>
</style>
