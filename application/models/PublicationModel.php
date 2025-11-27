<?php
class PublicationModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllPublications(?string $category = null, string $orderBy = 'sort_order', string $orderDirection = 'DESC'): array {
        try {
            $sql = "SELECT * FROM publications";
            $params = [];

            if ($category !== null) {
                $sql .= " WHERE category = :category";
                $params[':category'] = trim($category);
            }

            $allowedColumns = ['id', 'title', 'category', 'sort_order', 'uploaded_at', 'updated_at', 'file_type', 'file_size'];
            $orderBy = in_array($orderBy, $allowedColumns) ? $orderBy : 'sort_order';
            $orderDirection = strtoupper($orderDirection) === 'ASC' ? 'ASC' : 'DESC';

            $sql .= " ORDER BY $orderBy $orderDirection, id DESC";

            $this->db->query($sql);
            foreach ($params as $key => $value) {
                $this->db->bind($key, $value);
            }

            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("PublicationModel::getAllPublications - Database Error: " . $e->getMessage());
            throw new Exception("Failed to fetch publications: " . $e->getMessage());
        }
    }

    public function getPublicationById(int $id): ?array {
        try {
            $this->db->query("SELECT * FROM publications WHERE id = :id LIMIT 1");
            $this->db->bind(':id', $id, PDO::PARAM_INT);
            return $this->db->single() ?: null;
        } catch (PDOException $e) {
            error_log("PublicationModel::getPublicationById - Database Error: " . $e->getMessage());
            throw new Exception("Failed to fetch publication ID $id: " . $e->getMessage());
        }
    }

    public function getPublicationByFilename(string $filename): ?array {
        try {
            $this->db->query("SELECT * FROM publications WHERE filename = :filename LIMIT 1");
            $this->db->bind(':filename', basename(trim($filename)));
            return $this->db->single() ?: null;
        } catch (PDOException $e) {
            error_log("PublicationModel::getPublicationByFilename - Database Error: " . $e->getMessage());
            throw new Exception("Failed to fetch publication for filename $filename: " . $e->getMessage());
        }
    }

    public function createPublication(
        string $title,
        string $description,
        string $filename,
        string $file_type,
        int $file_size,
        string $category = 'general',
        int $sort_order = 0,
        ?int $uploaded_by = null,
        string $original_filename = '' // New parameter
    ): ?int {
        try {
            $title = trim($title);
            $description = trim($description);
            $filename = basename(trim($filename));
            $original_filename = basename(trim($original_filename));
            $file_type = strtolower(trim($file_type));
            $category = trim($category) ?: 'general';
            if (empty($title) || empty($filename) || $file_size < 0) {
                throw new Exception("Invalid input: title and filename must not be empty, file_size must be non-negative.");
            }

            $allowedFileTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar', 'txt'];
            if (!in_array($file_type, $allowedFileTypes)) {
                throw new Exception("Invalid file type: $file_type");
            }

            $this->db->query("INSERT INTO publications (title, description, filename, original_filename, file_type, file_size, category, sort_order, uploaded_by, uploaded_at) VALUES (:title, :description, :filename, :original_filename, :file_type, :file_size, :category, :sort_order, :uploaded_by, NOW())");
            $this->db->bind(':title', $title);
            $this->db->bind(':description', $description);
            $this->db->bind(':filename', $filename);
            $this->db->bind(':original_filename', $original_filename);
            $this->db->bind(':file_type', $file_type);
            $this->db->bind(':file_size', $file_size, PDO::PARAM_INT);
            $this->db->bind(':category', $category);
            $this->db->bind(':sort_order', $sort_order, PDO::PARAM_INT);
            $this->db->bind(':uploaded_by', $uploaded_by, PDO::PARAM_INT);

            if ($this->db->execute()) {
                return (int) $this->db->pdo->lastInsertId();
            }
            return null;
        } catch (Exception $e) {
            error_log("PublicationModel::createPublication - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function updatePublication(
        int $id,
        string $title,
        string $description,
        ?string $filename = null,
        ?string $file_type = null,
        ?int $file_size = null,
        string $category = 'general',
        int $sort_order = 0,
        ?string $original_filename = null // New parameter
    ): bool {
        try {
            $title = trim($title);
            $description = trim($description);
            $category = trim($category) ?: 'general';
            if (empty($title)) {
                throw new Exception("Invalid input: title must not be empty.");
            }
            if ($filename !== null) {
                $filename = basename(trim($filename));
                if (empty($filename)) {
                    throw new Exception("Invalid input: filename must not be empty.");
                }
            }
            if ($original_filename !== null) {
                $original_filename = basename(trim($original_filename));
            }
            if ($file_type !== null) {
                $file_type = strtolower(trim($file_type));
                $allowedFileTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar', 'txt'];
                if (!in_array($file_type, $allowedFileTypes)) {
                    throw new Exception("Invalid file type: $file_type");
                }
            }
            if ($file_size !== null && $file_size < 0) {
                throw new Exception("Invalid input: file_size must be non-negative.");
            }

            $sqlParts = [
                "title = :title",
                "description = :description",
                "category = :category",
                "sort_order = :sort_order"
            ];
            if ($filename !== null) {
                $sqlParts[] = "filename = :filename";
            }
            if ($original_filename !== null) {
                $sqlParts[] = "original_filename = :original_filename";
            }
            if ($file_type !== null) {
                $sqlParts[] = "file_type = :file_type";
            }
            if ($file_size !== null) {
                $sqlParts[] = "file_size = :file_size";
            }

            // Only include updated_at if the column exists
            $this->db->query("SHOW COLUMNS FROM publications LIKE 'updated_at'");
            if ($this->db->resultSet()) {
                $sqlParts[] = "updated_at = NOW()";
            }

            $sql = "UPDATE publications SET " . implode(', ', $sqlParts) . " WHERE id = :id";

            $this->db->query($sql);
            $this->db->bind(':id', $id, PDO::PARAM_INT);
            $this->db->bind(':title', $title);
            $this->db->bind(':description', $description);
            $this->db->bind(':category', $category);
            $this->db->bind(':sort_order', $sort_order, PDO::PARAM_INT);
            if ($filename !== null) {
                $this->db->bind(':filename', $filename);
            }
            if ($original_filename !== null) {
                $this->db->bind(':original_filename', $original_filename);
            }
            if ($file_type !== null) {
                $this->db->bind(':file_type', $file_type);
            }
            if ($file_size !== null) {
                $this->db->bind(':file_size', $file_size, PDO::PARAM_INT);
            }

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("PublicationModel::updatePublication - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function deletePublication(int $id): bool {
        try {
            $this->db->query("DELETE FROM publications WHERE id = :id");
            $this->db->bind(':id', $id, PDO::PARAM_INT);
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("PublicationModel::deletePublication - Database Error: " . $e->getMessage());
            throw new Exception("Failed to delete publication ID $id: " . $e->getMessage());
        }
    }
}