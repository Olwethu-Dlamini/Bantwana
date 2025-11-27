<?php
// admin/pages/programs_manage.php
require_once __DIR__ . '/../includes/init.php';

// --- Load Required Models ---
loadAdminClass('ProgramModel');
loadAdminClass('SettingModel');

// --- Handle Form Submission ---
$message = '';
$message_type = ''; // 'success' or 'error'

// Handle Hero Section Updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_hero_settings'])) {
    $settingModel = new SettingModel();
    
    // --- Handle Hero Image Upload ---
    $new_image_filename = null;
    if (isset($_FILES['programs_hero_image']) && $_FILES['programs_hero_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = BASE_PATH . '/public_html/images/';
        $uploadFile = $_FILES['programs_hero_image'];

        // Basic validation
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $message = "Invalid Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.";
            $message_type = 'error';
            logAdminActivity("Admin {$_SESSION['username']} failed to upload Programs hero image: Invalid file type.");
        } elseif ($uploadFile['size'] > $maxFileSize) {
            $message = "Hero image file is too large. Maximum size is 5MB.";
            $message_type = 'error';
            logAdminActivity("Admin {$_SESSION['username']} failed to upload Programs hero image: File too large.");
        } else {
            $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
            $new_image_filename = 'programs_hero_' . uniqid() . '.' . strtolower($fileExtension);
            $targetPath = $uploadDir . $new_image_filename;

            if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                // Successfully uploaded hero image
                if ($settingModel->set('programs_hero_image', $new_image_filename)) {
                    $message = "Programs hero section updated successfully! Hero image uploaded.";
                    $message_type = 'success';
                    logAdminActivity("Admin {$_SESSION['username']} uploaded new Programs hero image: $new_image_filename");
                } else {
                    // If DB update failed, delete the uploaded image
                    if (file_exists($uploadDir . $new_image_filename)) {
                        unlink($uploadDir . $new_image_filename);
                    }
                    $message = "Hero image uploaded, but failed to update database setting.";
                    $message_type = 'error';
                    logAdminActivity("Admin {$_SESSION['username']} failed to update Programs hero image setting after upload.");
                }
            } else {
                $message = "Error uploading hero image file.";
                $message_type = 'error';
                logAdminActivity("Admin {$_SESSION['username']} failed to move Programs hero image file.");
            }
        }
    } else if (isset($_FILES['programs_hero_image']) && $_FILES['programs_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
         // An upload error occurred (but not "no file")
         $message = "Error uploading hero image: " . $_FILES['programs_hero_image']['error'];
         $message_type = 'error';
         logAdminActivity("Admin {$_SESSION['username']} encountered upload error for Programs hero image: " . $_FILES['programs_hero_image']['error']);
    }

    // --- Handle Text Content Saving (only if no critical image upload error) ---
    if ($message_type !== 'error') {
        // Get text data from POST
        $programs_hero_title = trim($_POST['programs_hero_title'] ?? '');
        $programs_hero_subtitle = trim($_POST['programs_hero_subtitle'] ?? '');

        // Basic validation
        $errors = [];
        if (empty($programs_hero_title)) {
            $errors[] = "Hero Title is required.";
        }

        if (empty($errors)) {
            // Save text settings
            $save_success = true;
            $save_success &= $settingModel->set('programs_hero_title', $programs_hero_title);
            $save_success &= $settingModel->set('programs_hero_subtitle', $programs_hero_subtitle);

            if ($save_success) {
                if ($message_type !== 'success') { // Only set success message if image upload didn't already set one
                     $message = "Programs hero section updated successfully!";
                     $message_type = 'success';
                }
                logAdminActivity("Admin {$_SESSION['username']} updated Programs hero section text content." . ($new_image_filename ? " New hero image: $new_image_filename" : ""));
            } else {
                // If saving failed but a new image was uploaded, delete the new image
                if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                    unlink($uploadDir . $new_image_filename);
                }
                $message = "An error occurred while saving the hero content. Please try again.";
                $message_type = 'error';
                logAdminActivity("Admin {$_SESSION['username']} failed to update Programs hero section text content.");
            }
        } else {
            // If validation failed but a new image was uploaded, delete the new image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            $message = implode('<br>', $errors);
            $message_type = 'error';
            logAdminActivity("Admin {$_SESSION['username']} failed Programs hero content validation: " . implode(', ', $errors));
        }
    }
}

// Handle Program Management via AJAX (keep existing AJAX handlers)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $programModel = new ProgramModel();
    
    switch($_POST['action']) {
        case 'create_program':
            // Handle program creation
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $sort_order = intval($_POST['sort_order'] ?? 0);
            
            if (empty($title) || empty($content)) {
                echo json_encode(['status' => 'error', 'message' => 'Title and content are required.']);
                exit;
            }
            
            // Handle image upload for program
            $image_filename = null;
            if (isset($_FILES['program_image']) && $_FILES['program_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = BASE_PATH . '/public_html/images/programs/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $uploadFile = $_FILES['program_image'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $maxFileSize = 5 * 1024 * 1024; // 5MB
                
                if (in_array(strtolower($uploadFile['type']), $allowedTypes) && $uploadFile['size'] <= $maxFileSize) {
                    $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                    $image_filename = 'program_' . uniqid() . '.' . strtolower($fileExtension);
                    $targetPath = $uploadDir . $image_filename;
                    
                    if (!move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to upload program image.']);
                        exit;
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid image file or file too large.']);
                    exit;
                }
            }
            
            $result = $programModel->createProgram($title, $content, $image_filename, $sort_order);
            if ($result) {
                logAdminActivity("Admin {$_SESSION['username']} created new program: $title");
                echo json_encode(['status' => 'success', 'message' => 'Program created successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create program.']);
            }
            exit;
            
        case 'update_program':
            // Handle program update
            $id = intval($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $sort_order = intval($_POST['sort_order'] ?? 0);
            
            if ($id <= 0 || empty($title) || empty($content)) {
                echo json_encode(['status' => 'error', 'message' => 'Valid ID, title and content are required.']);
                exit;
            }
            
            // Handle image upload for program update
            $image_filename = null;
            if (isset($_FILES['program_image']) && $_FILES['program_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = BASE_PATH . '/public_html/images/programs/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $uploadFile = $_FILES['program_image'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $maxFileSize = 5 * 1024 * 1024; // 5MB
                
                if (in_array(strtolower($uploadFile['type']), $allowedTypes) && $uploadFile['size'] <= $maxFileSize) {
                    $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                    $image_filename = 'program_' . uniqid() . '.' . strtolower($fileExtension);
                    $targetPath = $uploadDir . $image_filename;
                    
                    if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                        // Delete old image if it exists
                        $oldProgram = $programModel->getProgramById($id);
                        if ($oldProgram && $oldProgram['image_filename']) {
                            $oldImagePath = $uploadDir . $oldProgram['image_filename'];
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to upload new program image.']);
                        exit;
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid image file or file too large.']);
                    exit;
                }
            }
            
            $result = $programModel->updateProgram($id, $title, $content, $image_filename, $sort_order);
            if ($result) {
                logAdminActivity("Admin {$_SESSION['username']} updated program: $title");
                echo json_encode(['status' => 'success', 'message' => 'Program updated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update program.']);
            }
            exit;
            
        case 'delete_program':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Valid program ID is required.']);
                exit;
            }
            
            // Get program details before deletion for cleanup
            $program = $programModel->getProgramById($id);
            if ($program && $program['image_filename']) {
                $imagePath = BASE_PATH . '/public_html/images/programs/' . $program['image_filename'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $result = $programModel->deleteProgram($id);
            if ($result) {
                logAdminActivity("Admin {$_SESSION['username']} deleted program ID: $id");
                echo json_encode(['status' => 'success', 'message' => 'Program deleted successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete program.']);
            }
            exit;
    }
    
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
    exit;
}

// --- Fetch Current Settings for Form Population ---
$programModel = new ProgramModel();
$programs = $programModel->getAllPrograms();
$settingModel = new SettingModel();
$heroSettingsKeys = ['programs_hero_title', 'programs_hero_subtitle', 'programs_hero_image'];
$currentHeroSettings = $settingModel->getMultiple($heroSettingsKeys, '');

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

$page_title = "Manage Programs - Bantwana CMS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body { font-family: Arial, sans-serif; background-color: #f1f1f1; padding-top: 20px; }
        .admin-container { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group label { font-weight: bold; }
        .section-header { border-bottom: 1px solid #dee2e6; padding-bottom: 10px; margin-bottom: 20px; margin-top: 30px; color: #495057; }
        .program-card { border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px; }
        .program-card-header { background-color: #e9ecef; padding: 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .program-card-body { padding: 15px; }
        .current-image-preview { max-width: 200px; max-height: 150px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .action-buttons .btn { margin-right: 5px; margin-bottom: 5px; }
        .modal-lg { max-width: 800px; }
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
        .error-message { color: #dc3545; font-size: 0.9em; display: none; }
        .character-count { font-size: 0.9em; color: #6c757d; }
        .character-count.warning { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Programs</h2>
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
            <div class="hero-preview" id="hero-preview" style="background-image: url('<?php echo BASE_URL . '/images/' . htmlspecialchars($currentHeroSettings['programs_hero_image'] ?? 'bg_5.jpg'); ?>');">
                <div class="hero-preview-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent justify-content-center p-0 mb-3">
                            <li class="breadcrumb-item text-white" aria-current="page">Programs</li>
                        </ol>
                    </nav>
                    <h1 id="hero-title-preview"><?php echo htmlspecialchars($currentHeroSettings['programs_hero_title'] ?? 'Our Programs'); ?></h1>
                    <p id="hero-subtitle-preview"><?php echo htmlspecialchars($currentHeroSettings['programs_hero_subtitle'] ?? 'Building resilient futures for vulnerable children and families.'); ?></p>
                </div>
            </div>

            <!-- Hero Section Management -->
            <div class="section-header">
                <h4>Programs Page Hero Section</h4>
            </div>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                <!-- Hero Image Upload -->
                <div class="form-group">
                    <label for="programs_hero_image">Hero Background Image:</label>
                    <input type="file" class="form-control-file" id="programs_hero_image" name="programs_hero_image" accept="image/*" onchange="previewImage(this)">
                    <small class="form-text text-muted">Upload a new background image for the Programs page hero section (JPG, JPEG, PNG, GIF, WEBP, max 5MB).</small>
                    <?php
                    $currentHeroImage = $currentHeroSettings['programs_hero_image'] ?? 'bg_5.jpg';
                    $heroImagePath = BASE_URL . '/images/' . htmlspecialchars($currentHeroImage);
                    $serverHeroImagePath = BASE_PATH . '/public_html/images/' . $currentHeroImage;
                    if (file_exists($serverHeroImagePath) && !empty($currentHeroImage)):
                        echo "<p class='mt-2'><strong>Current Hero Image:</strong></p>";
                        echo "<img src='$heroImagePath' id='programs_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail'>";
                        echo "<p class='text-muted mt-1' id='programs_hero_image_name'>" . htmlspecialchars($currentHeroImage) . "</p>";
                    else:
                        echo "<p class='mt-2 text-muted' id='programs_hero_image_name'>Current hero image: '" . htmlspecialchars($currentHeroImage) . "' (file not found or default).</p>";
                        echo "<img src='' id='programs_hero_image_preview' alt='Current Hero Image' class='current-image-preview img-thumbnail d-none'>";
                    endif;
                    ?>
                </div>
                <!-- Hero Text Fields -->
                <div class="form-group">
                    <label for="programs_hero_title">Hero Title * <span class="character-count" id="title-char-count">0/100</span></label>
                    <input type="text" class="form-control" id="programs_hero_title" name="programs_hero_title" value="<?php echo htmlspecialchars($currentHeroSettings['programs_hero_title'] ?? 'Our Programs'); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="programs_hero_subtitle">Hero Subtitle <span class="character-count" id="subtitle-char-count">0/200</span></label>
                    <textarea class="form-control" id="programs_hero_subtitle" name="programs_hero_subtitle" rows="2" maxlength="200"><?php echo htmlspecialchars($currentHeroSettings['programs_hero_subtitle'] ?? 'Building resilient futures for vulnerable children and families.'); ?></textarea>
                </div>

                <button type="submit" name="save_hero_settings" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Hero Section</button>
            </form>

            <!-- Programs List Management -->
            <div class="section-header d-flex justify-content-between align-items-center mt-5">
                <h4>Programs List</h4>
                <button class="btn btn-success" data-toggle="modal" data-target="#createProgramModal"><i class="fas fa-plus-circle mr-1"></i> Add New Program</button>
            </div>

            <div id="programs-list">
                <?php if (empty($programs)): ?>
                    <p class="text-center text-muted">No programs found. Click "Add New Program" to create one.</p>
                <?php else: ?>
                    <?php foreach ($programs as $program): ?>
                    <div class="program-card" data-id="<?php echo $program['id']; ?>">
                        <div class="program-card-header">
                            <strong><?php echo htmlspecialchars($program['title']); ?></strong>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline-primary edit-btn" data-id="<?php echo $program['id']; ?>" data-title="<?php echo htmlspecialchars($program['title']); ?>" data-content="<?php echo htmlspecialchars($program['content']); ?>" data-image="<?php echo htmlspecialchars($program['image_filename'] ?? ''); ?>" data-sort="<?php echo $program['sort_order']; ?>"><i class="fas fa-edit"></i> Edit</button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" data-id="<?php echo $program['id']; ?>" data-title="<?php echo htmlspecialchars($program['title']); ?>"><i class="fas fa-trash-alt"></i> Delete</button>
                            </div>
                        </div>
                        <div class="program-card-body">
                            <?php if (!empty($program['image_filename'])):
                                $imageUrl = BASE_URL . '/images/programs/' . htmlspecialchars($program['image_filename']);
                                $serverImagePath = BASE_PATH . '/public_html/images/programs/' . $program['image_filename'];
                                if (file_exists($serverImagePath)):
                            ?>
                                <p><strong>Image:</strong></p>
                                <img src="<?php echo $imageUrl; ?>" alt="<?php echo htmlspecialchars($program['title']); ?> Image" class="current-image-preview img-thumbnail">
                            <?php else: ?>
                                <p class="text-muted">Image file '<?php echo htmlspecialchars($program['image_filename']); ?>' not found on server.</p>
                            <?php endif;
                            else: ?>
                                <p class="text-muted">No image set for this program.</p>
                            <?php endif; ?>
                            <div class="mt-2">
                                <p><strong>Sort Order:</strong> <?php echo $program['sort_order']; ?></p>
                            </div>
                            <div class="mt-2">
                                <p><strong>Content Preview:</strong></p>
                                <div><?php echo substr(strip_tags($program['content']), 0, 150) . (strlen(strip_tags($program['content'])) > 150 ? '...' : ''); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modals -->

    <!-- Create Program Modal -->
    <div class="modal fade" id="createProgramModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="createProgramForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Program</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="create_program_title">Title *</label>
                            <input type="text" class="form-control" id="create_program_title" name="title" required>
                            <div class="error-message" id="create_program_title_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="create_program_image">Image</label>
                            <input type="file" class="form-control-file" id="create_program_image" name="program_image" accept="image/*">
                            <small class="form-text text-muted">Upload an image (JPG, JPEG, PNG, GIF, WEBP, max 5MB).</small>
                            <div class="error-message" id="create_program_image_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="create_program_sort_order">Sort Order</label>
                            <input type="number" class="form-control" id="create_program_sort_order" name="sort_order" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label for="create_program_content">Content *</label>
                            <textarea class="form-control summernote" id="create_program_content" name="content" required></textarea>
                            <div class="error-message" id="create_program_content_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Program Modal -->
    <div class="modal fade" id="editProgramModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editProgramForm" enctype="multipart/form-data">
                    <input type="hidden" id="edit_program_id" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Program</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_program_title_modal">Title *</label>
                            <input type="text" class="form-control" id="edit_program_title_modal" name="title" required>
                            <div class="error-message" id="edit_program_title_modal_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_program_image_modal">Image</label>
                            <input type="file" class="form-control-file" id="edit_program_image_modal" name="program_image" accept="image/*">
                            <small class="form-text text-muted">Upload a new image to replace the current one (JPG, JPEG, PNG, GIF, WEBP, max 5MB).</small>
                            <div class="error-message" id="edit_program_image_modal_error"></div>
                            <div id="current_image_container_modal" class="mt-2">
                                <p><strong>Current Image:</strong></p>
                                <img id="current_image_preview_modal" src="" alt="Current Program Image" class="current-image-preview img-thumbnail">
                                <p id="current_image_name_modal" class="text-muted"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_program_sort_order_modal">Sort Order</label>
                            <input type="number" class="form-control" id="edit_program_sort_order_modal" name="sort_order" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label for="edit_program_content_modal">Content *</label>
                            <textarea class="form-control summernote" id="edit_program_content_modal" name="content" required></textarea>
                            <div class="error-message" id="edit_program_content_modal_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteProgramModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the program <strong id="delete_program_title"></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Summernote
            $('.summernote').summernote({
                placeholder: 'Enter program content here...',
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

            const $titleInput = $('#programs_hero_title');
            const $subtitleInput = $('#programs_hero_subtitle');
            updateCharCount($titleInput, $('#title-char-count'), 100);
            updateCharCount($subtitleInput, $('#subtitle-char-count'), 200);

            $titleInput.on('input', function() {
                updateCharCount($(this), $('#title-char-count'), 100);
                $('#hero-title-preview').text($(this).val() || 'Our Programs');
            });

            $subtitleInput.on('input', function() {
                updateCharCount($(this), $('#subtitle-char-count'), 200);
                $('#hero-subtitle-preview').text($(this).val() || 'Building resilient futures for vulnerable children and families.');
            });

            // Real-time image preview
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    const preview = $('#programs_hero_image_preview');
                    const nameElement = $('#programs_hero_image_name');
                    const heroPreview = $('#hero-preview');

                    reader.onload = function(e) {
                        preview.attr('src', e.target.result).removeClass('d-none');
                        nameElement.text(input.files[0].name);
                        heroPreview.css('background-image', `url(${e.target.result})`);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('#programs_hero_image').change(function() {
                previewImage(this);
            });

            // Client-side file validation
            function validateFile(input, errorElement) {
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    if (file.size > maxSize) {
                        $(errorElement).text('File size exceeds 5MB.').show();
                        return false;
                    }
                    if (!allowedTypes.includes(file.type)) {
                        $(errorElement).text('Invalid file type. Allowed: JPG, PNG, GIF, WEBP.').show();
                        return false;
                    }
                    $(errorElement).hide();
                    return true;
                }
                return true; // No file selected is valid
            }

            // Clear error messages on input change
            $('input, textarea').on('input change', function() {
                const errorId = '#' + $(this).attr('id') + '_error';
                $(errorId).hide();
            });

            // Variable for program ID
            let currentProgramId = null;

            // Create Program
            $('#create_program_image').change(function() {
                validateFile(this, '#create_program_image_error');
            });

            $('#createProgramForm').on('submit', function(e) {
                e.preventDefault();
                if (!validateFile($('#create_program_image')[0], '#create_program_image_error')) {
                    return;
                }
                
                const formData = new FormData(this);
                formData.append('action', 'create_program');
                
                $.ajax({
                    url: '<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#createProgramForm button[type="submit"]').prop('disabled', true).text('Creating...');
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            $('#createProgramModal').modal('hide');
                            $('#createProgramForm')[0].reset();
                            $('.summernote').summernote('code', '');
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Create AJAX Error:", status, error);
                        alert('An error occurred while creating the program. Please try again.');
                    },
                    complete: function() {
                        $('#createProgramForm button[type="submit"]').prop('disabled', false).text('Create Program');
                    }
                });
            });

            // Edit Program
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                const title = $(this).data('title');
                const content = $(this).data('content');
                const image = $(this).data('image');
                const sort = $(this).data('sort');

                $('#edit_program_id').val(id);
                $('#edit_program_title_modal').val(title);
                $('#edit_program_sort_order_modal').val(sort);
                $('#edit_program_content_modal').summernote('code', content);

                if (image) {
                    const imageUrl = '<?php echo BASE_URL; ?>/images/programs/' + encodeURIComponent(image);
                    $('#current_image_preview_modal').attr('src', imageUrl).show();
                    $('#current_image_name_modal').text(image).show();
                    $('#current_image_container_modal').show();
                } else {
                    $('#current_image_preview_modal').hide();
                    $('#current_image_name_modal').text('No image set').show();
                    $('#current_image_container_modal').show();
                }

                $('#editProgramModal').modal('show');
            });

            $('#edit_program_image_modal').change(function() {
                validateFile(this, '#edit_program_image_modal_error');
            });

            $('#editProgramForm').on('submit', function(e) {
                e.preventDefault();
                if (!validateFile($('#edit_program_image_modal')[0], '#edit_program_image_modal_error')) {
                    return;
                }
                
                const formData = new FormData(this);
                formData.append('action', 'update_program');
                
                $.ajax({
                    url: '<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#editProgramForm button[type="submit"]').prop('disabled', true).text('Updating...');
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            $('#editProgramModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Update AJAX Error:", status, error);
                        alert('An error occurred while updating the program. Please try again.');
                    },
                    complete: function() {
                        $('#editProgramForm button[type="submit"]').prop('disabled', false).text('Update Program');
                    }
                });
            });

            // Delete Program
            $(document).on('click', '.delete-btn', function() {
                currentProgramId = $(this).data('id');
                const title = $(this).data('title');
                $('#delete_program_title').text(title);
                $('#deleteProgramModal').modal('show');
            });

            $('#confirmDeleteBtn').on('click', function() {
                if (currentProgramId) {
                    $.ajax({
                        url: '<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>',
                        type: 'POST',
                        data: { 
                            action: 'delete_program',
                            id: currentProgramId
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $('#confirmDeleteBtn').prop('disabled', true).text('Deleting...');
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                alert(response.message);
                                $('#deleteProgramModal').modal('hide');
                                location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Delete AJAX Error:", status, error);
                            alert('An error occurred while deleting the program. Please try again.');
                        },
                        complete: function() {
                            $('#confirmDeleteBtn').prop('disabled', false).text('Delete');
                            currentProgramId = null;
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>