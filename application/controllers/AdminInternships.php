<?php
class AdminInternships extends Controller {

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
        header('Location: ' . BASE_URL . '/admin/pages/internship_edit.php');
        exit;
    }

    public function getHeroSettings() {
        $this->checkAuth();
        header('Content-Type: application/json');

        try {
            $internshipModel = $this->model('InternshipModel');
            $heroKeys = ['internships_hero_title', 'internships_hero_subtitle', 'internships_hero_image'];
            $heroSettings = $internshipModel->getHeroSettings($heroKeys, '');

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

        $internshipModel = $this->model('InternshipModel');
        $updated_fields = [];
        $save_success = true;

        // Handle Hero Image Upload
        if (isset($_FILES['internships_hero_image']) && $_FILES['internships_hero_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public_html/images/';
            $uploadFile = $_FILES['internships_hero_image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
            $maxFileSize = 5 * 1024 * 1024;

            if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to upload Internships hero image: Invalid file type at " . date('Y-m-d H:i:s'));
                return;
            } elseif ($uploadFile['size'] > $maxFileSize) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Hero image file is too large. Maximum size is 5MB.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to upload Internships hero image: File too large at " . date('Y-m-d H:i:s'));
                return;
            } else {
                $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                $new_image_filename = 'internships_hero_' . uniqid() . '.' . strtolower($fileExtension);
                $targetPath = $uploadDir . $new_image_filename;

                if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                    if ($internshipModel->setHeroSetting('internships_hero_image', $new_image_filename)) {
                        $updated_fields['internships_hero_image'] = $new_image_filename;
                        $this->logActivity("Admin {$_SESSION['username']} uploaded new Internships hero image: $new_image_filename at " . date('Y-m-d H:i:s'));
                    } else {
                        unlink($targetPath);
                        http_response_code(500);
                        echo json_encode(['status' => 'error', 'message' => 'Hero image uploaded, but failed to update database setting.']);
                        $this->logActivity("Admin {$_SESSION['username']} failed to update Internships hero image setting in DB at " . date('Y-m-d H:i:s'));
                        return;
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error uploading Internships hero image file.']);
                    $this->logActivity("Admin {$_SESSION['username']} failed to upload Internships hero image file at " . date('Y-m-d H:i:s'));
                    return;
                }
            }
        } elseif (isset($_FILES['internships_hero_image']) && $_FILES['internships_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Error uploading Internships hero image: ' . $_FILES['internships_hero_image']['error']]);
            $this->logActivity("Admin {$_SESSION['username']} encountered upload error for Internships hero image: " . $_FILES['internships_hero_image']['error'] . " at " . date('Y-m-d H:i:s'));
            return;
        }

        // Handle Text Content
        $internships_hero_title = trim($_POST['internships_hero_title'] ?? '');
        $internships_hero_subtitle = trim($_POST['internships_hero_subtitle'] ?? '');

        if (!empty($internships_hero_title)) {
            if ($internshipModel->setHeroSetting('internships_hero_title', $internships_hero_title)) {
                $updated_fields['internships_hero_title'] = $internships_hero_title;
            } else {
                $save_success = false;
            }
        }
        if (!empty($internships_hero_subtitle)) {
            if ($internshipModel->setHeroSetting('internships_hero_subtitle', $internships_hero_subtitle)) {
                $updated_fields['internships_hero_subtitle'] = $internships_hero_subtitle;
            } else {
                $save_success = false;
            }
        }

        if (empty($updated_fields)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No valid data provided to update.']);
            $this->logActivity("Admin {$_SESSION['username']} failed to update Internships hero content: No valid data provided at " . date('Y-m-d H:i:s'));
            return;
        }

        if ($save_success) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Internships page hero content updated successfully!',
                'data' => $updated_fields
            ]);
            $this->logActivity("Admin {$_SESSION['username']} updated Internships page hero content: " . json_encode($updated_fields) . " at " . date('Y-m-d H:i:s'));
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'An error occurred while saving the hero content.']);
            $this->logActivity("Admin {$_SESSION['username']} failed to update Internships page hero content at " . date('Y-m-d H:i:s'));
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