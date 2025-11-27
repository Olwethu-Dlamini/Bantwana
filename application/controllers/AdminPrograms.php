<?php
// application/controllers/AdminPrograms.php
// This controller handles AJAX requests from the admin programs management page.

class AdminPrograms extends Controller {

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
         // This might redirect to the main programs admin page or show a list
         // For AJAX endpoints, individual methods are used.
         $this->checkAuth();
         // Redirect or load a view if needed for non-AJAX access
         header('Location: ' . BASE_URL . '/admin/pages/programs_manage.php'); // Or wherever your main admin page is
         exit;
    }

    /**
     * AJAX Endpoint: Get all programs (for initial load or refresh)
     */
    public function getAll() {
        $this->checkAuth();
        header('Content-Type: application/json');

        try {
            $programModel = $this->model('ProgramModel');
            $programs = $programModel->getAllPrograms();
            echo json_encode(['status' => 'success', 'data' => $programs]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch programs: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX Endpoint: Create a new program
     */
    public function create() {
        $this->checkAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        // Handle file upload first
        $new_image_filename = null;
        if (isset($_FILES['program_image']) && $_FILES['program_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public_html/images/programs/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $uploadFile = $_FILES['program_image'];

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.']);
                return;
            } elseif ($uploadFile['size'] > $maxFileSize) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Image file is too large. Maximum size is 5MB.']);
                return;
            } else {
                $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                $new_image_filename = 'program_' . uniqid() . '.' . strtolower($fileExtension);
                $targetPath = $uploadDir . $new_image_filename;

                if (!move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error uploading image file.']);
                    return;
                }
                // Image uploaded successfully, filename is in $new_image_filename
            }
        } else if (isset($_FILES['program_image']) && $_FILES['program_image']['error'] !== UPLOAD_ERR_NO_FILE) {
             http_response_code(400);
             echo json_encode(['status' => 'error', 'message' => 'Error uploading image: ' . $_FILES['program_image']['error']]);
             return;
        }
        // If no file was uploaded, $new_image_filename remains null

        // Get other data from POST
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $sort_order = intval($_POST['sort_order'] ?? 0);

        // Basic validation
        if (empty($title)) {
            // If image was uploaded but title is missing, delete the uploaded image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Title is required.']);
            return;
        }

        try {
            $programModel = $this->model('ProgramModel');
            $newId = $programModel->createProgram($title, $content, $new_image_filename, $sort_order);

            if ($newId) {
                // Fetch the newly created program to return it
                $newProgram = $programModel->getProgramById($newId);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Program created successfully!',
                    'data' => $newProgram // Return the full new program data
                ]);
                // Log activity
                $this->logActivity("Admin {$_SESSION['username']} created program: $title (ID: $newId)");
            } else {
                // If DB insert failed but image was uploaded, delete the image
                if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                    unlink($uploadDir . $new_image_filename);
                }
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to create program in database.']);
            }
        } catch (Exception $e) {
            // If an exception occurred after uploading, delete the image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to create program: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX Endpoint: Update an existing program
     */
    public function update() {
        $this->checkAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid program ID.']);
            return;
        }

        // Handle potential file upload
        $new_image_filename = null; // Will hold the new filename if uploaded
        $old_image_filename = null;  // Will hold the old filename to potentially delete
        $uploadDir = BASE_PATH . '/public_html/images/programs/';

        if (isset($_FILES['program_image']) && $_FILES['program_image']['error'] === UPLOAD_ERR_OK) {
             if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
             }
             $uploadFile = $_FILES['program_image'];

             $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
             $maxFileSize = 5 * 1024 * 1024; // 5MB

             if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                 http_response_code(400);
                 echo json_encode(['status' => 'error', 'message' => 'Invalid image file type for update. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.']);
                 return;
             } elseif ($uploadFile['size'] > $maxFileSize) {
                 http_response_code(400);
                 echo json_encode(['status' => 'error', 'message' => 'Image file for update is too large. Maximum size is 5MB.']);
                 return;
             } else {
                 $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                 $new_image_filename = 'program_' . uniqid() . '.' . strtolower($fileExtension);
                 $targetPath = $uploadDir . $new_image_filename;

                 if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                     // Successfully uploaded new image
                     // Need to get the old image filename to delete it
                     $programModel = $this->model('ProgramModel');
                     $existingProgram = $programModel->getProgramById($id);
                     if ($existingProgram) {
                         $old_image_filename = $existingProgram['image_filename'];
                     }
                 } else {
                     http_response_code(500);
                     echo json_encode(['status' => 'error', 'message' => 'Error uploading new image file.']);
                     return;
                 }
             }
        } else if (isset($_FILES['program_image']) && $_FILES['program_image']['error'] !== UPLOAD_ERR_NO_FILE) {
             http_response_code(400);
             echo json_encode(['status' => 'error', 'message' => 'Error uploading new image: ' . $_FILES['program_image']['error']]);
             return;
        }
        // If no new file was uploaded, $new_image_filename remains null

        // Get other data from POST
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $sort_order = intval($_POST['sort_order'] ?? 0);

        // Basic validation
        if (empty($title)) {
            // If a new image was uploaded but title is missing, delete the new image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Title is required for update.']);
            return;
        }

        try {
            $programModel = $this->model('ProgramModel');

            // Determine which image filename to use for the update
            // The model's updateProgram handles whether to update the image_filename column or not
            $image_filename_to_save = $new_image_filename;

            // Perform the update in the database
            $success = $programModel->updateProgram($id, $title, $content, $image_filename_to_save, $sort_order);

            if ($success) {
                // If update was successful and a new image was uploaded, delete the old image
                if ($new_image_filename !== null && !empty($old_image_filename)) {
                    $oldImagePath = $uploadDir . $old_image_filename;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Fetch the updated program to return it
                $updatedProgram = $programModel->getProgramById($id);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Program updated successfully!',
                    'data' => $updatedProgram // Return the full updated program data
                ]);
                // Log activity
                $this->logActivity("Admin {$_SESSION['username']} updated program ID: $id");

            } else {
                // If DB update failed but a new image was uploaded, delete the new image
                if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                    unlink($uploadDir . $new_image_filename);
                }
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to update program in database.']);
            }
        } catch (Exception $e) {
            // If an exception occurred after uploading a new image, delete the new image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update program: ' . $e->getMessage()]);
        }
    }


    /**
     * AJAX Endpoint: Delete a program
     */
    public function delete() {
        $this->checkAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid program ID for deletion.']);
            return;
        }

        try {
            $programModel = $this->model('ProgramModel');
            // Get program details before deleting for logging/image cleanup
            $programToDelete = $programModel->getProgramById($id);
            if (!$programToDelete) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Program not found for deletion.']);
                return;
            }

            $imageName = $programToDelete['image_filename'];
            $title = $programToDelete['title'];

            if ($programModel->deleteProgram($id)) {
                // Delete the associated image file if it exists
                if (!empty($imageName)) {
                    $imagePath = BASE_PATH . '/public_html/images/programs/' . $imageName;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Program \'' . htmlspecialchars($title) . '\' deleted successfully!'
                ]);
                // Log activity
                $this->logActivity("Admin {$_SESSION['username']} deleted program: $title (ID: $id)");
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete program.']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete program: ' . $e->getMessage()]);
        }
    }

    // --- Hero Section Management ---

    /**
     * AJAX Endpoint: Get Programs Hero Settings
     */
    public function getHeroSettings() {
        $this->checkAuth();
        header('Content-Type: application/json');

        try {
            $settingModel = $this->model('SettingModel');
            $heroKeys = [
                'programs_hero_title',
                'programs_hero_subtitle',
                'programs_hero_image'
            ];
            $heroSettings = $settingModel->getMultiple($heroKeys, '');

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
     * AJAX Endpoint: Update Programs Hero Settings
     */
    public function updateHeroSettings() {
        $this->checkAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $settingModel = $this->model('SettingModel');

        // --- Handle Hero Image Upload ---
        $new_image_filename = null;
        $old_image_filename = null;
        if (isset($_FILES['programs_hero_image']) && $_FILES['programs_hero_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public_html/images/';
            $uploadFile = $_FILES['programs_hero_image'];

            // Basic validation
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid Hero image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.']);
                return;
            } elseif ($uploadFile['size'] > $maxFileSize) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Hero image file is too large. Maximum size is 5MB.']);
                return;
            } else {
                // Generate a unique filename
                $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                $new_image_filename = 'programs_hero_' . uniqid() . '.' . strtolower($fileExtension);
                $targetPath = $uploadDir . $new_image_filename;

                if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                    // Successfully uploaded hero image
                    // Get old image filename to potentially delete later
                    $oldSettings = $settingModel->getMultiple(['programs_hero_image'], '');
                    $old_image_filename = $oldSettings['programs_hero_image'] ?? null;
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error uploading Programs hero image file.']);
                    return;
                }
            }
        } else if (isset($_FILES['programs_hero_image']) && $_FILES['programs_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
             http_response_code(400);
             echo json_encode(['status' => 'error', 'message' => 'Error uploading Programs hero image: ' . $_FILES['programs_hero_image']['error']]);
             return;
        }
        // --- End Hero Image Upload ---

        // Get text data from POST
        $programs_hero_title = trim($_POST['programs_hero_title'] ?? '');
        $programs_hero_subtitle = trim($_POST['programs_hero_subtitle'] ?? '');

        // Basic validation
        if (empty($programs_hero_title)) {
            // If image was uploaded but title is missing, delete the uploaded image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Hero Title is required.']);
            return;
        }

        // --- Save Settings ---
        $save_success = true;
        $save_success &= $settingModel->set('programs_hero_title', $programs_hero_title);
        $save_success &= $settingModel->set('programs_hero_subtitle', $programs_hero_subtitle);
        if ($new_image_filename !== null) {
            $save_success &= $settingModel->set('programs_hero_image', $new_image_filename);
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
                'message' => 'Programs hero section updated successfully!',
                'data' => [
                    'programs_hero_title' => $programs_hero_title,
                    'programs_hero_subtitle' => $programs_hero_subtitle,
                    'programs_hero_image' => $new_image_filename ?? ($settingModel->get('programs_hero_image', 'bg_5.jpg'))
                ]
            ]);
            $this->logActivity("Admin {$_SESSION['username']} updated Programs hero section at " . date('Y-m-d H:i:s') . ($new_image_filename ? " (New image: $new_image_filename)" : ""));
        } else {
            // If saving failed but a new image was uploaded, delete the new image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'An error occurred while saving the Programs hero section content. Please try again.']);
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

