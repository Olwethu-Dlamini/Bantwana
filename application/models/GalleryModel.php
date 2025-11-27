<?php
// application/models/GalleryModel.php

class GalleryModel {
    private $db;

    public function __construct() {
        $this->db = new Database(); // Assuming Database class is correctly implemented and loaded
    }

    // --- Gallery Methods ---

    /**
     * Get all galleries ordered by name.
     * @return array List of galleries.
     */
    public function getAllGalleries() {
        $this->db->query("SELECT * FROM galleries ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * Get a single gallery by its ID.
     * @param int $id Gallery ID.
     * @return array|false Gallery data or false if not found.
     */
    public function getGalleryById($id) {
        $this->db->query("SELECT * FROM galleries WHERE id = :id LIMIT 1");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get a single gallery by its slug.
     * @param string $slug Gallery slug.
     * @return array|false Gallery data or false if not found.
     */
    public function getGalleryBySlug($slug) {
        $this->db->query("SELECT * FROM galleries WHERE slug = :slug LIMIT 1");
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    /**
     * Create a new gallery.
     * @param string $name Gallery name.
     * @param string $description Gallery description.
     * @return int|false New gallery ID on success, false on failure.
     */
    public function createGallery($name, $description = '') {
        $slug = $this->generateUniqueSlug($name);
        $this->db->query("INSERT INTO galleries (name, slug, description) VALUES (:name, :slug, :description)");
        $this->db->bind(':name', $name);
        $this->db->bind(':slug', $slug);
        $this->db->bind(':description', $description);

        if ($this->db->execute()) {
            return $this->db->pdo->lastInsertId(); // Return new gallery ID
        }
        return false;
    }

     /**
     * Update an existing gallery.
     * @param int $id Gallery ID.
     * @param string $name Gallery name.
     * @param string $description Gallery description.
     * @return bool True on success, false on failure.
     */
    public function updateGallery($id, $name, $description = '') {
        // For simplicity, we'll keep the existing slug for now.
        // You could add logic to update slug and handle conflicts.
        $this->db->query("UPDATE galleries SET name = :name, description = :description WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $name);
        $this->db->bind(':description', $description);

        return $this->db->execute();
    }

    /**
     * Delete a gallery by its ID.
     * @param int $id Gallery ID.
     * @return bool True on success, false on failure.
     */
    public function deleteGallery($id) {
        // Deleting the gallery will cascade delete images due to foreign key constraint
        $this->db->query("DELETE FROM galleries WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }


    // --- Image Methods ---

    /**
     * Get images associated with a specific gallery ID, ordered by sort_order and upload date.
     * @param int $gallery_id The ID of the gallery.
     * @return array List of image data for the gallery.
     */
    public function getImagesByGalleryId($gallery_id) {
        $this->db->query("SELECT * FROM gallery_images WHERE gallery_id = :gallery_id ORDER BY sort_order ASC, uploaded_at DESC");
        $this->db->bind(':gallery_id', $gallery_id);
        return $this->db->resultSet();
    }

    /**
     * Get a single image by its ID.
     * @param int $id Image ID.
     * @return array|false Image data or false if not found.
     */
    public function getImageById($id) {
        $this->db->query("SELECT * FROM gallery_images WHERE id = :id LIMIT 1");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Add a new image to a gallery.
     * @param int $gallery_id The ID of the gallery.
     * @param string $filename The filename of the uploaded image.
     * @param string $alt_text Optional alt text.
     * @param string $caption Optional caption.
     * @param int $sort_order Optional sort order.
     * @return bool True on success, false on failure.
     */
    public function addImageToGallery($gallery_id, $filename, $alt_text = '', $caption = '', $sort_order = 0) {
        $this->db->query("INSERT INTO gallery_images (gallery_id, filename, alt_text, caption, sort_order) VALUES (:gallery_id, :filename, :alt_text, :caption, :sort_order)");
        $this->db->bind(':gallery_id', $gallery_id);
        $this->db->bind(':filename', $filename);
        $this->db->bind(':alt_text', $alt_text);
        $this->db->bind(':caption', $caption);
        $this->db->bind(':sort_order', $sort_order); // Default sort order

        return $this->db->execute();
    }

    /**
     * Delete an image by its ID.
     * @param int $id Image ID.
     * @return bool True on success, false on failure.
     */
    public function deleteImage($id) {
        $image = $this->getImageById($id);
        if (!$image) {
            return false; // Image doesn't exist
        }

        $this->db->query("DELETE FROM gallery_images WHERE id = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->execute();

        // if ($result) {
        //     // Optional: Delete the physical file from the server
        //     // $imagePath = BASE_PATH . '/public_html/images/gallery/' . $image['filename'];
        //     // if (file_exists($imagePath)) {
        //     //     unlink($imagePath);
        //     // }
        //     return true;
        // }
        return $result; // Return the result of the DB delete operation
    }

    /**
     * Update image details (alt text, caption).
     * @param int $id Image ID.
     * @param string $alt_text New alt text.
     * @param string $caption New caption.
     * @return bool True on success, false on failure.
     */
    public function updateImage($id, $alt_text, $caption) {
        $this->db->query("UPDATE gallery_images SET alt_text = :alt_text, caption = :caption WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':alt_text', $alt_text);
        $this->db->bind(':caption', $caption);
        return $this->db->execute();
    }


    // --- Combined Fetch Method for View ---

    /**
     * Fetches all galleries and their associated images (limited number for preview).
     * This is useful for the main gallery listing page.
     *
     * @param int $limitPerGallery Limit the number of images fetched per gallery (for preview).
     * @return array An array of galleries, each containing a 'images' key with its images.
     */
    public function getAllGalleriesWithImages($limitPerGallery = 6) {
        // 1. Get all galleries
        $galleries = $this->getAllGalleries();

        // 2. Loop through galleries and fetch limited images for each
        foreach ($galleries as &$gallery) { // Use reference '&' to modify the original array element
            $galleryId = $gallery['id'];
            // Fetch images with a limit for preview
            // Correctly bind the limit parameter as an integer
            $sql = "SELECT * FROM gallery_images WHERE gallery_id = :gallery_id ORDER BY sort_order ASC, uploaded_at DESC LIMIT :lim";
            $this->db->query($sql);
            $this->db->bind(':gallery_id', $galleryId);
            $this->db->bind(':lim', (int)$limitPerGallery, PDO::PARAM_INT); // Cast to int and specify type
            $gallery['images'] = $this->db->resultSet();
        }
        // Unset the reference to avoid potential issues
        unset($gallery);

        return $galleries;
    }


    // --- Helper Methods ---

    /**
     * Generates a unique slug for a gallery name.
     * @param string $name The gallery name.
     * @return string A unique slug.
     */
    private function generateUniqueSlug($name) {
        $original_slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9\-]/', '-', $name), '-'));
        $slug = $original_slug;
        $counter = 1;

        do {
            $this->db->query("SELECT COUNT(*) as count FROM galleries WHERE slug = :slug");
            $this->db->bind(':slug', $slug);
            $result = $this->db->single();
            $exists = $result && $result['count'] > 0; // Check if result exists and count > 0

            if ($exists) {
                $slug = $original_slug . '-' . $counter;
                $counter++;
            }
        } while ($exists);

        return $slug;
    }
}
?>