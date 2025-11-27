<?php
// application/models/SettingModel.php

class SettingModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get a setting value by its key.
     *
     * @param string $key The setting key.
     * @param mixed $default The default value to return if the key is not found.
     * @return mixed The setting value or the default value.
     */
    public function get($key, $default = null) {
        $this->db->query("SELECT setting_value FROM settings WHERE setting_key = :key");
        $this->db->bind(':key', $key);
        $row = $this->db->single();
        return $row ? $row['setting_value'] : $default;
    }

    /**
     * Set or update a setting value.
     * Note: This requires admin authentication in the controller calling it.
     *
     * @param string $key The setting key.
     * @param string $value The setting value.
     * @return bool True on success, false on failure.
     */
    public function set($key, $value) {
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
            error_log("SettingModel::set failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get multiple settings by an array of keys.
     * Returns an associative array [key => value].
     *
     * @param array $keys Array of setting keys to fetch.
     * @param mixed $default The default value for missing keys.
     * @return array Associative array of key => value.
     */
    public function getMultiple($keys, $default = null) {
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
}
?>
