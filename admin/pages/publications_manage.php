<?php
// admin/pages/publications_manage.php
require_once __DIR__ . '/../includes/init.php';

// --- Ensure Authentication ---
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

// --- Load Required Models ---
loadAdminClass('PublicationModel');
loadAdminClass('SettingModel');

// --- Initialize Message ---
$message = '';
$message_type = ''; // 'success' or 'error'

// --- Handle Hero Section Updates ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_hero_content'])) {
    $settingModel = new SettingModel();
    $new_image_filename = null;
    $uploadDir = BASE_PATH . '/public_html/images/';
    
    if (isset($_FILES['publications_hero_image']) && $_FILES['publications_hero_image']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $uploadFile = $_FILES['publications_hero_image'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileExtension = strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION));
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        error_log("Hero Image Upload - Name: {$uploadFile['name']}, Extension: $fileExtension, Size: {$uploadFile['size']}");

        if (!in_array($fileExtension, $allowedExtensions)) {
            $message = "Invalid hero image type. Allowed: JPG, PNG, GIF, WEBP.";
            $message_type = 'error';
        } elseif ($uploadFile['size'] > $maxFileSize) {
            $message = "Hero image too large. Max 5MB.";
            $message_type = 'error';
        } else {
            $new_image_filename = 'hero_' . uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $new_image_filename;
            if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                if ($settingModel->set('publications_hero_image', $new_image_filename)) {
                    $message = "Hero image updated.";
                    $message_type = 'success';
                } else {
                    unlink($targetPath);
                    $message = "Failed to save hero image to database.";
                    $message_type = 'error';
                }
            } else {
                $message = "Failed to upload hero image.";
                $message_type = 'error';
            }
        }
    }

    if ($message_type !== 'error') {
        $title = trim($_POST['publications_hero_title'] ?? '');
        $subtitle = trim($_POST['publications_hero_subtitle'] ?? '');
        if (empty($title)) {
            $message = "Hero title is required.";
            $message_type = 'error';
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
        } else {
            $save_success = $settingModel->set('publications_hero_title', $title) &&
                            $settingModel->set('publications_hero_subtitle', $subtitle);
            if ($save_success) {
                $message = $message ?: "Hero section updated.";
                $message_type = 'success';
            } else {
                if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                    unlink($uploadDir . $new_image_filename);
                }
                $message = $message ?: "Failed to save hero content.";
                $message_type = 'error';
            }
        }
    }
    if ($message) {
        error_log("Hero Update: [$message_type] $message");
    }
}

// --- Handle Publication Creation ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_publication'])) {
    $publicationModel = new PublicationModel();
    $uploadDir = BASE_PATH . '/public_html/images/publications/';
    $new_filename = null;
    $file_type = null;
    $file_size = null;
    $original_filename = null;

    if (isset($_FILES['publication_file']) && $_FILES['publication_file']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $uploadFile = $_FILES['publication_file'];
        $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar', 'jpg', 'png', 'gif', 'ppt', 'pptx'];
        $fileExtension = strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION));
        $original_filename = basename($uploadFile['name']);
        $maxFileSize = 10 * 1024 * 1024; // 10MB

        error_log("Publication Upload - Name: {$uploadFile['name']}, Extension: $fileExtension, Size: {$uploadFile['size']}");

        if (!in_array($fileExtension, $allowedExtensions)) {
            $message = "Invalid file type. Allowed: PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP, RAR, JPG, PNG, GIF, PPT, PPTX.";
            $message_type = 'error';
        } elseif ($uploadFile['size'] > $maxFileSize) {
            $message = "File too large. Max 10MB.";
            $message_type = 'error';
        } else {
            $new_filename = 'pub_' . uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $new_filename;
            if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                $file_type = $fileExtension;
                $file_size = $uploadFile['size'];
            } else {
                $message = "Failed to upload file.";
                $message_type = 'error';
            }
        }
    } else {
        $message = "Please select a file.";
        $message_type = 'error';
    }

    if ($message_type !== 'error' && $new_filename) {
        $title = trim($_POST['publication_title'] ?? '');
        $description = trim($_POST['publication_description'] ?? '');
        $category = trim($_POST['publication_category'] ?? 'general');
        $sort_order = intval($_POST['publication_sort_order'] ?? 0);
        $userId = $_SESSION['user_id'];

        if (empty($title)) {
            $message = "Publication title is required.";
            $message_type = 'error';
            if ($new_filename && file_exists($uploadDir . $new_filename)) {
                unlink($uploadDir . $new_filename);
            }
        } else {
            try {
                $newId = $publicationModel->createPublication($title, $description, $new_filename, $file_type, $file_size, $category, $sort_order, $userId, $original_filename);
                if ($newId) {
                    $message = "Publication '$title' created.";
                    $message_type = 'success';
                } else {
                    unlink($uploadDir . $new_filename);
                    $message = "Failed to save publication to database.";
                    $message_type = 'error';
                }
            } catch (Exception $e) {
                unlink($uploadDir . $new_filename);
                $message = "Error creating publication: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
    if ($message) {
        error_log("Create Publication: [$message_type] $message");
    }
}

// --- Handle Publication Update ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_publication'])) {
    $publicationModel = new PublicationModel();
    $publicationId = intval($_POST['publication_id'] ?? 0);
    $uploadDir = BASE_PATH . '/public_html/images/publications/';

    if ($publicationId <= 0) {
        $message = "Invalid publication ID.";
        $message_type = 'error';
    } else {
        $existing = $publicationModel->getPublicationById($publicationId);
        if (!$existing) {
            $message = "Publication not found.";
            $message_type = 'error';
        } else {
            $new_filename = $existing['filename'];
            $file_type = $existing['file_type'];
            $file_size = $existing['file_size'];
            $original_filename = $existing['original_filename'];
            $old_filename = $existing['filename'];

            if (isset($_FILES['publication_file_update']) && $_FILES['publication_file_update']['error'] === UPLOAD_ERR_OK) {
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $uploadFile = $_FILES['publication_file_update'];
                $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar', 'jpg', 'png', 'gif', 'ppt', 'pptx'];
                $fileExtension = strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION));
                $original_filename = basename($uploadFile['name']);
                $maxFileSize = 10 * 1024 * 1024;

                error_log("Update Upload - Name: {$uploadFile['name']}, Extension: $fileExtension, Size: {$uploadFile['size']}");

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $message = "Invalid file type. Allowed: PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP, RAR, JPG, PNG, GIF, PPT, PPTX.";
                    $message_type = 'error';
                } elseif ($uploadFile['size'] > $maxFileSize) {
                    $message = "File too large. Max 10MB.";
                    $message_type = 'error';
                } else {
                    $new_filename = 'pub_' . uniqid() . '.' . $fileExtension;
                    $targetPath = $uploadDir . $new_filename;
                    if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                        $file_type = $fileExtension;
                        $file_size = $uploadFile['size'];
                    } else {
                        $message = "Failed to upload new file.";
                        $message_type = 'error';
                    }
                }
            }

            if ($message_type !== 'error') {
                $title = trim($_POST['publication_title_update'] ?? '');
                $description = trim($_POST['publication_description_update'] ?? '');
                $category = trim($_POST['publication_category_update'] ?? 'general');
                $sort_order = intval($_POST['publication_sort_order_update'] ?? 0);

                if (empty($title)) {
                    $message = "Publication title is required.";
                    $message_type = 'error';
                    if ($new_filename !== $old_filename && file_exists($uploadDir . $new_filename)) {
                        unlink($uploadDir . $new_filename);
                    }
                } else {
                    try {
                        $success = $publicationModel->updatePublication($publicationId, $title, $description, $new_filename, $file_type, $file_size, $category, $sort_order, $original_filename);
                        if ($success) {
                            if ($new_filename !== $old_filename && file_exists($uploadDir . $old_filename)) {
                                unlink($uploadDir . $old_filename);
                            }
                            $message = "Publication '$title' updated.";
                            $message_type = 'success';
                        } else {
                            if ($new_filename !== $old_filename && file_exists($uploadDir . $new_filename)) {
                                unlink($uploadDir . $new_filename);
                            }
                            $message = "Failed to update publication in database.";
                            $message_type = 'error';
                        }
                    } catch (Exception $e) {
                        if ($new_filename !== $old_filename && file_exists($uploadDir . $new_filename)) {
                            unlink($uploadDir . $new_filename);
                        }
                        $message = "Error updating publication: " . $e->getMessage();
                        $message_type = 'error';
                    }
                }
            }
        }
    }
    if ($message) {
        error_log("Update Publication: [$message_type] $message");
    }
}

// --- Handle Publication Deletion ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_publication'])) {
    $publicationModel = new PublicationModel();
    $publicationId = intval($_POST['publication_id'] ?? 0);
    $uploadDir = BASE_PATH . '/public_html/images/publications/';

    if ($publicationId <= 0) {
        $message = "Invalid publication ID.";
        $message_type = 'error';
    } else {
        $publication = $publicationModel->getPublicationById($publicationId);
        if (!$publication) {
            $message = "Publication not found.";
            $message_type = 'error';
        } else {
            try {
                if ($publicationModel->deletePublication($publicationId)) {
                    $filename = $publication['filename'];
                    if ($filename && file_exists($uploadDir . $filename)) {
                        unlink($uploadDir . $filename);
                    }
                    $message = "Publication '{$publication['title']}' deleted.";
                    $message_type = 'success';
                } else {
                    $message = "Failed to delete publication from database.";
                    $message_type = 'error';
                }
            } catch (Exception $e) {
                $message = "Error deleting publication: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
    if ($message) {
        error_log("Delete Publication: [$message_type] $message");
    }
}

// --- Fetch Current Data ---
$settingModel = new SettingModel();
$publicationModel = new PublicationModel();

$heroContentKeys = [
    'publications_hero_title',
    'publications_hero_subtitle',
    'publications_hero_image'
];
$currentHeroSettings = $settingModel->getMultiple($heroContentKeys, '');

try {
    $publications = $publicationModel->getAllPublications();
    if ($publications === false || $publications === null) {
        $publications = [];
        $message = $message ?: "Failed to fetch publications from database.";
        $message_type = $message_type ?: 'error';
        error_log("PublicationModel::getAllPublications returned false or null");
    }
} catch (Exception $e) {
    $publications = [];
    $message = $message ?: "Error fetching publications: " . $e->getMessage();
    $message_type = $message_type ?: 'error';
    error_log("Error fetching publications: " . $e->getMessage());
}

// --- Helper function to format file size ---
function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return number_format($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

$page_title = "Manage Publications - Bantwana CMS";
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
        body { font-family: Arial, sans-serif; background-color: #f1f1f1; padding: 20px; }
        .admin-container { background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group label { font-weight: bold; }
        .section-header { border-bottom: 1px solid #dee2e6; padding-bottom: 10px; margin: 30px 0 20px; color: #495057; }
        .current-image-preview { max-width: 200px; max-height: 150px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .action-buttons .btn { margin-right: 5px; }
        .modal-lg { max-width: 800px; }
        .file-type-badge {
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            border-radius: 0.25rem;
            background: #e9ecef;
            color: #495057;
        }
        .file-type-badge.pdf { background: #dc3545; color: white; }
        .file-type-badge.doc, .file-type-badge.docx { background: #007bff; color: white; }
        .file-type-badge.xls, .file-type-badge.xlsx { background: #28a745; color: white; }
        .file-type-badge.zip, .file-type-badge.rar { background: #ffc107; color: black; }
        .file-type-badge.txt { background: #6c757d; color: white; }
        .file-type-badge.jpg, .file-type-badge.png, .file-type-badge.gif { background: #17a2b8; color: white; }
        .file-type-badge.ppt, .file-type-badge.pptx { background: #d81b60; color: white; }
        table th { cursor: pointer; }
        table th:hover { background: #e9ecef; }
        .truncate { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        th.sorted-asc::after { content: ' ▲'; }
        th.sorted-desc::after { content: ' ▼'; }
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
            <h2>Manage Publications</h2>
            <a href="../dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($message_type) === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="alert alert-info">
            <strong>Debug:</strong> Found <?php echo count($publications); ?> publications
        </div>

        <div class="admin-container">
            <!-- Hero Section Preview -->
            <h4><i class="fas fa-eye mr-2"></i>Hero Section Preview</h4>
            <div class="hero-preview" id="hero-preview" style="background-image: url('<?php echo BASE_URL . '/images/' . htmlspecialchars($currentHeroSettings['publications_hero_image'] ?? 'bg_3.jpg'); ?>');">
                <div class="hero-preview-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent justify-content-center p-0 mb-3">
                            <li class="breadcrumb-item text-white" aria-current="page">Publications</li>
                        </ol>
                    </nav>
                    <h1 id="hero-title-preview"><?php echo htmlspecialchars($currentHeroSettings['publications_hero_title'] ?? 'Our Publications'); ?></h1>
                    <p id="hero-subtitle-preview"><?php echo htmlspecialchars($currentHeroSettings['publications_hero_subtitle'] ?? 'Access our reports, manuals, and resources.'); ?></p>
                </div>
            </div>

            <div class="section-header">
                <h4>Publications Page Hero Section</h4>
            </div>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="publications_hero_image">Hero Image:</label>
                    <input type="file" class="form-control-file" id="publications_hero_image" name="publications_hero_image" accept="image/*" onchange="previewImage(this)">
                    <small class="form-text text-muted">JPG, PNG, GIF, WEBP (max 5MB).</small>
                    <?php
                    $heroImage = $currentHeroSettings['publications_hero_image'] ?? 'bg_3.jpg';
                    $heroImagePath = BASE_URL . '/images/' . htmlspecialchars($heroImage);
                    $serverPath = BASE_PATH . '/public_html/images/' . $heroImage;
                    if (file_exists($serverPath) && $heroImage):
                        echo "<p class='mt-2'><strong>Current Image:</strong></p>";
                        echo "<img src='$heroImagePath' id='publications_hero_image_preview' alt='Hero Image' class='current-image-preview img-thumbnail'>";
                        echo "<p class='text-muted mt-1' id='publications_hero_image_name'>" . htmlspecialchars($heroImage) . "</p>";
                    else:
                        echo "<p class='mt-2 text-muted' id='publications_hero_image_name'>Current image: '" . htmlspecialchars($heroImage) . "' (not found).</p>";
                        echo "<img src='' id='publications_hero_image_preview' alt='Hero Image' class='current-image-preview img-thumbnail d-none'>";
                    endif;
                    ?>
                </div>
                <div class="form-group">
                    <label for="publications_hero_title">Hero Title * <span class="character-count" id="title-char-count">0/100</span></label>
                    <input type="text" class="form-control" id="publications_hero_title" name="publications_hero_title" value="<?php echo htmlspecialchars($currentHeroSettings['publications_hero_title'] ?? 'Our Publications'); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="publications_hero_subtitle">Hero Subtitle <span class="character-count" id="subtitle-char-count">0/200</span></label>
                    <textarea class="form-control" id="publications_hero_subtitle" name="publications_hero_subtitle" rows="2" maxlength="200"><?php echo htmlspecialchars($currentHeroSettings['publications_hero_subtitle'] ?? 'Access our reports, manuals, and resources.'); ?></textarea>
                </div>
                <button type="submit" name="save_hero_content" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Hero</button>
            </form>

            <div class="section-header d-flex justify-content-between align-items-center mt-5">
                <h4>Publications List</h4>
                <button class="btn btn-success" data-toggle="modal" data-target="#createPublicationModal"><i class="fas fa-plus-circle mr-1"></i> Add Publication</button>
            </div>

            <div id="publications-list">
                <?php if (empty($publications)): ?>
                    <p class="text-center text-muted">No publications found.</p>
                <?php else: ?>
                    <table class="table table-bordered table-hover" id="publications-table">
                        <thead class="thead-light">
                            <tr>
                                <th data-sort="int">ID</th>
                                <th data-sort="string">Title</th>
                                <th data-sort="string">Description</th>
                                <th data-sort="string">Category</th>
                                <th data-sort="string">File Type</th>
                                <th data-sort="float">File Size</th>
                                <th data-sort="int">Sort Order</th>
                                <th data-sort="date">Uploaded At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($publications as $publication): ?>
                            <tr data-id="<?php echo $publication['id']; ?>">
                                <td><?php echo htmlspecialchars($publication['id']); ?></td>
                                <td class="truncate"><?php echo htmlspecialchars($publication['title']); ?></td>
                                <td class="truncate"><?php echo htmlspecialchars($publication['description'] ?? 'No description'); ?></td>
                                <td><?php echo htmlspecialchars($publication['category'] ?? 'general'); ?></td>
                                <td>
                                    <?php
                                    $fileType = strtolower($publication['file_type']);
                                    echo "<span class='file-type-badge $fileType'>" . strtoupper($fileType) . "</span>";
                                    ?>
                                </td>
                                <td data-sort-value="<?php echo $publication['file_size'] ?? 0; ?>">
                                    <?php echo formatFileSize($publication['file_size'] ?? 0); ?>
                                </td>
                                <td><?php echo htmlspecialchars($publication['sort_order'] ?? 0); ?></td>
                                <td data-sort-value="<?php echo strtotime($publication['uploaded_at']); ?>">
                                    <?php echo date('M j, Y', strtotime($publication['uploaded_at'])); ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary edit-btn"
                                            data-id="<?php echo $publication['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($publication['title']); ?>"
                                            data-description="<?php echo htmlspecialchars($publication['description'] ?? ''); ?>"
                                            data-category="<?php echo htmlspecialchars($publication['category'] ?? 'general'); ?>"
                                            data-sort="<?php echo $publication['sort_order'] ?? 0; ?>"
                                            data-filename="<?php echo htmlspecialchars($publication['filename'] ?? ''); ?>"
                                            data-original-filename="<?php echo htmlspecialchars($publication['original_filename'] ?? $publication['filename']); ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="<?php echo $publication['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($publication['title']); ?>">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                    <?php if ($publication['filename'] && file_exists(BASE_PATH . '/public_html/images/publications/' . $publication['filename'])): ?>
                                        <a href="<?php echo BASE_URL; ?>/download/<?php echo $publication['id']; ?>" class="btn btn-sm btn-outline-success" target="_blank">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createPublicationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="createPublicationForm" method="post" action="" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Publication</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="create_publication_title">Title * <span class="character-count" id="create-title-char-count">0/200</span></label>
                            <input type="text" class="form-control" id="create_publication_title" name="publication_title" required maxlength="200">
                        </div>
                        <div class="form-group">
                            <label for="create_publication_file">File *</label>
                            <input type="file" class="form-control-file" id="create_publication_file" name="publication_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar,.jpg,.png,.gif,.ppt,.pptx" required>
                            <small class="form-text text-muted">PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP, RAR, JPG, PNG, GIF, PPT, PPTX (max 10MB).</small>
                        </div>
                        <div class="form-group">
                            <label for="create_publication_category">Category</label>
                            <input type="text" class="form-control" id="create_publication_category" name="publication_category" value="general">
                        </div>
                        <div class="form-group">
                            <label for="create_publication_sort_order">Sort Order</label>
                            <input type="number" class="form-control" id="create_publication_sort_order" name="publication_sort_order" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label for="create_publication_description">Description <span class="character-count" id="create-description-char-count">0/500</span></label>
                            <textarea class="form-control" id="create_publication_description" name="publication_description" rows="3" maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="create_publication" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPublicationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editPublicationForm" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" id="edit_publication_id" name="publication_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Publication</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_publication_title_modal">Title * <span class="character-count" id="edit-title-char-count">0/200</span></label>
                            <input type="text" class="form-control" id="edit_publication_title_modal" name="publication_title_update" required maxlength="200">
                        </div>
                        <div class="form-group">
                            <label for="edit_publication_file_modal">Replace File</label>
                            <input type="file" class="form-control-file" id="edit_publication_file_modal" name="publication_file_update" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar,.jpg,.png,.gif,.ppt,.pptx">
                            <small class="form-text text-muted">PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP, RAR, JPG, PNG, GIF, PPT, PPTX (max 10MB).</small>
                            <div id="current_file_container_modal" class="mt-2">
                                <p><strong>Current File:</strong> <span id="current_file_name_modal"></span></p>
                                <a id="current_file_link_modal" href="#" target="_blank" class="btn btn-sm btn-outline-primary">View File</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_publication_category_modal">Category</label>
                            <input type="text" class="form-control" id="edit_publication_category_modal" name="publication_category_update" value="general">
                        </div>
                        <div class="form-group">
                            <label for="edit_publication_sort_order_modal">Sort Order</label>
                            <input type="number" class="form-control" id="edit_publication_sort_order_modal" name="publication_sort_order_update" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label for="edit_publication_description_modal">Description <span class="character-count" id="edit-description-char-count">0/500</span></label>
                            <textarea class="form-control" id="edit_publication_description_modal" name="publication_description_update" rows="3" maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_publication" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deletePublicationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deletePublicationForm" method="post" action="">
                    <input type="hidden" id="delete_publication_id" name="publication_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Delete publication <strong id="delete_publication_title"></strong>? This cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_publication" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
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

            const $titleInput = $('#publications_hero_title');
            const $subtitleInput = $('#publications_hero_subtitle');
            updateCharCount($titleInput, $('#title-char-count'), 100);
            updateCharCount($subtitleInput, $('#subtitle-char-count'), 200);

            $titleInput.on('input', function() {
                updateCharCount($(this), $('#title-char-count'), 100);
                $('#hero-title-preview').text($(this).val() || 'Our Publications');
            });

            $subtitleInput.on('input', function() {
                updateCharCount($(this), $('#subtitle-char-count'), 200);
                $('#hero-subtitle-preview').text($(this).val() || 'Access our reports, manuals, and resources.');
            });

            // Real-time image preview
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    const preview = $('#publications_hero_image_preview');
                    const nameElement = $('#publications_hero_image_name');
                    const heroPreview = $('#hero-preview');

                    reader.onload = function(e) {
                        preview.attr('src', e.target.result).removeClass('d-none');
                        nameElement.text(input.files[0].name);
                        heroPreview.css('background-image', `url(${e.target.result})`);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('#publications_hero_image').change(function() {
                previewImage(this);
            });

            // Character count for create/edit publication forms
            const $createTitleInput = $('#create_publication_title');
            const $createDescriptionInput = $('#create_publication_description');
            const $editTitleInput = $('#edit_publication_title_modal');
            const $editDescriptionInput = $('#edit_publication_description_modal');

            updateCharCount($createTitleInput, $('#create-title-char-count'), 200);
            updateCharCount($createDescriptionInput, $('#create-description-char-count'), 500);
            updateCharCount($editTitleInput, $('#edit-title-char-count'), 200);
            updateCharCount($editDescriptionInput, $('#edit-description-char-count'), 500);

            $createTitleInput.on('input', function() {
                updateCharCount($(this), $('#create-title-char-count'), 200);
            });

            $createDescriptionInput.on('input', function() {
                updateCharCount($(this), $('#create-description-char-count'), 500);
            });

            $editTitleInput.on('input', function() {
                updateCharCount($(this), $('#edit-title-char-count'), 200);
            });

            $editDescriptionInput.on('input', function() {
                updateCharCount($(this), $('#edit-description-char-count'), 500);
            });

            // Table sorting
            $('#publications-table th').click(function() {
                let table = $(this).parents('table').eq(0);
                let rows = table.find('tbody tr').toArray().sort(comparer($(this).index()));
                let sortType = $(this).data('sort');
                let isAsc = $(this).hasClass('sorted-asc');
                
                table.find('th').removeClass('sorted-asc sorted-desc');
                $(this).addClass(isAsc ? 'sorted-desc' : 'sorted-asc');
                if (isAsc) rows = rows.reverse();
                
                for (let i = 0; i < rows.length; i++) table.find('tbody').append(rows[i]);
            });

            function comparer(index) {
                return function(a, b) {
                    let th = $('#publications-table th').eq(index);
                    let sortType = th.data('sort');
                    let valA = getCellValue(a, index, sortType);
                    let valB = getCellValue(b, index, sortType);
                    
                    if (sortType === 'int' || sortType === 'float') {
                        return valA - valB;
                    } else if (sortType === 'date') {
                        return new Date(valA) - new Date(valB);
                    } else {
                        return valA.localeCompare(valB);
                    }
                };
            }

            function getCellValue(row, index, sortType) {
                let cell = $(row).children('td').eq(index);
                let value = cell.data('sort-value') || cell.text().trim();
                if (sortType === 'int' || sortType === 'float') {
                    return parseFloat(value) || 0;
                } else if (sortType === 'date') {
                    return parseInt(value) || 0;
                } else {
                    return value.toLowerCase();
                }
            }

            // Edit button
            $('.edit-btn').click(function() {
                $('#edit_publication_id').val($(this).data('id'));
                $('#edit_publication_title_modal').val($(this).data('title'));
                $('#edit_publication_description_modal').val($(this).data('description'));
                $('#edit_publication_category_modal').val($(this).data('category'));
                $('#edit_publication_sort_order_modal').val($(this).data('sort'));
                let filename = $(this).data('filename');
                let originalFilename = $(this).data('original-filename');
                if (filename) {
                    $('#current_file_name_modal').text(originalFilename || filename);
                    $('#current_file_link_modal').attr('href', '<?php echo BASE_URL; ?>/images/publications/' + encodeURIComponent(filename)).show();
                    $('#current_file_container_modal').show();
                } else {
                    $('#current_file_name_modal').text('No file');
                    $('#current_file_link_modal').hide();
                    $('#current_file_container_modal').hide();
                }
                updateCharCount($editTitleInput, $('#edit-title-char-count'), 200);
                updateCharCount($editDescriptionInput, $('#edit-description-char-count'), 500);
                $('#editPublicationModal').modal('show');
            });

            // Delete button
            $('.delete-btn').click(function() {
                $('#delete_publication_id').val($(this).data('id'));
                $('#delete_publication_title').text($(this).data('title'));
                $('#deletePublicationModal').modal('show');
            });

            // Form validation
            $('#createPublicationForm, #editPublicationForm').submit(function(e) {
                let title = $(this).find('[name="publication_title"], [name="publication_title_update"]').val().trim();
                let fileInput = $(this).find('[name="publication_file"], [name="publication_file_update"]')[0];
                if (!title) {
                    alert('Title is required.');
                    e.preventDefault();
                    return;
                }
                if (fileInput && fileInput.files[0]) {
                    let file = fileInput.files[0];
                    let ext = file.name.split('.').pop().toLowerCase();
                    let allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar', 'jpg', 'png', 'gif', 'ppt', 'pptx'];
                    if (!allowed.includes(ext)) {
                        alert('Invalid file type. Allowed: PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP, RAR, JPG, PNG, GIF, PPT, PPTX.');
                        e.preventDefault();
                        return;
                    }
                    if (file.size > 10 * 1024 * 1024) {
                        alert('File too large. Max 10MB.');
                        e.preventDefault();
                        return;
                    }
                } else if ($(this).attr('id') === 'createPublicationForm') {
                    alert('File is required.');
                    e.preventDefault();
                    return;
                }
            });
        });
    </script>
</body>
</html>