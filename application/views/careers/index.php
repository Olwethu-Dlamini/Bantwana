<?php
// Get hero data from controller
$heroImageFilename = $data['careersHero']['careers_hero_image'] ?? 'bg_1.jpg';
$heroImageUrl = BASE_URL . '/images/' . htmlspecialchars($heroImageFilename);
$heroTitle = $data['careersHero']['careers_hero_title'] ?? 'Build Your Career With Us';
$heroSubtitle = $data['careersHero']['careers_hero_subtitle'] ?? 'Join a team passionate about creating lasting change for children and families.';
?>

<!-- Hero Section -->
<div class="hero-wrap" style="background-image: url('<?php echo $heroImageUrl; ?>');" data-stellar-background-ratio="1">
    <div class="hero-gradient"></div>
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center" data-scrollax-parent="true">
            <div class="col-md-9 ftco-animate pb-5 text-center" data-scrollax=" properties: { translateY: '70%' }">
                <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <span class="mr-2"><a href="<?php echo BASE_URL; ?>/">Home</a></span> <span>Careers</span>
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
                <span class="subheading">Work With Us</span>
                <h2 class="mb-4">Current Opportunities</h2>
                <p>Bantwana Initiative Eswatini is always looking for talented and dedicated professionals who share our commitment to improving the lives of vulnerable children, youth, and families. Explore our current openings and join our mission-driven team.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 ftco-animate">
                <?php if (empty($data['jobs'])): ?>
                    <div class="row">
                        <div class="col-12 text-center">
                            <p class="text-muted">No job listings found at the moment. Please check back later.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($data['jobs'] as $index => $job): ?>
                    <?php
                    // Simple icon mapping (retained from original)
                    $iconClass = 'flaticon-briefcase';
                    switch (strtolower(substr($job['title'], 0, 5))) {
                        case 'progr':
                            $iconClass = 'flaticon-medical';
                            break;
                        case 'finan':
                        case 'accou':
                            $iconClass = 'flaticon-donation';
                            break;
                        case 'commu':
                        case 'outre':
                            $iconClass = 'flaticon-charity';
                            break;
                        case 'educ':
                            $iconClass = 'flaticon-reading';
                            break;
                        case 'socia':
                        case 'prote':
                            $iconClass = 'flaticon-shield';
                            break;
                        case 'adole':
                        case 'youth':
                            $iconClass = 'flaticon-user';
                            break;
                    }
                    ?>
                    <div class="job-listing bg-light p-4 mb-4">
                        <h3 class="mb-3"><?php echo htmlspecialchars($job['title']); ?></h3>
                        <p class="mb-2"><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                        <p class="mb-2"><strong>Type:</strong> <?php echo htmlspecialchars($job['type']); ?></p>
                        <p class="mb-3"><strong>Deadline:</strong> <?php echo !empty($job['deadline']) ? date('F j, Y', strtotime($job['deadline'])) : 'Open until filled'; ?></p>
                        <div><?php echo substr(strip_tags($job['description']), 0, 100) . (strlen(strip_tags($job['description'])) > 100 ? '...' : ''); ?></div>
                        <p class="mt-3">
                            <button class="btn btn-primary btn-outline-primary view-details-btn"
                                    data-id="<?php echo $job['id']; ?>"
                                    data-title="<?php echo htmlspecialchars($job['title']); ?>"
                                    data-location="<?php echo htmlspecialchars($job['location']); ?>"
                                    data-type="<?php echo htmlspecialchars($job['type']); ?>"
                                    data-deadline="<?php echo htmlspecialchars($job['deadline'] ?? ''); ?>"
                                    data-description="<?php echo htmlspecialchars($job['description']); ?>"
                                    data-requirements="<?php echo htmlspecialchars($job['requirements'] ?? ''); ?>"
                                    data-responsibilities="<?php echo htmlspecialchars($job['responsibilities'] ?? ''); ?>"
                                    data-benefits="<?php echo htmlspecialchars($job['benefits'] ?? ''); ?>"
                                    data-toggle="modal" data-target="#jobDetailsModal">
                                View Details & Apply
                            </button>
                        </p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="ftco-animate text-center mt-5">
                    <p class="mb-4">Didn't find the right position? We'd still love to hear from you.</p>
                    <p><a href="<?php echo BASE_URL; ?>/contact" class="btn btn-primary btn-outline-primary px-4 py-3">Send General Inquiry</a></p>
                </div>
            </div> <!-- .col-lg-8 -->

            <div class="col-lg-4 sidebar ftco-animate">
                <div class="sidebar-box ftco-animate bg-light p-4">
                    <h3 class="heading-2 mb-4">Our Culture & Benefits</h3>
                    <ul class="list-unstyled">
                        <li class="d-flex mb-3"><span class="icon mr-3"><i class="fas fa-heart"></i></span> <span>Meaningful work that directly impacts lives.</span></li>
                        <li class="d-flex mb-3"><span class="icon mr-3"><i class="fas fa-users"></i></span> <span>Collaborative and supportive team environment.</span></li>
                        <li class="d-flex mb-3"><span class="icon mr-3"><i class="fas fa-chart-line"></i></span> <span>Opportunities for professional growth and development.</span></li>
                        <li class="d-flex mb-3"><span class="icon mr-3"><i class="fas fa-umbrella-beach"></i></span> <span>Competitive leave and benefits package.</span></li>
                        <li class="d-flex"><span class="icon mr-3"><i class="fas fa-globe-africa"></i></span> <span>Experience working in a diverse, international setting.</span></li>
                    </ul>
                </div>

                <div class="sidebar-box ftco-animate bg-light p-4 mt-4">
                    <h3 class="heading-2 mb-4">Working at Bantwana</h3>
                    <p>At Bantwana, we value integrity, respect, collaboration, and innovation. We are committed to creating an inclusive workplace where all employees feel valued and empowered to contribute their best.</p>
                    <p>We offer a dynamic work environment in the beautiful Kingdom of Eswatini, with opportunities to learn, grow, and make a significant difference in the lives of those we serve.</p>
                </div>
            </div> <!-- .col-lg-4 -->
        </div> <!-- .row -->
    </div> <!-- .container -->
</section>

<!-- Job Details Modal -->
<div class="modal fade" id="jobDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobModalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Location:</strong> <span id="jobModalLocation"></span></p>
                <p><strong>Job Type:</strong> <span id="jobModalType"></span></p>
                <p><strong>Application Deadline:</strong> <span id="jobModalDeadline"></span></p>
                <h5>Description</h5>
                <div id="jobModalDescription"></div>
                <h5>Requirements</h5>
                <div id="jobModalRequirements"></div>
                <h5>Responsibilities</h5>
                <div id="jobModalResponsibilities"></div>
                <h5>Benefits</h5>
                <div id="jobModalBenefits"></div>
            </div>
            <div class="modal-footer">
                <a href="mailto:info@bantwana.org?subject=Application%20for%20" class="btn btn-primary btn-outline-primary" id="jobModalApplyBtn">Apply</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Optional: Call to Action Section -->
<section class="ftco-section-3 img" style="background-image: url(<?php echo BASE_URL; ?>/images/bg_6.jpg);">
    <div class="overlay"></div>
    <div class="container">
        <div class="row d-md-flex align-items-center justify-content-center text-center">
            <div class="col-md-8 ftco-animate">
                <h2 class="mb-3">Questions About Careers?</h2>
                <p>We're here to help! Contact us to learn more about working with us.</p>
                <p><a href="<?php echo BASE_URL; ?>/contact" class="btn btn-white btn-outline-white px-4 py-3">Contact Us</a></p>
            </div>
        </div>
    </div>
</section>
<!-- End Call to Action Section -->

<!-- Include dependencies -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>/js/jquery.stellar.min.js"></script>
<script src="<?php echo BASE_URL; ?>/js/scrollax.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Stellar.js for parallax effect
        $.stellar({
            horizontalScrolling: false,
            verticalOffset: 150
        });

        // Populate Job Details Modal
        $('.view-details-btn').on('click', function() {
            const title = $(this).data('title');
            const location = $(this).data('location');
            const type = $(this).data('type');
            const deadline = $(this).data('deadline') ? new Date($(this).data('deadline')).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Open until filled';
            const description = $(this).data('description');
            const requirements = $(this).data('requirements') || 'Not specified';
            const responsibilities = $(this).data('responsibilities') || 'Not specified';
            const benefits = $(this).data('benefits') || 'Not specified';

            $('#jobModalTitle').text(title);
            $('#jobModalLocation').text(location);
            $('#jobModalType').text(type);
            $('#jobModalDeadline').text(deadline);
            $('#jobModalDescription').html(description);
            $('#jobModalRequirements').html(requirements);
            $('#jobModalResponsibilities').html(responsibilities);
            $('#jobModalBenefits').html(benefits);
            $('#jobModalApplyBtn').attr('href', 'mailto:info@bantwana.org?subject=Application%20for%20' + encodeURIComponent(title));

            $('#jobDetailsModal').modal('show');
        });
    });
</script>