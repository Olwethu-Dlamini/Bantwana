<!-- application/views/about/index.php -->
<!-- Hero Section -->
<?php
// Get the hero image filename from settings, defaulting to 'bg_2.jpg'
$heroImageFilename = $data['aboutContent']['about_hero_image'] ?? 'bg_2.jpg';
$aboutHistoryImage = !empty($data['aboutContent']['about_history_image']) ? $data['aboutContent']['about_history_image'] : 'event-1.jpg';
// Construct the full URL path for the image
$heroImageUrl = BASE_URL . '/images/' . htmlspecialchars($heroImageFilename);
$historyImageUrl = BASE_URL . '/images/' . htmlspecialchars($aboutHistoryImage);

?>
<div class="hero-wrap" style="background-image: url('<?php echo $heroImageUrl; ?>');" data-stellar-background-ratio="1">
    <div class="hero-gradient"></div> <!-- Gradient overlay -->
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center" data-scrollax-parent="true">
            <div class="col-md-9 ftco-animate pb-5 text-center" data-scrollax=" properties: { translateY: '70%' }">
                <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <span class="mr-2"><a href="<?php echo BASE_URL; ?>/">Home</a></span> <span>About Us</span>
                </p>
                <h1 class="mb-3 bread" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <?php echo htmlspecialchars($data['aboutContent']['about_hero_title'] ?? 'Our Journey of Hope'); ?>
                </h1>
            </div>
        </div>
    </div>
</div>
<!-- End Hero Section -->

<section class="ftco-section ftco-no-pt ftco-no-pb">
    <div class="container">
        <div class="row d-flex no-gutters">
            <div class="col-md-6 d-flex">
                <!-- Use dynamic history image -->
                <div class="img img-video d-flex align-items-center justify-content-center" style="height: 400px; background-color: #f0f0f0;">
                    <img src="<?php echo $historyImageUrl; ?>" alt="Bantwana Initiative's story" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>
            <div class="col-md-6 pl-md-5 py-5 py-md-0">
                <div class="row justify-content-start pt-3 pb-3">
                    <div class="col-md-12 heading-section ftco-animate">
                        <span class="subheading">Our Story</span>
                        <h2 class="mb-4">
                            <?php echo htmlspecialchars($data['aboutContent']['about_story_heading'] ?? 'Where Necessity Met Compassion'); ?>
                        </h2>
                        <p><?php echo nl2br(htmlspecialchars($data['aboutContent']['about_story_text_1'] ?? '')); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($data['aboutContent']['about_story_text_2'] ?? '')); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($data['aboutContent']['about_story_text_3'] ?? '')); ?></p>

                        <div class="tabulation-2 mt-4">
                            <div class="tab-content bg-light rounded">
                                <div class="tab-pane container p-0 active">
                                    <p class="font-weight-bold">
                                        <?php echo htmlspecialchars($data['aboutContent']['about_mission_title'] ?? 'Our Mission:'); ?>
                                    </p>
                                    <p>"<?php echo nl2br(htmlspecialchars($data['aboutContent']['about_mission_text'] ?? '')); ?>"</p>
                                    <p class="font-weight-bold mt-3">
                                        <?php echo htmlspecialchars($data['aboutContent']['about_vision_title'] ?? 'Our Vision:'); ?>
                                    </p>
                                    <p>"<?php echo nl2br(htmlspecialchars($data['aboutContent']['about_vision_text'] ?? '')); ?>"</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-4">
                            <p class="mb-0"><a href="<?php echo BASE_URL; ?>/contact" class="btn btn-primary py-3 px-4">Connect With Us</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ftco-section bg-light">
    <div class="container">
        <div class="row justify-content-center pb-3">
            <div class="col-md-7 text-center heading-section ftco-animate">
                <span class="subheading">Our Approach</span>
                <h2 class="mb-4"><?php echo htmlspecialchars($data['aboutContent']['about_approach_title'] ?? 'Holistic Care, Lasting Impact'); ?></h2>
                <p class="mb-5"><?php echo nl2br(htmlspecialchars($data['aboutContent']['about_approach_intro'] ?? 'We tackle complex challenges with comprehensive strategies, addressing root causes and fostering long-term resilience.')); ?></p>
            </div>
        </div>
        <div class="row">
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <?php
                $number_key = "about_approach_item_{$i}_number";
                $heading_key = "about_approach_item_{$i}_heading";
                $text_key = "about_approach_item_{$i}_text";
                $number = $data['aboutContent'][$number_key] ?? '';
                $heading = $data['aboutContent'][$heading_key] ?? '';
                $text = $data['aboutContent'][$text_key] ?? '';
                ?>
                <?php if (!empty($heading) || !empty($text)): // Only show if content exists ?>
                <div class="col-md-4 ftco-animate">
                    <div class="work mb-4 d-flex">
                        <div class="text pl-4">
                            <?php if (!empty($number)): ?>
                                <span class="number"><?php echo htmlspecialchars($number); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($heading)): ?>
                                <h3><a href="#"><?php echo htmlspecialchars($heading); ?></a></h3>
                            <?php endif; ?>
                            <?php if (!empty($text)): ?>
                                <p><?php echo nl2br(htmlspecialchars($text)); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    </div>
</section>

<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center pb-3">
            <div class="col-md-10 text-center heading-section ftco-animate">
                <span class="subheading">
                    <?php echo htmlspecialchars($data['aboutContent']['about_values_title'] ?? 'Our Values'); ?>
                </span>
                <h2 class="mb-4">The Principles That Guide Us</h2>
                <p class="mb-5">
                    <?php echo nl2br(htmlspecialchars($data['aboutContent']['about_values_intro'] ?? 'These core values are the bedrock of everything we do, ensuring our actions are ethical, effective, and centered on the people we serve.')); ?>
                </p>
            </div>
        </div>
        <div class="row">
            <!-- Values items would go here - for brevity, using static list -->
            <!-- You can make these dynamic too if needed -->
            <div class="col-md-6 col-lg-4 ftco-animate">
                <div class="staff">
                    <div class="info-wrap d-flex">
                        <div class="icon d-flex align-items-center justify-content-center bg-primary">
                            <span class="flaticon-heart"></span>
                        </div>
                        <div class="text pl-3">
                            <h3>Honesty</h3>
                            <span>We act with openness, transparency, and fairness in all our engagements.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 ftco-animate">
                <div class="staff">
                    <div class="info-wrap d-flex">
                        <div class="icon d-flex align-items-center justify-content-center bg-primary">
                            <span class="flaticon-handshake"></span>
                        </div>
                        <div class="text pl-3">
                            <h3>Integrity</h3>
                            <span>We uphold our commitments with unwavering accountability.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 ftco-animate">
                <div class="staff">
                    <div class="info-wrap d-flex">
                        <div class="icon d-flex align-items-center justify-content-center bg-primary">
                            <span class="flaticon-user"></span>
                        </div>
                        <div class="text pl-3">
                            <h3>Respect</h3>
                            <span>We honour the dignity of every individual, putting people first.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 ftco-animate">
                <div class="staff">
                    <div class="info-wrap d-flex">
                        <div class="icon d-flex align-items-center justify-content-center bg-primary">
                            <span class="flaticon-network"></span>
                        </div>
                        <div class="text pl-3">
                            <h3>Collaboration</h3>
                            <span>We embrace teamwork and partnership for collective impact.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 ftco-animate">
                <div class="staff">
                    <div class="info-wrap d-flex">
                        <div class="icon d-flex align-items-center justify-content-center bg-primary">
                            <span class="flaticon-growth"></span>
                        </div>
                        <div class="text pl-3">
                            <h3>Resilience</h3>
                            <span>We are committed to continuous learning and adaptation.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 ftco-animate">
                <div class="staff">
                    <div class="info-wrap d-flex">
                        <div class="icon d-flex align-items-center justify-content-center bg-primary">
                            <span class="flaticon-idea"></span>
                        </div>
                        <div class="text pl-3">
                            <h3>Innovation</h3>
                            <span>We foster creativity to address challenges effectively.</span>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-md-6 col-lg-4 offset-lg-4 ftco-animate">
                <div class="staff">
                    <div class="info-wrap d-flex">
                        <div class="icon d-flex align-items-center justify-content-center bg-primary">
                            <span class="flaticon-headphones"></span>
                        </div>
                        <div class="text pl-3">
                            <h3>Responsiveness</h3>
                            <span>We actively listen and respond with empathy and timeliness.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ftco-counter ftco-section img bg-light" id="section-counter" style="background-image: url(<?php echo BASE_URL; ?>/images/bg_4.jpg);">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-6 col-lg-3 justify-content-center counter-wrap ftco-animate">
                        <div class="block-18 text-center">
                            <div class="text">
                                <strong class="number" data-number="<?php echo (int)($data['aboutContent']['about_stats_children_number'] ?? 0); ?>">
                                    <?php echo htmlspecialchars($data['aboutContent']['about_stats_children_number'] ?? '0'); ?>
                                </strong>
                                <span><?php echo htmlspecialchars($data['aboutContent']['about_stats_children_text'] ?? 'Children Supported'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 justify-content-center counter-wrap ftco-animate">
                        <div class="block-18 text-center">
                            <div class="text">
                                <strong class="number" data-number="<?php echo (int)($data['aboutContent']['about_stats_staff_number'] ?? 0); ?>">
                                    <?php echo htmlspecialchars($data['aboutContent']['about_stats_staff_number'] ?? '0'); ?>
                                </strong>
                                <span><?php echo htmlspecialchars($data['aboutContent']['about_stats_staff_text'] ?? 'Dedicated Staff & Volunteers'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 justify-content-center counter-wrap ftco-animate">
                        <div class="block-18 text-center">
                            <div class="text">
                                <strong class="number" data-number="<?php echo (int)($data['aboutContent']['about_stats_regions_number'] ?? 0); ?>">
                                    <?php echo htmlspecialchars($data['aboutContent']['about_stats_regions_number'] ?? '0'); ?>
                                </strong>
                                <span><?php echo htmlspecialchars($data['aboutContent']['about_stats_regions_text'] ?? 'Regions Impacted'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 justify-content-center counter-wrap ftco-animate">
                        <div class="block-18 text-center">
                            <div class="text">
                                <strong class="number" data-number="<?php echo (int)($data['aboutContent']['about_stats_years_number'] ?? 0); ?>">
                                    <?php echo htmlspecialchars($data['aboutContent']['about_stats_years_number'] ?? '0'); ?>
                                </strong>
                                <span><?php echo htmlspecialchars($data['aboutContent']['about_stats_years_text'] ?? 'Years of Service'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
