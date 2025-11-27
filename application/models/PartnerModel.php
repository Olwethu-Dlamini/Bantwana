<?php
// application/models/PartnerModel.php

class PartnerModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get multiple partner-related settings by their keys.
     * @param array $keys Array of setting keys to fetch.
     * @param mixed $default The default value to return for missing keys.
     * @return array Associative array of key => value.
     */
    public function getHeroSettings($keys, $default = null) {
        if (empty($keys)) {
            return [];
        }

        // Create placeholders for the IN clause
        $placeholders = str_repeat('?,', count($keys) - 1) . '?';
        $sql = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ($placeholders)";
        $this->db->query($sql);
        // Bind keys
        foreach ($keys as $index => $key) {
            // PDO uses 1-based indexing for bindParam with placeholders
            $this->db->bind($index + 1, $key);
        }

        $results = $this->db->resultSet();
        $settings = [];
        // Initialize with defaults
        foreach ($keys as $key) {
            $settings[$key] = $default;
        }
        // Override with actual values
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $settings;
    }

    /**
     * Set or update a partner-related setting value.
     * Note: This requires admin authentication in the controller calling it.
     *
     * @param string $key The setting key.
     * @param string $value The setting value.
     * @return bool True on success, false on failure.
     */
    public function setHeroSetting($key, $value) {
        // Use INSERT ... ON DUPLICATE KEY UPDATE for efficiency
        // Requires `setting_key` to be a UNIQUE or PRIMARY KEY (which it is)
        $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value";
        $this->db->query($sql);
        $this->db->bind(':key', $key);
        $this->db->bind(':value', $value);

        try {
            $this->db->execute();
            return true;
        } catch (PDOException $e) {
            error_log("PartnerModel::setHeroSetting failed: " . $e->getMessage());
            return false;
        }
    }
}
?>