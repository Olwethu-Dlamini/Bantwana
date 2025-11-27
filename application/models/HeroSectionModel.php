<?php
// application/models/HeroSectionModel.php

class HeroSectionModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get hero section data for a specific page slug.
     *
     * @param string $slug
     * @return array|false The hero section data array or false if not found.
     */
    public function getHeroSectionByPageSlug($slug) {
        $sql = "SELECT * FROM hero_sections WHERE page_slug = :slug";
        $this->db->query($sql);
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    /**
     * Update the hero section for a specific page slug.
     *
     * @param string $slug
     * @param array $data
     * @return bool True on success, false on failure.
     */
    public function updateHeroSection($slug, $data) {
        $setClauses = [];
        $params = [];

        foreach ($data as $key => $value) {
            $setClauses[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $sql = "UPDATE hero_sections SET " . implode(', ', $setClauses) . " WHERE page_slug = :slug";
        $params[':slug'] = $slug;

        try {
            $this->db->query($sql);
            foreach ($params as $param => $value) {
                $this->db->bind($param, $value);
            }
            $this->db->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Error updating hero section: " . $e->getMessage());
            return false;
        }
    }
}
?>