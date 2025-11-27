<?php
// admin/pages/internship_edit.php
require_once __DIR__ . '/../includes/init.php';

// --- Load Required Models ---
loadAdminClass('InternshipModel');

// --- Handle Form Submission ---
$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_internships_hero'])) {
    $internshipModel = new InternshipModel();

    // --- Handle Hero Image Upload ---
    $new_image_filename = null;
    if (isset($_FILES['internships_hero_image']) && $_FILES['internships_hero_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = BASE_PATH . '/public_html/images/';
        $uploadFile = $_FILES['internships_hero_image'];

        // Basic validation
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $message = "Invalid Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.";
            $message_type = 'error';
            logAdminActivity("Admin {$_SESSION['username']} failed to upload Internships hero image: Invalid file type.");
        } elseif ($uploadFile['size'] > $maxFileSize) {
            $message = "Hero image file is too large. Maximum size is 5MB.";
            $message_type = 'error';
            logAdminActivity("Admin {$_SESSION['username']} failed to upload Internships hero image: File too large.");
        } else {
            $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
            $new_image_filename = 'internships_hero_' . uniqid() . '.' . strtolower($fileExtension);
            $targetPath = $uploadDir . $new_image_filename;

            if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                // Successfully uploaded hero image
                if ($internshipModel->setHeroSetting('internships_hero_image', $new_image_filename)) {
                    $message = "Internships page hero image uploaded successfully!";
                    $message_type = 'success';
                    logAdminActivity("Admin {$_SESSION['username']} uploaded new Internships hero image: $new_image_filename");
                } else {
                    // If DB update failed, delete the uploaded image
                    if (file_exists($uploadDir . $new_image_filename)) {
                        unlink($uploadDir . $new_image_filename);
                    }
                    $message = "Hero image uploaded, but failed to update database setting.";
                    $message_type = 'error';
                    logAdminActivity("Admin {$_SESSION['username']} failed to update Internships hero image setting after upload.");
                }
            } else {
                $message = "Error uploading hero image file.";
                $message_type = 'error';
                logAdminActivity("Admin {$_SESSION['username']} failed to move Internships hero image file.");
            }
        }
    } else if (isset($_FILES['internships_hero_image']) && $_FILES['internships_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
         // An upload error occurred (but not "no file")
         $message = "Error uploading hero image: " . $_FILES['internships_hero_image']['error'];
         $message_type = 'error';
         logAdminActivity("Admin {$_SESSION['username']} encountered upload error for Internships hero image: " . $_FILES['internships_hero_image']['error']);
    }
    // --- End Hero Image Upload ---

    // --- Handle Text Content Saving (only if no critical image upload error) ---
    if ($message_type !== 'error') {
        // Get text data from POST
        $internships_hero_title = trim($_POST['internships_hero_title'] ?? '');
        $internships_hero_subtitle = trim($_POST['internships_hero_subtitle'] ?? '');

        // Basic validation
        $errors = [];
        if (empty($internships_hero_title)) {
            $errors[] = "Hero Title is required.";
        }

        if (empty($errors)) {
            // Save text settings
            $save_success = true;
            $save_success &= $internshipModel->setHeroSetting('internships_hero_title', $internships_hero_title);
            $save_success &= $internshipModel->setHeroSetting('internships_hero_subtitle', $internships_hero_subtitle);

            if ($save_success) {
                if ($message_type !== 'success') { // Only set success message if image upload didn't already set one
                     $message = "Internships page hero content updated successfully!";
                     $message_type = 'success';
                }
                logAdminActivity("Admin {$_SESSION['username']} updated Internships page hero text content." . ($new_image_filename ? " New hero image: $new_image_filename" : ""));
            } else {
                // If saving failed but a new image was uploaded, delete the new image
                if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                    unlink($uploadDir . $new_image_filename);
                }
                $message = "An error occurred while saving the content. Please try again.";
                $message_type = 'error';
                logAdminActivity("Admin {$_SESSION['username']} failed to update Internships page hero text content.");
            }
        } else {
            // If validation failed but a new image was uploaded, delete the new image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            $message = implode('<br>', $errors);
            $message_type = 'error';
            logAdminActivity("Admin {$_SESSION['username']} failed Internships content validation: " . implode(', ', $errors));
        }
    }
    // If there was an image upload error, the message is already set.
}

// --- Fetch Current Settings for Form Population ---
$internshipModel = new InternshipModel();
$internshipsContentKeys = ['internships_hero_title', 'internships_hero_subtitle', 'internships_hero_image'];
$currentSettings = $internshipModel->getHeroSettings($internshipsContentKeys, '');

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

$page_title = "Edit Internships Page Hero - Bantwana CMS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
        .character-count { font-size: 0.9em; color: #6c757d; }
        .character-count.warning { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Internships Page Hero</h2>
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
            <div class="hero-preview" id="hero-preview" style="background-image: url('<?php echo BASE_URL . '/images/' . htmlspecialchars($currentSettings['internships_hero_image'] ?? 'bg_2.jpg'); ?>');">
                <div class="hero-preview-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent justify-content-center p-0 mb-3">
                            <li class="breadcrumb-item text-white" aria-current="page">Internships</li>
                        </ol>
                    </nav>
                    <h1 id="hero-title-preview"><?php echo htmlspecialchars($currentSettings['internships_hero_title'] ?? 'Gain Experience, Make an Impact'); ?></h1>
                    <p id="hero-subtitle-preview"><?php echo htmlspecialchars($currentSettings['internships_hero_subtitle'] ?? 'Apply your academic knowledge in a real-world development setting.'); ?></p>
                </div>
            </div>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                <input type="hidden" name="save_internships_hero" value="1">
                
                <div class="section-header">
                    <h4>Internships Page Hero Section</h4>
                </div>
                <div class="form-group">
                    <label for="internships_hero_title">Hero Title * <span class="character-count" id="title-char-count">0/100</span></label>
                    <input type="text" class="form-control" id="internships_hero_title" name="internships_hero_title" value="<?php echo htmlspecialchars($currentSettings['internships_hero_title'] ?? 'Gain Experience, Make an Impact'); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="internships_hero_subtitle">Hero Subtitle <span class="character-count" id="subtitle-char-count">0/200</span></label>
                    <textarea class="form-control" id="internships_hero_subtitle" name="internships_hero_subtitle" rows="2" maxlength="200"><?php echo htmlspecialchars($currentSettings['internships_hero_subtitle'] ?? 'Apply your academic knowledge in a real-world development setting.'); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="internships_hero_image">Hero Background Image:</label>
                    <input type="file" class="form-control-file" id="internships_hero_image" name="internships_hero_image" accept="image/*" onchange="previewImage(this)">
                    <small class="form-text text-muted">Upload a new background image (JPG, JPEG, PNG, GIF, WEBP, max 5MB).</small>
                    <?php
                    $currentHeroImage = $currentSettings['internships_hero_image'] ?? 'bg_2.jpg';
                    $heroImagePath = BASE_URL . '/images/' . htmlspecialchars($currentHeroImage);
                    $serverHeroImagePath = BASE_PATH . '/public_html/images/' . $currentHeroImage;
                    if (file_exists($serverHeroImagePath) && !empty($currentHeroImage)):
                        echo "<p class='mt-2'><strong>Current Hero Image:</strong></p>";
                        echo "<img src='$heroImagePath' id='internships_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail'>";
                        echo "<p class='text-muted mt-1' id='internships_hero_image_name'>" . htmlspecialchars($currentHeroImage) . "</p>";
                    else:
                        echo "<p class='mt-2 text-muted' id='internships_hero_image_name'>Current image: '" . htmlspecialchars($currentHeroImage) . "' (file not found or default).</p>";
                        echo "<img src='' id='internships_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail d-none'>";
                    endif;
                    ?>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Changes</button>
                <a href="../dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

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

            const $titleInput = $('#internships_hero_title');
            const $subtitleInput = $('#internships_hero_subtitle');
            updateCharCount($titleInput, $('#title-char-count'), 100);
            updateCharCount($subtitleInput, $('#subtitle-char-count'), 200);

            $titleInput.on('input', function() {
                updateCharCount($(this), $('#title-char-count'), 100);
                $('#hero-title-preview').text($(this).val() || 'Gain Experience, Make an Impact');
            });

            $subtitleInput.on('input', function() {
                updateCharCount($(this), $('#subtitle-char-count'), 200);
                $('#hero-subtitle-preview').text($(this).val() || 'Apply your academic knowledge in a real-world development setting.');
            });

            // Real-time image preview
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    const preview = $('#internships_hero_image_preview');
                    const nameElement = $('#internships_hero_image_name');
                    const heroPreview = $('#hero-preview');

                    reader.onload = function(e) {
                        preview.attr('src', e.target.result).removeClass('d-none');
                        nameElement.text(input.files[0].name);
                        heroPreview.css('background-image', `url(${e.target.result})`);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('#internships_hero_image').change(function() {
                previewImage(this);
            });
        });
    </script>
</body>
</html>