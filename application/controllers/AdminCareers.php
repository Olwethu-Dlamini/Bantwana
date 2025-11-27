<?php
class AdminCareers extends Controller {

    private function checkAuth() {
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
    }

    public function index() {
        $this->checkAuth();
        header('Location: ' . BASE_URL . '/admin/pages/careers_manage.php');
        exit;
    }

    public function getAllJobs() {
        $this->checkAuth();
        header('Content-Type: application/json');

        try {
            $jobModel = $this->model('JobModel');
            $jobs = $jobModel->getAllJobs();
            echo json_encode(['status' => 'success', 'data' => $jobs]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch jobs: ' . $e->getMessage()]);
        }
    }

    public function createJob() {
        $this->checkAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $jobModel = $this->model('JobModel');
        $job_data = [
            'title' => trim($_POST['job_title'] ?? ''),
            'location' => trim($_POST['job_location'] ?? ''),
            'type' => trim($_POST['job_type'] ?? 'Full-time'),
            'deadline' => !empty($_POST['job_deadline']) ? trim($_POST['job_deadline']) : null,
            'description' => trim($_POST['job_description'] ?? ''),
            'requirements' => trim($_POST['job_requirements'] ?? ''),
            'responsibilities' => trim($_POST['job_responsibilities'] ?? ''),
            'benefits' => trim($_POST['job_benefits'] ?? ''),
            'apply_link' => trim($_POST['job_apply_link'] ?? ''),
            'is_active' => isset($_POST['job_is_active']) ? 1 : 0,
            'sort_order' => intval($_POST['job_sort_order'] ?? 0)
        ];

        $errors = [];
        if (empty($job_data['title'])) {
            $errors[] = "Job Title is required.";
        }
        if (empty($job_data['location'])) {
            $errors[] = "Job Location is required.";
        }
        if (empty($job_data['description'])) {
            $errors[] = "Job Description is required.";
        }

        if (empty($errors)) {
            $newId = $jobModel->createJob($job_data);
            if ($newId) {
                $newJob = $jobModel->getJobById($newId);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Job \'' . htmlspecialchars($job_data['title']) . '\' created successfully!',
                    'data' => $newJob
                ]);
                $this->logActivity("Admin {$_SESSION['username']} created job: {$job_data['title']} (ID: $newId)");
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to create job in database.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to create job: {$job_data['title']} at " . date('Y-m-d H:i:s'));
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => implode('<br>', $errors)]);
            $this->logActivity("Admin {$_SESSION['username']} failed job creation validation: " . implode(', ', $errors) . " at " . date('Y-m-d H:i:s'));
        }
    }

    public function updateJob() {
        $this->checkAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $jobModel = $this->model('JobModel');
        $job_id = intval($_POST['job_id'] ?? 0);

        if ($job_id <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid job ID for update.']);
            return;
        }

        $job_data = [
            'title' => trim($_POST['job_title'] ?? ''),
            'location' => trim($_POST['job_location'] ?? ''),
            'type' => trim($_POST['job_type'] ?? 'Full-time'),
            'deadline' => !empty($_POST['job_deadline']) ? trim($_POST['job_deadline']) : null,
            'description' => trim($_POST['job_description'] ?? ''),
            'requirements' => trim($_POST['job_requirements'] ?? ''),
            'responsibilities' => trim($_POST['job_responsibilities'] ?? ''),
            'benefits' => trim($_POST['job_benefits'] ?? ''),
            'apply_link' => trim($_POST['job_apply_link'] ?? ''),
            'is_active' => isset($_POST['job_is_active']) ? 1 : 0,
            'sort_order' => intval($_POST['job_sort_order'] ?? 0)
        ];

        $errors = [];
        if (empty($job_data['title'])) {
            $errors[] = "Job Title is required for update.";
        }
        if (empty($job_data['location'])) {
            $errors[] = "Job Location is required for update.";
        }
        if (empty($job_data['description'])) {
            $errors[] = "Job Description is required for update.";
        }

        if (empty($errors)) {
            $success = $jobModel->updateJob($job_id, $job_data);
            if ($success) {
                $updatedJob = $jobModel->getJobById($job_id);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Job \'' . htmlspecialchars($job_data['title']) . '\' updated successfully!',
                    'data' => $updatedJob
                ]);
                $this->logActivity("Admin {$_SESSION['username']} updated job ID: $job_id (Title: {$job_data['title']})");
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to update job in database.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to update job ID: $job_id at " . date('Y-m-d H:i:s'));
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => implode('<br>', $errors)]);
            $this->logActivity("Admin {$_SESSION['username']} failed job update validation: " . implode(', ', $errors) . " at " . date('Y-m-d H:i:s'));
        }
    }

    public function deleteJob() {
        $this->checkAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $jobModel = $this->model('JobModel');
        $job_id = intval($_POST['id'] ?? 0);

        if ($job_id <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid job ID for deletion.']);
            return;
        }

        $jobToDelete = $jobModel->getJobById($job_id);
        if (!$jobToDelete) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Job not found for deletion.']);
            return;
        }

        $title = $jobToDelete['title'];
        if ($jobModel->deleteJob($job_id)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Job \'' . htmlspecialchars($title) . '\' deleted successfully!'
            ]);
            $this->logActivity("Admin {$_SESSION['username']} deleted job: $title (ID: $job_id)");
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete job.']);
            $this->logActivity("Admin {$_SESSION['username']} failed to delete job ID: $job_id at " . date('Y-m-d H:i:s'));
        }
    }

    public function toggleActiveStatus() {
        $this->checkAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $jobModel = $this->model('JobModel');
        $job_id = intval($_POST['id'] ?? 0);

        if ($job_id <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid job ID for status toggle.']);
            return;
        }

        $jobToToggle = $jobModel->getJobById($job_id);
        if (!$jobToToggle) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Job not found for status toggle.']);
            return;
        }

        $title = $jobToToggle['title'];
        $currentStatus = $jobToToggle['is_active'];
        if ($jobModel->toggleActiveStatus($job_id)) {
            $newStatus = $currentStatus ? 0 : 1;
            $statusText = $newStatus ? 'activated' : 'deactivated';
            echo json_encode([
                'status' => 'success',
                'message' => "Job '$title' has been $statusText.",
                'data' => ['id' => $job_id, 'is_active' => $newStatus]
            ]);
            $this->logActivity("Admin {$_SESSION['username']} toggled job status: $title (ID: $job_id) to " . ($newStatus ? 'Active' : 'Inactive') . " at " . date('Y-m-d H:i:s'));
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to toggle job status.']);
            $this->logActivity("Admin {$_SESSION['username']} failed to toggle job status ID: $job_id at " . date('Y-m-d H:i:s'));
        }
    }

    public function updateHeroSettings() {
        $this->checkAuth();
        header('Content-Type: application/json');
        error_log("POST Data: " . print_r($_POST, true));
        error_log("FILES Data: " . print_r($_FILES, true));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $settingModel = $this->model('SettingModel');
        $updated_fields = [];
        $save_success = true;

        // Handle Hero Image Upload
        if (isset($_FILES['careers_hero_image']) && $_FILES['careers_hero_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public_html/images/';
            $uploadFile = $_FILES['careers_hero_image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
            $maxFileSize = 5 * 1024 * 1024;

            if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to upload Careers hero image: Invalid file type at " . date('Y-m-d H:i:s'));
                return;
            } elseif ($uploadFile['size'] > $maxFileSize) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Hero image file is too large. Maximum size is 5MB.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to upload Careers hero image: File too large at " . date('Y-m-d H:i:s'));
                return;
            } else {
                $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                $new_image_filename = 'careers_hero_' . uniqid() . '.' . strtolower($fileExtension);
                $targetPath = $uploadDir . $new_image_filename;

                if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                    if ($settingModel->set('careers_hero_image', $new_image_filename)) {
                        $updated_fields['careers_hero_image'] = $new_image_filename;
                        $this->logActivity("Admin {$_SESSION['username']} uploaded new Careers hero image: $new_image_filename at " . date('Y-m-d H:i:s'));
                    } else {
                        unlink($targetPath);
                        http_response_code(500);
                        echo json_encode(['status' => 'error', 'message' => 'Hero image uploaded, but failed to update database setting.']);
                        $this->logActivity("Admin {$_SESSION['username']} failed to update Careers hero image setting in DB at " . date('Y-m-d H:i:s'));
                        return;
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error uploading Careers hero image file.']);
                    $this->logActivity("Admin {$_SESSION['username']} failed to upload Careers hero image file at " . date('Y-m-d H:i:s'));
                    return;
                }
            }
        } elseif (isset($_FILES['careers_hero_image']) && $_FILES['careers_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Error uploading Careers hero image: ' . $_FILES['careers_hero_image']['error']]);
            $this->logActivity("Admin {$_SESSION['username']} encountered upload error for Careers hero image: " . $_FILES['careers_hero_image']['error'] . " at " . date('Y-m-d H:i:s'));
            return;
        }

        // Handle Text Content
        $careers_hero_title = trim($_POST['careers_hero_title'] ?? '');
        $careers_hero_subtitle = trim($_POST['careers_hero_subtitle'] ?? '');

        if (!empty($careers_hero_title)) {
            if ($settingModel->set('careers_hero_title', $careers_hero_title)) {
                $updated_fields['careers_hero_title'] = $careers_hero_title;
            } else {
                $save_success = false;
            }
        }
        if (!empty($careers_hero_subtitle)) {
            if ($settingModel->set('careers_hero_subtitle', $careers_hero_subtitle)) {
                $updated_fields['careers_hero_subtitle'] = $careers_hero_subtitle;
            } else {
                $save_success = false;
            }
        }

        if (empty($updated_fields)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No valid data provided to update.']);
            $this->logActivity("Admin {$_SESSION['username']} failed to update Careers hero content: No valid data provided at " . date('Y-m-d H:i:s'));
            return;
        }

        if ($save_success) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Careers page hero content updated successfully!',
                'data' => $updated_fields
            ]);
            $this->logActivity("Admin {$_SESSION['username']} updated Careers page hero content: " . json_encode($updated_fields) . " at " . date('Y-m-d H:i:s'));
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'An error occurred while saving the hero content.']);
            $this->logActivity("Admin {$_SESSION['username']} failed to update Careers page hero content at " . date('Y-m-d H:i:s'));
        }
    }

    public function getHeroSettings() {
        $this->checkAuth();
        header('Content-Type: application/json');

        try {
            $settingModel = $this->model('SettingModel');
            $heroContentKeys = ['careers_hero_title', 'careers_hero_subtitle', 'careers_hero_image'];
            $heroSettings = $settingModel->getMultiple($heroContentKeys, '');
            echo json_encode([
                'status' => 'success',
                'data' => $heroSettings
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to fetch hero settings: ' . $e->getMessage()
            ]);
        }
    }

    private function logActivity($message) {
        try {
            $logDb = new Database();
            $stmt = $logDb->pdo->prepare('INSERT INTO logs (timestamp, message) VALUES (NOW(), ?)');
            $stmt->execute([$message]);
        } catch (Exception $e) {
            error_log("CMS Admin Log Error: " . $e->getMessage());
        }
    }
}