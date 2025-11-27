<?php
// application/controllers/Download.php
class Download extends Controller {
    private $publicationModel;

    public function __construct() {
        $this->publicationModel = $this->model('PublicationModel');
    }

    public function index($id = null) {
        if (!$id) {
            http_response_code(400);
            echo "Invalid publication ID.";
            exit;
        }

        $publicationId = intval($id);
        $publication = $this->publicationModel->getPublicationById($publicationId);

        if (!$publication || !$publication['filename']) {
            http_response_code(404);
            echo "Publication or file not found.";
            exit;
        }

        $filePath = BASE_PATH . '/public_html/images/publications/' . $publication['filename'];
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo "File not found on server.";
            exit;
        }

        // Sanitize the title for use as a filename
        $title = preg_replace('/[^A-Za-z0-9\-_\. ]/', '', $publication['title']); // Remove invalid characters
        $title = str_replace(' ', '_', trim($title)); // Replace spaces with underscores
        if (empty($title)) {
            $title = 'publication_' . $publicationId; // Fallback if title is empty
        }

        // Append the file extension from file_type
        $fileExtension = strtolower($publication['file_type']);
        $downloadFilename = $title . '.' . $fileExtension;

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}
?>