<?php
// admin/pages/donate_edit.php
require_once __DIR__ . '/../includes/init.php';

// --- Load Configuration and Models ---
loadAdminClass('DonateModel'); // Load the DonateModel

// --- Handle Form Submission (Including Hero Image Upload) ---
$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_donate_content'])) {
    // Load the DonateModel
    $donateModel = new DonateModel();

    // --- Handle Hero Image Upload ---
    $new_image_filename = null;
    if (isset($_FILES['donate_hero_image']) && $_FILES['donate_hero_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = BASE_PATH . '/public_html/images/';
        $uploadFile = $_FILES['donate_hero_image'];

        // Basic validation
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $message = "Invalid Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.";
            $message_type = 'error';
            logAdminActivity("Admin {$_SESSION['username']} failed to upload Donate hero image: Invalid file type.");
        } elseif ($uploadFile['size'] > $maxFileSize) {
            $message = "Hero image file is too large. Maximum size is 5MB.";
            $message_type = 'error';
            logAdminActivity("Admin {$_SESSION['username']} failed to upload Donate hero image: File too large.");
        } else {
            $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
            $new_image_filename = 'donate_hero_' . uniqid() . '.' . strtolower($fileExtension);
            $targetPath = $uploadDir . $new_image_filename;

            if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                // Successfully uploaded hero image
                if ($donateModel->set('donate_hero_image', $new_image_filename)) {
                    $message = "Donate page content updated successfully! Hero image uploaded.";
                    $message_type = 'success';
                    logAdminActivity("Admin {$_SESSION['username']} uploaded new Donate hero image: $new_image_filename");
                } else {
                    // If DB update failed, delete the uploaded image
                    if (file_exists($uploadDir . $new_image_filename)) {
                        unlink($uploadDir . $new_image_filename);
                    }
                    $message = "Hero image uploaded, but failed to update database setting.";
                    $message_type = 'error';
                    logAdminActivity("Admin {$_SESSION['username']} failed to update Donate hero image setting after upload.");
                }
            } else {
                $message = "Error uploading hero image file.";
                $message_type = 'error';
                logAdminActivity("Admin {$_SESSION['username']} failed to move Donate hero image file.");
            }
        }
    } else if (isset($_FILES['donate_hero_image']) && $_FILES['donate_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
         // An upload error occurred (but not "no file")
         $message = "Error uploading hero image: " . $_FILES['donate_hero_image']['error'];
         $message_type = 'error';
         logAdminActivity("Admin {$_SESSION['username']} encountered upload error for Donate hero image: " . $_FILES['donate_hero_image']['error']);
    }
    // If no file was uploaded, $new_image_filename remains null

    // --- Handle Text Content Saving (only if no critical image upload error) ---
    if ($message_type !== 'error') {
        // Get text data from POST
        $donate_hero_title = trim($_POST['donate_hero_title'] ?? '');
        $donate_hero_subtitle = trim($_POST['donate_hero_subtitle'] ?? '');
        $donate_main_heading = trim($_POST['donate_main_heading'] ?? '');
        $donate_main_subheading = trim($_POST['donate_main_subheading'] ?? '');
        $donate_main_content = trim($_POST['donate_main_content'] ?? '');

        // Basic validation
        $errors = [];
        if (empty($donate_hero_title)) {
            $errors[] = "Hero Title is required.";
        }
        if (empty($donate_main_heading)) {
            $errors[] = "Main Heading is required.";
        }
        if (empty($donate_main_content)) {
            $errors[] = "Main Content is required.";
        }

        if (empty($errors)) {
            // Save text settings
            $save_success = true;
            $save_success &= $donateModel->set('donate_hero_title', $donate_hero_title);
            $save_success &= $donateModel->set('donate_hero_subtitle', $donate_hero_subtitle);
            $save_success &= $donateModel->set('donate_main_heading', $donate_main_heading);
            $save_success &= $donateModel->set('donate_main_subheading', $donate_main_subheading);
            $save_success &= $donateModel->set('donate_main_content', $donate_main_content);

            if ($save_success) {
                if ($message_type !== 'success') { // Only set success message if image upload didn't already set one
                     $message = "Donate page content updated successfully!";
                     $message_type = 'success';
                }
                logAdminActivity("Admin {$_SESSION['username']} updated Donate page text content." . ($new_image_filename ? " New hero image: $new_image_filename" : ""));
            } else {
                // If saving failed but a new image was uploaded, delete the new image
                if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                    unlink($uploadDir . $new_image_filename);
                }
                $message = "An error occurred while saving the content. Please try again.";
                $message_type = 'error';
                logAdminActivity("Admin {$_SESSION['username']} failed to update Donate page text content.");
            }
        } else {
            // If validation failed but a new image was uploaded, delete the new image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            $message = implode('<br>', $errors);
            $message_type = 'error';
            logAdminActivity("Admin {$_SESSION['username']} failed Donate content validation: " . implode(', ', $errors));
        }
    }
    // If there was an image upload error, the message is already set.
}

// --- Fetch Current Settings for Form Population ---
$donateModel = new DonateModel();
$donateContentKeys = [
    'donate_hero_image',
    'donate_hero_title',
    'donate_hero_subtitle',
    'donate_main_heading',
    'donate_main_subheading',
    'donate_main_content'
];
$currentSettings = $donateModel->getMultiple($donateContentKeys, '');

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

$page_title = "Edit Donate Page Content - Bantwana CMS";
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
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
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
        <?php // include '../includes/header.php'; ?> <!-- Optional -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Donate Page Content</h2>
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
            <div class="hero-preview" id="hero-preview" style="background-image: url('<?php echo BASE_URL . '/images/' . htmlspecialchars($currentSettings['donate_hero_image'] ?? 'bg_5.jpg'); ?>');">
                <div class="hero-preview-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent justify-content-center p-0 mb-3">
                            <li class="breadcrumb-item text-white" aria-current="page">Donate</li>
                        </ol>
                    </nav>
                    <h1 id="hero-title-preview"><?php echo htmlspecialchars($currentSettings['donate_hero_title'] ?? 'Invest in Our Future'); ?></h1>
                    <p id="hero-subtitle-preview"><?php echo htmlspecialchars($currentSettings['donate_hero_subtitle'] ?? 'Transparent, accountable, and impactful giving.'); ?></p>
                </div>
            </div>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                <!-- Hero Section -->
                <div class="section-header">
                    <h4>Donate Page Hero Section</h4>
                </div>
                <!-- Hero Image Upload -->
                <div class="form-group">
                    <label for="donate_hero_image">Hero Background Image:</label>
                    <input type="file" class="form-control-file" id="donate_hero_image" name="donate_hero_image" accept="image/*" onchange="previewImage(this)">
                    <small class="form-text text-muted">Upload a new background image for the Donate page hero section (JPG, JPEG, PNG, GIF, WEBP, max 5MB).</small>
                    <?php
                    $currentHeroImage = $currentSettings['donate_hero_image'] ?? 'bg_5.jpg';
                    $heroImagePath = BASE_URL . '/images/' . htmlspecialchars($currentHeroImage);
                    $serverHeroImagePath = BASE_PATH . '/public_html/images/' . $currentHeroImage;
                    if (file_exists($serverHeroImagePath) && !empty($currentHeroImage)):
                        echo "<p class='mt-2'><strong>Current Hero Image:</strong></p>";
                        echo "<img src='$heroImagePath' id='donate_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail'>";
                        echo "<p class='text-muted mt-1' id='donate_hero_image_name'>" . htmlspecialchars($currentHeroImage) . "</p>";
                    else:
                        echo "<p class='mt-2 text-muted' id='donate_hero_image_name'>Current hero image: '" . htmlspecialchars($currentHeroImage) . "' (file not found or default).</p>";
                        echo "<img src='' id='donate_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail d-none'>";
                    endif;
                    ?>
                </div>
                <!-- Hero Text Fields -->
                <div class="form-group">
                    <label for="donate_hero_title">Hero Title * <span class="character-count" id="title-char-count">0/100</span></label>
                    <input type="text" class="form-control" id="donate_hero_title" name="donate_hero_title" value="<?php echo htmlspecialchars($currentSettings['donate_hero_title'] ?? 'Invest in Our Future'); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="donate_hero_subtitle">Hero Subtitle <span class="character-count" id="subtitle-char-count">0/200</span></label>
                    <textarea class="form-control" id="donate_hero_subtitle" name="donate_hero_subtitle" rows="2" maxlength="200"><?php echo htmlspecialchars($currentSettings['donate_hero_subtitle'] ?? 'Transparent, accountable, and impactful giving.'); ?></textarea>
                </div>

                <!-- Main Content Section -->
                <div class="section-header mt-5">
                    <h4>Main Content Section</h4>
                </div>
                <div class="form-group">
                    <label for="donate_main_heading">Main Heading * <span class="character-count" id="main-heading-char-count">0/100</span></label>
                    <input type="text" class="form-control" id="donate_main_heading" name="donate_main_heading" value="<?php echo htmlspecialchars($currentSettings['donate_main_heading'] ?? 'Your Donation at Work'); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="donate_main_subheading">Main Subheading <span class="character-count" id="main-subheading-char-count">0/200</span></label>
                    <input type="text" class="form-control" id="donate_main_subheading" name="donate_main_subheading" value="<?php echo htmlspecialchars($currentSettings['donate_main_subheading'] ?? 'Transparency and Accountability'); ?>" maxlength="200">
                </div>
                <div class="form-group">
                    <label for="donate_main_content">Main Content *</label>
                    <textarea class="form-control summernote" id="donate_main_content" name="donate_main_content" required><?php echo htmlspecialchars($currentSettings['donate_main_content'] ?? '<p>We are committed to using your generous support effectively and efficiently to maximize our impact on the lives of vulnerable children and families.</p>'); ?></textarea>
                </div>

                <button type="submit" name="save_donate_content" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Changes</button>
                <a href="../dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>

        <?php // include '../includes/footer.php'; ?> <!-- Optional -->
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Summernote editors
            $('.summernote').summernote({
                placeholder: 'Enter content here...',
                tabsize: 2,
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Character count for hero title, subtitle, main heading, and main subheading
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

            const $titleInput = $('#donate_hero_title');
            const $subtitleInput = $('#donate_hero_subtitle');
            const $mainHeadingInput = $('#donate_main_heading');
            const $mainSubheadingInput = $('#donate_main_subheading');

            updateCharCount($titleInput, $('#title-char-count'), 100);
            updateCharCount($subtitleInput, $('#subtitle-char-count'), 200);
            updateCharCount($mainHeadingInput, $('#main-heading-char-count'), 100);
            updateCharCount($mainSubheadingInput, $('#main-subheading-char-count'), 200);

            $titleInput.on('input', function() {
                updateCharCount($(this), $('#title-char-count'), 100);
                $('#hero-title-preview').text($(this).val() || 'Invest in Our Future');
            });

            $subtitleInput.on('input', function() {
                updateCharCount($(this), $('#subtitle-char-count'), 200);
                $('#hero-subtitle-preview').text($(this).val() || 'Transparent, accountable, and impactful giving.');
            });

            $mainHeadingInput.on('input', function() {
                updateCharCount($(this), $('#main-heading-char-count'), 100);
            });

            $mainSubheadingInput.on('input', function() {
                updateCharCount($(this), $('#main-subheading-char-count'), 200);
            });

            // Real-time image preview
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    const preview = $('#donate_hero_image_preview');
                    const nameElement = $('#donate_hero_image_name');
                    const heroPreview = $('#hero-preview');

                    reader.onload = function(e) {
                        preview.attr('src', e.target.result).removeClass('d-none');
                        nameElement.text(input.files[0].name);
                        heroPreview.css('background-image', `url(${e.target.result})`);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('#donate_hero_image').change(function() {
                previewImage(this);
            });
        });
    </script>
</body>
</html>