<?php
// application/models/JobModel.php
class JobModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
        try {
            $this->db->pdo->query("SELECT 1");
            error_log("JobModel: Database connection successful");
        } catch (PDOException $e) {
            error_log("JobModel: Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed. Check logs for details.");
        }
    }

    /**
     * Get all jobs for admin panel (includes inactive jobs)
     * @param string $orderBy Column to sort by
     * @param string $orderDirection ASC or DESC
     * @return array List of all jobs
     */
    public function getAllJobs($orderBy = 'sort_order', $orderDirection = 'ASC'): array {
        try {
            $allowedColumns = ['id', 'title', 'location', 'type', 'deadline', 'is_active', 'sort_order', 'created_at'];
            $orderBy = in_array($orderBy, $allowedColumns) ? $orderBy : 'sort_order';
            $orderDirection = strtoupper($orderDirection) === 'DESC' ? 'DESC' : 'ASC';
            
            $sql = "SELECT * FROM jobs ORDER BY $orderBy $orderDirection, id ASC";
            $this->db->query($sql);
            $results = $this->db->resultSet();
            
            error_log("JobModel::getAllJobs: Retrieved " . count($results) . " jobs");
            return $results ?: [];
        } catch (PDOException $e) {
            error_log("JobModel::getAllJobs failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get only active jobs for public display
     * @param string $orderBy Column to sort by
     * @param string $orderDirection ASC or DESC
     * @return array List of active jobs
     */
    public function getAllActiveJobs($orderBy = 'sort_order', $orderDirection = 'ASC'): array {
        try {
            $allowedColumns = ['id', 'title', 'location', 'type', 'deadline', 'sort_order', 'created_at'];
            $orderBy = in_array($orderBy, $allowedColumns) ? $orderBy : 'sort_order';
            $orderDirection = strtoupper($orderDirection) === 'DESC' ? 'DESC' : 'ASC';
            
            $sql = "SELECT * FROM jobs WHERE is_active = 1 ORDER BY $orderBy $orderDirection, id ASC";
            $this->db->query($sql);
            $results = $this->db->resultSet();
            
            error_log("JobModel::getAllActiveJobs: Retrieved " . count($results) . " active jobs");
            return $results ?: [];
        } catch (PDOException $e) {
            error_log("JobModel::getAllActiveJobs failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a single job by ID
     * @param int $id Job ID
     * @return array|null Job data or null if not found
     */
    public function getJobById(int $id): ?array {
        try {
            $this->db->query("SELECT * FROM jobs WHERE id = :id LIMIT 1");
            $this->db->bind(':id', $id, PDO::PARAM_INT);
            $result = $this->db->single();
            
            error_log("JobModel::getJobById: " . ($result ? "Found job ID $id" : "Job ID $id not found"));
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("JobModel::getJobById failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new job
     * @param string $title Job title
     * @param string $location Job location
     * @param string $type Job type (Full-time, Part-time, etc.)
     * @param string $deadline Application deadline (YYYY-MM-DD format or empty)
     * @param string $description Job description
     * @param string $requirements Job requirements
     * @param string $responsibilities Job responsibilities
     * @param string $benefits Job benefits
     * @param string $apply_link External application link
     * @param int $is_active Active status (1 for active, 0 for inactive)
     * @param int $sort_order Sort order
     * @param int $created_by User ID who created the job
     * @return int|false New job ID on success, false on failure
     */
    public function createJob(
        string $title,
        string $location,
        string $type = 'Full-time',
        string $deadline = '',
        string $description = '',
        string $requirements = '',
        string $responsibilities = '',
        string $benefits = '',
        string $apply_link = '',
        int $is_active = 1,
        int $sort_order = 0,
        int $created_by = null
    ) {
        try {
            // Validate required fields
            $title = trim($title);
            $location = trim($location);
            $description = trim($description);
            
            if (empty($title) || empty($location) || empty($description)) {
                throw new Exception("Title, location, and description are required");
            }
            
            // Handle empty deadline
            $deadline = !empty(trim($deadline)) ? trim($deadline) : null;
            
            $sql = "INSERT INTO jobs (title, location, type, deadline, description, requirements, responsibilities, benefits, apply_link, is_active, sort_order, created_by, created_at) 
                    VALUES (:title, :location, :type, :deadline, :description, :requirements, :responsibilities, :benefits, :apply_link, :is_active, :sort_order, :created_by, NOW())";
            
            $this->db->query($sql);
            $this->db->bind(':title', $title);
            $this->db->bind(':location', $location);
            $this->db->bind(':type', $type);
            $this->db->bind(':deadline', $deadline);
            $this->db->bind(':description', $description);
            $this->db->bind(':requirements', $requirements);
            $this->db->bind(':responsibilities', $responsibilities);
            $this->db->bind(':benefits', $benefits);
            $this->db->bind(':apply_link', $apply_link);
            $this->db->bind(':is_active', $is_active, PDO::PARAM_INT);
            $this->db->bind(':sort_order', $sort_order, PDO::PARAM_INT);
            $this->db->bind(':created_by', $created_by, PDO::PARAM_INT);

            if ($this->db->execute()) {
                $newId = (int) $this->db->pdo->lastInsertId();
                error_log("JobModel::createJob: Created job ID $newId: $title");
                return $newId;
            }
            
            error_log("JobModel::createJob: Failed to create job: $title");
            return false;
        } catch (Exception $e) {
            error_log("JobModel::createJob failed: " . $e->getMessage() . " | Title: $title");
            return false;
        }
    }

    /**
     * Update an existing job
     * @param int $id Job ID
     * @param string $title Job title
     * @param string $location Job location
     * @param string $type Job type
     * @param string $deadline Application deadline
     * @param string $description Job description
     * @param string $requirements Job requirements
     * @param string $responsibilities Job responsibilities
     * @param string $benefits Job benefits
     * @param string $apply_link External application link
     * @param int $is_active Active status
     * @param int $sort_order Sort order
     * @return bool True on success, false on failure
     */
    public function updateJob(
        int $id,
        string $title,
        string $location,
        string $type = 'Full-time',
        string $deadline = '',
        string $description = '',
        string $requirements = '',
        string $responsibilities = '',
        string $benefits = '',
        string $apply_link = '',
        int $is_active = 1,
        int $sort_order = 0
    ): bool {
        try {
            // Validate required fields
            $title = trim($title);
            $location = trim($location);
            $description = trim($description);
            
            if (empty($title) || empty($location) || empty($description)) {
                throw new Exception("Title, location, and description are required");
            }
            
            // Handle empty deadline
            $deadline = !empty(trim($deadline)) ? trim($deadline) : null;
            
            $sql = "UPDATE jobs SET 
                    title = :title, 
                    location = :location, 
                    type = :type, 
                    deadline = :deadline, 
                    description = :description, 
                    requirements = :requirements, 
                    responsibilities = :responsibilities, 
                    benefits = :benefits, 
                    apply_link = :apply_link, 
                    is_active = :is_active, 
                    sort_order = :sort_order, 
                    updated_at = NOW() 
                    WHERE id = :id";
            
            $this->db->query($sql);
            $this->db->bind(':id', $id, PDO::PARAM_INT);
            $this->db->bind(':title', $title);
            $this->db->bind(':location', $location);
            $this->db->bind(':type', $type);
            $this->db->bind(':deadline', $deadline);
            $this->db->bind(':description', $description);
            $this->db->bind(':requirements', $requirements);
            $this->db->bind(':responsibilities', $responsibilities);
            $this->db->bind(':benefits', $benefits);
            $this->db->bind(':apply_link', $apply_link);
            $this->db->bind(':is_active', $is_active, PDO::PARAM_INT);
            $this->db->bind(':sort_order', $sort_order, PDO::PARAM_INT);

            $success = $this->db->execute();
            error_log("JobModel::updateJob: " . ($success ? "Updated job ID $id: $title" : "Failed to update job ID $id"));
            return $success;
        } catch (Exception $e) {
            error_log("JobModel::updateJob failed: " . $e->getMessage() . " | ID: $id | Title: $title");
            return false;
        }
    }

    /**
     * Delete a job
     * @param int $id Job ID
     * @return bool True on success, false on failure
     */
    public function deleteJob(int $id): bool {
        try {
            $this->db->query("DELETE FROM jobs WHERE id = :id");
            $this->db->bind(':id', $id, PDO::PARAM_INT);
            $success = $this->db->execute();
            
            error_log("JobModel::deleteJob: " . ($success ? "Deleted job ID $id" : "Failed to delete job ID $id"));
            return $success;
        } catch (PDOException $e) {
            error_log("JobModel::deleteJob failed: " . $e->getMessage() . " | ID: $id");
            return false;
        }
    }

    /**
     * Toggle active status of a job
     * @param int $id Job ID
     * @param int $is_active New active status (0 or 1)
     * @return bool True on success, false on failure
     */
    public function toggleActiveStatus(int $id, int $is_active): bool {
        try {
            $this->db->query("UPDATE jobs SET is_active = :is_active, updated_at = NOW() WHERE id = :id");
            $this->db->bind(':id', $id, PDO::PARAM_INT);
            $this->db->bind(':is_active', $is_active, PDO::PARAM_INT);
            $success = $this->db->execute();
            
            $status = $is_active ? 'activated' : 'deactivated';
            error_log("JobModel::toggleActiveStatus: " . ($success ? "Job ID $id $status" : "Failed to toggle job ID $id"));
            return $success;
        } catch (PDOException $e) {
            error_log("JobModel::toggleActiveStatus failed: " . $e->getMessage() . " | ID: $id");
            return false;
        }
    }

    /**
     * Get jobs count by status
     * @return array Associative array with 'active' and 'inactive' counts
     */
    public function getJobsCountByStatus(): array {
        try {
            $this->db->query("SELECT 
                              SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                              SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
                              FROM jobs");
            $result = $this->db->single();
            
            return [
                'active' => (int)($result['active'] ?? 0),
                'inactive' => (int)($result['inactive'] ?? 0)
            ];
        } catch (PDOException $e) {
            error_log("JobModel::getJobsCountByStatus failed: " . $e->getMessage());
            return ['active' => 0, 'inactive' => 0];
        }
    }
}