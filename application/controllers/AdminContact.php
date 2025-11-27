<?php
class AdminContact extends Controller {

    private function checkAuth() {
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized: Please log in']);
            exit;
        }
    }

    public function index() {
        $this->checkAuth();
        $contactModel = $this->model('ContactModel');
        $heroKeys = ['contact_hero_title', 'contact_hero_subtitle', 'contact_hero_image'];
        try {
            $data = [
                'title' => 'Manage Contact Page - Bantwana Initiative Eswatini',
                'contactHero' => $contactModel->getHeroSettings($heroKeys, '')
            ];
            error_log("AdminContact::index: Loaded settings: " . json_encode($data['contactHero']));
            $this->view('admin/contact_manage', $data);
        } catch (Exception $e) {
            error_log("AdminContact::index failed: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to load hero settings: ' . $e->getMessage()]);
        }
    }

    public function updateHeroSettings() {
        $this->checkAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method: POST required']);
            $this->logActivity("Admin {$_SESSION['username']} attempted invalid request method");
            return;
        }

        error_log("AdminContact::updateHeroSettings: POST data: " . json_encode($_POST));
        error_log("AdminContact::updateHeroSettings: FILES data: " . json_encode($_FILES));

        $contactModel = $this->model('ContactModel');
        $hero_data = [
            'contact_hero_title' => trim($_POST['contact_hero_title'] ?? ''),
            'contact_hero_subtitle' => trim($_POST['contact_hero_subtitle'] ?? ''),
            'contact_hero_image' => $contactModel->getHeroSettings(['contact_hero_image'], 'bg_7.jpg')['contact_hero_image']
        ];

        $errors = [];
        if (empty($hero_data['contact_hero_title'])) {
            $errors[] = 'Hero Title is required';
        }

        // Handle image upload
        if (isset($_FILES['contact_hero_image']) && $_FILES['contact_hero_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public_html/images/';
            if (!is_writable($uploadDir)) {
                $errors[] = 'Image upload directory is not writable';
                error_log("AdminContact::updateHeroSettings: Directory not writable: $uploadDir");
            } else {
                $fileExtension = strtolower(pathinfo($_FILES['contact_hero_image']['name'], PATHINFO_EXTENSION));
                $new_image_filename = 'contact_hero_' . uniqid() . '.' . $fileExtension;
                $targetPath = $uploadDir . $new_image_filename;

                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array($fileExtension, $allowedTypes)) {
                    $errors[] = 'Invalid image format: Only JPG, JPEG, PNG, GIF, WEBP allowed';
                } elseif ($_FILES['contact_hero_image']['size'] > 5 * 1024 * 1024) {
                    $errors[] = 'Image size exceeds 5MB limit';
                } elseif (!move_uploaded_file($_FILES['contact_hero_image']['tmp_name'], $targetPath)) {
                    $errors[] = 'Failed to upload image';
                    error_log("AdminContact::updateHeroSettings: Image upload failed: " . json_encode($_FILES['contact_hero_image']));
                } else {
                    $hero_data['contact_hero_image'] = $new_image_filename;
                    error_log("AdminContact::updateHeroSettings: Image uploaded: $new_image_filename");
                }
            }
        } elseif (isset($_FILES['contact_hero_image']) && $_FILES['contact_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Image upload error: Code ' . $_FILES['contact_hero_image']['error'];
            error_log("AdminContact::updateHeroSettings: Image upload error: " . $_FILES['contact_hero_image']['error']);
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => implode('; ', $errors)]);
            $this->logActivity("Admin {$_SESSION['username']} failed to update contact hero settings: " . implode('; ', $errors));
            return;
        }

        try {
            $success = true;
            $failed_keys = [];
            foreach ($hero_data as $key => $value) {
                try {
                    if (!$contactModel->setHeroSetting($key, $value)) {
                        $success = false;
                        $failed_keys[] = $key;
                        error_log("AdminContact::updateHeroSettings: Failed to update $key");
                    }
                } catch (Exception $e) {
                    $success = false;
                    $failed_keys[] = $key;
                    error_log("AdminContact::updateHeroSettings: Exception updating $key: " . $e->getMessage());
                }
            }

            if ($success) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Contact hero section updated successfully',
                    'data' => $hero_data
                ]);
                $this->logActivity("Admin {$_SESSION['username']} updated contact hero settings: " . json_encode($hero_data));
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to update settings: ' . implode(', ', $failed_keys)]);
                $this->logActivity("Admin {$_SESSION['username']} failed to update contact hero settings: Failed keys: " . implode(', ', $failed_keys));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
            $this->logActivity("Admin {$_SESSION['username']} failed to update contact hero settings: " . $e->getMessage());
        }
    }

    private function logActivity($message) {
        try {
            $logDb = new Database();
            $stmt = $logDb->pdo->prepare('INSERT INTO logs (timestamp, message) VALUES (NOW(), ?)');
            $stmt->execute([$message]);
            error_log("AdminContact::logActivity: Logged: $message");
        } catch (PDOException $e) {
            error_log("AdminContact::logActivity failed: " . $e->getMessage());
        }
    }
}
?>