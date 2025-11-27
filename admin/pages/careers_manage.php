<?php
// admin/pages/careers_manage.php
require_once __DIR__ . '/../includes/init.php';

// --- Ensure Authentication ---
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    error_log("Authentication failed: Redirecting to login");
    header('Location: ../login.php');
    exit;
}

// --- Load Required Models ---
loadAdminClass('SettingModel');
loadAdminClass('JobModel');

// --- Initialize Message ---
$message = '';
$message_type = ''; // 'success' or 'error'

// --- Initialize Models ---
$settingModel = new SettingModel();
$jobModel = new JobModel();

// --- Handle Hero Section Updates ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_hero_content'])) {
    $new_image_filename = null;
    $uploadDir = BASE_PATH . '/public_html/images/';
    
    if (isset($_FILES['careers_hero_image']) && $_FILES['careers_hero_image']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $uploadFile = $_FILES['careers_hero_image'];
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
            if (!move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                $message = "Failed to upload hero image.";
                $message_type = 'error';
                error_log("Hero Image Upload: Failed to move file to $targetPath");
            } elseif (!$settingModel->set('careers_hero_image', $new_image_filename)) {
                unlink($targetPath);
                $message = "Failed to save hero image to database.";
                $message_type = 'error';
                error_log("Hero Image Update: Failed to save to database");
            }
        }
    }

    if ($message_type !== 'error') {
        $title = trim($_POST['careers_hero_title'] ?? '');
        $subtitle = trim($_POST['careers_hero_subtitle'] ?? '');
        if (empty($title)) {
            $message = "Hero title is required.";
            $message_type = 'error';
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            error_log("Hero Update: Title is empty");
        } else {
            $save_success = $settingModel->set('careers_hero_title', $title) &&
                            $settingModel->set('careers_hero_subtitle', $subtitle);
            if ($save_success) {
                $message = $message ?: "Hero section updated successfully.";
                $message_type = 'success';
                error_log("Hero Update: Successfully saved title and subtitle");
            } else {
                if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                    unlink($uploadDir . $new_image_filename);
                }
                $message = $message ?: "Failed to save hero content to database.";
                $message_type = 'error';
                error_log("Hero Update: Failed to save title or subtitle to database");
            }
        }
    }
}

// --- Handle Job Creation ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_job'])) {
    $title = trim($_POST['job_title'] ?? '');
    $location = trim($_POST['job_location'] ?? '');
    $type = trim($_POST['job_type'] ?? 'Full-time');
    $deadline = trim($_POST['job_deadline'] ?? '');
    $description = trim($_POST['job_description'] ?? '');
    $requirements = trim($_POST['job_requirements'] ?? '');
    $responsibilities = trim($_POST['job_responsibilities'] ?? '');
    $benefits = trim($_POST['job_benefits'] ?? '');
    $apply_link = trim($_POST['job_apply_link'] ?? '');
    $is_active = isset($_POST['job_is_active']) ? 1 : 0;
    $sort_order = intval($_POST['job_sort_order'] ?? 0);
    $userId = (int)($_SESSION['user_id'] ?? 0);

    if (empty($title) || empty($location) || empty($description)) {
        $message = "Job title, location, and description are required.";
        $message_type = 'error';
        error_log("Create Job: Missing required fields - Title: '$title', Location: '$location', Description: " . strlen($description));
    } elseif ($apply_link && !filter_var($apply_link, FILTER_VALIDATE_URL)) {
        $message = "Invalid application link. Must be a valid URL.";
        $message_type = 'error';
        error_log("Create Job: Invalid apply_link: '$apply_link'");
    } else {
        try {
            $newId = $jobModel->createJob($title, $location, $type, $deadline, $description, $requirements, $responsibilities, $benefits, $apply_link, $is_active, $sort_order, $userId);
            if ($newId) {
                $message = "Job '$title' created successfully.";
                $message_type = 'success';
                error_log("Create Job: Successfully created job ID $newId: '$title'");
            } else {
                $message = "Failed to create job in database.";
                $message_type = 'error';
                error_log("Create Job: Failed to create job: '$title'");
            }
        } catch (Exception $e) {
            $message = "Error creating job: " . $e->getMessage();
            $message_type = 'error';
            error_log("Create Job: Exception - " . $e->getMessage());
        }
    }
}

// --- Handle Job Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_job'])) {
    $jobId = intval($_POST['job_id'] ?? 0);
    $title = trim($_POST['job_title'] ?? '');
    $location = trim($_POST['job_location'] ?? '');
    $type = trim($_POST['job_type'] ?? 'Full-time');
    $deadline = trim($_POST['job_deadline'] ?? '');
    $description = trim($_POST['job_description'] ?? '');
    $requirements = trim($_POST['job_requirements'] ?? '');
    $responsibilities = trim($_POST['job_responsibilities'] ?? '');
    $benefits = trim($_POST['job_benefits'] ?? '');
    $apply_link = trim($_POST['job_apply_link'] ?? '');
    $is_active = isset($_POST['job_is_active']) ? 1 : 0;
    $sort_order = intval($_POST['job_sort_order'] ?? 0);

    if ($jobId <= 0) {
        $message = "Invalid job ID.";
        $message_type = 'error';
        error_log("Update Job: Invalid job ID: $jobId");
    } else {
        $existing = $jobModel->getJobById($jobId);
        if (!$existing) {
            $message = "Job not found.";
            $message_type = 'error';
            error_log("Update Job: Job ID $jobId not found");
        } elseif (empty($title) || empty($location) || empty($description)) {
            $message = "Job title, location, and description are required.";
            $message_type = 'error';
            error_log("Update Job: Missing required fields - Title: '$title', Location: '$location', Description: " . strlen($description));
        } elseif ($apply_link && !filter_var($apply_link, FILTER_VALIDATE_URL)) {
            $message = "Invalid application link. Must be a valid URL.";
            $message_type = 'error';
            error_log("Update Job: Invalid apply_link: '$apply_link'");
        } else {
            try {
                $success = $jobModel->updateJob($jobId, $title, $location, $type, $deadline, $description, $requirements, $responsibilities, $benefits, $apply_link, $is_active, $sort_order);
                if ($success) {
                    $message = "Job '$title' updated successfully.";
                    $message_type = 'success';
                    error_log("Update Job: Successfully updated job ID $jobId: '$title'");
                } else {
                    $message = "Failed to update job in database.";
                    $message_type = 'error';
                    error_log("Update Job: Failed to update job ID $jobId: '$title'");
                }
            } catch (Exception $e) {
                $message = "Error updating job: " . $e->getMessage();
                $message_type = 'error';
                error_log("Update Job: Exception - " . $e->getMessage());
            }
        }
    }
}

// --- Handle Job Deletion ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_job'])) {
    $jobId = intval($_POST['job_id'] ?? 0);

    if ($jobId <= 0) {
        $message = "Invalid job ID.";
        $message_type = 'error';
        error_log("Delete Job: Invalid job ID: $jobId");
    } else {
        $job = $jobModel->getJobById($jobId);
        if (!$job) {
            $message = "Job not found.";
            $message_type = 'error';
            error_log("Delete Job: Job ID $jobId not found");
        } else {
            try {
                if ($jobModel->deleteJob($jobId)) {
                    $message = "Job '{$job['title']}' deleted successfully.";
                    $message_type = 'success';
                    error_log("Delete Job: Successfully deleted job ID $jobId: '{$job['title']}'");
                } else {
                    $message = "Failed to delete job from database.";
                    $message_type = 'error';
                    error_log("Delete Job: Failed to delete job ID $jobId");
                }
            } catch (Exception $e) {
                $message = "Error deleting job: " . $e->getMessage();
                $message_type = 'error';
                error_log("Delete Job: Exception - " . $e->getMessage());
            }
        }
    }
}

// --- Handle Job Status Toggle ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_job_status'])) {
    $jobId = intval($_POST['job_id'] ?? 0);

    if ($jobId <= 0) {
        $message = "Invalid job ID.";
        $message_type = 'error';
        error_log("Toggle Job Status: Invalid job ID: $jobId");
    } else {
        $job = $jobModel->getJobById($jobId);
        if (!$job) {
            $message = "Job not found.";
            $message_type = 'error';
            error_log("Toggle Job Status: Job ID $jobId not found");
        } else {
            try {
                $newStatus = $job['is_active'] ? 0 : 1;
                if ($jobModel->toggleActiveStatus($jobId, $newStatus)) {
                    $message = "Job '{$job['title']}' " . ($newStatus ? 'activated' : 'deactivated') . " successfully.";
                    $message_type = 'success';
                    error_log("Toggle Job Status: Successfully toggled job ID $jobId to " . ($newStatus ? 'active' : 'inactive'));
                } else {
                    $message = "Failed to toggle job status.";
                    $message_type = 'error';
                    error_log("Toggle Job Status: Failed to toggle job ID $jobId");
                }
            } catch (Exception $e) {
                $message = "Error toggling job status: " . $e->getMessage();
                $message_type = 'error';
                error_log("Toggle Job Status: Exception - " . $e->getMessage());
            }
        }
    }
}

// --- Fetch Current Data ---
$heroContentKeys = [
    'careers_hero_title',
    'careers_hero_subtitle',
    'careers_hero_image'
];
$currentHeroSettings = $settingModel->getMultiple($heroContentKeys, '');
$currentHeroSettings['careers_hero_title'] = $currentHeroSettings['careers_hero_title'] ?? 'Build Your Career With Us';
$currentHeroSettings['careers_hero_subtitle'] = $currentHeroSettings['careers_hero_subtitle'] ?? 'Join a team passionate about creating lasting change for children and families.';
$currentHeroSettings['careers_hero_image'] = $currentHeroSettings['careers_hero_image'] ?? 'bg_1.jpg';

try {
    $jobs = $jobModel->getAllJobs();
    if ($jobs === false || $jobs === null) {
        $jobs = [];
        $message = $message ?: "Failed to fetch jobs from database.";
        $message_type = $message_type ?: 'error';
        error_log("Fetch Jobs: JobModel::getAllJobs returned false or null");
    }
} catch (Exception $e) {
    $jobs = [];
    $message = $message ?: "Error fetching jobs: " . $e->getMessage();
    $message_type = $message_type ?: 'error';
    error_log("Fetch Jobs: Exception - " . $e->getMessage());
}

$page_title = "Manage Careers - Bantwana CMS";
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
        table th { cursor: pointer; }
        table th:hover { background: #e9ecef; }
        .truncate { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        th.sorted-asc::after { content: ' ▲'; }
        th.sorted-desc::after { content: ' ▼'; }
        .status-active { color: #28a745; font-weight: bold; }
        .status-inactive { color: #dc3545; font-weight: bold; }
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
        .simple-textarea {
            min-height: 150px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .formatting-help {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 5px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Careers</h2>
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

        <div class="admin-container">
            <!-- Hero Section Preview -->
            <h4><i class="fas fa-eye mr-2"></i>Hero Section Preview</h4>
            <div class="hero-preview" id="hero-preview" style="background-image: url('<?php echo BASE_URL . '/images/' . htmlspecialchars($currentHeroSettings['careers_hero_image']); ?>');">
                <div class="hero-preview-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent justify-content-center p-0 mb-3">
                            <li class="breadcrumb-item text-white" aria-current="page">Careers</li>
                        </ol>
                    </nav>
                    <h1 id="hero-title-preview"><?php echo htmlspecialchars($currentHeroSettings['careers_hero_title']); ?></h1>
                    <p id="hero-subtitle-preview"><?php echo htmlspecialchars($currentHeroSettings['careers_hero_subtitle']); ?></p>
                </div>
            </div>

            <div class="section-header">
                <h4>Careers Page Hero Section</h4>
            </div>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="careers_hero_image">Hero Image:</label>
                    <input type="file" class="form-control-file" id="careers_hero_image" name="careers_hero_image" accept="image/*">
                    <small class="form-text text-muted">JPG, PNG, GIF, WEBP (max 5MB).</small>
                    <?php
                    $heroImage = $currentHeroSettings['careers_hero_image'];
                    $heroImagePath = BASE_URL . '/images/' . htmlspecialchars($heroImage);
                    $serverPath = BASE_PATH . '/public_html/images/' . $heroImage;
                    if (file_exists($serverPath) && $heroImage):
                        echo "<p class='mt-2'><strong>Current Image:</strong></p>";
                        echo "<img src='$heroImagePath' id='careers_hero_image_preview' alt='Hero Image' class='current-image-preview img-thumbnail'>";
                        echo "<p class='text-muted mt-1' id='careers_hero_image_name'>" . htmlspecialchars($heroImage) . "</p>";
                    else:
                        echo "<p class='mt-2 text-muted' id='careers_hero_image_name'>Current image: '" . htmlspecialchars($heroImage) . "' (not found).</p>";
                        echo "<img src='' id='careers_hero_image_preview' alt='Hero Image' class='current-image-preview img-thumbnail d-none'>";
                    endif;
                    ?>
                </div>
                <div class="form-group">
                    <label for="careers_hero_title">Hero Title * <span class="character-count" id="title-char-count">0/100</span></label>
                    <input type="text" class="form-control" id="careers_hero_title" name="careers_hero_title" value="<?php echo htmlspecialchars($currentHeroSettings['careers_hero_title']); ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="careers_hero_subtitle">Hero Subtitle <span class="character-count" id="subtitle-char-count">0/200</span></label>
                    <textarea class="form-control" id="careers_hero_subtitle" name="careers_hero_subtitle" rows="2" maxlength="200"><?php echo htmlspecialchars($currentHeroSettings['careers_hero_subtitle']); ?></textarea>
                </div>
                <button type="submit" name="save_hero_content" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Hero</button>
            </form>

            <div class="section-header d-flex justify-content-between align-items-center mt-5">
                <h4>Job Listings</h4>
                <button class="btn btn-success" data-toggle="modal" data-target="#createJobModal"><i class="fas fa-plus-circle mr-1"></i> Add Job</button>
            </div>

            <div id="jobs-list">
                <?php if (empty($jobs)): ?>
                    <p class="text-center text-muted">No jobs found.</p>
                <?php else: ?>
                    <table class="table table-bordered table-hover" id="jobs-table">
                        <thead class="thead-light">
                            <tr>
                                <th data-sort="int">ID</th>
                                <th data-sort="string">Title</th>
                                <th data-sort="string">Location</th>
                                <th data-sort="string">Type</th>
                                <th data-sort="date">Deadline</th>
                                <th data-sort="string">Status</th>
                                <th data-sort="int">Sort Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $job): ?>
                            <tr data-id="<?php echo $job['id']; ?>">
                                <td><?php echo htmlspecialchars($job['id']); ?></td>
                                <td class="truncate"><?php echo htmlspecialchars($job['title']); ?></td>
                                <td><?php echo htmlspecialchars($job['location']); ?></td>
                                <td><?php echo htmlspecialchars($job['type']); ?></td>
                                <td data-sort-value="<?php echo $job['deadline'] ? strtotime($job['deadline']) : '9999999999'; ?>">
                                    <?php echo !empty($job['deadline']) ? date('M j, Y', strtotime($job['deadline'])) : 'Open until filled'; ?>
                                </td>
                                <td data-sort-value="<?php echo $job['is_active']; ?>">
                                    <span class="<?php echo $job['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $job['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($job['sort_order']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary edit-btn"
                                            data-id="<?php echo $job['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($job['title']); ?>"
                                            data-location="<?php echo htmlspecialchars($job['location']); ?>"
                                            data-type="<?php echo htmlspecialchars($job['type']); ?>"
                                            data-deadline="<?php echo htmlspecialchars($job['deadline'] ?? ''); ?>"
                                            data-description="<?php echo htmlspecialchars($job['description']); ?>"
                                            data-requirements="<?php echo htmlspecialchars($job['requirements']); ?>"
                                            data-responsibilities="<?php echo htmlspecialchars($job['responsibilities']); ?>"
                                            data-benefits="<?php echo htmlspecialchars($job['benefits']); ?>"
                                            data-apply_link="<?php echo htmlspecialchars($job['apply_link']); ?>"
                                            data-is_active="<?php echo $job['is_active']; ?>"
                                            data-sort_order="<?php echo $job['sort_order']; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="<?php echo $job['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($job['title']); ?>">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                    <button class="btn btn-sm <?php echo $job['is_active'] ? 'btn-outline-warning' : 'btn-outline-success'; ?> toggle-status-btn"
                                            data-id="<?php echo $job['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($job['title']); ?>"
                                            data-is_active="<?php echo $job['is_active']; ?>">
                                        <i class="fas <?php echo $job['is_active'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                        <?php echo $job['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

   <!-- Create Job Modal -->
<div class="modal fade" id="createJobModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createJobForm" method="post" action="">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Job</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="create_job_title">Job Title *</label>
                        <input type="text" class="form-control" id="create_job_title" name="job_title" required maxlength="200">
                    </div>
                    <div class="form-group">
                        <label for="create_job_location">Location *</label>
                        <input type="text" class="form-control" id="create_job_location" name="job_location" value="Manzini, Eswatini" required>
                    </div>
                    <div class="form-group">
                        <label for="create_job_type">Job Type</label>
                        <select class="form-control" id="create_job_type" name="job_type">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Volunteer">Volunteer</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="create_job_deadline">Application Deadline</label>
                        <input type="date" class="form-control" id="create_job_deadline" name="job_deadline">
                        <small class="form-text text-muted">Leave blank if 'Open until filled'.</small>
                    </div>
                    <div class="form-group">
                        <label for="create_job_sort_order">Sort Order</label>
                        <input type="number" class="form-control" id="create_job_sort_order" name="job_sort_order" value="0" min="0">
                        <small class="form-text text-muted">Lower numbers appear first.</small>
                    </div>
                    <div class="form-group">
                        <label for="create_job_is_active">Active Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="create_job_is_active" name="job_is_active" value="1" checked>
                            <label class="form-check-label" for="create_job_is_active">Mark as Active (visible to public)</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="create_job_description">Job Description *</label>
                        <textarea class="form-control simple-textarea" id="create_job_description" name="job_description" required rows="6" placeholder="Enter the main job description here..."></textarea>
                        <div class="formatting-help">
                            <strong>Tip:</strong> Use line breaks to separate paragraphs. Text will be displayed as entered.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="create_job_requirements">Requirements</label>
                        <textarea class="form-control simple-textarea" id="create_job_requirements" name="job_requirements" rows="6" placeholder="e.g., Bachelor's degree in relevant field&#10;3+ years experience&#10;Strong communication skills"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="create_job_responsibilities">Responsibilities</label>
                        <textarea class="form-control simple-textarea" id="create_job_responsibilities" name="job_responsibilities" rows="6" placeholder="e.g., Lead project initiatives&#10;Manage team members&#10;Report to senior management"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="create_job_benefits">Benefits</label>
                        <textarea class="form-control simple-textarea" id="create_job_benefits" name="job_benefits" rows="6" placeholder="e.g., Competitive salary&#10;Health insurance&#10;Professional development opportunities"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="create_job_apply_link">External Application Link</label>
                        <input type="url" class="form-control" id="create_job_apply_link" name="job_apply_link" placeholder="https://example.com/apply">
                        <small class="form-text text-muted">Optional: Link to an external application form or job posting.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_job" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Create Job</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Job Modal -->
<div class="modal fade" id="editJobModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editJobForm" method="post" action="">
                <input type="hidden" id="edit_job_id" name="job_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Job</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_job_title_modal">Job Title *</label>
                        <input type="text" class="form-control" id="edit_job_title_modal" name="job_title" required maxlength="200">
                    </div>
                    <div class="form-group">
                        <label for="edit_job_location_modal">Location *</label>
                        <input type="text" class="form-control" id="edit_job_location_modal" name="job_location" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_job_type_modal">Job Type</label>
                        <select class="form-control" id="edit_job_type_modal" name="job_type">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Volunteer">Volunteer</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_job_deadline_modal">Application Deadline</label>
                        <input type="date" class="form-control" id="edit_job_deadline_modal" name="job_deadline">
                        <small class="form-text text-muted">Leave blank if 'Open until filled'.</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_job_sort_order_modal">Sort Order</label>
                        <input type="number" class="form-control" id="edit_job_sort_order_modal" name="job_sort_order" value="0" min="0">
                        <small class="form-text text-muted">Lower numbers appear first.</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_job_is_active_modal">Active Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_job_is_active_modal" name="job_is_active" value="1">
                            <label class="form-check-label" for="edit_job_is_active_modal">Mark as Active (visible to public)</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_job_description_modal">Job Description *</label>
                        <textarea class="form-control simple-textarea" id="edit_job_description_modal" name="job_description" required rows="6"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_job_requirements_modal">Requirements</label>
                        <textarea class="form-control simple-textarea" id="edit_job_requirements_modal" name="job_requirements" rows="6"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_job_responsibilities_modal">Responsibilities</label>
                        <textarea class="form-control simple-textarea" id="edit_job_responsibilities_modal" name="job_responsibilities" rows="6"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_job_benefits_modal">Benefits</label>
                        <textarea class="form-control simple-textarea" id="edit_job_benefits_modal" name="job_benefits" rows="6"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_job_apply_link_modal">External Application Link</label>
                        <input type="url" class="form-control" id="edit_job_apply_link_modal" name="job_apply_link" placeholder="https://example.com/apply">
                        <small class="form-text text-muted">Optional: Link to an external application form or job posting.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_job" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update Job</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Job Modal -->
<div class="modal fade" id="deleteJobModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteJobForm" method="post" action="">
                <input type="hidden" id="delete_job_id" name="job_id">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the job <strong id="delete_job_title"></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_job" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toggle Status Modal -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="toggleStatusForm" method="post" action="">
                <input type="hidden" id="toggle_job_id" name="job_id">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Status Toggle</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Change the status of job <strong id="toggle_job_title"></strong>?</p>
                    <p>This will <strong id="toggle_action"></strong> the job listing.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="toggle_job_status" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Confirm</button>
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
            } else {
                counter.removeClass('warning');
            }
        }

        const $titleInput = $('#careers_hero_title');
        const $subtitleInput = $('#careers_hero_subtitle');
        updateCharCount($titleInput, $('#title-char-count'), 100);
        updateCharCount($subtitleInput, $('#subtitle-char-count'), 200);

        $titleInput.on('input', function() {
            updateCharCount($(this), $('#title-char-count'), 100);
            $('#hero-title-preview').text($(this).val() || 'Build Your Career With Us');
        });

        $subtitleInput.on('input', function() {
            updateCharCount($(this), $('#subtitle-char-count'), 200);
            $('#hero-subtitle-preview').text($(this).val() || 'Join a team passionate about creating lasting change for children and families.');
        });

        // Real-time image preview
        $('#careers_hero_image').change(function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                const preview = $('#careers_hero_image_preview');
                const nameElement = $('#careers_hero_image_name');
                const heroPreview = $('#hero-preview');
                const file = this.files[0];

                reader.onload = function(e) {
                    preview.attr('src', e.target.result).removeClass('d-none');
                    nameElement.text(file.name);
                    heroPreview.css('background-image', `url(${e.target.result})`);
                };

                reader.readAsDataURL(file);
            }
        });

        // Table sorting
        $('#jobs-table th').click(function() {
            let table = $(this).parents('table').eq(0);
            let rows = table.find('tbody tr').toArray().sort(comparer($(this).index()));
            let isAsc = $(this).hasClass('sorted-asc');
            
            table.find('th').removeClass('sorted-asc sorted-desc');
            $(this).addClass(isAsc ? 'sorted-desc' : 'sorted-asc');
            if (isAsc) rows = rows.reverse();
            
            for (let i = 0; i < rows.length; i++) {
                table.find('tbody').append(rows[i]);
            }
        });

        function comparer(index) {
            return function(a, b) {
                let th = $('#jobs-table th').eq(index);
                let sortType = th.data('sort');
                let valA = getCellValue(a, index, sortType);
                let valB = getCellValue(b, index, sortType);
                
                if (sortType === 'int') {
                    return valA - valB;
                } else if (sortType === 'date') {
                    return valA - valB;
                } else {
                    return valA.localeCompare(valB);
                }
            };
        }

        function getCellValue(row, index, sortType) {
            let cell = $(row).children('td').eq(index);
            let value = cell.data('sort-value') !== undefined ? cell.data('sort-value') : cell.text().trim();
            
            if (sortType === 'int') {
                return parseInt(value) || 0;
            } else if (sortType === 'date') {
                return parseInt(value) || 9999999999;
            } else {
                return String(value).toLowerCase();
            }
        }

        // Edit button click handler
        $(document).on('click', '.edit-btn', function() {
            const btn = $(this);
            $('#edit_job_id').val(btn.data('id'));
            $('#edit_job_title_modal').val(btn.data('title'));
            $('#edit_job_location_modal').val(btn.data('location'));
            $('#edit_job_type_modal').val(btn.data('type'));
            $('#edit_job_deadline_modal').val(btn.data('deadline') || '');
            $('#edit_job_sort_order_modal').val(btn.data('sort_order'));
            $('#edit_job_is_active_modal').prop('checked', btn.data('is_active') == 1);
            $('#edit_job_description_modal').val(btn.data('description') || '');
            $('#edit_job_requirements_modal').val(btn.data('requirements') || '');
            $('#edit_job_responsibilities_modal').val(btn.data('responsibilities') || '');
            $('#edit_job_benefits_modal').val(btn.data('benefits') || '');
            $('#edit_job_apply_link_modal').val(btn.data('apply_link') || '');
            $('#editJobModal').modal('show');
        });

        // Delete button click handler
        $(document).on('click', '.delete-btn', function() {
            const btn = $(this);
            $('#delete_job_id').val(btn.data('id'));
            $('#delete_job_title').text(btn.data('title'));
            $('#deleteJobModal').modal('show');
        });

        // Toggle status button click handler
        $(document).on('click', '.toggle-status-btn', function() {
            const btn = $(this);
            const isActive = btn.data('is_active');
            $('#toggle_job_id').val(btn.data('id'));
            $('#toggle_job_title').text(btn.data('title'));
            $('#toggle_action').text(isActive == 1 ? 'deactivate' : 'activate');
            $('#toggleStatusModal').modal('show');
        });

        // Form validation for create and edit forms
        $('#createJobForm, #editJobForm').on('submit', function(e) {
            const form = $(this);
            const title = form.find('[name="job_title"]').val().trim();
            const location = form.find('[name="job_location"]').val().trim();
            const description = form.find('[name="job_description"]').val().trim();
            const applyLink = form.find('[name="job_apply_link"]').val().trim();
            
            // Validate required fields
            if (!title) {
                alert('Job title is required.');
                e.preventDefault();
                return false;
            }
            
            if (!location) {
                alert('Job location is required.');
                e.preventDefault();
                return false;
            }
            
            if (!description) {
                alert('Job description is required.');
                e.preventDefault();
                return false;
            }
            
            // Validate URL if provided
            if (applyLink && !applyLink.match(/^https?:\/\/.+/)) {
                alert('External application link must be a valid URL starting with http:// or https://');
                e.preventDefault();
                return false;
            }
            
            // All validation passed
            return true;
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
</body>
</html>