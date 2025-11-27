<?php
// Initialize SettingModel to get hero data
require_once BASE_PATH . '/application/models/SettingModel.php';
$settingModel = new SettingModel();

// Get hero data from SettingModel instead of controller
$heroImageFilename = $settingModel->get('volunteer_hero_image', 'bg_4.jpg');
$heroImageUrl = BASE_URL . '/images/' . htmlspecialchars($heroImageFilename);
$heroTitle = htmlspecialchars($settingModel->get('volunteer_hero_title', 'Give Your Time'));
$heroSubtitle = htmlspecialchars($settingModel->get('volunteer_hero_subtitle', 'Make a direct impact in the lives of vulnerable children and families.'));

// Get any session messages
$volunteer_message = '';
$volunteer_message_type = '';
if (isset($_SESSION['volunteer_message'])) {
    $volunteer_message = $_SESSION['volunteer_message'];
    $volunteer_message_type = $_SESSION['volunteer_message_type'] ?? 'info';
    unset($_SESSION['volunteer_message'], $_SESSION['volunteer_message_type']);
}

// Get form data and errors if they exist
$form_data = $_SESSION['form_data'] ?? [];
$form_errors = $_SESSION['form_errors'] ?? [];
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data'], $_SESSION['form_errors']);
}
?>

<!-- Hero Section -->
<div class="hero-wrap position-relative" style="background-image: url('<?php echo $heroImageUrl; ?>'); background-size: cover; background-position: center;" data-stellar-background-ratio="1">
    <div class="hero-gradient position-absolute w-100 h-100" style="background: rgba(0,0,0,0.4);"></div>
    <div class="container position-relative">
        <div class="row no-gutters slider-text align-items-end justify-content-center" data-scrollax-parent="true">
            <div class="col-md-9 ftco-animate pb-5 text-center" data-scrollax="properties: { translateY: '70%' }">
                <nav aria-label="breadcrumb">
                    <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                        <span class="mr-2"><a href="<?php echo BASE_URL; ?>/">Home</a></span>
                        <span>Volunteer</span>
                    </p>
                </nav>
                <h1 class="mb-3 bread" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <?php echo $heroTitle; ?>
                </h1>
                <p class="mb-0" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <?php echo $heroSubtitle; ?>
                </p>
            </div>
        </div>
    </div>
</div>
<!-- End Hero Section -->

<!-- Display Messages -->
<?php if (!empty($volunteer_message)): ?>
<div class="container mt-4">
    <div class="alert alert-<?php echo htmlspecialchars($volunteer_message_type); ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($volunteer_message); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
<?php endif; ?>

<section class="ftco-section bg-light">
    <div class="container">
        <div class="row justify-content-center pb-5">
            <div class="col-md-10 text-center heading-section ftco-animate">
                <span class="subheading text-uppercase text-primary">Join Our Team</span>
                <h2 class="mb-4">Volunteer Opportunities</h2>
                <p class="lead">Your skills, passion, and time are invaluable. Join Bantwana Initiative Eswatini to make a meaningful impact.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 ftco-animate">
                <h3 class="mb-4">Why Volunteer With Us?</h3>
                <p>Volunteering with Bantwana offers a rewarding opportunity to:</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check-circle text-primary mr-2"></i> Support vulnerable children, youth, and families directly</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-primary mr-2"></i> Gain hands-on experience in community development</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-primary mr-2"></i> Collaborate with dedicated local and international teams</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-primary mr-2"></i> Contribute to the fight against HIV/AIDS and poverty</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-primary mr-2"></i> Join a passionate community driving positive change</li>
                </ul>

                <h3 class="mt-5 mb-4">Volunteer Roles</h3>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 p-3 shadow-sm">
                            <div class="card-body d-flex">
                                <div class="icon mr-3"><i class="fas fa-book-reader fa-2x text-primary"></i></div>
                                <div>
                                    <h4 class="card-title">Mentoring & Tutoring</h4>
                                    <p class="card-text">Provide academic support and life coaching to empower children and youth.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 p-3 shadow-sm">
                            <div class="card-body d-flex">
                                <div class="icon mr-3"><i class="fas fa-hands-helping fa-2x text-primary"></i></div>
                                <div>
                                    <h4 class="card-title">Community Outreach</h4>
                                    <p class="card-text">Organize events, awareness campaigns, and support groups in local communities.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 p-3 shadow-sm">
                            <div class="card-body d-flex">
                                <div class="icon mr-3"><i class="fas fa-desktop fa-2x text-primary"></i></div>
                                <div>
                                    <h4 class="card-title">Office Support</h4>
                                    <p class="card-text">Assist with administrative tasks, data entry, and communications.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 p-3 shadow-sm">
                            <div class="card-body d-flex">
                                <div class="icon mr-3"><i class="fas fa-tools fa-2x text-primary"></i></div>
                                <div>
                                    <h4 class="card-title">Specialized Skills</h4>
                                    <p class="card-text">Contribute expertise in healthcare, IT, construction, or training.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 sidebar ftco-animate">
                <div class="sidebar-box bg-white p-4 shadow-sm rounded text-center">
                    <h3 class="heading-2 mb-4">Get In Touch</h3>
                    <div class="icon d-flex align-items-center justify-content-center mx-auto mb-4" style="background-color: #4CAF50; width: 80px; height: 80px; border-radius: 50%;">
                        <i class="fas fa-envelope text-white" style="font-size: 2rem;"></i>
                    </div>
                    <p class="mb-4">Ready to make a difference? Contact us to learn more about volunteer opportunities with Bantwana Initiative Eswatini.</p>
                    <div class="contact-info mb-4">
                        <h5 class="mb-3">Contact Our Volunteer Team</h5>
                        <p class="mb-2">
                            <a href="mailto:info@bantwana.co.sz?subject=Volunteer Inquiry&body=Hello,%0D%0A%0D%0AI am interested in volunteering with Bantwana Initiative Eswatini. Please provide me with more information about available volunteer opportunities.%0D%0A%0D%0AThank you." 
                               class="btn btn-primary btn-lg px-4 py-3" 
                               style="text-decoration: none;">
                                <i class="fas fa-envelope mr-2"></i>info@bantwana.co.sz
                            </a>
                        </p>
                        <small class="text-muted">Click to send us an email about volunteering</small>
                    </div>
                    <div class="additional-info">
                        <h6 class="mb-2">What to include in your email:</h6>
                        <ul class="text-left small text-muted list-unstyled">
                            <li><i class="fas fa-check text-primary mr-1"></i> Your name and contact information</li>
                            <li><i class="fas fa-check text-primary mr-1"></i> Areas of interest (mentoring, outreach, etc.)</li>
                            <li><i class="fas fa-check text-primary mr-1"></i> Your availability</li>
                            <li><i class="fas fa-check text-primary mr-1"></i> Any relevant skills or experience</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>