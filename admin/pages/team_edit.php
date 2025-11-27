<?php
// admin/pages/team_edit.php
require_once __DIR__ . '/../includes/init.php';

// --- Load Required Models ---
loadAdminClass('SettingModel');

// --- Handle Form Submission (Including Hero Image & Thulani Image Uploads) ---
$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_team_content'])) {
    $settingModel = new SettingModel();

    // --- Handle Hero Image Upload ---
    if (isset($_FILES['team_hero_image']) && $_FILES['team_hero_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = BASE_PATH . '/public_html/images/';
        $uploadFile = $_FILES['team_hero_image'];

        // Basic validation
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $message = "Invalid Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.";
            $message_type = 'error';
            // --- Log the failed upload ---
            $logMessage = "Admin {$_SESSION['username']} failed to upload Team Hero image: Invalid file type at " . date('Y-m-d H:i:s');
            logAdminActivity($logMessage);
        } elseif ($uploadFile['size'] > $maxFileSize) {
            $message = "Hero image file is too large. Maximum size is 5MB.";
            $message_type = 'error';
            // --- Log the failed upload ---
            $logMessage = "Admin {$_SESSION['username']} failed to upload Team Hero image: File too large at " . date('Y-m-d H:i:s');
            logAdminActivity($logMessage);
        } else {
            // Generate a unique filename
            $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
            $new_hero_filename = 'team_hero_' . uniqid() . '.' . strtolower($fileExtension);
            $targetPath = $uploadDir . $new_hero_filename;

            if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                // Successfully uploaded hero image
                if ($settingModel->set('team_hero_image', $new_hero_filename)) {
                    $message = "Team page hero image uploaded successfully!";
                    $message_type = 'success';
                    // --- Log the successful update ---
                    $logMessage = "Admin {$_SESSION['username']} uploaded new Team hero image: $new_hero_filename at " . date('Y-m-d H:i:s');
                    logAdminActivity($logMessage);
                } else {
                    // If DB update failed, delete the uploaded image
                    if (file_exists($uploadDir . $new_hero_filename)) {
                        unlink($uploadDir . $new_hero_filename);
                    }
                    $message = "Hero image uploaded, but failed to update database setting.";
                    $message_type = 'error';
                    // --- Log the failed update ---
                    $logMessage = "Admin {$_SESSION['username']} failed to update Team hero image setting in DB after upload at " . date('Y-m-d H:i:s');
                    logAdminActivity($logMessage);
                }
            } else {
                $message = "Error uploading Team hero image file.";
                $message_type = 'error';
                // --- Log the failed upload ---
                $logMessage = "Admin {$_SESSION['username']} failed to upload Team hero image file at " . date('Y-m-d H:i:s');
                logAdminActivity($logMessage);
            }
        }
    } else if (isset($_FILES['team_hero_image']) && $_FILES['team_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // An upload error occurred (but not "no file")
        $message = "Error uploading Team hero image: " . $_FILES['team_hero_image']['error'];
        $message_type = 'error';
        // --- Log the upload error ---
        $logMessage = "Admin {$_SESSION['username']} encountered upload error for Team hero image: " . $_FILES['team_hero_image']['error'] . " at " . date('Y-m-d H:i:s');
        logAdminActivity($logMessage);
    }
    // --- End Hero Image Upload ---

    // --- Handle Thulani Image Upload ---
    if ($message_type !== 'error' && isset($_FILES['team_thulani_image']) && $_FILES['team_thulani_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = BASE_PATH . '/public_html/images/';
        $uploadFile = $_FILES['team_thulani_image'];

        // Basic validation
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $message = "Invalid Thulani image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.";
            $message_type = 'error';
            // --- Log the failed upload ---
            $logMessage = "Admin {$_SESSION['username']} failed to upload Thulani image: Invalid file type at " . date('Y-m-d H:i:s');
            logAdminActivity($logMessage);
        } elseif ($uploadFile['size'] > $maxFileSize) {
            $message = "Thulani image file is too large. Maximum size is 5MB.";
            $message_type = 'error';
            // --- Log the failed upload ---
            $logMessage = "Admin {$_SESSION['username']} failed to upload Thulani image: File too large at " . date('Y-m-d H:i:s');
            logAdminActivity($logMessage);
        } else {
            // Generate a unique filename
            $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
            $new_thulani_filename = 'thulani_' . uniqid() . '.' . strtolower($fileExtension);
            $targetPath = $uploadDir . $new_thulani_filename;

            if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                // Successfully uploaded Thulani image
                if ($settingModel->set('team_thulani_image', $new_thulani_filename)) {
                    if ($message_type === 'success') {
                        $message .= " Thulani image uploaded successfully!";
                    } else {
                        $message = "Thulani image uploaded successfully!";
                        $message_type = 'success';
                    }
                    // --- Log the successful update ---
                    $logMessage = "Admin {$_SESSION['username']} uploaded new Thulani image: $new_thulani_filename at " . date('Y-m-d H:i:s');
                    logAdminActivity($logMessage);
                } else {
                    // If DB update failed, delete the uploaded image
                    if (file_exists($uploadDir . $new_thulani_filename)) {
                        unlink($uploadDir . $new_thulani_filename);
                    }
                    $message = "Thulani image uploaded, but failed to update database setting.";
                    $message_type = 'error';
                    // --- Log the failed update ---
                    $logMessage = "Admin {$_SESSION['username']} failed to update Thulani image setting in DB after upload at " . date('Y-m-d H:i:s');
                    logAdminActivity($logMessage);
                }
            } else {
                $message = "Error uploading Thulani image file.";
                $message_type = 'error';
                // --- Log the failed upload ---
                $logMessage = "Admin {$_SESSION['username']} failed to upload Thulani image file at " . date('Y-m-d H:i:s');
                logAdminActivity($logMessage);
            }
        }
    } else if ($message_type !== 'error' && isset($_FILES['team_thulani_image']) && $_FILES['team_thulani_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // An upload error occurred for Thulani image (but not "no file")
        $message_temp = "Error uploading Thulani image: " . $_FILES['team_thulani_image']['error'];
        if ($message_type !== 'error') {
            $message = $message_temp;
            $message_type = 'error';
        } else {
            $message .= " " . $message_temp;
        }
        // --- Log the upload error ---
        $logMessage = "Admin {$_SESSION['username']} encountered upload error for Thulani image: " . $_FILES['team_thulani_image']['error'] . " at " . date('Y-m-d H:i:s');
        logAdminActivity($logMessage);
    }
    // --- End Thulani Image Upload ---

    // --- Handle Text Content Saving (only if no critical image upload error) ---
    if ($message_type !== 'error') {
        // Get text data from form
        $team_hero_title = trim($_POST['team_hero_title'] ?? '');
        $team_hero_subtitle = trim($_POST['team_hero_subtitle'] ?? '');

        // Basic validation
        $errors = [];
        if (empty($team_hero_title)) {
            $errors[] = "Hero Title is required.";
        }

        if (empty($errors)) {
            // Save text settings
            $save_success = true;
            $save_success &= $settingModel->set('team_hero_title', $team_hero_title);
            $save_success &= $settingModel->set('team_hero_subtitle', $team_hero_subtitle);

            if ($save_success) {
                if ($message_type !== 'success') { // Only set success message if image upload didn't already set one
                    $message = "Team page content updated successfully!";
                    $message_type = 'success';
                }
                // --- Log the successful update ---
                $logMessage = "Admin {$_SESSION['username']} updated Team page text content at " . date('Y-m-d H:i:s');
                logAdminActivity($logMessage);
            } else {
                // If saving failed but images were uploaded, delete the new images
                if (isset($new_hero_filename) && file_exists($uploadDir . $new_hero_filename)) {
                    unlink($uploadDir . $new_hero_filename);
                }
                if (isset($new_thulani_filename) && file_exists($uploadDir . $new_thulani_filename)) {
                    unlink($uploadDir . $new_thulani_filename);
                }
                if ($message_type !== 'error') { // Only set error message if image upload didn't already set one
                    $message = "An error occurred while saving the text content. Please try again.";
                    $message_type = 'error';
                }
                // --- Log the failed update ---
                $logMessage = "Admin {$_SESSION['username']} failed to update Team page text content at " . date('Y-m-d H:i:s');
                logAdminActivity($logMessage);
            }
        } else {
            // If validation failed but images were uploaded, delete the new images
            if (isset($new_hero_filename) && file_exists($uploadDir . $new_hero_filename)) {
                unlink($uploadDir . $new_hero_filename);
            }
            if (isset($new_thulani_filename) && file_exists($uploadDir . $new_thulani_filename)) {
                unlink($uploadDir . $new_thulani_filename);
            }
            if ($message_type !== 'error') { // Only set error message if image upload didn't already set one
                $message = implode('<br>', $errors);
                $message_type = 'error';
            }
        }
    }
    // If there was an image upload error, the message is already set.
}

// --- Fetch Current Settings for Form Population ---
$settingModel = new SettingModel();
$teamContentKeys = [
    'team_hero_image',
    'team_hero_title',
    'team_hero_subtitle',
    'team_thulani_image'
];
$currentSettings = $settingModel->getMultiple($teamContentKeys, '');

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

$page_title = "Edit Team Page Content - Bantwana CMS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
        .character-count { font-size: 0.9em; color: #6c757d; }
        .character-count.warning { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <?php // include '../includes/header.php'; ?> <!-- Optional -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Team Page Content</h2>
            <a href="../dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
        </div>

        <!-- Display Message -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="admin-container">
            <!-- Hero Section Preview -->
            <h4><i class="fas fa-eye mr-2"></i>Hero Section Preview</h4>
            <div class="hero-preview" id="hero-preview" style="background-image: url('<?php echo BASE_URL . '/images/' . htmlspecialchars($currentSettings['team_hero_image'] ?? 'bg_team.jpg'); ?>');">
                <div class="hero-preview-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent justify-content-center p-0 mb-3">
                            <li class="breadcrumb-item text-white" aria-current="page">Team</li>
                        </ol>
                    </nav>
                    <h1 id="hero-title-preview"><?php echo htmlspecialchars($currentSettings['team_hero_title'] ?? 'Our Team'); ?></h1>
                    <p id="hero-subtitle-preview"><?php echo htmlspecialchars($currentSettings['team_hero_subtitle'] ?? 'Meet the dedicated individuals driving our mission.'); ?></p>
                </div>
            </div>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                <!-- Hero Section -->
                <div class="section-header">
                    <h4>Team Page Hero Section</h4>
                </div>
                <!-- Hero Image Upload -->
                <div class="form-group">
                    <label for="team_hero_image">Hero Background Image:</label>
                    <input type="file" class="form-control-file" id="team_hero_image" name="team_hero_image" accept="image/*" onchange="previewImage(this)">
                    <small class="form-text text-muted">Upload a new background image for the Team page hero section (JPG, JPEG, PNG, GIF, WEBP, max 5MB).</small>
                    <?php
                    $currentHeroImage = $currentSettings['team_hero_image'] ?? 'bg_team.jpg';
                    $heroImagePath = BASE_URL . '/images/' . htmlspecialchars($currentHeroImage);
                    $serverHeroImagePath = BASE_PATH . '/public_html/images/' . $currentHeroImage;
                    if (file_exists($serverHeroImagePath) && !empty($currentHeroImage)):
                        echo "<p class='mt-2'><strong>Current Hero Image:</strong></p>";
                        echo "<img src='$heroImagePath' id='team_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail'>";
                        echo "<p class='text-muted mt-1' id='team_hero_image_name'>" . htmlspecialchars($currentHeroImage) . "</p>";
                    else:
                        echo "<p class='mt-2 text-muted' id='team_hero_image_name'>Current hero image: '" . htmlspecialchars($currentHeroImage) . "' (file not found or default).</p>";
                        echo "<img src='' id='team_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail d-none'>";
                    endif;
                    ?>
                </div>
                <!-- Hero Text Fields -->
                <div class="form-group">
                    <label for="team_hero_title">Hero Title * <span class="character-count" id="title-char-count">0/100</span></label>
                    <input type="text" class="form-control" id="team_hero_title" name="team_hero_title" value="<?php echo htmlspecialchars($currentSettings['team_hero_title'] ?? 'Our Team'); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="team_hero_subtitle">Hero Subtitle <span class="character-count" id="subtitle-char-count">0/200</span></label>
                    <textarea class="form-control" id="team_hero_subtitle" name="team_hero_subtitle" rows="2" maxlength="200"><?php echo htmlspecialchars($currentSettings['team_hero_subtitle'] ?? 'Meet the dedicated individuals driving our mission.'); ?></textarea>
                </div>

                <!-- Thulani Earnshaw Section -->
                <div class="section-header mt-5">
                    <h4>Country Director: Thulani Earnshaw</h4>
                </div>
                <!-- Thulani Image Upload -->
                <div class="form-group">
                    <label for="team_thulani_image">Thulani's Image:</label>
                    <input type="file" class="form-control-file" id="team_thulani_image" name="team_thulani_image" accept="image/*">
                    <small class="form-text text-muted">Upload a new image for Thulani Earnshaw (JPG, JPEG, PNG, GIF, WEBP, max 5MB).</small>
                    <?php
                    $currentThulaniImage = $currentSettings['team_thulani_image'] ?? 'thulani_earnshaw.jpg';
                    $thulaniImagePath = BASE_URL . '/images/' . htmlspecialchars($currentThulaniImage);
                    $serverThulaniImagePath = BASE_PATH . '/public_html/images/' . $currentThulaniImage;
                    if (file_exists($serverThulaniImagePath) && !empty($currentThulaniImage)):
                        echo "<p class='mt-2'><strong>Current Thulani Image:</strong></p>";
                        echo "<img src='$thulaniImagePath' alt='Current Thulani Image' class='current-image-preview img-thumbnail'>";
                        echo "<p class='text-muted mt-1'>" . htmlspecialchars($currentThulaniImage) . "</p>";
                    else:
                        echo "<p class='mt-2 text-muted'>Current Thulani image: '" . htmlspecialchars($currentThulaniImage) . "' (file not found or default).</p>";
                    endif;
                    ?>
                </div>
                <!-- Note: Content for Thulani is hardcoded in the public view for now -->

                <button type="submit" name="save_team_content" class="btn btn-primary mt-4"><i class="fas fa-save mr-1"></i> Save Changes</button>
                <a href="../dashboard.php" class="btn btn-secondary mt-4">Cancel</a>
            </form>
        </div>

        <?php // include '../includes/footer.php'; ?> <!-- Optional -->
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Character count for hero title and subtitle
            function updateCharCount(input, counter, maxLength) {
                const length = input.val().length;
                counter.text(`${length}/${maxLength}`);
                if (length > maxLength) {
                    counter.addClass('warning');
                    input[0].setCustomValidity('Input exceeds maximum length.');
                } else {
                    counter.removeClass('warning');
                    input[0].setCustomValidity('');
                }
            }

            const $titleInput = $('#team_hero_title');
            const $subtitleInput = $('#team_hero_subtitle');
            updateCharCount($titleInput, $('#title-char-count'), 100);
            updateCharCount($subtitleInput, $('#subtitle-char-count'), 200);

            $titleInput.on('input', function() {
                updateCharCount($(this), $('#title-char-count'), 100);
                $('#hero-title-preview').text($(this).val() || 'Our Team');
            });

            $subtitleInput.on('input', function() {
                updateCharCount($(this), $('#subtitle-char-count'), 200);
                $('#hero-subtitle-preview').text($(this).val() || 'Meet the dedicated individuals driving our mission.');
            });

            // Real-time image preview
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    const preview = $('#team_hero_image_preview');
                    const nameElement = $('#team_hero_image_name');
                    const heroPreview = $('#hero-preview');

                    reader.onload = function(e) {
                        preview.attr('src', e.target.result).removeClass('d-none');
                        nameElement.text(input.files[0].name);
                        heroPreview.css('background-image', `url(${e.target.result})`);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('#team_hero_image').change(function() {
                previewImage(this);
            });
        });
    </script>
</body>
</html>