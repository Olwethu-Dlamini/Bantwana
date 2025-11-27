<?php
class CareerModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
        try {
            $this->db->pdo->query("SELECT 1");
            error_log("CareerModel: Database connection successful");
        } catch (PDOException $e) {
            error_log("CareerModel: Database connection failed: " . $e->getMessage());
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
            error_log("CareerModel::getHeroSettings: Retrieved settings: " . print_r($settings, true));
            return $settings;
        } catch (PDOException $e) {
            error_log("CareerModel::getHeroSettings failed: " . $e->getMessage() . " | SQL: $sql");
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
            error_log("CareerModel::setHeroSetting: Successfully updated $key = $value");
            return true;
        } catch (PDOException $e) {
            error_log("CareerModel::setHeroSetting failed: " . $e->getMessage() . " | SQL: $sql | Key: $key | Value: $value");
            return false;
        }
    }

    public function getAllJobs() {
        $this->db->query("SELECT * FROM jobs ORDER BY sort_order ASC, id ASC");
        try {
            $results = $this->db->resultSet();
            error_log("CareerModel::getAllJobs: Retrieved " . count($results) . " jobs");
            return $results;
        } catch (PDOException $e) {
            error_log("CareerModel::getAllJobs failed: " . $e->getMessage());
            return [];
        }
    }

    public function getJobById($id) {
        $this->db->query("SELECT * FROM jobs WHERE id = :id LIMIT 1");
        $this->db->bind(':id', $id);
        try {
            $result = $this->db->single();
            error_log("CareerModel::getJobById: " . ($result ? "Found job ID $id" : "Job ID $id not found"));
            return $result;
        } catch (PDOException $e) {
            error_log("CareerModel::getJobById failed: " . $e->getMessage());
            return false;
        }
    }

    public function createJob($title, $description, $location, $type, $deadline, $sort_order = 0) {
        try {
            $this->db->query("INSERT INTO jobs (title, description, location, type, deadline, sort_order) VALUES (:title, :description, :location, :type, :deadline, :sort_order)");
            $this->db->bind(':title', $title);
            $this->db->bind(':description', $description);
            $this->db->bind(':location', $location);
            $this->db->bind(':type', $type);
            $this->db->bind(':deadline', $deadline);
            $this->db->bind(':sort_order', $sort_order);

            if ($this->db->execute()) {
                $newId = $this->db->pdo->lastInsertId();
                error_log("CareerModel::createJob: Created job ID $newId: $title");
                return $newId;
            }
            error_log("CareerModel::createJob: Failed to create job: $title");
            return false;
        } catch (PDOException $e) {
            error_log("CareerModel::createJob failed: " . $e->getMessage() . " | Title: $title");
            return false;
        }
    }

    public function updateJob($id, $title, $description, $location, $type, $deadline, $sort_order = 0) {
        try {
            $sql = "UPDATE jobs SET title = :title, description = :description, location = :location, type = :type, deadline = :deadline, sort_order = :sort_order WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':title', $title);
            $this->db->bind(':description', $description);
            $this->db->bind(':location', $location);
            $this->db->bind(':type', $type);
            $this->db->bind(':deadline', $deadline);
            $this->db->bind(':sort_order', $sort_order);

            $success = $this->db->execute();
            error_log("CareerModel::updateJob: " . ($success ? "Updated job ID $id: $title" : "Failed to update job ID $id"));
            return $success;
        } catch (PDOException $e) {
            error_log("CareerModel::updateJob failed: " . $e->getMessage() . " | ID: $id | Title: $title");
            return false;
        }
    }

    public function deleteJob($id) {
        try {
            $this->db->query("DELETE FROM jobs WHERE id = :id");
            $this->db->bind(':id', $id);
            $success = $this->db->execute();
            error_log("CareerModel::deleteJob: " . ($success ? "Deleted job ID $id" : "Failed to delete job ID $id"));
            return $success;
        } catch (PDOException $e) {
            error_log("CareerModel::deleteJob failed: " . $e->getMessage() . " | ID: $id");
            return false;
        }
    }
}