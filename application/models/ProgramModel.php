<?php
class ProgramModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all programs ordered by sort_order, then by id.
     * @param int|null $offset For pagination (optional).
     * @param int|null $limit  For pagination (optional).
     * @return array List of programs.
     */
    public function getAllPrograms($offset = null, $limit = null) {
        $sql = "SELECT * FROM programs ORDER BY sort_order ASC, id ASC";
        if ($offset !== null && $limit !== null) {
            $sql .= " LIMIT :offset, :limit";
        }
        $this->db->query($sql);
        if ($offset !== null && $limit !== null) {
            $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
            $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        }
        return $this->db->resultSet();
    }

    /**
     * Get the total count of programs.
     * @return int Total number of programs.
     */
    public function getTotalProgramsCount() {
        $this->db->query("SELECT COUNT(*) as total FROM programs");
        $row = $this->db->single();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Get a single program by its ID.
     * @param int $id Program ID.
     * @return array|false Program data or false if not found.
     */
    public function getProgramById($id) {
        $this->db->query("SELECT * FROM programs WHERE id = :id LIMIT 1");
        $this->db->bind(':id', (int)$id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Create a new program.
     * @param string $title Program title.
     * @param string $content Program content (HTML).
     * @param string|null $image_filename Filename of the uploaded image.
     * @param int $sort_order Sort order.
     * @return int|false New program ID on success, false on failure.
     */
    public function createProgram($title, $content, $image_filename, $sort_order = 0) {
        $sort_order = (int)$sort_order;
        $this->db->query("INSERT INTO programs (title, content, image_filename, sort_order) VALUES (:title, :content, :image_filename, :sort_order)");
        $this->db->bind(':title', $title);
        $this->db->bind(':content', $content);
        $this->db->bind(':image_filename', $image_filename, $image_filename === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $this->db->bind(':sort_order', $sort_order, PDO::PARAM_INT);
        if ($this->db->execute()) {
            return $this->db->pdo->lastInsertId();
        }
        error_log("Failed to create program: " . print_r($this->db->error, true));
        return false;
    }

    /**
     * Update an existing program.
     * @param int $id Program ID.
     * @param string $title Program title.
     * @param string $content Program content (HTML).
     * @param string|null $image_filename New image filename (null if not changing).
     * @param int $sort_order Sort order.
     * @return bool True on success, false on failure.
     */
    public function updateProgram($id, $title, $content, $image_filename = null, $sort_order = 0) {
        $id = (int)$id;
        $sort_order = (int)$sort_order;
        $sql = $image_filename !== null
            ? "UPDATE programs SET title = :title, content = :content, image_filename = :image_filename, sort_order = :sort_order WHERE id = :id"
            : "UPDATE programs SET title = :title, content = :content, sort_order = :sort_order WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':title', $title);
        $this->db->bind(':content', $content);
        if ($image_filename !== null) {
            $this->db->bind(':image_filename', $image_filename, PDO::PARAM_STR);
        }
        $this->db->bind(':sort_order', $sort_order, PDO::PARAM_INT);
        $result = $this->db->execute();
        if (!$result) {
            error_log("Failed to update program ID $id: " . print_r($this->db->error, true));
        }
        return $result;
    }

    /**
     * Delete a program by its ID.
     * @param int $id Program ID.
     * @return bool True on success, false on failure.
     */
    public function deleteProgram($id) {
        $id = (int)$id;
        $program = $this->getProgramById($id);
        if ($program && !empty($program['image_filename'])) {
            $imagePath = BASE_PATH . '/public_html/images/programs/' . $program['image_filename'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $this->db->query("DELETE FROM programs WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $result = $this->db->execute();
        if (!$result) {
            error_log("Failed to delete program ID $id: " . print_r($this->db->error, true));
        }
        return $result;
    }
}
?>