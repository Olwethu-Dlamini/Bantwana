<?php
class SettingModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
        try {
            $this->db->pdo->query("SELECT 1");
            error_log("SettingModel: Database connection successful");
        } catch (PDOException $e) {
            error_log("SettingModel: Database connection failed: " . $e->getMessage());
            throw new Exception("Failed to connect to database: " . $e->getMessage());
        }
    }

    /**
     * Get a single setting value by its key.
     *
     * @param string $key The setting key.
     * @param mixed $default The default value if the key is not found.
     * @return mixed The setting value or the default value.
     */
    public function get($key, $default = null) {
        $sql = "SELECT setting_value FROM settings WHERE setting_key = :key";
        $this->db->query($sql);
        $this->db->bind(':key', $key);
        try {
            $row = $this->db->single();
            $value = $row ? $row['setting_value'] : $default;
            error_log("SettingModel::get: Retrieved $key = " . json_encode($value));
            return $value;
        } catch (PDOException $e) {
            error_log("SettingModel::get failed: SQL: $sql | Key: $key | Error: " . $e->getMessage());
            throw new Exception("Failed to fetch setting $key: " . $e->getMessage());
        }
    }

    /**
     * Get multiple settings by an array of keys.
     *
     * @param array $keys Array of setting keys to fetch.
     * @param mixed $default The default value for missing keys.
     * @return array Associative array of key => value.
     */
    public function getSettings($keys, $default = null) {
        if (empty($keys)) {
            error_log("SettingModel::getSettings: No keys provided");
            throw new Exception("No setting keys provided");
        }

        $placeholders = str_repeat('?,', count($keys) - 1) . '?';
        $sql = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ($placeholders)";
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
            error_log("SettingModel::getSettings: Retrieved settings: " . json_encode($settings));
            return $settings;
        } catch (PDOException $e) {
            error_log("SettingModel::getSettings failed: SQL: $sql | Error: " . $e->getMessage() . " | Keys: " . implode(', ', $keys));
            throw new Exception("Failed to fetch settings: " . $e->getMessage());
        }
    }

    /**
     * Set or update a setting value.
     *
     * @param string $key The setting key.
     * @param string $value The setting value.
     * @return bool True on success.
     * @throws Exception on failure.
     */
    public function setSetting($key, $value) {
        $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value";
        $this->db->query($sql);
        $this->db->bind(':key', $key);
        $this->db->bind(':value', $value);

        try {
            $success = $this->db->execute();
            if (!$success) {
                error_log("SettingModel::setSetting: Failed to execute query for key=$key, value=$value");
                throw new Exception("Failed to execute query for setting $key");
            }
            error_log("SettingModel::setSetting: Successfully updated $key = $value");
            return true;
        } catch (PDOException $e) {
            error_log("SettingModel::setSetting failed: SQL: $sql | Key: $key | Value: $value | Error: " . $e->getMessage());
            throw new Exception("Failed to update setting $key: " . $e->getMessage());
        }
    }
}
?>