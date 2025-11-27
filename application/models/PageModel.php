<?php
// application/models/PageModel.php

class PageModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Find a page by its slug.
     *
     * @param string $slug
     * @return array|false The page data array or false if not found.
     */
    public function getPageBySlug($slug) {
        $sql = "SELECT * FROM pages WHERE slug = :slug";
        $this->db->query($sql);
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    /**
     * Update a page by its slug.
     *
     * @param string $slug
     * @param array $data
     * @return bool True on success, false on failure.
     */
    public function updatePage($slug, $data) {
        $setClauses = [];
        $params = [];

        foreach ($data as $key => $value) {
            $setClauses[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $sql = "UPDATE pages SET " . implode(', ', $setClauses) . " WHERE slug = :slug";
        $params[':slug'] = $slug;

        try {
            $this->db->query($sql);
            foreach ($params as $param => $value) {
                $this->db->bind($param, $value);
            }
            $this->db->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Error updating page: " . $e->getMessage());
            return false;
        }
    }
}
?>