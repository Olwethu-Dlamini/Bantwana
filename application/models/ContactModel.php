<?php
class ContactModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
        try {
            $this->db->pdo->query("SELECT 1");
            error_log("ContactModel: Database connection successful");
        } catch (PDOException $e) {
            error_log("ContactModel: Database connection failed: " . $e->getMessage());
            throw new Exception("Failed to connect to database: " . $e->getMessage());
        }
    }

    public function getHeroSettings($keys, $default = null) {
        if (empty($keys)) {
            error_log("ContactModel::getHeroSettings: No keys provided");
            throw new Exception("No setting keys provided");
        }

        $placeholders = str_repeat('?,', count($keys) - 1) . '?';
        $sql = "SELECT setting_key, setting_value FROM contact_settings WHERE setting_key IN ($placeholders)";
        $this->db->query($sql);
        foreach ($keys as $index => $key) {
            $this->db->bind($index + 1, $key);
        }

        try {
            $results = $this->db->resultSet();
            $settings = array_fill_keys($keys, $default);
            foreach ($results as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            error_log("ContactModel::getHeroSettings: Retrieved settings: " . json_encode($settings));
            return $settings;
        } catch (PDOException $e) {
            error_log("ContactModel::getHeroSettings failed: SQL: $sql | Error: " . $e->getMessage() . " | Keys: " . implode(', ', $keys));
            throw new Exception("Failed to fetch hero settings: " . $e->getMessage());
        }
    }

    public function setHeroSetting($key, $value) {
        $sql = "INSERT INTO contact_settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value";
        $this->db->query($sql);
        $this->db->bind(':key', $key);
        $this->db->bind(':value', $value);

        try {
            $success = $this->db->execute();
            if (!$success) {
                error_log("ContactModel::setHeroSetting: Failed to execute query for key=$key, value=$value");
                throw new Exception("Failed to execute query for setting $key");
            }
            error_log("ContactModel::setHeroSetting: Successfully updated $key = $value");
            return true;
        } catch (PDOException $e) {
            error_log("ContactModel::setHeroSetting failed: SQL: $sql | Key: $key | Value: $value | Error: " . $e->getMessage());
            throw new Exception("Failed to update setting $key: " . $e->getMessage());
        }
    }
}
?>