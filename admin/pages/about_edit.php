<?php
// admin/pages/about_edit.php
session_start();

// --- Authentication Check (Basic) ---
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

// --- Define Paths Correctly for the Subdomain Context ---
// For subdomain: /home/bantwana/admin
// Application is at: /home/bantwana/application
define('ADMIN_BASE_PATH', dirname($_SERVER['DOCUMENT_ROOT'])); // Points to /home/bantwana
if (!defined('BASE_PATH')) {
    define('BASE_PATH', ADMIN_BASE_PATH); // /home/bantwana
}

// --- Define BASE_URL for the Main Domain ---
if (!defined('BASE_URL')) {
    // Remove 'admin.' from the host to get main domain
    $host = $_SERVER['HTTP_HOST'];
    $mainHost = str_replace('admin.', '', $host);
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    define('BASE_URL', $protocol . '://' . $mainHost);
}

// --- Autoload core classes ---
function loadAdminClass($className) {
    $coreFile = BASE_PATH . '/application/core/' . $className . '.php';
    if (file_exists($coreFile)) {
        require_once $coreFile;
        return;
    }
    $modelFile = BASE_PATH . '/application/models/' . $className . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return;
    }
    error_log("Required admin class '$className' not found at expected locations ($coreFile or $modelFile).");
}

// --- Load Configuration ---
$configPath = BASE_PATH . '/application/config/config.php';
if (file_exists($configPath)) {
    require_once $configPath;
} else {
    die("Configuration error: Unable to load application config at $configPath");
}

// Load required classes
loadAdminClass('Database');
loadAdminClass('SettingModel');

// --- Handle Form Submission (Including Image Uploads) ---
$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_about_content'])) {
        $settingModel = new SettingModel();
        $overall_success = true; // Flag to track if any part failed

        // --- Handle Hero Image Upload ---
        $new_hero_filename = null;
        if (isset($_FILES['about_hero_image']) && $_FILES['about_hero_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public_html/images/';
            $uploadFile = $_FILES['about_hero_image'];

            // Basic validation
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $message = "Invalid Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.";
                $message_type = 'error';
                $overall_success = false;
            } elseif ($uploadFile['size'] > $maxFileSize) {
                $message = "Hero image file is too large. Maximum size is 5MB.";
                $message_type = 'error';
                $overall_success = false;
            } else {
                // Generate a unique filename
                $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                $new_hero_filename = 'about_hero_' . uniqid() . '.' . strtolower($fileExtension);
                $targetPath = $uploadDir . $new_hero_filename;

                if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                    // Successfully uploaded hero image
                    if ($settingModel->set('about_hero_image', $new_hero_filename)) {
                        $message = "Hero image uploaded successfully!";
                        $message_type = 'success';
                    } else {
                        $message = "Hero image uploaded, but failed to save filename to database.";
                        $message_type = 'error';
                        $overall_success = false;
                    }
                } else {
                    $message = "Error uploading Hero image file.";
                    $message_type = 'error';
                    $overall_success = false;
                }
            }
        } else if (isset($_FILES['about_hero_image']) && $_FILES['about_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
             $message = "Error uploading Hero image: " . $_FILES['about_hero_image']['error'];
             $message_type = 'error';
             $overall_success = false;
        }
        // --- End Hero Image Upload ---

        // --- Handle History Image Upload (Independent of Hero Image Outcome) ---
        $new_history_filename = null;
        // Check if a history image file was actually provided for upload
        if (isset($_FILES['about_history_image']) && $_FILES['about_history_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['about_history_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = BASE_PATH . '/public_html/images/';
                $uploadFile = $_FILES['about_history_image'];

                // Basic validation
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
                $maxFileSize = 5 * 1024 * 1024; // 5MB

                if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $message_temp = "Invalid History image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.";
                    if ($message_type !== 'error') {
                        $message = $message_temp;
                        $message_type = 'error';
                    } else {
                        $message .= " " . $message_temp;
                    }
                    $overall_success = false;
                } elseif ($uploadFile['size'] > $maxFileSize) {
                    $message_temp = "History image file is too large. Maximum size is 5MB.";
                    if ($message_type !== 'error') {
                        $message = $message_temp;
                        $message_type = 'error';
                    } else {
                        $message .= " " . $message_temp;
                    }
                    $overall_success = false;
                } else {
                    // Generate a unique filename
                    $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                    $new_history_filename = 'about_history_' . uniqid() . '.' . strtolower($fileExtension);
                    $targetPath = $uploadDir . $new_history_filename;

                    if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                        // Successfully uploaded history image
                        if ($settingModel->set('about_history_image', $new_history_filename)) {
                            $message_temp = "History image uploaded successfully!";
                            if ($message_type === 'success') {
                                // Append to existing hero success message
                                $message .= " " . $message_temp;
                            } else if ($message_type !== 'error') {
                                // No prior message, set new one
                                $message = $message_temp;
                                $message_type = 'success';
                            } // If $message_type was 'error' from hero, keep the error message
                        } else {
                            $message_temp = "History image uploaded, but failed to save filename to database.";
                            if ($message_type !== 'error') {
                                $message = $message_temp;
                                $message_type = 'error';
                            } else {
                                $message .= " " . $message_temp;
                            }
                            $overall_success = false;
                        }
                    } else {
                        $message_temp = "Error uploading History image file.";
                        if ($message_type !== 'error') {
                            $message = $message_temp;
                            $message_type = 'error';
                        } else {
                            $message .= " " . $message_temp;
                        }
                        $overall_success = false;
                    }
                }
            } else {
                // An error occurred during history image upload (e.g., exceeds size limit)
                $message_temp = "Error uploading History image (Code: " . $_FILES['about_history_image']['error'] . ").";
                if ($message_type !== 'error') {
                    $message = $message_temp;
                    $message_type = 'error';
                } else {
                    $message .= " " . $message_temp;
                }
                $overall_success = false;
            }
        }
        // If no history file was selected, do nothing for history image.

        // --- Handle Text Settings Update (Only if uploads didn't fatally error) ---
        if ($overall_success) { // Proceed only if uploads were successful or not attempted
            // Define keys and fetch values from POST
            $text_settings_map = [
                // Hero Section
                'about_hero_title' => $_POST['about_hero_title'] ?? '',
                // Story Section
                'about_story_heading' => $_POST['about_story_heading'] ?? '',
                'about_story_text_1' => $_POST['about_story_text_1'] ?? '',
                'about_story_text_2' => $_POST['about_story_text_2'] ?? '',
                'about_story_text_3' => $_POST['about_story_text_3'] ?? '',
                // Mission/Vision
                'about_mission_title' => $_POST['about_mission_title'] ?? '',
                'about_mission_text' => $_POST['about_mission_text'] ?? '',
                'about_vision_title' => $_POST['about_vision_title'] ?? '',
                'about_vision_text' => $_POST['about_vision_text'] ?? '',
                // Approach Section
                'about_approach_title' => $_POST['about_approach_title'] ?? '',
                'about_approach_intro' => $_POST['about_approach_intro'] ?? '',
                // Approach Items (1-6)
                'about_approach_item_1_number' => $_POST['about_approach_item_1_number'] ?? '',
                'about_approach_item_1_heading' => $_POST['about_approach_item_1_heading'] ?? '',
                'about_approach_item_1_text' => $_POST['about_approach_item_1_text'] ?? '',
                'about_approach_item_2_number' => $_POST['about_approach_item_2_number'] ?? '',
                'about_approach_item_2_heading' => $_POST['about_approach_item_2_heading'] ?? '',
                'about_approach_item_2_text' => $_POST['about_approach_item_2_text'] ?? '',
                'about_approach_item_3_number' => $_POST['about_approach_item_3_number'] ?? '',
                'about_approach_item_3_heading' => $_POST['about_approach_item_3_heading'] ?? '',
                'about_approach_item_3_text' => $_POST['about_approach_item_3_text'] ?? '',
                'about_approach_item_4_number' => $_POST['about_approach_item_4_number'] ?? '',
                'about_approach_item_4_heading' => $_POST['about_approach_item_4_heading'] ?? '',
                'about_approach_item_4_text' => $_POST['about_approach_item_4_text'] ?? '',
                'about_approach_item_5_number' => $_POST['about_approach_item_5_number'] ?? '',
                'about_approach_item_5_heading' => $_POST['about_approach_item_5_heading'] ?? '',
                'about_approach_item_5_text' => $_POST['about_approach_item_5_text'] ?? '',
                'about_approach_item_6_number' => $_POST['about_approach_item_6_number'] ?? '',
                'about_approach_item_6_heading' => $_POST['about_approach_item_6_heading'] ?? '',
                'about_approach_item_6_text' => $_POST['about_approach_item_6_text'] ?? '',
                // Values Section Titles (if made editable)
                'about_values_title' => $_POST['about_values_title'] ?? 'The Principles That Guide Us',
                'about_values_intro' => $_POST['about_values_intro'] ?? 'Our core values ensure our actions are ethical, effective, and centered on the communities we serve.',
                // Stats Section
                'about_stats_title' => $_POST['about_stats_title'] ?? 'By The Numbers',
                'about_stats_children_number' => $_POST['about_stats_children_number'] ?? '14,328',
                'about_stats_children_text' => $_POST['about_stats_children_text'] ?? 'Children Supported',
                'about_stats_staff_number' => $_POST['about_stats_staff_number'] ?? '15',
                'about_stats_staff_text' => $_POST['about_stats_staff_text'] ?? 'Dedicated Staff & Volunteers',
                'about_stats_regions_number' => $_POST['about_stats_regions_number'] ?? '4',
                'about_stats_regions_text' => $_POST['about_stats_regions_text'] ?? 'Regions Impacted',
                'about_stats_years_number' => $_POST['about_stats_years_number'] ?? '17',
                'about_stats_years_text' => $_POST['about_stats_years_text'] ?? 'Years of Service',
            ];

            $text_save_success = true;
            foreach ($text_settings_map as $key => $value) {
                // Trim whitespace from values before saving
                $trimmed_value = trim($value);
                if (!$settingModel->set($key, $trimmed_value)) {
                    $text_save_success = false;
                    error_log("Failed to save setting: $key"); // Log specific failures
                }
            }

            if ($text_save_success) {
                // Update success message if text save was successful and no prior error
                if ($message_type !== 'error' && !empty($message)) {
                    // Message already set by image upload, append confirmation
                    $message .= " Content updated.";
                } else if ($message_type !== 'error' && empty($message)) {
                    // No image message, set text success message
                    $message = "About page content updated successfully!";
                    $message_type = 'success';
                }
                // If $message_type is 'error', an image upload failed, keep that error message

                // --- Log the successful update ---
                $logMessage = "Admin {$_SESSION['username']} updated About page content at " . date('Y-m-d H:i:s');
                if ($new_hero_filename) {
                    $logMessage .= " (New hero image: $new_hero_filename)";
                }
                if ($new_history_filename) {
                    $logMessage .= " (New history image: $new_history_filename)";
                }
                logAdminActivity($logMessage);

            } else {
                // Text saving failed
                if ($message_type !== 'error') {
                    // Prior actions were successful, now text failed
                    $message = "Content updated partially. Failed to save some text settings.";
                    $message_type = 'error';
                } else {
                    // Prior action (image upload) failed, append text failure
                    $message .= " Failed to save some text settings.";
                }
                // --- Log the partial failure ---
                $logMessage = "Admin {$_SESSION['username']} failed to update some About page text content at " . date('Y-m-d H:i:s');
                logAdminActivity($logMessage);
            }
        } else {
             // Overall success was false due to image upload errors, message is already set.
             // Log the failure
             $logMessage = "Admin {$_SESSION['username']} failed to update About page content due to image upload errors at " . date('Y-m-d H:i:s') . " - Error: $message";
             logAdminActivity($logMessage);
        }
    }
}

// --- Fetch Current Settings for Form Population ---
$settingModel = new SettingModel();
// List all keys needed for the form
$aboutContentKeys = [
    'about_hero_image',
    'about_history_image',
    'about_hero_title',
    'about_story_heading',
    'about_story_text_1',
    'about_story_text_2',
    'about_story_text_3',
    'about_mission_title',
    'about_mission_text',
    'about_vision_title',
    'about_vision_text',
    'about_approach_title',
    'about_approach_intro',
    // Approach Items (1-6)
    'about_approach_item_1_number', 'about_approach_item_1_heading', 'about_approach_item_1_text',
    'about_approach_item_2_number', 'about_approach_item_2_heading', 'about_approach_item_2_text',
    'about_approach_item_3_number', 'about_approach_item_3_heading', 'about_approach_item_3_text',
    'about_approach_item_4_number', 'about_approach_item_4_heading', 'about_approach_item_4_text',
    'about_approach_item_5_number', 'about_approach_item_5_heading', 'about_approach_item_5_text',
    'about_approach_item_6_number', 'about_approach_item_6_heading', 'about_approach_item_6_text',
    // Values Section Titles (currently static in view, but keys exist for potential future editing)
    'about_values_title',
    'about_values_intro',
    // Stats Section
    'about_stats_title',
    'about_stats_children_number', 'about_stats_children_text',
    'about_stats_staff_number', 'about_stats_staff_text',
    'about_stats_regions_number', 'about_stats_regions_text',
    'about_stats_years_number', 'about_stats_years_text',
];

// Fetch settings, defaulting to empty string if not found
$currentSettings = $settingModel->getMultiple($aboutContentKeys, '');

// Set default values for hero section
$currentSettings['about_hero_image'] = $currentSettings['about_hero_image'] ?: 'bg_2.jpg';
$currentSettings['about_hero_title'] = $currentSettings['about_hero_title'] ?: 'Our Journey of Hope';

// --- Function to log admin activities ---
function logAdminActivity($message) {
    try {
        $logDb = new Database();
        $stmt = $logDb->pdo->prepare('INSERT INTO logs (timestamp, message) VALUES (NOW(), ?)');
        $stmt->execute([$message]);
    } catch (Exception $e) {
        error_log("CMS Admin Log Error: " . $e->getMessage());
    }
}

$page_title = "Edit About Page Content - Bantwana CMS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f1f1f1; padding-top: 20px; }
        .admin-container { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group label { font-weight: bold; }
        .section-header { border-bottom: 1px solid #dee2e6; padding-bottom: 10px; margin-bottom: 20px; margin-top: 30px; color: #495057; }
        .subsection-header { border-left: 3px solid #007bff; padding-left: 10px; margin-top: 20px; margin-bottom: 15px; color: #333; }
        .current-image-preview { max-width: 200px; max-height: 150px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .hero-preview {
            background-size: cover;
            background-position: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            padding: 60px 20px;
            margin: 20px 0;
            border-radius: 5px;
            position: relative;
        }
        .hero-preview::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.4);
            border-radius: 5px;
        }
        .hero-preview-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }
        .character-count {
            font-size: 0.8em;
            color: #6c757d;
            float: right;
        }
        textarea.form-control { min-height: 100px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <?php // include '../includes/header.php'; ?> <!-- Optional -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit About/Who are we Page Content</h2>
            <a href="../dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
        </div>

        <!-- Display Message -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo ($message_type === 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Hero Preview -->
        <div class="admin-container">
            <h4><i class="fas fa-eye mr-2"></i>Hero Section Preview</h4>
            <div class="hero-preview" id="hero-preview" style="background-image: url('<?php echo BASE_URL . '/images/' . htmlspecialchars($currentSettings['about_hero_image']); ?>');">
                <div class="hero-preview-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent justify-content-center p-0 mb-3">
                            <li class="breadcrumb-item text-white" aria-current="page">About</li>
                        </ol>
                    </nav>
                    <h1 class="mb-3" id="preview-title"><?php echo htmlspecialchars($currentSettings['about_hero_title']); ?></h1>
                </div>
            </div>
        </div>

        <div class="admin-container">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">

                <!-- Hero Section -->
                <div class="section-header">
                    <h4><i class="fas fa-image mr-2"></i>Hero Section</h4>
                </div>
                <!-- Hero Image Upload -->
                <div class="form-group">
                    <label for="about_hero_image">Hero Background Image</label>
                    <input type="file" class="form-control-file" id="about_hero_image" name="about_hero_image" 
                           accept="image/*" onchange="previewImage(this)">
                    <small class="form-text text-muted">Upload a new background image for the About page hero section (JPG, JPEG, PNG, GIF, WEBP, max 5MB).</small>
                    <?php
                    $currentHeroImage = $currentSettings['about_hero_image'];
                    $heroImagePath = BASE_URL . '/images/' . htmlspecialchars($currentHeroImage);
                    $serverHeroImagePath = BASE_PATH . '/public_html/images/' . $currentHeroImage;
                    if (file_exists($serverHeroImagePath) && !empty($currentHeroImage)) {
                        echo "<p class='mt-2'><strong>Current Hero Image:</strong></p>";
                        echo "<img src='$heroImagePath' id='about_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail'>";
                        echo "<p class='text-muted mt-1' id='about_hero_image_name'>" . htmlspecialchars($currentHeroImage) . "</p>";
                    } else {
                        echo "<p class='mt-2 text-muted' id='about_hero_image_name'>Current hero image: " . htmlspecialchars($currentHeroImage) . " (file not found or default).</p>";
                        echo "<img src='' id='about_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail d-none'>"; // Hidden if no valid image
                    }
                    ?>
                </div>
                <!-- Hero Title -->
                <div class="form-group">
                    <label for="about_hero_title">Hero Title</label>
                    <div class="character-count">
                        <span id="title-count"><?php echo strlen($currentSettings['about_hero_title']); ?></span>/255
                    </div>
                    <input type="text" class="form-control" id="about_hero_title" name="about_hero_title" 
                           value="<?php echo htmlspecialchars($currentSettings['about_hero_title']); ?>" 
                           maxlength="255">
                    <small class="form-text text-muted">Main heading displayed on the About page hero section.</small>
                </div>

                <!-- Story/History Section -->
                <div class="section-header mt-5">
                    <h4>Story / History Section</h4>
                </div>
                <!-- History Image Upload -->
                <div class="form-group">
                    <label for="about_history_image">History Content Image:</label>
                    <input type="file" class="form-control-file" id="about_history_image" name="about_history_image" accept="image/*">
                    <small class="form-text text-muted">Upload a new image for the history/story section (JPG, JPEG, PNG, GIF, WEBP, max 5MB).</small>
                    <?php
                    // Use the setting value, defaulting to 'event-1.jpg' if missing/empty
                    $currentHistoryImage = !empty($currentSettings['about_history_image']) ? $currentSettings['about_history_image'] : 'event-1.jpg';
                    $historyImagePath = BASE_URL . '/images/' . htmlspecialchars($currentHistoryImage);
                    $serverHistoryImagePath = BASE_PATH . '/public_html/images/' . $currentHistoryImage;
                    if (file_exists($serverHistoryImagePath) && !empty($currentHistoryImage)) {
                        echo "<p class='mt-2'><strong>Current History Image:</strong></p>";
                        echo "<img src='$historyImagePath' alt='Current History Image' class='current-image-preview img-thumbnail'>";
                    } else {
                        echo "<p class='mt-2 text-muted'>Current history image: '{$currentHistoryImage}' (file not found or default).</p>";
                    }
                    ?>
                </div>
                <!-- Story Heading -->
                <div class="form-group">
                    <label for="about_story_heading">Story Heading:</label>
                    <input type="text" class="form-control" id="about_story_heading" name="about_story_heading" value="<?php echo htmlspecialchars($currentSettings['about_story_heading'] ?? 'Where Necessity Met Compassion'); ?>">
                </div>
                <!-- Story Paragraphs -->
                <div class="form-group">
                    <label for="about_story_text_1">Story Paragraph 1:</label>
                    <textarea class="form-control" id="about_story_text_1" name="about_story_text_1" rows="3"><?php echo htmlspecialchars($currentSettings['about_story_text_1'] ?? 'Founded in 2008 in Eswatini’s Lubombo region, the Bantwana Initiative emerged from a deep need to support orphaned and vulnerable children, youth, and families impacted by HIV & AIDS and poverty.'); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="about_story_text_2">Story Paragraph 2:</label>
                    <textarea class="form-control" id="about_story_text_2" name="about_story_text_2" rows="3"><?php echo htmlspecialchars($currentSettings['about_story_text_2'] ?? 'From a small local effort, we’ve grown into a national beacon of hope, guided by our mission to foster well-being and resilience through holistic care, protection, and empowerment.'); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="about_story_text_3">Story Paragraph 3:</label>
                    <textarea class="form-control" id="about_story_text_3" name="about_story_text_3" rows="3"><?php echo htmlspecialchars($currentSettings['about_story_text_3'] ?? 'Our path is paved with collaboration, innovation, and an unwavering commitment to a brighter future for every child.'); ?></textarea>
                </div>

                <!-- Mission & Vision Section -->
                <div class="section-header mt-5">
                    <h4>Mission & Vision Section</h4>
                </div>
                <!-- Mission -->
                <div class="form-group">
                    <label for="about_mission_title">Mission Title:</label>
                    <input type="text" class="form-control" id="about_mission_title" name="about_mission_title" value="<?php echo htmlspecialchars($currentSettings['about_mission_title'] ?? 'Our Mission:'); ?>">
                </div>
                <div class="form-group">
                    <label for="about_mission_text">Mission Text:</label>
                    <textarea class="form-control" id="about_mission_text" name="about_mission_text" rows="3"><?php echo htmlspecialchars($currentSettings['about_mission_text'] ?? '"To enhance the well-being and resilience of vulnerable children, youth, and their families affected by HIV & AIDS and poverty through holistic care, protection, and empowerment."'); ?></textarea>
                </div>
                <!-- Vision -->
                <div class="form-group">
                    <label for="about_vision_title">Vision Title:</label>
                    <input type="text" class="form-control" id="about_vision_title" name="about_vision_title" value="<?php echo htmlspecialchars($currentSettings['about_vision_title'] ?? 'Our Vision:'); ?>">
                </div>
                <div class="form-group">
                    <label for="about_vision_text">Vision Text:</label>
                    <textarea class="form-control" id="about_vision_text" name="about_vision_text" rows="3"><?php echo htmlspecialchars($currentSettings['about_vision_text'] ?? '"A society where every child is healthy, safe, and empowered to thrive in a nurturing, equitable environment."'); ?></textarea>
                </div>

                <!-- Approach Section -->
                <div class="section-header mt-5">
                    <h4>Approach Section</h4>
                </div>
                <div class="form-group">
                    <label for="about_approach_title">Approach Title:</label>
                    <input type="text" class="form-control" id="about_approach_title" name="about_approach_title" value="<?php echo htmlspecialchars($currentSettings['about_approach_title'] ?? 'Holistic Care, Lasting Impact'); ?>">
                </div>
                <div class="form-group">
                    <label for="about_approach_intro">Approach Introduction:</label>
                    <textarea class="form-control" id="about_approach_intro" name="about_approach_intro" rows="2"><?php echo htmlspecialchars($currentSettings['about_approach_intro'] ?? 'We address complex challenges with comprehensive strategies, targeting root causes to build long-term resilience.'); ?></textarea>
                </div>

                <!-- Approach Items (1-6) -->
                <div class="subsection-header">
                    <h5>Approach Items</h5>
                </div>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="form-row mb-3">
                    <div class="col-md-1">
                        <input type="text" class="form-control" name="about_approach_item_<?php echo $i; ?>_number" value="<?php echo htmlspecialchars($currentSettings["about_approach_item_{$i}_number"] ?? "0{$i}"); ?>" placeholder="Num">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="about_approach_item_<?php echo $i; ?>_heading" value="<?php echo htmlspecialchars($currentSettings["about_approach_item_{$i}_heading"] ?? "Approach Item {$i}"); ?>" placeholder="Heading">
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="about_approach_item_<?php echo $i; ?>_text" value="<?php echo htmlspecialchars($currentSettings["about_approach_item_{$i}_text"] ?? "Description for approach item {$i}."); ?>" placeholder="Description">
                    </div>
                </div>
                <?php endfor; ?>

                <!-- Stats Section -->
                <div class="section-header mt-5">
                    <h4>Statistics Section</h4>
                </div>
                <div class="form-group">
                    <label for="about_stats_title">Stats Section Title:</label>
                    <input type="text" class="form-control" id="about_stats_title" name="about_stats_title" value="<?php echo htmlspecialchars($currentSettings['about_stats_title'] ?? 'By The Numbers'); ?>">
                </div>
                <div class="stats-grid">
                    <div class="form-group">
                        <label for="about_stats_children_number">Children Number:</label>
                        <input type="text" class="form-control" id="about_stats_children_number" name="about_stats_children_number" value="<?php echo htmlspecialchars($currentSettings['about_stats_children_number'] ?? '14,328'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="about_stats_children_text">Children Text:</label>
                        <input type="text" class="form-control" id="about_stats_children_text" name="about_stats_children_text" value="<?php echo htmlspecialchars($currentSettings['about_stats_children_text'] ?? 'Children Supported'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="about_stats_staff_number">Staff Number:</label>
                        <input type="text" class="form-control" id="about_stats_staff_number" name="about_stats_staff_number" value="<?php echo htmlspecialchars($currentSettings['about_stats_staff_number'] ?? '15'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="about_stats_staff_text">Staff Text:</label>
                        <input type="text" class="form-control" id="about_stats_staff_text" name="about_stats_staff_text" value="<?php echo htmlspecialchars($currentSettings['about_stats_staff_text'] ?? 'Dedicated Staff & Volunteers'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="about_stats_regions_number">Regions Number:</label>
                        <input type="text" class="form-control" id="about_stats_regions_number" name="about_stats_regions_number" value="<?php echo htmlspecialchars($currentSettings['about_stats_regions_number'] ?? '4'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="about_stats_regions_text">Regions Text:</label>
                        <input type="text" class="form-control" id="about_stats_regions_text" name="about_stats_regions_text" value="<?php echo htmlspecialchars($currentSettings['about_stats_regions_text'] ?? 'Regions Impacted'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="about_stats_years_number">Years Number:</label>
                        <input type="text" class="form-control" id="about_stats_years_number" name="about_stats_years_number" value="<?php echo htmlspecialchars($currentSettings['about_stats_years_number'] ?? '17'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="about_stats_years_text">Years Text:</label>
                        <input type="text" class="form-control" id="about_stats_years_text" name="about_stats_years_text" value="<?php echo htmlspecialchars($currentSettings['about_stats_years_text'] ?? 'Years of Service'); ?>">
                    </div>
                </div>

                <!-- Hidden Fields for Values Section Titles (if made editable later) -->
                <!-- These are kept static in the view for now, but the settings exist -->
                <input type="hidden" name="about_values_title" value="<?php echo htmlspecialchars($currentSettings['about_values_title'] ?? 'The Principles That Guide Us'); ?>">
                <input type="hidden" name="about_values_intro" value="<?php echo htmlspecialchars($currentSettings['about_values_intro'] ?? 'Our core values ensure our actions are ethical, effective, and centered on the communities we serve.'); ?>">

                <!-- Submit Button -->
                <div class="form-group">
                    <button type="submit" name="save_about_content" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save Changes
                    </button>
                    <a href="../dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

        <?php // include '../includes/footer.php'; ?> <!-- Optional -->
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Character count for hero title
        $('#about_hero_title').on('input', function() {
            const length = $(this).val().length;
            $('#title-count').text(length);
            $('#preview-title').text($(this).val() || 'Our Journey of Hope');
        });

        // Image preview function
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                const preview = document.getElementById('about_hero_image_preview');
                const nameElement = document.getElementById('about_hero_image_name');
                const heroPreview = document.getElementById('hero-preview');
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    nameElement.textContent = input.files[0].name;
                    // Update hero preview background
                    heroPreview.style.backgroundImage = `url(${e.target.result})`;
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form validation for hero title
        $('form').on('submit', function(e) {
            const title = $('#about_hero_title').val().trim();
            
            if (title.length > 255) {
                e.preventDefault();
                alert('Hero Title must not exceed 255 characters.');
                $('#about_hero_title').focus();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>