<?php
class InternshipModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
        try {
            $this->db->pdo->query("SELECT 1");
            error_log("InternshipModel: Database connection successful");
        } catch (PDOException $e) {
            error_log("InternshipModel: Database connection failed: " . $e->getMessage());
            die("Database connection failed. Check logs for details.");
        }
    }

    public function getHeroSettings($keys, $default = null) {
        if (empty($keys)) {
            return [];
        }

        $placeholders = str_repeat('?,', count($keys) - 1) . '?';
        $sql = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ($placeholders)";
        $this->db->query($sql);
        foreach ($keys as $index => $key) {
            $this->db->bind($index + 1, $key);
        }

        try {
            $results = $this->db->resultSet();
            $settings = [];
            foreach ($keys as $key) {
                $settings[$key] = $default;
            }
            foreach ($results as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            error_log("getHeroSettings: Retrieved settings: " . print_r($settings, true));
            return $settings;
        } catch (PDOException $e) {
            error_log("getHeroSettings failed: " . $e->getMessage() . " | SQL: $sql");
            return [];
        }
    }

    public function setHeroSetting($key, $value) {
        $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value";
        $this->db->query($sql);
        $this->db->bind(':key', $key);
        $this->db->bind(':value', $value);

        try {
            $this->db->execute();
            error_log("setHeroSetting: Successfully updated $key = $value");
            return true;
        } catch (PDOException $e) {
            error_log("InternshipModel::setHeroSetting failed: " . $e->getMessage() . " | SQL: $sql | Key: $key | Value: $value");
            return false;
        }
    }
}