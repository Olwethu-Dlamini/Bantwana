<!-- application/views/contact/index.php -->

<?php
// Initialize SettingModel to get hero data
require_once BASE_PATH . '/application/models/SettingModel.php';
$settingModel = new SettingModel();

// Get hero data from SettingModel instead of controller
$heroImageFilename = $settingModel->get('contact_hero_image', 'bg_7.jpg');
$heroImageUrl = BASE_URL . '/images/' . htmlspecialchars($heroImageFilename);
$heroTitle = $settingModel->get('contact_hero_title', 'Get In Touch');
$heroSubtitle = $settingModel->get('contact_hero_subtitle', "We'd love to hear from you. Reach out with any questions or comments.");
?>

<!-- Hero Section -->
<div class="hero-wrap" style="background-image: url('<?php echo $heroImageUrl; ?>');" data-stellar-background-ratio="1">
    <div class="hero-gradient"></div>
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center" data-scrollax-parent="true">
            <div class="col-md-9 ftco-animate pb-5 text-center" data-scrollax=" properties: { translateY: '70%' }">
                <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <span class="mr-2"><a href="<?php echo BASE_URL; ?>/">Home</a></span> <span>Contact</span>
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


<section class="ftco-section bg-light">
    <div class="container">
        <div class="row justify-content-center pb-5">
            <div class="col-md-12 text-center heading-section ftco-animate">
                <span class="subheading">Contact</span>
                <h2 class="mb-4">Contact Information</h2>
                <p>Have questions or want to learn more? Use the form or contact us directly using the information below.</p>
            </div>
        </div>

        <div class="row">
            <!-- Contact Info Cards -->
            <div class="col-md-6 col-lg-3 d-flex ftco-animate">
                <div class="align-self-stretch box p-4 text-center bg-white w-100">
                    <div class="icon d-flex align-items-center justify-content-center mx-auto mb-4" style="background-color: #4CAF50; width: 70px; height: 70px; border-radius: 50%;">
                        <span class="icon icon-map-marker text-white" style="font-size: 2rem;"></span>
                    </div>
                    <h3 class="mb-3">Our Address</h3>
                    <p class="mb-0">Bantwana Offices, Lot 482, Corner of Mimosa and Syringa Road, Courts Valley<br>Manzini, Eswatini</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex ftco-animate">
                <div class="align-self-stretch box p-4 text-center bg-white w-100">
                    <div class="icon d-flex align-items-center justify-content-center mx-auto mb-4" style="background-color: #4CAF50; width: 70px; height: 70px; border-radius: 50%;">
                        <span class="icon icon-phone text-white" style="font-size: 2rem;"></span>
                    </div>
                    <h3 class="mb-3">Phone Number</h3>
                    <p class="mb-0"><a href="tel:+26825052848">+268 2505 2848 | +268 7643 2289 | +268 7943 2289</a></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex ftco-animate">
                <div class="align-self-stretch box p-4 text-center bg-white w-100">
                    <div class="icon d-flex align-items-center justify-content-center mx-auto mb-4" style="background-color: #4CAF50; width: 70px; height: 70px; border-radius: 50%;">
                        <span class="icon icon-envelope text-white" style="font-size: 2rem;"></span>
                    </div>
                    <h3 class="mb-3">Email Address</h3>
                    <p class="mb-0"><a href="mailto:info@bantwana.co.sz">info@bantwana.co.sz</a></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex ftco-animate">
                <div class="align-self-stretch box p-4 text-center bg-white w-100">
                    <div class="icon d-flex align-items-center justify-content-center mx-auto mb-4" style="background-color: #4CAF50; width: 70px; height: 70px; border-radius: 50%;">
                        <span class="fab fa-facebook-f text-white" style="font-size: 2rem;"></span>
                    </div>
                    <h3 class="mb-3">Follow Us</h3>
                    <p class="mb-0"><a href="https://www.facebook.com/batwanaeswatini" target="_blank" rel="noopener noreferrer">Bantwana Initiative Eswatini</a></p>
                </div>
            </div>
        </div>

        <!-- Contact Form & Map Row -->
        <div class="row d-flex mt-5">
            <!-- Contact Form Column -->
            <div class="col-md-6 ftco-animate">
                <div class="bg-white p-4 h-100 d-flex flex-column">
                    <h3 class="mb-4">Send us a Message</h3>
                    <div class="modal-body">
                        <!-- Salesforce Web-to-Lead Form -->
                        <form action="https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8&orgId=00D07000000ZhMb" method="POST" onsubmit="return validateRecaptcha();">
                            <input type="hidden" name="captcha_settings" value='{"keyname":"BantwanaEswatini","fallback":"true","orgId":"00D07000000ZhMb","ts":""}'>
                            <input type="hidden" name="oid" value="00D07000000ZhMb">
                            <input type="hidden" name="retURL" value="https://bantwana.org.sz">
                            <input type="hidden" name="lead_source" value="Website">

                            <div class="form-group">
                                <label for="salutation">Salutation</label>
                                <select class="form-control" id="salutation" name="salutation">
                                    <option value="">--None--</option>
                                    <option value="Mr.">Mr.</option>
                                    <option value="Ms.">Ms.</option>
                                    <option value="Mrs.">Mrs.</option>
                                    <option value="Dr.">Dr.</option>
                                    <option value="Prof.">Prof.</option>
                                    <option value="Mx.">Mx.</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input class="form-control" id="first_name" maxlength="40" name="first_name" type="text" />
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input class="form-control" id="last_name" maxlength="80" name="last_name" type="text" />
                            </div>

                            <div class="form-group">
                                <label for="00NP200000AOp0v">Preferred Email</label>
                                <input class="form-control" id="00NP200000AOp0v" maxlength="40" name="00NP200000AOp0v" type="email" required />
                            </div>

                            <div class="form-group">
                                <label for="00NP200000AOp0w">Preferred Phone</label>
                                <input class="form-control" id="00NP200000AOp0w" maxlength="40" name="00NP200000AOp0w" type="tel" required />
                            </div>

                            <div class="form-group">
                                <label for="company">Company</label>
                                <input class="form-control" id="company" maxlength="40" name="company" type="text" />
                            </div>

                            <div class="form-group">
                                <label for="state">State/Province</label>
                                <input class="form-control" id="state" maxlength="20" name="state" type="text" />
                            </div>

                            <div class="form-group">
                                <label for="country">Country</label>
                                <input class="form-control" id="country" maxlength="40" name="country" type="text" />
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description"></textarea>
                            </div>

                            <!-- Google reCAPTCHA -->
                            <div class="g-recaptcha" data-sitekey="6Ldi77ErAAAAAG-PXLBOTQFTj6tCYtWDkBraFy_s"></div><br>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <input type="submit" class="btn btn-primary" name="submit" value="Submit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Map Column -->
            <div class="col-md-6 ftco-animate">
                <div class="bg-white" style="height: 500px;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3599.808005029023!2d31.3873551!3d-26.4982162!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1eef4f004154a67b%3A0x5c8c9df3d19eb432!2sBantwana%20offices%20Manzini!5e0!3m2!1sen!2ses!4v1722859201234!5m2!1sen!2ses"
                            width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google reCAPTCHA Script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
function validateRecaptcha() {
    var response = grecaptcha.getResponse();
    if (response.length === 0) {
        alert("⚠️ Please confirm you are not a robot by checking the reCAPTCHA box.");
        return false; // Block submission
    }
    return true; // Allow submission
}
</script>