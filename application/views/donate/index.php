<!-- application/views/donate/index.php - Approach 3: Comprehensive & Informative (Clean Design with Bantwana Colors) -->


<!-- Hero Section -->
<div class="hero-wrap donate-hero" 
     style="background-image: url('<?php echo BASE_URL; ?>/images/<?php echo htmlspecialchars($data['donateHero']['donate_hero_image']); ?>');" 
     data-stellar-background-ratio="1">
    <div class="hero-gradient"></div> <!-- Gradient overlay -->
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center" data-scrollax-parent="true">
            <div class="col-md-9 ftco-animate pb-5 text-center" data-scrollax="properties: { translateY: '70%' }">
                <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <span class="mr-2"><a href="<?php echo BASE_URL; ?>/">Home</a></span> 
                    <span>Donate</span>
                </p>
                <h1 class="mb-3 bread" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <?php echo htmlspecialchars($data['donateHero']['donate_hero_title']); ?>
                </h1>
                <p class="mb-0" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">
                    <?php echo htmlspecialchars($data['donateHero']['donate_hero_subtitle']); ?>
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
                <span class="subheading"><?php echo htmlspecialchars($data['donateMain']['donate_main_subheading']); ?></span>
                <h2 class="mb-4"><?php echo htmlspecialchars($data['donateMain']['donate_main_heading']); ?></h2>
                <p><?php echo nl2br($data['donateMain']['donate_main_content']); ?></p>
            </div>
        </div>


        <div class="row">
            <!-- Main Content Column -->
            <div class="col-lg-8 ftco-animate">
                <h3 class="mb-4">Financial Stewardship</h3>
                <p>At Bantwana Initiative Eswatini, we believe in complete transparency. We are committed to ensuring that your donation has the maximum possible impact. Our financial practices are guided by the highest standards of accountability.</p>

                <!-- Financial Breakdown (Clean Layout) -->
                <div class="row pt-3">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="bg-highlight p-4 h-100 d-flex flex-column">
                            <h4 class="mb-3">Program Expenses <span class="badge badge-primary">85%</span></h4>
                            <p>The vast majority of your donation goes directly into funding our programs that serve children, youth, and families. This includes:</p>
                            <ul class="flex-grow-1">
                                <li>Direct program services</li>
                                <li>Staff salaries for program delivery</li>
                                <li>Materials and supplies</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-highlight p-4 h-100 d-flex flex-column">
                            <h4 class="mb-3">Administrative & Fundraising <span class="badge badge-secondary">15%</span></h4>
                            <p>A small portion covers the essential costs of running our organization and raising funds to support our mission:</p>
                            <ul class="flex-grow-1">
                                <li>Office rent and utilities</li>
                                <li>Accounting and legal fees</li>
                                <li>Fundraising campaign costs</li>
                                <li>Governance and management</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <p class="mt-3"><small><em>*Percentages are illustrative. Please refer to our latest Annual Report for exact figures.</em></small></p>
                <!-- End Financial Breakdown -->

                <!-- Impact Stats Section -->
                <div class="sidebar-box ftco-animate bg-white p-4 mt-5">
                    <h3 class="heading-2 mb-4 text-center">Our Impact By The Numbers</h3>
                    <div class="row text-center">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="block-18">
                                <div class="text">
                                    <strong class="number" data-number="14328">15788</strong>
                                    <span>Children Supported</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="block-18">
                                <div class="text">
                                    <strong class="number" data-number="95">93</strong>%
                                    <span>Program Efficiency</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="block-18">
                                <div class="text">
                                    <strong class="number" data-number="16">17</strong>
                                    <span>Years of Service</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Impact Stats Section -->
            </div> <!-- .col-lg-8 -->

            <!-- Sidebar Column -->
            <div class="col-lg-4 sidebar ftco-animate">
                <!-- Ways to Give -->
                <div class="sidebar-box ftco-animate">
                    <h3 class="heading-2">Ways to Give</h3>
                    <div class="list-group">
                        <!-- Added data-toggle and data-target for the modal -->
                        <a href="#online-donation" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-toggle="modal" data-target="#donationModal">
                            <span><i class="fas fa-donate mr-2"></i> Online Donation</span>
                            <span class="badge badge-primary badge-pill">Popular</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#donationModal"><i class="fas fa-sync-alt mr-2"></i> Monthly Giving</a>
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#donationModal"><i class="fas fa-gift mr-2"></i> Planned Giving / Legacy Gifts</a>
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#donationModal"><i class="fas fa-hands-helping mr-2"></i> Corporate Matching Gifts</a>
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#donationModal"><i class="fas fa-tshirt mr-2"></i> Donate Goods</a>
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#donationModal"><i class="fas fa-calendar-alt mr-2"></i> Fundraise for Us</a>
                        <a href="<?php echo BASE_URL; ?>/volunteer" class="list-group-item list-group-item-action"><i class="fas fa-user-friends mr-2"></i> Volunteer Your Time</a>
                    </div>
                </div>
                <!-- End Ways to Give -->

                <!-- Secure Online Donation Form Placeholder -->
                <div class="sidebar-box ftco-animate" id="online-donation">
                    <h3 class="heading-2">Secure Online Donation</h3>
                    <p>Make a secure donation online using your credit card or bank account.</p>
                    <!-- Placeholder for secure payment form -->
                    <div class="bg-light p-4 text-center border rounded">
                        <p><i class="fas fa-lock fa-2x mb-3" style="color: var(--bantwana-green);"></i></p>
                        
                        <!-- Updated button to trigger the modal -->
                        <p><a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#donationModal">Donate Securely Now</a></p>
                    </div>
                </div>
                <!-- End Secure Online Donation -->
            </div> <!-- .col-lg-4 -->
        </div> <!-- .row -->
    </div> <!-- .container -->
</section>

<!-- Donation Modal -->
<div class="modal fade" id="donationModal" tabindex="-1" role="dialog" aria-labelledby="donationModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="donationModalTitle">Make a Donation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- ---------------------------------------------------------------------- -->
                <!-- The Salesforce Web-to-Lead Form is integrated below -->
                <!-- ---------------------------------------------------------------------- -->
                <form action="https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8&orgId=00D07000000ZhMb" method="POST">

                <input type=hidden name='captcha_settings' value='{"keyname":"BantwanaEswatini","fallback":"true","orgId":"00D07000000ZhMb","ts":""}'>
                <input type=hidden name="oid" value="00D07000000ZhMb">
                <input type=hidden name="retURL" value="https://bantwana.org.sz">
                <input type="hidden" name="lead_source" value="Website">

                <!-- ---------------------------------------------------------------------- -->
                <!-- NOTE: These fields are optional debugging elements. Please uncomment -->
                <!-- these lines if you wish to test in debug mode. -->
                <!-- <input type="hidden" name="debug" value=1> -->
                <!-- <input type="hidden" name="debugEmail" -->
                <!-- value="ebenezer.amoako@trailconsult.com"> -->
                <!-- ---------------------------------------------------------------------- -->

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

                <!-- Replace 'YOUR_V2_SITE_KEY' with the key you get from Google for reCAPTCHA v2 -->
                <div class="g-recaptcha" data-sitekey="6Ldi77ErAAAAAG-PXLBOTQFTj6tCYtWDkBraFy_s"></div><br>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-primary" name="submit" value="Submit Donation">
                </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add jQuery, Popper.js, and Bootstrap JS for modal functionality -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js"></script>
<script>
  function timestamp() { var response = document.getElementById("g-recaptcha-response"); if (response == null || response.value.trim() == "") {var elems = JSON.parse(document.getElementsByName("captcha_settings")[0].value);elems["ts"] = JSON.stringify(new Date().getTime());document.getElementsByName("captcha_settings")[0].value = JSON.stringify(elems); } } setInterval(timestamp, 500);
</script>
