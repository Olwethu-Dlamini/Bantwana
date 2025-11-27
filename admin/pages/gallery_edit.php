<?php
// admin/pages/gallery_edit.php
require_once __DIR__ . '/../includes/init.php';

// --- Load Required Models ---
loadAdminClass('SettingModel');
loadAdminClass('GalleryModel');

// --- Handle Actions (Create/Edit/Delete Gallery/Image) ---
$message = '';
$message_type = '';
$action_performed = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $galleryModel = new GalleryModel();
        $settingModel = new SettingModel();

        switch ($action) {
            // --- Hero Image Upload ---
            case 'upload_hero':
                if (isset($_FILES['gallery_hero_image']) && $_FILES['gallery_hero_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = BASE_PATH . '/public_html/images/';
                    $uploadFile = $_FILES['gallery_hero_image'];
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
                    $maxFileSize = 5 * 1024 * 1024;

                    if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $message = "Invalid Hero image file type.";
                        $message_type = 'error';
                    } elseif ($uploadFile['size'] > $maxFileSize) {
                        $message = "Hero image file is too large.";
                        $message_type = 'error';
                    } else {
                        $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                        $new_filename = 'gallery_hero_' . uniqid() . '.' . strtolower($fileExtension);
                        $targetPath = $uploadDir . $new_filename;

                        if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                            if ($settingModel->set('gallery_hero_image', $new_filename)) {
                                $message = "Gallery hero image uploaded successfully!";
                                $message_type = 'success';
                                $action_performed = true;
                                logAdminActivity("Admin {$_SESSION['username']} uploaded new gallery hero image: $new_filename");
                            } else {
                                $message = "Image uploaded, but failed to update database setting.";
                                $message_type = 'error';
                            }
                        } else {
                            $message = "Error uploading hero image file.";
                            $message_type = 'error';
                        }
                    }
                } else if (isset($_FILES['gallery_hero_image']) && $_FILES['gallery_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $message = "Error uploading hero image: " . $_FILES['gallery_hero_image']['error'];
                    $message_type = 'error';
                }
                break;

            // --- Create Gallery ---
            case 'create_gallery':
                $name = trim($_POST['gallery_name'] ?? '');
                $description = trim($_POST['gallery_description'] ?? '');
                if (empty($name)) {
                    $message = "Gallery name is required.";
                    $message_type = 'error';
                } else {
                    $gallery_id = $galleryModel->createGallery($name, $description);
                    if ($gallery_id) {
                        $message = "Gallery '$name' created successfully!";
                        $message_type = 'success';
                        $action_performed = true;
                        logAdminActivity("Admin {$_SESSION['username']} created gallery: $name (ID: $gallery_id)");
                    } else {
                        $message = "Failed to create gallery.";
                        $message_type = 'error';
                    }
                }
                break;

            // --- Edit Gallery ---
            case 'edit_gallery':
                $id = intval($_POST['gallery_id'] ?? 0);
                $name = trim($_POST['gallery_name'] ?? '');
                $description = trim($_POST['gallery_description'] ?? '');
                if ($id <= 0 || empty($name)) {
                    $message = "Invalid gallery data for editing.";
                    $message_type = 'error';
                } else {
                    if ($galleryModel->updateGallery($id, $name, $description)) {
                        $message = "Gallery updated successfully!";
                        $message_type = 'success';
                        $action_performed = true;
                        logAdminActivity("Admin {$_SESSION['username']} updated gallery ID: $id");
                    } else {
                        $message = "Failed to update gallery.";
                        $message_type = 'error';
                    }
                }
                break;

            // --- Delete Gallery ---
            case 'delete_gallery':
                 $id = intval($_POST['gallery_id'] ?? 0);
                 if ($id <= 0) {
                     $message = "Invalid gallery ID for deletion.";
                     $message_type = 'error';
                 } else {
                     // Verify gallery exists before deleting
                     $gallery = $galleryModel->getGalleryById($id);
                     if ($gallery) {
                         if ($galleryModel->deleteGallery($id)) {
                             $message = "Gallery '{$gallery['name']}' deleted successfully!";
                             $message_type = 'success';
                             $action_performed = true;
                             logAdminActivity("Admin {$_SESSION['username']} deleted gallery: {$gallery['name']} (ID: $id)");
                         } else {
                             $message = "Failed to delete gallery.";
                             $message_type = 'error';
                         }
                     } else {
                         $message = "Gallery not found.";
                         $message_type = 'error';
                     }
                 }
                 break;

            // --- Upload Images to Gallery ---
            case 'upload_images':
                $gallery_id = intval($_POST['gallery_id'] ?? 0);
                if ($gallery_id <= 0) {
                    $message = "Invalid gallery selected for image upload.";
                    $message_type = 'error';
                    break;
                }

                // Verify gallery exists
                $gallery = $galleryModel->getGalleryById($gallery_id);
                if (!$gallery) {
                    $message = "Selected gallery for image upload not found.";
                    $message_type = 'error';
                    break;
                }

                if (isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'])) {
                    $uploadDir = BASE_PATH . '/public_html/images/gallery/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $file_count = count($_FILES['gallery_images']['name']);
                    $uploaded_count = 0;
                    $errors = [];

                    for ($i = 0; $i < $file_count; $i++) {
                        // Skip if no file was selected for this input
                        if ($_FILES['gallery_images']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                            continue;
                        }

                        if ($_FILES['gallery_images']['error'][$i] === UPLOAD_ERR_OK) {
                            $tmp_name = $_FILES['gallery_images']['tmp_name'][$i];
                            $name = $_FILES['gallery_images']['name'][$i];
                            $type = $_FILES['gallery_images']['type'][$i];
                            $size = $_FILES['gallery_images']['size'][$i];

                            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
                            $maxFileSize = 10 * 1024 * 1024; // 10MB per image

                            if (!in_array(strtolower($type), $allowedTypes) && !in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                $errors[] = "Invalid file type for '$name'.";
                            } elseif ($size > $maxFileSize) {
                                $errors[] = "'$name' is too large (max 10MB).";
                            } else {
                                $fileExtension = pathinfo($name, PATHINFO_EXTENSION);
                                $new_filename = 'img_' . uniqid() . '.' . strtolower($fileExtension);
                                $targetPath = $uploadDir . $new_filename;

                                if (move_uploaded_file($tmp_name, $targetPath)) {
                                    // Add to database
                                    if ($galleryModel->addImageToGallery($gallery_id, $new_filename)) {
                                        $uploaded_count++;
                                    } else {
                                        $errors[] = "Failed to save '$name' to database.";
                                        // Optionally, delete the uploaded file if DB save fails
                                        if (file_exists($targetPath)) {
                                            unlink($targetPath);
                                        }
                                    }
                                } else {
                                    $errors[] = "Error uploading '$name'.";
                                }
                            }
                        } else {
                            $errors[] = "Error uploading file #" . ($i+1) . ": " . $_FILES['gallery_images']['error'][$i];
                        }
                    }

                    if ($uploaded_count > 0) {
                        $msg = "$uploaded_count image(s) uploaded to '{$gallery['name']}' successfully!";
                        if (!empty($errors)) {
                            $msg .= " Some errors occurred: " . implode(', ', $errors);
                            $message_type = 'warning'; // Partial success
                        } else {
                            $message_type = 'success';
                        }
                        $message = $msg;
                        $action_performed = true;
                        logAdminActivity("Admin {$_SESSION['username']} uploaded $uploaded_count images to gallery: {$gallery['name']} (ID: $gallery_id)");
                    } elseif (!empty($errors)) {
                        $message = "Errors occurred during upload: " . implode(', ', $errors);
                        $message_type = 'error';
                    } else {
                        // This case might occur if all selected files were skipped (e.g., all were "no file")
                        $message = "No valid images were selected for upload.";
                        $message_type = 'info';
                    }
                } else {
                    $message = "No images selected for upload.";
                    $message_type = 'info';
                }
                break;

            // --- Delete Image ---
            case 'delete_image':
                $image_id = intval($_POST['image_id'] ?? 0);
                if ($image_id <= 0) {
                    $message = "Invalid image ID for deletion.";
                    $message_type = 'error';
                } else {
                    // Get image details for logging/file deletion
                    $image = $galleryModel->getImageById($image_id);
                    if ($image) {
                        $imageName = $image['filename'];
                        $galleryId = $image['gallery_id'];
                        // Get gallery name for logging
                        $gallery = $galleryModel->getGalleryById($galleryId);
                        $galleryName = $gallery ? $gallery['name'] : "Unknown Gallery (ID: $galleryId)";

                        if ($galleryModel->deleteImage($image_id)) {
                            // Optional: Delete physical file
                            $imagePath = BASE_PATH . '/public_html/images/gallery/' . $imageName;
                            if (file_exists($imagePath)) {
                                unlink($imagePath);
                            }
                            $message = "Image deleted successfully!";
                            $message_type = 'success';
                            $action_performed = true;
                            logAdminActivity("Admin {$_SESSION['username']} deleted image: $imageName from gallery: $galleryName");
                        } else {
                            $message = "Failed to delete image.";
                            $message_type = 'error';
                        }
                    } else {
                        $message = "Image not found.";
                        $message_type = 'error';
                    }
                }
                break;

            // --- Edit Image Details (Alt Text, Caption) ---
            case 'edit_image_details':
                $image_id = intval($_POST['image_id'] ?? 0);
                $alt_text = trim($_POST['image_alt_text'] ?? '');
                $caption = trim($_POST['image_caption'] ?? '');

                if ($image_id <= 0) {
                    $message = "Invalid image ID for editing details.";
                    $message_type = 'error';
                } else {
                    $image = $galleryModel->getImageById($image_id);
                    if ($image) {
                        if ($galleryModel->updateImage($image_id, $alt_text, $caption)) {
                            $message = "Image details updated successfully!";
                            $message_type = 'success';
                            $action_performed = true;
                            logAdminActivity("Admin {$_SESSION['username']} updated details for image ID: $image_id");
                        } else {
                            $message = "Failed to update image details.";
                            $message_type = 'error';
                        }
                    } else {
                        $message = "Image not found.";
                        $message_type = 'error';
                    }
                }
                break;

            default:
                $message = "Unknown action.";
                $message_type = 'error';
        }
    }
}

// --- Fetch Data for Display ---
$settingModel = new SettingModel();
$galleryModel = new GalleryModel();

$currentHeroImage = $settingModel->get('gallery_hero_image', 'bg_6.jpg');
$galleries = $galleryModel->getAllGalleries();

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

$page_title = "Manage Gallery - Bantwana CMS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; }
        .admin-container { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .section-header { border-bottom: 2px solid #dee2e6; padding-bottom: 10px; margin-bottom: 20px; color: #495057; }
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
        .gallery-card { border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; }
        .gallery-card-header { background-color: #e9ecef; padding: 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .gallery-card-body { padding: 15px; }
        .image-scroll-container {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            padding-bottom: 10px;
            margin-bottom: 10px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }
        .image-scroll-container::-webkit-scrollbar {
            height: 8px;
        }
        .image-scroll-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        .image-scroll-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .image-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .image-item { flex: 0 0 auto; width: 150px; position: relative; text-align: center; }
        .image-item img { width: 100%; height: 150px; object-fit: cover; border-radius: 5px; border: 1px solid #eee; }
        .image-item .image-actions { position: absolute; top: 5px; right: 5px; display: none; }
        .image-item:hover .image-actions { display: block; }
        .image-item .image-info { font-size: 0.8em; margin-top: 5px; color: #6c757d; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .modal-lg { max-width: 800px; }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Gallery</h2>
            <a href="../dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo ($message_type === 'success' || $message_type === 'warning') ? ($message_type === 'success' ? 'success' : 'warning') : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Hero Preview -->
        <div class="admin-container">
            <h4><i class="fas fa-eye mr-2"></i>Hero Section Preview</h4>
            <div class="hero-preview" id="hero-preview" style="background-image: url('<?php echo BASE_URL . '/images/' . htmlspecialchars($currentHeroImage); ?>');">
                <div class="hero-preview-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent justify-content-center p-0 mb-3">
                            <li class="breadcrumb-item text-white" aria-current="page">Gallery</li>
                        </ol>
                    </nav>
                    <h1 class="mb-3">Our Gallery</h1>
                </div>
            </div>

            <!-- Hero Image Upload -->
            <div class="section-header">
                <h4><i class="fas fa-image mr-2"></i> Hero Section Image</h4>
            </div>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_hero">
                <div class="form-group">
                    <label for="gallery_hero_image">Upload New Hero Image</label>
                    <input type="file" class="form-control-file" id="gallery_hero_image" name="gallery_hero_image" 
                           accept="image/*" onchange="previewImage(this)">
                    <small class="form-text text-muted">JPG, JPEG, PNG, GIF, WEBP (Max 5MB)</small>
                    <?php
                    $heroImagePath = BASE_URL . '/images/' . htmlspecialchars($currentHeroImage);
                    $serverHeroImagePath = BASE_PATH . '/public_html/images/' . $currentHeroImage;
                    if (file_exists($serverHeroImagePath) && !empty($currentHeroImage)):
                    ?>
                        <p class="mt-2"><strong>Current Hero Image:</strong></p>
                        <img src="<?php echo $heroImagePath; ?>" id="gallery_hero_image_preview" alt="Current Hero Image" class="current-image-preview img-thumbnail">
                        <p class="text-muted mt-1" id="gallery_hero_image_name"><?php echo htmlspecialchars($currentHeroImage); ?></p>
                    <?php else: ?>
                        <p class="text-muted mt-2" id="gallery_hero_image_name">Current image file not found or default set (<?php echo htmlspecialchars($currentHeroImage); ?>).</p>
                        <img src="" id="gallery_hero_image_preview" alt="Current Hero Image" class="current-image-preview img-thumbnail d-none">
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-1"></i> Upload Hero Image</button>
            </form>
        </div>

        <div class="admin-container mt-4">
            <!-- Galleries Section -->
            <div class="section-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-images mr-2"></i> Galleries</h4>
                <button class="btn btn-success" data-toggle="modal" data-target="#createGalleryModal"><i class="fas fa-plus-circle mr-1"></i> Create New Gallery</button>
            </div>

            <?php if (empty($galleries)): ?>
                <p class="text-center text-muted mt-4">No galleries found. Create one to get started.</p>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($galleries as $gallery): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="gallery-card h-100 d-flex flex-column">
                            <div class="gallery-card-header">
                                <strong><?php echo htmlspecialchars($gallery['name']); ?></strong>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary mr-1" data-toggle="modal" data-target="#editGalleryModal<?php echo $gallery['id']; ?>" title="Edit Gallery">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info mr-1" data-toggle="modal" data-target="#uploadImagesModal<?php echo $gallery['id']; ?>" title="Upload Images">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteGallery(<?php echo $gallery['id']; ?>, '<?php echo htmlspecialchars($gallery['name']); ?>')" title="Delete Gallery">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="gallery-card-body flex-grow-1 d-flex flex-column">
                                <p class="text-muted flex-grow-1"><?php echo htmlspecialchars($gallery['description'] ?: 'No description'); ?></p>
                                <hr>
                                <h6>Images:</h6>
                                <?php
                                $images = $galleryModel->getImagesByGalleryId($gallery['id']);
                                if (empty($images)):
                                ?>
                                    <p class="text-muted">No images in this gallery.</p>
                                <?php else: ?>
                                    <div class="image-scroll-container">
                                        <?php foreach ($images as $image): ?>
                                        <div class="image-item">
                                            <img src="<?php echo BASE_URL; ?>/images/gallery/<?php echo htmlspecialchars($image['filename']); ?>" alt="<?php echo htmlspecialchars($image['alt_text'] ?: 'Gallery Image'); ?>">
                                            <div class="image-actions">
                                                <button class="btn btn-sm btn-outline-primary btn-sm mr-1" data-toggle="modal" data-target="#editImageModal<?php echo $image['id']; ?>" title="Edit Details">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger btn-sm" onclick="confirmDeleteImage(<?php echo $image['id']; ?>)" title="Delete Image">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <?php if (!empty($image['caption'])): ?>
                                                <div class="image-info" title="<?php echo htmlspecialchars($image['caption']); ?>">
                                                    <?php echo htmlspecialchars(substr($image['caption'], 0, 20) . (strlen($image['caption']) > 20 ? '...' : '')); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modals -->

    <!-- Create Gallery Modal -->
    <div class="modal fade" id="createGalleryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="action" value="create_gallery">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Gallery</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new_gallery_name">Gallery Name *</label>
                            <input type="text" class="form-control" id="new_gallery_name" name="gallery_name" required>
                        </div>
                        <div class="form-group">
                            <label for="new_gallery_description">Description</label>
                            <textarea class="form-control" id="new_gallery_description" name="gallery_description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Gallery</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Gallery Modals (One for each gallery) -->
    <?php foreach ($galleries as $gallery): ?>
    <div class="modal fade" id="editGalleryModal<?php echo $gallery['id']; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="action" value="edit_gallery">
                    <input type="hidden" name="gallery_id" value="<?php echo $gallery['id']; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Gallery: <?php echo htmlspecialchars($gallery['name']); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_gallery_name_<?php echo $gallery['id']; ?>">Gallery Name *</label>
                            <input type="text" class="form-control" id="edit_gallery_name_<?php echo $gallery['id']; ?>" name="gallery_name" value="<?php echo htmlspecialchars($gallery['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_gallery_description_<?php echo $gallery['id']; ?>">Description</label>
                            <textarea class="form-control" id="edit_gallery_description_<?php echo $gallery['id']; ?>" name="gallery_description" rows="3"><?php echo htmlspecialchars($gallery['description']); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Upload Images Modals (One for each gallery) -->
    <?php foreach ($galleries as $gallery): ?>
    <div class="modal fade" id="uploadImagesModal<?php echo $gallery['id']; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload_images">
                    <input type="hidden" name="gallery_id" value="<?php echo $gallery['id']; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Images to: <?php echo htmlspecialchars($gallery['name']); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="gallery_images_<?php echo $gallery['id']; ?>">Select Images:</label>
                            <input type="file" class="form-control-file" id="gallery_images_<?php echo $gallery['id']; ?>" name="gallery_images[]" multiple accept="image/*">
                            <small class="form-text text-muted">JPG, JPEG, PNG, GIF, WEBP (Max 10MB each)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload Images</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Edit Image Modals (One for each image) -->
    <?php
    // Re-fetch images for all galleries to populate modals
    foreach ($galleries as $gallery) {
        $images = $galleryModel->getImagesByGalleryId($gallery['id']);
        foreach ($images as $image) {
    ?>
    <div class="modal fade" id="editImageModal<?php echo $image['id']; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="action" value="edit_image_details">
                    <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Image Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                         <div class="text-center mb-3">
                             <img src="<?php echo BASE_URL; ?>/images/gallery/<?php echo htmlspecialchars($image['filename']); ?>" alt="<?php echo htmlspecialchars($image['alt_text'] ?: 'Gallery Image'); ?>" class="img-fluid" style="max-height: 200px;">
                         </div>
                        <div class="form-group">
                            <label for="edit_image_alt_text_<?php echo $image['id']; ?>">Alt Text</label>
                            <input type="text" class="form-control" id="edit_image_alt_text_<?php echo $image['id']; ?>" name="image_alt_text" value="<?php echo htmlspecialchars($image['alt_text']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="edit_image_caption_<?php echo $image['id']; ?>">Caption</label>
                            <textarea class="form-control" id="edit_image_caption_<?php echo $image['id']; ?>" name="image_caption" rows="3"><?php echo htmlspecialchars($image['caption']); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
        }
    }
    ?>

    <!-- Hidden Forms for Deletions -->
    <form id="deleteGalleryForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <input type="hidden" name="action" value="delete_gallery">
        <input type="hidden" name="gallery_id" id="delete_gallery_id">
    </form>
    <form id="deleteImageForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <input type="hidden" name="action" value="delete_image">
        <input type="hidden" name="image_id" id="delete_image_id">
    </form>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDeleteGallery(galleryId, galleryName) {
            if (confirm(`Are you sure you want to delete the gallery "${galleryName}" and ALL its images? This action cannot be undone.`)) {
                document.getElementById('delete_gallery_id').value = galleryId;
                document.getElementById('deleteGalleryForm').submit();
            }
        }

        function confirmDeleteImage(imageId) {
            if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
                document.getElementById('delete_image_id').value = imageId;
                document.getElementById('deleteImageForm').submit();
            }
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                const preview = document.getElementById('gallery_hero_image_preview');
                const nameElement = document.getElementById('gallery_hero_image_name');
                const heroPreview = document.getElementById('hero-preview');

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    nameElement.textContent = input.files[0].name;
                    heroPreview.style.backgroundImage = `url(${e.target.result})`;
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        <?php if ($action_performed): ?>
        document.addEventListener('DOMContentLoaded', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        <?php endif; ?>
    </script>
</body>
</html>