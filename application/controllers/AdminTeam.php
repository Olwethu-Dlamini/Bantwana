<?php
// application/controllers/AdminTeam.php

class AdminTeam extends Controller {

    // Ensure user is logged in (implement your auth check logic)
    private function checkAuth() {
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
             http_response_code(401); // Unauthorized
             echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
             exit;
        }
    }

    public function index() {
        $this->checkAuth();

        // Load models
        $settingModel = $this->model('SettingModel');

        // --- Fetch Hero Section Settings ---
        $heroContentKeys = [
            'team_hero_title',
            'team_hero_subtitle',
            'team_hero_image'
        ];
        $teamHeroSettings = $settingModel->getMultiple($heroContentKeys, '');
        // Provide defaults if settings are not found
        $teamHeroSettings['team_hero_title'] = $teamHeroSettings['team_hero_title'] ?? 'Our Team';
        $teamHeroSettings['team_hero_subtitle'] = $teamHeroSettings['team_hero_subtitle'] ?? 'Meet the dedicated individuals driving our mission.';
        $teamHeroSettings['team_hero_image'] = $teamHeroSettings['team_hero_image'] ?? 'bg_team.jpg'; // Default image

        // Prepare data for the view
        $data = [
            'title' => 'Manage Team Page - Bantwana CMS',
            'currentPage' => 'team', // For highlighting nav link if you add one
            'teamHero' => $teamHeroSettings
        ];

        // Load the view
        $this->view('admin/team/index', $data);
    }

    /**
     * AJAX Endpoint: Update Team Hero Settings
     */
    public function updateHeroSettings() {
        $this->checkAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $settingModel = $this->model('SettingModel');

        // --- Handle Hero Image Upload ---
        $new_image_filename = null;
        if (isset($_FILES['team_hero_image']) && $_FILES['team_hero_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public_html/images/';
            $uploadFile = $_FILES['team_hero_image'];

            // Basic validation
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid Team Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.']);
                return;
            } elseif ($uploadFile['size'] > $maxFileSize) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Team Hero image file is too large. Maximum size is 5MB.']);
                return;
            } else {
                // Generate a unique filename
                $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                $new_image_filename = 'team_hero_' . uniqid() . '.' . strtolower($fileExtension);
                $targetPath = $uploadDir . $new_image_filename;

                if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                    // Successfully uploaded hero image
                    // Get old image filename to potentially delete later
                    $oldSettings = $settingModel->getMultiple(['team_hero_image'], '');
                    $old_image_filename = $oldSettings['team_hero_image'] ?? null;
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error uploading Team Hero image file.']);
                    return;
                }
            }
        } else if (isset($_FILES['team_hero_image']) && $_FILES['team_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
             http_response_code(400);
             echo json_encode(['status' => 'error', 'message' => 'Error uploading Team Hero image: ' . $_FILES['team_hero_image']['error']]);
             return;
        }
        // --- End Hero Image Upload ---

        // Get text data from POST
        $team_hero_title = trim($_POST['team_hero_title'] ?? '');
        $team_hero_subtitle = trim($_POST['team_hero_subtitle'] ?? '');

        // Basic validation
        if (empty($team_hero_title)) {
            // If image was uploaded but title is missing, delete the uploaded image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Team Hero Title is required.']);
            return;
        }

        // --- Save Settings ---
        $save_success = true;
        $save_success &= $settingModel->set('team_hero_title', $team_hero_title);
        $save_success &= $settingModel->set('team_hero_subtitle', $team_hero_subtitle);
        if ($new_image_filename !== null) {
            $save_success &= $settingModel->set('team_hero_image', $new_image_filename);
        }

        if ($save_success) {
            // If a new image was uploaded and saved successfully, delete the old one
            if ($new_image_filename !== null && !empty($old_image_filename) && $old_image_filename !== $new_image_filename) {
                $oldImagePath = $uploadDir . $old_image_filename;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            echo json_encode([
                'status' => 'success',
                'message' => 'Team page hero section updated successfully!',
                'data' => [
                    'team_hero_title' => $team_hero_title,
                    'team_hero_subtitle' => $team_hero_subtitle,
                    'team_hero_image' => $new_image_filename ?? ($settingModel->get('team_hero_image', 'bg_team.jpg'))
                ]
            ]);
            $this->logActivity("Admin {$_SESSION['username']} updated Team page hero section at " . date('Y-m-d H:i:s') . ($new_image_filename ? " (New image: $new_image_filename)" : ""));
        } else {
            // If saving failed but a new image was uploaded, delete the new image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'An error occurred while saving the Team page hero section content. Please try again.']);
        }
    }

    // --- Helper Function for Logging ---
    private function logActivity($message) {
        try {
            // Create a new DB connection for logging to avoid potential conflicts
            $logDb = new Database();
            $stmt = $logDb->pdo->prepare('INSERT INTO logs (timestamp, message) VALUES (NOW(), ?)');
            $stmt->execute([$message]);
        } catch (Exception $e) {
            error_log("CMS Admin Log Error: " . $e->getMessage());
        }
    }
}
?>