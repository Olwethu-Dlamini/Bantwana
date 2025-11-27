<?php
// application/controllers/AdminPartner.php

class AdminPartner extends Controller {

    // Ensure user is logged in
    private function checkAuth() {
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
             http_response_code(401); // Unauthorized
             echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
             exit;
        }
    }

    public function index() {
         // Redirect to the edit page or show dashboard
         $this->checkAuth();
         header('Location: ' . BASE_URL . '/admin/pages/partner_edit.php');
         exit;
    }

    /**
     * AJAX Endpoint: Get Partner Page Hero Settings
     */
    public function getHeroSettings() {
        $this->checkAuth();
        header('Content-Type: application/json');

        try {
            $partnerModel = $this->model('PartnerModel');
            $heroKeys = [
                'partner_hero_title',
                'partner_hero_subtitle',
                'partner_hero_image'
            ];
            $heroSettings = $partnerModel->getHeroSettings($heroKeys, '');

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

    /**
     * AJAX Endpoint: Update Partner Page Hero Settings (including image upload)
     */
    public function updateHeroSettings() {
        $this->checkAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $partnerModel = $this->model('PartnerModel');

        // --- Handle Hero Image Upload ---
        $new_image_filename = null;
        if (isset($_FILES['partner_hero_image']) && $_FILES['partner_hero_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public_html/images/';
            $uploadFile = $_FILES['partner_hero_image'];

            // Basic validation
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to upload Partner hero image: Invalid file type at " . date('Y-m-d H:i:s'));
                return;
            } elseif ($uploadFile['size'] > $maxFileSize) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Hero image file is too large. Maximum size is 5MB.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to upload Partner hero image: File too large at " . date('Y-m-d H:i:s'));
                return;
            } else {
                // Generate a unique filename
                $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                $new_image_filename = 'partner_hero_' . uniqid() . '.' . strtolower($fileExtension);
                $targetPath = $uploadDir . $new_image_filename;

                if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                    // Successfully uploaded hero image
                    if ($partnerModel->setHeroSetting('partner_hero_image', $new_image_filename)) {
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Partner page hero image uploaded successfully!',
                            'data' => ['partner_hero_image' => $new_image_filename] // Return new filename
                        ]);
                        $this->logActivity("Admin {$_SESSION['username']} uploaded new Partner hero image: $new_image_filename at " . date('Y-m-d H:i:s'));
                        // Optional: Delete old image file if a new one was uploaded
                        // $oldImage = $partnerModel->getHeroSettings(['partner_hero_image'], 'bg_5.jpg')['partner_hero_image'];
                        // if ($oldImage !== 'bg_5.jpg' && file_exists($uploadDir . $oldImage)) {
                        //     unlink($uploadDir . $oldImage);
                        // }
                    } else {
                        // If DB update failed, delete the uploaded image
                        if (file_exists($uploadDir . $new_image_filename)) {
                            unlink($uploadDir . $new_image_filename);
                        }
                        http_response_code(500);
                        echo json_encode(['status' => 'error', 'message' => 'Hero image uploaded, but failed to update database setting.']);
                        $this->logActivity("Admin {$_SESSION['username']} failed to update Partner hero image setting in DB after upload at " . date('Y-m-d H:i:s'));
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error uploading Partner hero image file.']);
                    $this->logActivity("Admin {$_SESSION['username']} failed to upload Partner hero image file at " . date('Y-m-d H:i:s'));
                }
            }
        } else if (isset($_FILES['partner_hero_image']) && $_FILES['partner_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
             // An upload error occurred (but not "no file")
             http_response_code(400);
             echo json_encode(['status' => 'error', 'message' => 'Error uploading Partner hero image: ' . $_FILES['partner_hero_image']['error']]);
             $this->logActivity("Admin {$_SESSION['username']} encountered upload error for Partner hero image: " . $_FILES['partner_hero_image']['error'] . " at " . date('Y-m-d H:i:s'));
             return;
        }
        // --- End Hero Image Upload ---

        // --- Handle Text Content Saving (only if no critical image upload error) ---
        if ($new_image_filename === null || (isset($_FILES['partner_hero_image']) && $_FILES['partner_hero_image']['error'] === UPLOAD_ERR_OK)) {
            // Get text data from POST
            $partner_hero_title = trim($_POST['partner_hero_title'] ?? '');
            $partner_hero_subtitle = trim($_POST['partner_hero_subtitle'] ?? '');

            // Basic validation
            $errors = [];
            if (empty($partner_hero_title)) {
                $errors[] = "Hero Title is required.";
            }
            // Add more validation as needed...

            if (empty($errors)) {
                // Save text settings
                $save_success = true;
                $save_success &= $partnerModel->setHeroSetting('partner_hero_title', $partner_hero_title);
                $save_success &= $partnerModel->setHeroSetting('partner_hero_subtitle', $partner_hero_subtitle);

                if ($save_success) {
                    // Only set success message if image upload didn't already set one
                    if ($new_image_filename === null) {
                         echo json_encode([
                            'status' => 'success',
                            'message' => 'Partner page hero content updated successfully!',
                            'data' => [
                                'partner_hero_title' => $partner_hero_title,
                                'partner_hero_subtitle' => $partner_hero_subtitle
                            ]
                        ]);
                        $this->logActivity("Admin {$_SESSION['username']} updated Partner page hero text content at " . date('Y-m-d H:i:s'));
                    }
                    // If image was uploaded, the success message is already sent
                } else {
                    // Only set error message if image upload didn't already set one
                    if ($new_image_filename === null) {
                        http_response_code(500);
                        echo json_encode(['status' => 'error', 'message' => 'An error occurred while saving the hero text content. Please try again.']);
                        $this->logActivity("Admin {$_SESSION['username']} failed to update Partner page hero text content at " . date('Y-m-d H:i:s'));
                    }
                    // If image was uploaded, the error message is already sent
                }
            } else {
                // Only set error message if image upload didn't already set one
                if ($new_image_filename === null) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => implode('<br>', $errors)]);
                    $this->logActivity("Admin {$_SESSION['username']} failed Partner hero content validation: " . implode(', ', $errors) . " at " . date('Y-m-d H:i:s'));
                }
                // If image was uploaded, the error message is already sent
            }
        }
        // If there was an image upload error, the message is already set.
    }

    // --- Helper Function for Logging ---
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
?>