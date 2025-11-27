<?php
// admin/pages/home_edit.php
require_once __DIR__ . '/../includes/init.php';
require_once BASE_PATH . '/application/models/SettingModel.php';

// --- Handle Form Submission (Including Hero Image Upload) ---
$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_home_content'])) {
    $settingModel = new SettingModel();

    // --- Handle Hero Image Upload ---
    $new_image_filename = null;
    if (isset($_FILES['home_hero_image']) && $_FILES['home_hero_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = BASE_PATH . '/public_html/images/';
        $uploadFile = $_FILES['home_hero_image'];

        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Basic validation
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $message = "Invalid Home Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.";
            $message_type = 'error';
            // --- Log the failed upload ---
            $logMessage = "Admin {$_SESSION['username']} failed to upload Home hero image: Invalid file type at " . date('Y-m-d H:i:s');
            logAdminActivity($logMessage);
        } elseif ($uploadFile['size'] > $maxFileSize) {
            $message = "Home Hero image file is too large. Maximum size is 5MB.";
            $message_type = 'error';
            // --- Log the failed upload ---
            $logMessage = "Admin {$_SESSION['username']} failed to upload Home hero image: File too large at " . date('Y-m-d H:i:s');
            logAdminActivity($logMessage);
        } else {
            // Generate a unique filename
            $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
            $new_image_filename = 'home_hero_' . uniqid() . '.' . strtolower($fileExtension);
            $targetPath = $uploadDir . $new_image_filename;

            if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                // Delete old image file if it exists and is not the default
                $oldImageFilename = $settingModel->get('home_hero_image', 'bg_7.jpg');
                if ($oldImageFilename && $oldImageFilename !== 'bg_7.jpg' && file_exists($uploadDir . $oldImageFilename)) {
                    unlink($uploadDir . $oldImageFilename);
                }

                // Successfully uploaded hero image
                if ($settingModel->set('home_hero_image', $new_image_filename)) {
                    $message = "Home page hero image uploaded successfully!";
                    $message_type = 'success';
                    // --- Log the successful update ---
                    $logMessage = "Admin {$_SESSION['username']} uploaded new Home hero image: $new_image_filename at " . date('Y-m-d H:i:s');
                    logAdminActivity($logMessage);
                } else {
                    // If DB update failed, delete the uploaded image
                    if (file_exists($targetPath)) {
                        unlink($targetPath);
                    }
                    $message = "Hero image uploaded, but failed to update database setting.";
                    $message_type = 'error';
                    // --- Log the failed update ---
                    $logMessage = "Admin {$_SESSION['username']} failed to update Home hero image setting in DB after upload at " . date('Y-m-d H:i:s');
                    logAdminActivity($logMessage);
                }
            } else {
                $message = "Error uploading Home hero image file.";
                $message_type = 'error';
                // --- Log the failed upload ---
                $logMessage = "Admin {$_SESSION['username']} failed to upload Home hero image file at " . date('Y-m-d H:i:s');
                logAdminActivity($logMessage);
            }
        }
    } else if (isset($_FILES['home_hero_image']) && $_FILES['home_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
         // An upload error occurred (but not "no file")
         $message = "Error uploading Home hero image: " . $_FILES['home_hero_image']['error'];
         $message_type = 'error';
         // --- Log the upload error ---
         $logMessage = "Admin {$_SESSION['username']} encountered upload error for Home hero image: " . $_FILES['home_hero_image']['error'] . " at " . date('Y-m-d H:i:s');
         logAdminActivity($logMessage);
    }
    // --- End Hero Image Upload ---

    // --- Handle Text Content Saving (only if no critical image upload error) ---
    if ($message_type !== 'error') {
        // Get text data from form
        $home_hero_title = trim($_POST['home_hero_title'] ?? '');
        $home_hero_subtitle = trim($_POST['home_hero_subtitle'] ?? '');
        $home_counter_main_text = trim($_POST['home_counter_main_text'] ?? '');
        $home_counter_number = trim($_POST['home_counter_number'] ?? '');
        $home_counter_unit = trim($_POST['home_counter_unit'] ?? '');
        $home_counter_donate_title = trim($_POST['home_counter_donate_title'] ?? '');
        $home_counter_donate_text = trim($_POST['home_counter_donate_text'] ?? '');
        $home_counter_volunteer_title = trim($_POST['home_counter_volunteer_title'] ?? '');
        $home_counter_volunteer_text = trim($_POST['home_counter_volunteer_text'] ?? '');

        // Basic validation
        $errors = [];
        if (empty($home_hero_title)) {
            $errors[] = "Home Hero Title is required.";
        }
        if (strlen($home_hero_title) > 255) {
            $errors[] = "Home Hero Title must not exceed 255 characters.";
        }
        if (strlen($home_hero_subtitle) > 500) {
            $errors[] = "Home Hero Subtitle must not exceed 500 characters.";
        }
        if (empty($home_counter_number)) {
            $errors[] = "Counter Number is required.";
        }
        if (strlen($home_counter_main_text) > 100) {
            $errors[] = "Counter Main Text must not exceed 100 characters.";
        }
        if (strlen($home_counter_unit) > 100) {
            $errors[] = "Counter Unit Text must not exceed 100 characters.";
        }
        if (strlen($home_counter_donate_title) > 100) {
            $errors[] = "Donate Box Title must not exceed 100 characters.";
        }
        if (strlen($home_counter_donate_text) > 255) {
            $errors[] = "Donate Box Text must not exceed 255 characters.";
        }
        if (strlen($home_counter_volunteer_title) > 100) {
            $errors[] = "Volunteer Box Title must not exceed 100 characters.";
        }
        if (strlen($home_counter_volunteer_text) > 255) {
            $errors[] = "Volunteer Box Text must not exceed 255 characters.";
        }

        if (empty($errors)) {
            // Save text settings
            $save_success = true;
            $save_success &= $settingModel->set('home_hero_title', $home_hero_title);
            $save_success &= $settingModel->set('home_hero_subtitle', $home_hero_subtitle);
            $save_success &= $settingModel->set('home_counter_main_text', $home_counter_main_text);
            $save_success &= $settingModel->set('home_counter_number', $home_counter_number);
            $save_success &= $settingModel->set('home_counter_unit', $home_counter_unit);
            $save_success &= $settingModel->set('home_counter_donate_title', $home_counter_donate_title);
            $save_success &= $settingModel->set('home_counter_donate_text', $home_counter_donate_text);
            $save_success &= $settingModel->set('home_counter_volunteer_title', $home_counter_volunteer_title);
            $save_success &= $settingModel->set('home_counter_volunteer_text', $home_counter_volunteer_text);

            if ($save_success) {
                if ($message_type !== 'success') { // Only set success message if image upload didn't already set one
                     $message = "Home page content updated successfully!";
                     $message_type = 'success';
                } else {
                     // Append to existing success message
                     $message .= " Hero and counter content also updated successfully!";
                }
                // --- Log the successful update ---
                $logMessage = "Admin {$_SESSION['username']} updated Home page hero and counter content at " . date('Y-m-d H:i:s');
                logAdminActivity($logMessage);
            } else {
                if ($message_type !== 'error') { // Only set error message if image upload didn't already set one
                    $message = "An error occurred while saving the content. Please try again.";
                    $message_type = 'error';
                } else {
                    // Append to existing error message
                    $message .= " Additionally, failed to save hero or counter content.";
                }
                // --- Log the failed update ---
                $logMessage = "Admin {$_SESSION['username']} failed to update Home page hero or counter content at " . date('Y-m-d H:i:s');
                logAdminActivity($logMessage);
            }
        } else {
            if ($message_type !== 'error') { // Only set error message if image upload didn't already set one
                $message = implode('<br>', $errors);
                $message_type = 'error';
            } else {
                // Append validation errors to existing error message
                $message .= " Additionally: " . implode(', ', $errors);
            }
            // --- Log the validation errors ---
            $logMessage = "Admin {$_SESSION['username']} failed Home content validation: " . implode(', ', $errors) . " at " . date('Y-m-d H:i:s');
            logAdminActivity($logMessage);
        }
    }
    // If there was an image upload error, the message is already set.
}

// --- Fetch Current Settings for Form Population ---
$settingModel = new SettingModel();
$homeContentKeys = [
    'home_hero_image',
    'home_hero_title',
    'home_hero_subtitle',
    'home_counter_main_text',
    'home_counter_number',
    'home_counter_unit',
    'home_counter_donate_title',
    'home_counter_donate_text',
    'home_counter_volunteer_title',
    'home_counter_volunteer_text'
];
$currentSettings = $settingModel->getMultiple($homeContentKeys, '');

// Set default values if settings don't exist
$currentSettings['home_hero_image'] = $currentSettings['home_hero_image'] ?: 'bg_7.jpg';
$currentSettings['home_hero_title'] = $currentSettings['home_hero_title'] ?: 'Bantwana Initiative Eswatini';
$currentSettings['home_hero_subtitle'] = $currentSettings['home_hero_subtitle'] ?: 'To enhance the well-being and resilience of vulnerable children, youth, and their families affected by HIV & AIDS and poverty through holistic care, protection, and empowerment.';
$currentSettings['home_counter_main_text'] = $currentSettings['home_counter_main_text'] ?: 'Served Over';
$currentSettings['home_counter_number'] = $currentSettings['home_counter_number'] ?: '0';
$currentSettings['home_counter_unit'] = $currentSettings['home_counter_unit'] ?: 'Children in 4 countries in Africa';
$currentSettings['home_counter_donate_title'] = $currentSettings['home_counter_donate_title'] ?: 'Support Our Work';
$currentSettings['home_counter_donate_text'] = $currentSettings['home_counter_donate_text'] ?: 'Your contribution makes a direct impact. Help us continue our vital programs.';
$currentSettings['home_counter_volunteer_title'] = $currentSettings['home_counter_volunteer_title'] ?: 'Be a Volunteer';
$currentSettings['home_counter_volunteer_text'] = $currentSettings['home_counter_volunteer_text'] ?: 'Give your time and skills to make a difference in our communities.';

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

$page_title = "Edit Home Page Content - Bantwana CMS";
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
        .counter-preview {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        .counter-box {
            flex: 1;
            min-width: 200px;
            text-align: center;
            padding: 10px;
        }
        .character-count {
            font-size: 0.8em;
            color: #6c757d;
            float: right;
        }
        .future-feature {
            background-color: #f8f9fa;
            border: 1px dashed #6c757d;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php // include '../includes/header.php'; ?> <!-- Optional -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Home Page Content</h2>
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
            <div class="hero-preview" id="hero-preview" style="background-image: url('<?php echo BASE_URL . '/images/' . htmlspecialchars($currentSettings['home_hero_image']); ?>');">
                <div class="hero-preview-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent justify-content-center p-0 mb-3">
                            <li class="breadcrumb-item text-white" aria-current="page">Home</li>
                        </ol>
                    </nav>
                    <h1 class="mb-3" id="preview-title"><?php echo htmlspecialchars($currentSettings['home_hero_title']); ?></h1>
                    <p class="mb-0" id="preview-subtitle"><?php echo htmlspecialchars($currentSettings['home_hero_subtitle']); ?></p>
                </div>
            </div>
        </div>

        <!-- Counter Preview -->
        <div class="admin-container">
            <h4><i class="fas fa-eye mr-2"></i>Counter Section Preview</h4>
            <div class="counter-preview">
                <div class="counter-box">
                    <p id="preview-counter-main"><?php echo htmlspecialchars($currentSettings['home_counter_main_text']); ?></p>
                    <h3 id="preview-counter-number"><?php echo htmlspecialchars($currentSettings['home_counter_number']); ?></h3>
                    <p id="preview-counter-unit"><?php echo htmlspecialchars($currentSettings['home_counter_unit']); ?></p>
                </div>
                <div class="counter-box">
                    <h4 id="preview-donate-title"><?php echo htmlspecialchars($currentSettings['home_counter_donate_title']); ?></h4>
                    <p id="preview-donate-text"><?php echo htmlspecialchars($currentSettings['home_counter_donate_text']); ?></p>
                </div>
                <div class="counter-box">
                    <h4 id="preview-volunteer-title"><?php echo htmlspecialchars($currentSettings['home_counter_volunteer_title']); ?></h4>
                    <p id="preview-volunteer-text"><?php echo htmlspecialchars($currentSettings['home_counter_volunteer_text']); ?></p>
                </div>
            </div>
        </div>

        <div class="admin-container">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">

                <!-- Hero Section -->
                <div class="section-header">
                    <h4><i class="fas fa-home mr-2"></i>Home Page Hero Section</h4>
                </div>
                
                <div class="form-group">
                    <label for="home_hero_title">Hero Title *</label>
                    <div class="character-count">
                        <span id="title-count"><?php echo strlen($currentSettings['home_hero_title']); ?></span>/255
                    </div>
                    <input type="text" class="form-control" id="home_hero_title" name="home_hero_title" 
                           value="<?php echo htmlspecialchars($currentSettings['home_hero_title']); ?>" 
                           required maxlength="255">
                    <small class="form-text text-muted">Main heading displayed on the home page hero section.</small>
                </div>
                
                <div class="form-group">
                    <label for="home_hero_subtitle">Hero Subtitle</label>
                    <div class="character-count">
                        <span id="subtitle-count"><?php echo strlen($currentSettings['home_hero_subtitle']); ?></span>/500
                    </div>
                    <textarea class="form-control" id="home_hero_subtitle" name="home_hero_subtitle" 
                              rows="3" maxlength="500"><?php echo htmlspecialchars($currentSettings['home_hero_subtitle']); ?></textarea>
                    <small class="form-text text-muted">Descriptive text displayed below the hero title.</small>
                </div>
                
                <div class="form-group">
                    <label for="home_hero_image">Hero Background Image</label>
                    <input type="file" class="form-control-file" id="home_hero_image" name="home_hero_image" 
                           accept="image/*" onchange="previewImage(this)">
                    <small class="form-text text-muted">Upload a new background image for the Home page hero section (JPG, JPEG, PNG, GIF, WEBP, max 5MB).</small>
                    
                    <?php
                    $currentHeroImage = $currentSettings['home_hero_image'];
                    $heroImagePath = BASE_URL . '/images/' . htmlspecialchars($currentHeroImage);
                    $serverHeroImagePath = BASE_PATH . '/public_html/images/' . $currentHeroImage;
                    if (file_exists($serverHeroImagePath) && !empty($currentHeroImage)):
                        echo "<p class='mt-2'><strong>Current Hero Image:</strong></p>";
                        echo "<img src='$heroImagePath' id='home_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail'>";
                        echo "<p class='text-muted mt-1' id='home_hero_image_name'>" . htmlspecialchars($currentHeroImage) . "</p>";
                    else:
                        echo "<p class='mt-2 text-muted' id='home_hero_image_name'>Current image: " . htmlspecialchars($currentHeroImage) . " (file not found or default).</p>";
                        echo "<img src='' id='home_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail d-none'>"; // Hidden if no valid image
                    endif;
                    ?>
                </div>

                <!-- Counter Section -->
                <div class="section-header">
                    <h4><i class="fas fa-chart-bar mr-2"></i>Counter Section</h4>
                </div>
                
                <div class="form-group">
                    <label for="home_counter_main_text">Counter Main Text</label>
                    <div class="character-count">
                        <span id="counter-main-count"><?php echo strlen($currentSettings['home_counter_main_text']); ?></span>/100
                    </div>
                    <input type="text" class="form-control" id="home_counter_main_text" name="home_counter_main_text" 
                           value="<?php echo htmlspecialchars($currentSettings['home_counter_main_text']); ?>" 
                           maxlength="100">
                    <small class="form-text text-muted">Text above the counter number (e.g., "Served Over").</small>
                </div>
                
                <div class="form-group">
                    <label for="home_counter_number">Counter Number *</label>
                    <input type="text" class="form-control" id="home_counter_number" name="home_counter_number" 
                           value="<?php echo htmlspecialchars($currentSettings['home_counter_number']); ?>" 
                           required>
                    <small class="form-text text-muted">Number displayed in the counter (e.g., "14,328"). Use commas for thousands separator if needed.</small>
                </div>
                
                <div class="form-group">
                    <label for="home_counter_unit">Counter Unit Text</label>
                    <div class="character-count">
                        <span id="counter-unit-count"><?php echo strlen($currentSettings['home_counter_unit']); ?></span>/100
                    </div>
                    <input type="text" class="form-control" id="home_counter_unit" name="home_counter_unit" 
                           value="<?php echo htmlspecialchars($currentSettings['home_counter_unit']); ?>" 
                           maxlength="100">
                    <small class="form-text text-muted">Text below the counter number (e.g., "Children in 4 countries in Africa").</small>
                </div>
                
                <div class="form-group">
                    <label for="home_counter_donate_title">Donate Box Title</label>
                    <div class="character-count">
                        <span id="donate-title-count"><?php echo strlen($currentSettings['home_counter_donate_title']); ?></span>/100
                    </div>
                    <input type="text" class="form-control" id="home_counter_donate_title" name="home_counter_donate_title" 
                           value="<?php echo htmlspecialchars($currentSettings['home_counter_donate_title']); ?>" 
                           maxlength="100">
                    <small class="form-text text-muted">Title for the donate box (e.g., "Support Our Work").</small>
                </div>
                
                <div class="form-group">
                    <label for="home_counter_donate_text">Donate Box Text</label>
                    <div class="character-count">
                        <span id="donate-text-count"><?php echo strlen($currentSettings['home_counter_donate_text']); ?></span>/255
                    </div>
                    <textarea class="form-control" id="home_counter_donate_text" name="home_counter_donate_text" 
                              rows="2" maxlength="255"><?php echo htmlspecialchars($currentSettings['home_counter_donate_text']); ?></textarea>
                    <small class="form-text text-muted">Descriptive text for the donate box.</small>
                </div>
                
                <div class="form-group">
                    <label for="home_counter_volunteer_title">Volunteer Box Title</label>
                    <div class="character-count">
                        <span id="volunteer-title-count"><?php echo strlen($currentSettings['home_counter_volunteer_title']); ?></span>/100
                    </div>
                    <input type="text" class="form-control" id="home_counter_volunteer_title" name="home_counter_volunteer_title" 
                           value="<?php echo htmlspecialchars($currentSettings['home_counter_volunteer_title']); ?>" 
                           maxlength="100">
                    <small class="form-text text-muted">Title for the volunteer box (e.g., "Be a Volunteer").</small>
                </div>
                
                <div class="form-group">
                    <label for="home_counter_volunteer_text">Volunteer Box Text</label>
                    <div class="character-count">
                        <span id="volunteer-text-count"><?php echo strlen($currentSettings['home_counter_volunteer_text']); ?></span>/255
                    </div>
                    <textarea class="form-control" id="home_counter_volunteer_text" name="home_counter_volunteer_text" 
                              rows="2" maxlength="255"><?php echo htmlspecialchars($currentSettings['home_counter_volunteer_text']); ?></textarea>
                    <small class="form-text text-muted">Descriptive text for the volunteer box.</small>
                </div>

                <div class="form-group">
                    <button type="submit" name="save_home_content" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save Changes
                    </button>
                    <a href="../dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Bootstrap JS and dependencies -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
            // Character count for hero title
            $('#home_hero_title').on('input', function() {
                const length = $(this).val().length;
                $('#title-count').text(length);
                $('#preview-title').text($(this).val() || 'Bantwana Initiative Eswatini');
            });

            // Character count for hero subtitle
            $('#home_hero_subtitle').on('input', function() {
                const length = $(this).val().length;
                $('#subtitle-count').text(length);
                $('#preview-subtitle').text($(this).val() || 'To enhance the well-being and resilience of vulnerable children, youth, and their families affected by HIV & AIDS and poverty through holistic care, protection, and empowerment.');
            });

            // Character count for counter main text
            $('#home_counter_main_text').on('input', function() {
                const length = $(this).val().length;
                $('#counter-main-count').text(length);
                $('#preview-counter-main').text($(this).val() || 'Served Over');
            });

            // Update counter number preview
            $('#home_counter_number').on('input', function() {
                $('#preview-counter-number').text($(this).val() || '0');
            });

            // Character count for counter unit
            $('#home_counter_unit').on('input', function() {
                const length = $(this).val().length;
                $('#counter-unit-count').text(length);
                $('#preview-counter-unit').text($(this).val() || 'Children in 4 countries in Africa');
            });

            // Character count for donate title
            $('#home_counter_donate_title').on('input', function() {
                const length = $(this).val().length;
                $('#donate-title-count').text(length);
                $('#preview-donate-title').text($(this).val() || 'Support Our Work');
            });

            // Character count for donate text
            $('#home_counter_donate_text').on('input', function() {
                const length = $(this).val().length;
                $('#donate-text-count').text(length);
                $('#preview-donate-text').text($(this).val() || 'Your contribution makes a direct impact. Help us continue our vital programs.');
            });

            // Character count for volunteer