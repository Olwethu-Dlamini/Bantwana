<?php
defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__, 2));
defined('BASE_URL') || define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/bantwana/public_html');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['title'] ?? 'Bantwana Initiative Eswatini - Empowering Vulnerable Children and Families'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($data['description'] ?? 'Bantwana Initiative Eswatini is a non-profit organization dedicated to improving the lives of vulnerable children, youth, and families through community development, child protection, and health programs in Eswatini.'); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($data['keywords'] ?? 'bantwana eswatini, child protection eswatini, community development eswatini, non-profit eswatini, youth empowerment eswatini, family support eswatini'); ?>">
    <link rel="canonical" href="<?php echo rtrim(BASE_URL, '/') . '/' . ($data['currentPage'] ?? 'home'); ?>">
    <!-- Structured Data (JSON-LD) for Organization -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Bantwana Initiative Eswatini",
        "url": "<?php echo rtrim(BASE_URL, '/'); ?>",
        "logo": "<?php echo rtrim(BASE_URL, '/') . '/images/bantwanalogo.png'; ?>",
        "description": "Bantwana Initiative Eswatini enhances the well-being and resilience of vulnerable children, youth, and families affected by HIV/AIDS and poverty through holistic care, protection, and empowerment.",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+268-2505-2848",
            "contactType": "customer service",
            "areaServed": "SZ",
            "availableLanguage": "English"
        },
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Lot 482, Corner of Mimosa and Syringa Road, Courts Valley",
            "addressLocality": "Manzini",
            "addressCountry": "Eswatini"
        },
        "sameAs": [
            "https://www.facebook.com/bantwanaeswatini",
            "https://www.linkedin.com/company/bantwanainitiativeeswatini/"
        ]
    }
    </script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Dosis:200,300,400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Overpass:300,400,400i,600,700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" integrity="sha512-PgQMlq+nqFLV4ylk1gwUOgm6CtIIXkKwaIHp/PAIWHzig/lKZSEGKEysh0TCVbHJXCLN7WetD8TFecIky75ZfQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Theme CSS -->
    <?php
    $cssFiles = [
        'open-iconic-bootstrap.min.css', 'animate.css', 'owl.carousel.min.css',
        'owl.theme.default.min.css', 'magnific-popup.css', 'aos.css',
        'ionicons.min.css', 'bootstrap-datepicker.css', 'jquery.timepicker.css',
        'flaticon.css', 'icomoon.css', 'style.css', 'custom.css'
    ];
    foreach ($cssFiles as $file) {
        echo '<link rel="stylesheet" href="' . rtrim(BASE_URL, '/') . '/css/' . $file . '">' . PHP_EOL;
    }
    ?>
    <style>
        /* Gradient overlay for hero section */
        .donate-hero {
            position: relative;
            height: clamp(400px, 65vh, 800px);
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
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
        .donate-hero .container {
            position: relative;
            z-index: 2;
        }
        .donate-hero .breadcrumbs {
            font-size: clamp(0.9rem, 1.8vw, 1.1rem);
            color: #f8f8f8;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
        }
        .donate-hero .bread {
            font-size: clamp(2rem, 4.5vw, 3rem);
            font-weight: 900;
            color: #ffffff;
            text-shadow: 0 3px 15px rgba(0, 0, 0, 0.7);
        }
        .donate-hero p.mb-0 {
            font-size: clamp(1rem, 2vw, 1.25rem);
            font-weight: 600;
            color: #f8f8f8;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
        }
        @media (max-width: 768px) {
            .donate-hero {
                height: clamp(350px, 60vh, 600px);
                padding: 1.5rem;
            }
            .donate-hero .bread {
                font-size: clamp(1.75rem, 4vw, 2.5rem);
            }
            .donate-hero p.mb-0 {
                font-size: clamp(0.9rem, 1.8vw, 1.1rem);
            }
        }
        @media (max-width: 480px) {
            .donate-hero {
                padding: 1rem;
            }
            .donate-hero .bread {
                font-size: clamp(1.5rem, 3.5vw, 2rem);
            }
            .donate-hero p.mb-0 {
                font-size: clamp(0.8rem, 1.6vw, 1rem);
            }
        }
    </style>
</head>
<body>
    <!-- Header / Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar" role="navigation">
        <div class="container">
            <a class="navbar-brand" href="<?php echo rtrim(BASE_URL, '/'); ?>/">
                <img src="<?php echo rtrim(BASE_URL, '/') . '/images/bantwanalogo.png'; ?>" alt="Bantwana Initiative Logo" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span>
            </button>
            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <?php
                    $navItems = [
                        'home' => 'Home',
                        'about' => 'About',
                        'donate' => 'Donate',
                        'programs' => 'Programs',
                        'news' => 'News & Resources',
                        'get_involved' => 'Get Involved',
                        'contact' => 'Contact'
                    ];
                    foreach ($navItems as $key => $label) {
                        $active = (isset($data['currentPage']) && $data['currentPage'] === $key) ? 'active' : '';
                        if ($key === 'get_involved') {
                            echo "<li class='nav-item dropdown $active'>
                                    <a href='" . rtrim(BASE_URL, '/') . "/$key' class='nav-link dropdown-toggle' id='getInvolvedDropdown' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        $label
                                    </a>
                                    <div class='dropdown-menu' aria-labelledby='getInvolvedDropdown'>
                                        <a class='dropdown-item' href='" . rtrim(BASE_URL, '/') . "/volunteer'>Volunteering</a>
                                        <a class='dropdown-item' href='" . rtrim(BASE_URL, '/') . "/internship'>Internships</a>
                                        <a class='dropdown-item' href='" . rtrim(BASE_URL, '/') . "/careers'>Careers</a>
                                        <a class='dropdown-item' href='" . rtrim(BASE_URL, '/') . "/partner'>Partner with Us</a>
                                    </div>
                                </li>";
                        } elseif ($key === 'about') {
                            echo "<li class='nav-item dropdown $active'>
                                    <a href='" . rtrim(BASE_URL, '/') . "/$key' class='nav-link dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        $label
                                    </a>
                                    <div class='dropdown-menu'>
                                        <a class='dropdown-item' href='" . rtrim(BASE_URL, '/') . "/about'>Who Are We</a>
                                        <a class='dropdown-item' href='" . rtrim(BASE_URL, '/') . "/team'>Our Team</a>
                                    </div>
                                </li>";
                        } elseif ($key === 'news') {
                            echo "<li class='nav-item dropdown $active'>
                                    <a href='" . rtrim(BASE_URL, '/') . "/$key' class='nav-link dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        $label
                                    </a>
                                    <div class='dropdown-menu'>
                                        <a class='dropdown-item' href='" . rtrim(BASE_URL, '/') . "/blog'>Newsletter & Socials</a>
                                        <a class='dropdown-item' href='" . rtrim(BASE_URL, '/') . "/publications'>Publications</a>
                                        <a class='dropdown-item' href='" . rtrim(BASE_URL, '/') . "/gallery'>Gallery</a>
                                    </div>
                                </li>";
                        } else {
                            echo "<li class='nav-item $active'><a href='" . rtrim(BASE_URL, '/') . "/$key' class='nav-link'>$label</a></li>";
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- END nav -->
    <!-- Main Content -->
    <main role="main">
        <?php echo isset($content) ? $content : (isset($view) ? require_once BASE_PATH . '/application/views/' . $view . '.php' : ''); ?>
    </main>
    <!-- Footer -->
    <footer class="ftco-footer ftco-section img">
        <div class="overlay"></div>
        <div class="container">
            <div class="row mb-5">
                <!-- About Us -->
                <div class="col-md-3">
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2">About Us</h2>
                        <p>Established in 2008, Bantwana Initiative Eswatini provides comprehensive care to orphaned and vulnerable children and families nationwide.</p>
                        <ul class="ftco-footer-social list-unstyled mt-3">
                            <li class="ftco-animate d-inline-block mr-2">
                                <a href="https://www.facebook.com/bantwanaeswatini" target="_blank" rel="noopener"><span class="icon-facebook" aria-hidden="true"></span></a>
                            </li>
                            <li class="ftco-animate d-inline-block mr-2">
                                <a href="https://www.linkedin.com/company/bantwanainitiativeeswatini/" target="_blank" rel="noopener"><span class="icon-linkedin" aria-hidden="true"></span></a>
                            </li>
                             <li class="ftco-animate d-inline-block mr-2">
                                <a href="https://www.instagram.com/bantwana_initiative_eswatini" target="_blank" rel="noopener"><span class="icon-instagram" aria-hidden="true"></span></a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
                <!-- Recent News -->
                <div class="col-md-4">
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2">Recent News</h2>
                        <div class="block-21 mb-3 d-flex">
                            <div class="text">
                                <h3 class="heading"><a href="<?php echo rtrim(BASE_URL, '/') . '/blog'; ?>">Stay tuned for updates from Bantwana.</a></h3>
                            </div>
                        </div>
                        <p><a href="<?php echo rtrim(BASE_URL, '/') . '/blog'; ?>" class="btn btn-outline-white">View All News</a></p>
                    </div>
                </div>
                <!-- Quick Links -->
                <div class="col-md-2">
                    <div class="ftco-footer-widget mb-4 ml-md-4">
                        <h2 class="ftco-heading-2">Quick Links</h2>
                        <ul class="list-unstyled">
                            <?php
                            $quickLinks = [
                                '/' => 'Home',
                                '/about' => 'About Us',
                                '/programs' => 'Our Programs',
                                '/donate' => 'Donate',
                                '/volunteer' => 'Volunteer',
                                '/gallery' => 'Gallery',
                                '/contact' => 'Contact'
                            ];
                            foreach ($quickLinks as $url => $text) {
                                echo "<li><a href='" . rtrim(BASE_URL, '/') . $url . "' class='py-1 d-block'>$text</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <!-- Contact -->
                <div class="col-md-3">
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2">Contact Us</h2>
                        <div class="block-23 mb-3">
                            <ul>
                                <li>
                                    <span class="icon icon-map-marker"></span>
                                    <span class="text">Bantwana Offices, Lot 482, Corner of Mimosa and Syringa Road, Courts Valley, Manzini, Eswatini</span>
                                </li>
                                <li>
                                    <a href="tel:+26825052848"><span class="icon icon-phone"></span><span class="text">+268 2505 2848 | +268 7643 2289 | +268 7943 2289</span></a>
                                </li>
                                <li>
                                    <a href="mailto:info@bantwana.co.sz"><span class="icon icon-envelope"></span><span class="text">info@bantwana.co.sz</span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Copyright -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>&copy; <script>document.write(new Date().getFullYear());</script> Bantwana Initiative Eswatini. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Loader -->
    <div id="ftco-loader" class="show fullscreen">
        <svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/>
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#F96D00"/>
        </svg>
    </div>
    <!-- JS Libraries -->
    <?php
    $jsFiles = [
        'jquery.min.js', 'jquery-migrate-3.0.1.min.js', 'popper.min.js', 'bootstrap.min.js',
        'jquery.easing.1.3.js', 'jquery.waypoints.min.js', 'jquery.stellar.min.js',
        'owl.carousel.min.js', 'jquery.magnific-popup.min.js', 'aos.js',
        'jquery.animateNumber.min.js', 'bootstrap-datepicker.js', 'jquery.timepicker.min.js',
        'scrollax.min.js', 'google-map.js', 'main.js'
    ];
    foreach ($jsFiles as $js) {
        echo '<script src="' . rtrim(BASE_URL, '/') . '/js/' . $js . '"></script>' . PHP_EOL;
    }
    ?>
    <!-- Google Maps -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap"></script>
    <!-- Facebook SDK -->
    <script>
        (function(d, s, id) {
            if (d.getElementById(id)) return;
            let js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v23.0&appId=395042390636886";
            d.getElementsByTagName(s)[0].parentNode.insertBefore(js, s);
        }(document, 'script', 'facebook-jssdk'));
    </script>
    <!-- AJAX for dynamic social content -->
    <script>
        $(document).ready(function () {
            function loadContent(source) {
                $('#dynamic-content').html('<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</p>');
                $.ajax({
                    url: '<?php echo rtrim(BASE_URL, '/') . '/home/fetchPosts'; ?>',
                    type: 'GET',
                    data: { source },
                    dataType: 'html',
                    success: function (response) {
                        $('#dynamic-content').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        $('#dynamic-content').html('<p class="text-center text-danger">Failed to load content. Try again later.</p>');
                    }
                });
            }
            $('#load-facebook').click(function (e) {
                e.preventDefault();
                loadContent('facebook');
            });
            $('#load-linkedin').click(function (e) {
                e.preventDefault();
                loadContent('linkedin');
            });
            $('#clear-content').click(function (e) {
                e.preventDefault();
                $('#dynamic-content').html('<p class="text-center text-muted">Click a button above to load content.</p>');
            });
        });
    </script>
</body>
</html>