<?php
// application/controllers/AdminPublications.php

class AdminPublications extends Controller {

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
         // This might redirect to the main admin publications page or show a list
         $this->checkAuth();
         header('Location: ' . BASE_URL . '/admin/pages/publications_manage.php'); // Adjust path if needed
         exit;
    }

    /**
     * AJAX Endpoint: Get all publications (for initial load or refresh)
     */
    public function getAll() {
        $this->checkAuth();
        header('Content-Type: application/json');

        try {
            $publicationModel = $this->model('PublicationModel');
            $publications = $publicationModel->getAllPublications();
            echo json_encode(['status' => 'success', 'data' => $publications]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch publications: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX Endpoint: Create a new publication
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
        if (isset($_FILES['publication_image']) && $_FILES['publication_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public_html/images/publications/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $uploadFile = $_FILES['publication_image'];

            // Basic validation
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid Publication image file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to upload Publication image: Invalid file type at " . date('Y-m-d H:i:s'));
                return;
            } elseif ($uploadFile['size'] > $maxFileSize) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Publication image file is too large. Maximum size is 5MB.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to upload Publication image: File too large at " . date('Y-m-d H:i:s'));
                return;
            } else {
                $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                $new_image_filename = 'publication_' . uniqid() . '.' . strtolower($fileExtension);
                $targetPath = $uploadDir . $new_image_filename;

                if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                    // Successfully uploaded publication image
                    $this->logActivity("Admin {$_SESSION['username']} uploaded new Publication image: $new_image_filename at " . date('Y-m-d H:i:s'));
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error uploading Publication image file.']);
                    $this->logActivity("Admin {$_SESSION['username']} failed to upload Publication image file at " . date('Y-m-d H:i:s'));
                    return;
                }
            }
        } else if (isset($_FILES['publication_image']) && $_FILES['publication_image']['error'] !== UPLOAD_ERR_NO_FILE) {
             http_response_code(400);
             echo json_encode(['status' => 'error', 'message' => 'Error uploading Publication image: ' . $_FILES['publication_image']['error']]);
             $this->logActivity("Admin {$_SESSION['username']} encountered upload error for Publication image: " . $_FILES['publication_image']['error'] . " at " . date('Y-m-d H:i:s'));
             return;
        }
        // If no file was uploaded, $new_image_filename remains null

        // Get other data from POST
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $sort_order = intval($_POST['sort_order'] ?? 0);

        // Basic validation
        $errors = [];
        if (empty($title)) {
            // If image was uploaded but title is missing, delete the uploaded image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Title is required.']);
            return;
        }
        // Add more validation as needed...

        if (empty($errors)) {
            try {
                $publicationModel = $this->model('PublicationModel');
                $newId = $publicationModel->createPublication($title, $content, $new_image_filename, $sort_order);

                if ($newId) {
                    // Fetch the newly created publication to return it
                    $newPublication = $publicationModel->getPublicationById($newId);
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Publication created successfully!',
                        'data' => $newPublication // Return the full new publication data
                    ]);
                    $this->logActivity("Admin {$_SESSION['username']} created publication: $title (ID: $newId) at " . date('Y-m-d H:i:s'));
                } else {
                    // If DB insert failed but image was uploaded, delete the image
                    if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                        unlink($uploadDir . $new_image_filename);
                    }
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Failed to create publication in database.']);
                    $this->logActivity("Admin {$_SESSION['username']} failed to create publication in DB after upload at " . date('Y-m-d H:i:s'));
                }
            } catch (Exception $e) {
                // If an exception occurred after uploading, delete the image
                if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                    unlink($uploadDir . $new_image_filename);
                }
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to create publication: ' . $e->getMessage()]);
                $this->logActivity("Admin {$_SESSION['username']} failed to create publication (Exception): " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
            }
        } else {
            // If validation failed but a new image was uploaded, delete the new image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => implode('<br>', $errors)]);
            $this->logActivity("Admin {$_SESSION['username']} failed Publication validation: " . implode(', ', $errors) . " at " . date('Y-m-d H:i:s'));
        }
    }

    /**
     * AJAX Endpoint: Update an existing publication
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
            echo json_encode(['status' => 'error', 'message' => 'Invalid publication ID.']);
            return;
        }

        // Handle potential file upload
        $new_image_filename = null; // Will hold the new filename if uploaded
        $old_image_filename = null;  // Will hold the old filename to potentially delete
        $uploadDir = BASE_PATH . '/public_html/images/publications/';

        if (isset($_FILES['publication_image']) && $_FILES['publication_image']['error'] === UPLOAD_ERR_OK) {
             if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
             }
             $uploadFile = $_FILES['publication_image'];

             // Basic validation
             $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
             $maxFileSize = 5 * 1024 * 1024; // 5MB

             if (!in_array(strtolower($uploadFile['type']), $allowedTypes) && !in_array(strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                 http_response_code(400);
                 echo json_encode(['status' => 'error', 'message' => 'Invalid Publication image file type for update. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.']);
                 $this->logActivity("Admin {$_SESSION['username']} failed to upload Publication image for update: Invalid file type at " . date('Y-m-d H:i:s'));
                 return;
             } elseif ($uploadFile['size'] > $maxFileSize) {
                 http_response_code(400);
                 echo json_encode(['status' => 'error', 'message' => 'Publication image file for update is too large. Maximum size is 5MB.']);
                 $this->logActivity("Admin {$_SESSION['username']} failed to upload Publication image for update: File too large at " . date('Y-m-d H:i:s'));
                 return;
             } else {
                 $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
                 $new_image_filename = 'publication_' . uniqid() . '.' . strtolower($fileExtension);
                 $targetPath = $uploadDir . $new_image_filename;

                 if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                     // Successfully uploaded new image
                     // Need to get the old image filename to delete it
                     $publicationModel = $this->model('PublicationModel');
                     $existingPublication = $publicationModel->getPublicationById($id);
                     if ($existingPublication) {
                         $old_image_filename = $existingPublication['image_filename'];
                     }
                     $this->logActivity("Admin {$_SESSION['username']} uploaded new Publication image for update: $new_image_filename at " . date('Y-m-d H:i:s'));
                 } else {
                     http_response_code(500);
                     echo json_encode(['status' => 'error', 'message' => 'Error uploading new Publication image file.']);
                     $this->logActivity("Admin {$_SESSION['username']} failed to upload new Publication image file for update at " . date('Y-m-d H:i:s'));
                     return;
                 }
             }
        } else if (isset($_FILES['publication_image']) && $_FILES['publication_image']['error'] !== UPLOAD_ERR_NO_FILE) {
             http_response_code(400);
             echo json_encode(['status' => 'error', 'message' => 'Error uploading new Publication image: ' . $_FILES['publication_image']['error']]);
             $this->logActivity("Admin {$_SESSION['username']} encountered upload error for new Publication image: " . $_FILES['publication_image']['error'] . " at " . date('Y-m-d H:i:s'));
             return;
        }
        // If no new file was uploaded, $new_image_filename remains null

        // Get other data from POST
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $sort_order = intval($_POST['sort_order'] ?? 0);

        // Basic validation
        $errors = [];
        if (empty($title)) {
            // If a new image was uploaded but title is missing, delete the new image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Title is required for update.']);
            return;
        }
        // Add more validation as needed...

        if (empty($errors)) {
            try {
                $publicationModel = $this->model('PublicationModel');

                // Determine which image filename to use for the update
                $image_filename_to_save = $new_image_filename;

                // Perform the update in the database
                $success = $publicationModel->updatePublication($id, $title, $content, $new_image_filename !== null ? $new_image_filename : null, $sort_order);

                if ($success) {
                    // If update was successful and a new image was uploaded, delete the old image
                    if ($new_image_filename !== null && !empty($old_image_filename)) {
                        $oldImagePath = $uploadDir . $old_image_filename;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                        $this->logActivity("Admin {$_SESSION['username']} deleted old Publication image: $old_image_filename after update at " . date('Y-m-d H:i:s'));
                    }

                    // Fetch the updated publication to return it
                    $updatedPublication = $publicationModel->getPublicationById($id);
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Publication updated successfully!',
                        'data' => $updatedPublication // Return the full updated publication data
                    ]);
                    $this->logActivity("Admin {$_SESSION['username']} updated publication ID: $id at " . date('Y-m-d H:i:s'));

                } else {
                    // If DB update failed but a new image was uploaded, delete the new image
                    if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                        unlink($uploadDir . $new_image_filename);
                    }
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update publication in database.']);
                    $this->logActivity("Admin {$_SESSION['username']} failed to update publication ID: $id in DB at " . date('Y-m-d H:i:s'));
                }
            } catch (Exception $e) {
                // If an exception occurred after uploading a new image, delete the new image
                if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                    unlink($uploadDir . $new_image_filename);
                }
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to update publication: ' . $e->getMessage()]);
                $this->logActivity("Admin {$_SESSION['username']} failed to update publication ID: $id (Exception): " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
            }
        } else {
            // If validation failed but a new image was uploaded, delete the new image
            if ($new_image_filename && file_exists($uploadDir . $new_image_filename)) {
                unlink($uploadDir . $new_image_filename);
            }
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => implode('<br>', $errors)]);
            $this->logActivity("Admin {$_SESSION['username']} failed Publication update validation: " . implode(', ', $errors) . " at " . date('Y-m-d H:i:s'));
        }
    }


    /**
     * AJAX Endpoint: Delete a publication
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
            echo json_encode(['status' => 'error', 'message' => 'Invalid publication ID for deletion.']);
            return;
        }

        try {
            $publicationModel = $this->model('PublicationModel');
            // Get publication details before deleting for logging/image cleanup
            $publicationToDelete = $publicationModel->getPublicationById($id);
            if (!$publicationToDelete) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Publication not found for deletion.']);
                return;
            }

            $imageName = $publicationToDelete['image_filename'];
            $title = $publicationToDelete['title'];

            if ($publicationModel->deletePublication($id)) {
                // Delete the associated image file if it exists
                if (!empty($imageName)) {
                    $imagePath = BASE_PATH . '/public_html/images/publications/' . $imageName;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                        $this->logActivity("Admin {$_SESSION['username']} deleted Publication image: $imageName at " . date('Y-m-d H:i:s'));
                    }
                }
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Publication \'' . htmlspecialchars($title) . '\' deleted successfully!'
                ]);
                $this->logActivity("Admin {$_SESSION['username']} deleted publication: $title (ID: $id) at " . date('Y-m-d H:i:s'));
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete publication.']);
                $this->logActivity("Admin {$_SESSION['username']} failed to delete publication ID: $id at " . date('Y-m-d H:i:s'));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete publication: ' . $e->getMessage()]);
            $this->logActivity("Admin {$_SESSION['username']} failed to delete publication ID: $id (Exception): " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
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