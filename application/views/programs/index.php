<?php
// Ensure BASE_URL and BASE_PATH are defined
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/bantwana/public_html');
}
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

// Get hero image, title, and subtitle
$heroImageFilename = $data['programsHero']['programs_hero_image'] ?? 'bg_5.jpg';
$heroImageUrl = BASE_URL . '/images/' . htmlspecialchars($heroImageFilename);
$heroTitle = $data['programsHero']['programs_hero_title'] ?? 'Our Programs';
$heroSubtitle = $data['programsHero']['programs_hero_subtitle'] ?? 'Holistic support for children, youth, and families.';
?>

<style>
    .bantwana-green-bg {
        background-color: #009444; /* Bantwana Green */
        color: #ffffff;
    }
    .bantwana-green-bg .close {
        color: #ffffff;
        opacity: 0.9;
    }
    .bantwana-orange-btn {
        background-color: #FDB913; /* Bantwana Orange */
        border-color: #FDB913;
        color: #ffffff;
    }
    .bantwana-orange-btn:hover {
        background-color: #eA9A00;
        border-color: #eA9A00;
        color: #ffffff;
    }
    .modal-body p {
        font-size: 16px;
        line-height: 1.8;
    }
    .pagination .page-item.active .page-link {
        background-color: #FDB913;
        border-color: #FDB913;
    }
    .pagination .page-link {
        color: #009444;
    }
</style>


<div class="hero-wrap" style="background-image: url('<?php echo $heroImageUrl; ?>');" data-stellar-background-ratio="1">
    <div class="hero-gradient"></div> <!-- Gradient overlay -->
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center" data-scrollax-parent="true">
            <div class="col-md-9 ftco-animate pb-5 text-center" data-scrollax="properties: { translateY: '70%' }">
                <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <span class="mr-2"><a href="<?php echo BASE_URL; ?>/">Home</a></span> <span>Programs</span>
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




<section class="ftco-section bg-light">
    <div class="container">
        <div class="row justify-content-center mb-5 pb-3">
            <div class="col-md-7 heading-section ftco-animate text-center">
                <h2 class="mb-4">Our Latest Programs</h2>
                <p>Discover the impactful initiatives we run to support children, youth, and families.</p>
            </div>
        </div>
        <div class="row">
            <?php if (!empty($data['programs'])) : ?>
                <?php foreach ($data['programs'] as $program) : ?>
                    <div class="col-md-4 d-flex ftco-animate">
                        <div class="blog-entry align-self-stretch">
                            <a href="#" class="block-20" style="background-image: url('<?php echo BASE_URL . '/images/' . htmlspecialchars($program['image']); ?>');" data-toggle="modal" data-target="#programModal<?php echo $program['id']; ?>">
                            </a>
                            <div class="text p-4 d-block">
                                <div class="meta mb-3">
                                    
                                    <div><a href="#"><span class="icon-map-marker"></span> <?php echo htmlspecialchars($program['location']); ?></a></div>
                                </div>
                                <h3 class="heading mb-3"><a href="#" data-toggle="modal" data-target="#programModal<?php echo $program['id']; ?>"><?php echo htmlspecialchars($program['title']); ?></a></h3>
                                <p><?php echo htmlspecialchars($program['summary']); ?></p>
                                <p><button type="button" class="btn btn-outline-primary py-2 px-3" data-toggle="modal" data-target="#programModal<?php echo $program['id']; ?>">Learn More</button></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col-md-12 text-center">
                    <p>No programs found. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="row mt-5">
            <div class="col text-center">
                <div class="block-27">
                    <ul>
                        <?php if ($data['pagination']['currentPage'] > 1) : ?>
                            <li><a href="?page=<?php echo $data['pagination']['currentPage'] - 1; ?>">&lt;</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $data['pagination']['totalPages']; $i++) : ?>
                            <li class="<?php echo ($i == $data['pagination']['currentPage']) ? 'active' : ''; ?>">
                                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($data['pagination']['currentPage'] < $data['pagination']['totalPages']) : ?>
                            <li><a href="?page=<?php echo $data['pagination']['currentPage'] + 1; ?>">&gt;</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($data['programs'])) : ?>
    <?php foreach ($data['programs'] as $program) : ?>
        <div class="modal fade" id="programModal<?php echo $program['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="programModalLabel<?php echo $program['id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bantwana-green-bg">
                        <h5 class="modal-title" id="programModalLabel<?php echo $program['id']; ?>"><?php echo htmlspecialchars($program['title']); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php 
                        // Render HTML content directly, assuming $program['details'] is sanitized to prevent XSS.
                        // Ensure content is sanitized in the controller or database layer before output.
                        echo $program['details']; 
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a href="<?php echo BASE_URL; ?>/donate" class="btn bantwana-orange-btn">Support This Program</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<section class="ftco-section-3 img" style="background-image: url(<?php echo BASE_URL; ?>/images/bg_6.jpg);">
    <div class="overlay"></div>
    <div class="container">
        <div class="row d-md-flex align-items-center justify-content-center text-center">
            <div class="col-md-8 ftco-animate">
                <h2 class="mb-3">Support Our Programs</h2>
                <p>Your contribution makes a direct impact. Learn how you can help sustain and expand our vital work.</p>
                <p><a href="<?php echo BASE_URL; ?>/donate" class="btn btn-white btn-outline-white px-4 py-3">Donate Now</a> <a href="<?php echo BASE_URL; ?>/volunteer" class="btn btn-white btn-outline-white px-4 py-3">Volunteer</a></p>
            </div>
        </div>
    </div>
</section>