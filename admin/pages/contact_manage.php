<?php
// admin/pages/contact_edit.php
require_once __DIR__ . '/../includes/init.php';
require_once BASE_PATH . '/application/models/SettingModel.php';

// --- Handle Form Submission (Including Hero Image Upload) ---
$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_contact_hero'])) {
    $settingModel = new SettingModel();

    // --- Handle Hero Image Upload ---
    $new_image_filename = null;
    if (isset($_FILES['contact_hero_image']) && $_FILES['contact_hero_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = BASE_PATH . '/public_html/images/';
        $uploadFile = $_FILES['contact_hero_image'];

        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Basic validation
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $message = "Invalid Contact Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.";
            $message_type = 'error';
             // --- Log the failed upload ---
            $logMessage = "Admin {$_SESSION['username']} failed to upload Contact hero image: Invalid file type at " . date('Y-m-d H:i:s');
            logAdminActivity($logMessage);
        } elseif ($uploadFile['size'] > $maxFileSize) {
            $message = "Contact Hero image file is too large. Maximum size is 5MB.";
            $message_type = 'error';
             // --- Log the failed upload ---
            $logMessage = "Admin {$_SESSION['username']} failed to upload Contact hero image: File too large at " . date('Y-m-d H:i:s');
            logAdminActivity($logMessage);
        } else {
            // Generate a unique filename
            $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
            $new_image_filename = 'contact_hero_' . uniqid() . '.' . strtolower($fileExtension);
            $targetPath = $uploadDir . $new_image_filename;

            if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                // Delete old image file if it exists and is not the default
                $oldImageFilename = $settingModel->get('contact_hero_image', 'bg_7.jpg');
                if ($oldImageFilename && $oldImageFilename !== 'bg_7.jpg' && file_exists($uploadDir . $oldImageFilename)) {
                    unlink($uploadDir . $oldImageFilename);
                }

                // Successfully uploaded hero image
                if ($settingModel->set('contact_hero_image', $new_image_filename)) {
                    $message = "Contact page hero image uploaded successfully!";
                    $message_type = 'success';
                     // --- Log the successful update ---
                    $logMessage = "Admin {$_SESSION['username']} uploaded new Contact hero image: $new_image_filename at " . date('Y-m-d H:i:s');
                    logAdminActivity($logMessage);
                } else {
                    // If DB update failed, delete the uploaded image
                    if (file_exists($targetPath)) {
                        unlink($targetPath);
                    }
                    $message = "Hero image uploaded, but failed to update database setting.";
                    $message_type = 'error';
                     // --- Log the failed update ---
                    $logMessage = "Admin {$_SESSION['username']} failed to update Contact hero image setting in DB after upload at " . date('Y-m-d H:i:s');
                    logAdminActivity($logMessage);
                }
            } else {
                $message = "Error uploading Contact hero image file.";
                $message_type = 'error';
                 // --- Log the failed upload ---
                $logMessage = "Admin {$_SESSION['username']} failed to upload Contact hero image file at " . date('Y-m-d H:i:s');
                logAdminActivity($logMessage);
            }
        }
    } else if (isset($_FILES['contact_hero_image']) && $_FILES['contact_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
         // An upload error occurred (but not "no file")
         $message = "Error uploading Contact hero image: " . $_FILES['contact_hero_image']['error'];
         $message_type = 'error';
         // --- Log the upload error ---
         $logMessage = "Admin {$_SESSION['username']} encountered upload error for Contact hero image: " . $_FILES['contact_hero_image']['error'] . " at " . date('Y-m-d H:i:s');
         logAdminActivity($logMessage);
    }
    // --- End Hero Image Upload ---

    // --- Handle Text Content Saving (only if no critical image upload error) ---
    if ($message_type !== 'error') {
        // Get text data from form
        $contact_hero_title = trim($_POST['contact_hero_title'] ?? '');
        $contact_hero_subtitle = trim($_POST['contact_hero_subtitle'] ?? '');

        // Basic validation
        $errors = [];
        if (empty($contact_hero_title)) {
            $errors[] = "Contact Hero Title is required.";
        }
        if (strlen($contact_hero_title) > 255) {
            $errors[] = "Contact Hero Title must not exceed 255 characters.";
        }
        if (strlen($contact_hero_subtitle) > 500) {
            $errors[] = "Contact Hero Subtitle must not exceed 500 characters.";
        }

        if (empty($errors)) {
            // Save text settings
            $save_success = true;
            $save_success &= $settingModel->set('contact_hero_title', $contact_hero_title);
            $save_success &= $settingModel->set('contact_hero_subtitle', $contact_hero_subtitle);

            if ($save_success) {
                if ($message_type !== 'success') { // Only set success message if image upload didn't already set one
                     $message = "Contact page hero content updated successfully!";
                     $message_type = 'success';
                } else {
                     // Append to existing success message
                     $message .= " Hero text content also updated successfully!";
                }
                // --- Log the successful update ---
                $logMessage = "Admin {$_SESSION['username']} updated Contact page hero text content at " . date('Y-m-d H:i:s');
                logAdminActivity($logMessage);
            } else {
                if ($message_type !== 'error') { // Only set error message if image upload didn't already set one
                    $message = "An error occurred while saving the hero text content. Please try again.";
                    $message_type = 'error';
                } else {
                    // Append to existing error message
                    $message .= " Additionally, failed to save hero text content.";
                }
                // --- Log the failed update ---
                $logMessage = "Admin {$_SESSION['username']} failed to update Contact page hero text content at " . date('Y-m-d H:i:s');
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
        }
    }
    // If there was an image upload error, the message is already set.
}

// --- Fetch Current Settings for Form Population ---
$settingModel = new SettingModel();
$contactContentKeys = [
    'contact_hero_image',
    'contact_hero_title',
    'contact_hero_subtitle'
];
$currentSettings = $settingModel->getMultiple($contactContentKeys, '');

// Set default values if settings don't exist
$currentSettings['contact_hero_image'] = $currentSettings['contact_hero_image'] ?: 'bg_7.jpg';
$currentSettings['contact_hero_title'] = $currentSettings['contact_hero_title'] ?: 'Get In Touch';
$currentSettings['contact_hero_subtitle'] = $currentSettings['contact_hero_subtitle'] ?: "We'd love to hear from you. Reach out with any questions or comments.";

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

$page_title = "Edit Contact Page Hero - Bantwana CMS";
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
        .character-count {
            font-size: 0.8em;
            color: #6c757d;
            float: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php // include '../includes/header.php'; ?> <!-- Optional -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Contact Page Hero</h2>
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
            <div class="hero-preview" id="hero-preview" style="background-image: url('<?php echo BASE_URL . '/images/' . htmlspecialchars($currentSettings['contact_hero_image']); ?>');">
                <div class="hero-preview-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent justify-content-center p-0 mb-3">
                            <li class="breadcrumb-item"><a href="#" class="text-white">Home</a></li>
                            <li class="breadcrumb-item text-white-50" aria-current="page">Contact</li>
                        </ol>
                    </nav>
                    <h1 class="mb-3" id="preview-title"><?php echo htmlspecialchars($currentSettings['contact_hero_title']); ?></h1>
                    <p class="mb-0" id="preview-subtitle"><?php echo htmlspecialchars($currentSettings['contact_hero_subtitle']); ?></p>
                </div>
            </div>
        </div>

        <div class="admin-container">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">

                <!-- Hero Section -->
                <div class="section-header">
                    <h4><i class="fas fa-image mr-2"></i>Contact Page Hero Section</h4>
                </div>
                
                <div class="form-group">
                    <label for="contact_hero_title">Hero Title *</label>
                    <div class="character-count">
                        <span id="title-count"><?php echo strlen($currentSettings['contact_hero_title']); ?></span>/255
                    </div>
                    <input type="text" class="form-control" id="contact_hero_title" name="contact_hero_title" 
                           value="<?php echo htmlspecialchars($currentSettings['contact_hero_title']); ?>" 
                           required maxlength="255">
                    <small class="form-text text-muted">Main heading displayed on the hero section.</small>
                </div>
                
                <div class="form-group">
                    <label for="contact_hero_subtitle">Hero Subtitle</label>
                    <div class="character-count">
                        <span id="subtitle-count"><?php echo strlen($currentSettings['contact_hero_subtitle']); ?></span>/500
                    </div>
                    <textarea class="form-control" id="contact_hero_subtitle" name="contact_hero_subtitle" 
                              rows="3" maxlength="500"><?php echo htmlspecialchars($currentSettings['contact_hero_subtitle']); ?></textarea>
                    <small class="form-text text-muted">Descriptive text displayed below the hero title.</small>
                </div>
                
                <div class="form-group">
                    <label for="contact_hero_image">Hero Background Image:</label>
                    <input type="file" class="form-control-file" id="contact_hero_image" name="contact_hero_image" 
                           accept="image/*" onchange="previewImage(this)">
                    <small class="form-text text-muted">Upload a new background image for the Contact page hero section (JPG, JPEG, PNG, GIF, WEBP, max 5MB).</small>
                    
                    <?php
                    $currentHeroImage = $currentSettings['contact_hero_image'];
                    $heroImagePath = BASE_URL . '/images/' . htmlspecialchars($currentHeroImage);
                    $serverHeroImagePath = BASE_PATH . '/public_html/images/' . $currentHeroImage;
                    if (file_exists($serverHeroImagePath) && !empty($currentHeroImage)):
                        echo "<p class='mt-2'><strong>Current Hero Image:</strong></p>";
                        echo "<img src='$heroImagePath' id='contact_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail'>";
                        echo "<p class='text-muted mt-1' id='contact_hero_image_name'>" . htmlspecialchars($currentHeroImage) . "</p>";
                    else:
                        echo "<p class='mt-2 text-muted' id='contact_hero_image_name'>Current image: " . htmlspecialchars($currentHeroImage) . " (file not found or default).</p>";
                        echo "<img src='' id='contact_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail d-none'>"; // Hidden if no valid image
                    endif;
                    ?>
                </div>

                <div class="form-group">
                    <button type="submit" name="save_contact_hero" class="btn btn-primary">
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

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Character count for title
        $('#contact_hero_title').on('input', function() {
            const length = $(this).val().length;
            $('#title-count').text(length);
            $('#preview-title').text($(this).val() || 'Get In Touch');
        });

        // Character count for subtitle
        $('#contact_hero_subtitle').on('input', function() {
            const length = $(this).val().length;
            $('#subtitle-count').text(length);
            $('#preview-subtitle').text($(this).val() || "We'd love to hear from you. Reach out with any questions or comments.");
        });

        // Image preview function
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                const preview = document.getElementById('contact_hero_image_preview');
                const nameElement = document.getElementById('contact_hero_image_name');
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

        // Form validation
        $('form').on('submit', function(e) {
            const title = $('#contact_hero_title').val().trim();
            const subtitle = $('#contact_hero_subtitle').val().trim();
            
            if (!title) {
                e.preventDefault();
                alert('Hero Title is required.');
                $('#contact_hero_title').focus();
                return false;
            }
            
            if (title.length > 255) {
                e.preventDefault();
                alert('Hero Title must not exceed 255 characters.');
                $('#contact_hero_title').focus();
                return false;
            }
            
            if (subtitle.length > 500) {
                e.preventDefault();
                alert('Hero Subtitle must not exceed 500 characters.');
                $('#contact_hero_subtitle').focus();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>