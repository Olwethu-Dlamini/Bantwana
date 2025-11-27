<?php
// application/controllers/AdminDonate.php

class AdminDonate extends Controller {

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
         // Redirect to the main admin donate page or show list if needed
         $this->checkAuth();
         header('Location: ' . BASE_URL . '/admin/pages/donate_edit.php'); // Adjust path if needed
         exit;
    }

    /**
     * Handle saving Donate page content (Hero + Main Content)
     */
    public function saveContent() {
        $this->checkAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $donateModel = $this->model('DonateModel');

        // --- Handle Hero Image Upload ---
        $new_image_filename = null;
        if (isset($_FILES['donate_hero_image']) && $_FILES['donate_hero_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public_html/images/';
            $uploadFile = $_FILES['donate_hero_image'];

            // Basic validation
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to upload Donate hero image: Invalid file type.");
                return;
            } elseif ($uploadFile['size'] > $maxFileSize) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Hero image file is too large. Maximum size is 5MB.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to upload Donate hero image: File too large.");
                return;
            } else {
                $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                $new_image_filename = 'donate_hero_' . uniqid() . '.' . strtolower($fileExtension);
                $targetPath = $uploadDir . $new_image_filename;

                if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                    // Successfully uploaded hero image
                    if ($donateModel->set('donate_hero_image', $new_image_filename)) {
                        // Optional: Delete old image file if a new one was uploaded
                        // $oldImage = $donateModel->get('donate_hero_image', 'bg_5.jpg');
                        // if ($oldImage !== 'bg_5.jpg' && file_exists($uploadDir . $oldImage)) {
                        //     unlink($uploadDir . $oldImage);
                        // }
                        $this->logActivity("Admin {$_SESSION['username']} uploaded new Donate hero image: $new_image_filename");
                    } else {
                        // If DB update failed, delete the uploaded image
                        if (file_exists($uploadDir . $new_image_filename)) {
                            unlink($uploadDir . $new_image_filename);
                        }
                        http_response_code(500);
                        echo json_encode(['status' => 'error', 'message' => 'Hero image uploaded, but failed to update database setting.']);
                        $this->logActivity("Admin {$_SESSION['username']} failed to update Donate hero image setting after upload.");
                        return;
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error uploading hero image file.']);
                    $this->logActivity("Admin {$_SESSION['username']} failed to move Donate hero image file.");
                    return;
                }
            }
        } else if (isset($_FILES['donate_hero_image']) && $_FILES['donate_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
             // An upload error occurred (but not "no file")
             http_response_code(400);
             echo json_encode(['status' => 'error', 'message' => 'Error uploading hero image: ' . $_FILES['donate_hero_image']['error']]);
             $this->logActivity("Admin {$_SESSION['username']} encountered upload error for Donate hero image: " . $_FILES['donate_hero_image']['error']);
             return;
        }
        // If no file was uploaded, $new_image_filename remains null

        // --- Handle Text Content Saving ---
        // Get text data from POST
        $donate_hero_title = trim($_POST['donate_hero_title'] ?? '');
        $donate_hero_subtitle = trim($_POST['donate_hero_subtitle'] ?? '');
        $donate_main_heading = trim($_POST['donate_main_heading'] ?? '');
        $donate_main_subheading = trim($_POST['donate_main_subheading'] ?? '');
        $donate_main_content = trim($_POST['donate_main_content'] ?? '');

        // Basic validation (example)
        $errors = [];
        if (empty($donate_hero_title)) {
            $errors[] = "Hero Title is required.";
        }
        // Add more validation as needed...

        if (empty($errors)) {
            // Save text settings
            $save_success = true;
            $save_success &= $donateModel->set('donate_hero_title', $donate_hero_title);
            $save_success &= $donateModel->set('donate_hero_subtitle', $donate_hero_subtitle);
            $save_success &= $donateModel->set('donate_main_heading', $donate_main_heading);
            $save_success &= $donateModel->set('donate_main_subheading', $donate_main_subheading);
            $save_success &= $donateModel->set('donate_main_content', $donate_main_content);

            if ($save_success) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Donate page content updated successfully!' . ($new_image_filename ? ' Hero image uploaded.' : '')
                ]);
                $this->logActivity("Admin {$_SESSION['username']} updated Donate page text content." . ($new_image_filename ? " New hero image: $new_image_filename" : ""));
            } else {
                // If saving failed but a new image was uploaded, delete the new image
                if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                    unlink($uploadDir . $new_image_filename);
                }
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'An error occurred while saving the content. Please try again.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to update Donate page text content.");
            }
        } else {
            // If validation failed but a new image was uploaded, delete the new image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => implode('<br>', $errors)]);
            $this->logActivity("Admin {$_SESSION['username']} failed Donate content validation: " . implode(', ', $errors));
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