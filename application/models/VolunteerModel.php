<?php
class VolunteerModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Create a new volunteer signup record.
     * @param array $data Associative array containing volunteer data.
     *                       Expected keys: 'name', 'email', 'phone', 'interests', 'availability', 'message'
     * @return int|false New volunteer ID on success, false on failure.
     */
    public function createVolunteer($data) {
        try {
            // Validate inputs
            $errors = [];
            if (empty($data['name']) || strlen($data['name']) > 255) {
                $errors[] = 'Name is required and must be 255 characters or less.';
            }
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 255) {
                $errors[] = 'A valid email (255 characters or less) is required.';
            }
            if (!empty($data['phone']) && !preg_match('/^[\d\s\-\+\(\)]{0,50}$/', $data['phone'])) {
                $errors[] = 'Invalid phone number format.';
            }
            if (!empty($data['interests']) && !in_array($data['interests'], ['mentoring', 'community', 'office', 'skills', 'other', ''])) {
                $errors[] = 'Invalid interest selected.';
            }
            if (!empty($data['availability']) && strlen($data['availability']) > 65535) {
                $errors[] = 'Availability must be 65,535 characters or less.';
            }
            if (!empty($data['message']) && strlen($data['message']) > 65535) {
                $errors[] = 'Message must be 65,535 characters or less.';
            }

            // Check for duplicate email
            $this->db->query("SELECT id FROM volunteers WHERE email = :email");
            $this->db->bind(':email', $data['email']);
            if ($this->db->single()) {
                $errors[] = 'This email is already registered.';
            }

            if (!empty($errors)) {
                error_log("VolunteerModel::createVolunteer - Validation Errors: " . implode(', ', $errors));
                return false;
            }

            $this->db->query("INSERT INTO volunteers (name, email, phone, interests, availability, message, ip_address, user_agent) VALUES (:name, :email, :phone, :interests, :availability, :message, :ip_address, :user_agent)");
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':interests', $data['interests']);
            $this->db->bind(':availability', $data['availability']);
            $this->db->bind(':message', $data['message']);
            $this->db->bind(':ip_address', $_SERVER['REMOTE_ADDR'] ?? '');
            $this->db->bind(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');

            if ($this->db->execute()) {
                return $this->db->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("VolunteerModel::createVolunteer - Database Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all volunteer signup records (for admin view).
     * @param string $orderBy Column to order by (default: 'created_at').
     * @param string $orderDirection Direction (ASC or DESC, default: DESC).
     * @return array List of volunteer records.
     */
    public function getAllVolunteers($orderBy = 'created_at', $orderDirection = 'DESC') {
        try {
            $allowedColumns = ['id', 'name', 'email', 'phone', 'interests', 'availability', 'message', 'created_at', 'ip_address', 'user_agent'];
            if (!in_array($orderBy, $allowedColumns)) {
                $orderBy = 'created_at';
            }
            $orderDirection = strtoupper($orderDirection) === 'DESC' ? 'DESC' : 'ASC';

            $sql = "SELECT * FROM volunteers ORDER BY $orderBy $orderDirection, id ASC";
            $this->db->query($sql);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("VolunteerModel::getAllVolunteers - Database Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a single volunteer record by ID (for admin view/details).
     * @param int $id Volunteer ID.
     * @return array|false Volunteer data or false if not found.
     */
    public function getVolunteerById($id) {
        try {
            $this->db->query("SELECT * FROM volunteers WHERE id = :id LIMIT 1");
            $this->db->bind(':id', $id);
            return $this->db->single();
        } catch (PDOException $e) {
            error_log("VolunteerModel::getVolunteerById - Database Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a volunteer signup record by ID.
     * @param int $id Volunteer ID.
     * @return bool True on success, false on failure.
     */
    public function deleteVolunteer($id) {
        try {
            $this->db->query("DELETE FROM volunteers WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("VolunteerModel::deleteVolunteer - Database Error: " . $e->getMessage());
            return false;
        }
    }
}