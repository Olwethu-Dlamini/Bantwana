<!-- application/views/internships/index.php -->

<?php
// Get hero data from controller
$heroImageFilename = $data['internshipsHero']['internships_hero_image'] ?? 'bg_2.jpg';
$heroImageUrl = BASE_URL . '/images/' . htmlspecialchars($heroImageFilename);
$heroTitle = $data['internshipsHero']['internships_hero_title'] ?? 'Gain Experience, Make an Impact';
$heroSubtitle = $data['internshipsHero']['internships_hero_subtitle'] ?? 'Apply your academic knowledge in a real-world development setting.';
?>

<!-- Hero Section -->
<div class="hero-wrap" style="background-image: url('<?php echo $heroImageUrl; ?>');" data-stellar-background-ratio="1">
    <div class="hero-gradient"></div>
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center" data-scrollax-parent="true">
            <div class="col-md-9 ftco-animate pb-5 text-center" data-scrollax=" properties: { translateY: '70%' }">
                <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <span class="mr-2"><a href="<?php echo BASE_URL; ?>/">Home</a></span> <span>Internships</span>
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
                <span class="subheading">Professional Development</span>
                <h2 class="mb-4">Bantwana Internship Program</h2>
                <p>Bantwana Initiative Eswatini offers internship programs designed to provide students and recent graduates with hands-on experience in community development, public health, social work, and non-profit management within the unique context of Eswatini.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 ftco-animate">
                <h3 class="mb-4">Program Overview</h3>
                <p>Bantwana Initiative Eswatini offers internship opportunities students in tertiary institutions and recent graduates, providing them with practical work experience, mentorship, and skills development.  is to provide the student with the necessary practical experience in a setting where program services interventions allow learning as the primary objective Interns engage in program areas including child protection, violence against children & gender-based violence (GBV) prevention and response, youth empowerment, and community development.
                    <br></br>
An internship is a well-defined short-term work/learning experience to help students prepare and comply with a particular coursework at tertiary institution. This is a prescribed learning period away from the classroom to gain industry experience or satisfy requirements for a qualification. It must be prescribed by a tertiary institution.
</p>

                <h3 class="mt-5 mb-4">Internship Policy Procedure</h3>
                <p>The aim of the internship program is to provide the student with the necessary practical
                experience in a setting where program services interventions allow learning as the primary
                objective. The following procedures will guide the organisation in the functioning of the Interns and staff:</p>
                <p>The student must submit the following to <i class="icon icon-envelope mr-2"></i> <a href="mailto:info@bantwana.co.sz">info@bantwana.co.sz</a></p>

                        <ul class="list-unstyled">
                            <li>Write an application letter (addressed to the Director of Operations)</li>
                            <li>Attach a curriculum vitae</li>
                            <li>Attached recent academic results</li>
                        <li> Attached an Internship letter from the tertiary institution</li>
                        </ul>

                </p>
            </div> <!-- .col-lg-8 -->

            <div class="col-lg-4 sidebar ftco-animate">
                <div class="sidebar-box ftco-animate bg-light p-4">
                    <h3 class="heading-2 mb-4">Important Information</h3>
                    <ul class="list-unstyled">
                        <li class="d-flex mb-3"><span class="icon mr-3"><i class="fas fa-map-marker-alt"></i></span> <span>Location: Primarily in Eswatini, with potential for regional travel.</span></li>
                        <li class="d-flex mb-3"><span class="icon mr-3"><i class="fas fa-clock"></i></span> <span>Duration: Flexible, typically 3-12 months.</span></li>
                        <li class="d-flex mb-3"><span class="icon mr-3"><i class="fas fa-calendar-alt"></i></span> <span>Start Dates: Flexible, based on program needs.</span></li>
                        </ul>
                </div>

                <div class="sidebar-box ftco-animate bg-light p-4 mt-4">
                    <h3 class="heading-2 mb-4">Contact</h3>
                    <p>For inquiries about internships, please contact us.</p>
                    <p><i class="icon icon-envelope mr-2"></i> <a href="mailto:info@bantwana.co.sz">info@bantwana.co.sz</a></p>
                    <p><i class="icon icon-phone mr-2"></i> +268 2505 2848</p>
                </div>
            </div> <!-- .col-lg-4 -->
        </div> <!-- .row -->
    </div> <!-- .container -->
</section>