<?php
// application/models/TeamMemberModel.php

class TeamMemberModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all team members ordered by sort_order.
     * @return array List of team members.
     */
    public function getAllTeamMembers() {
        $this->db->query("SELECT * FROM team_members ORDER BY sort_order ASC, id ASC");
        return $this->db->resultSet();
    }

    /**
     * Get a single team member by ID.
     * @param int $id Team member ID.
     * @return array|false Team member data or false if not found.
     */
    public function getTeamMemberById($id) {
        $this->db->query("SELECT * FROM team_members WHERE id = :id LIMIT 1");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

     /**
     * Create a new team member.
     * @param string $name Team member name.
     * @param string $title Team member title.
     * @param string $bio Team member biography (HTML).
     * @param string|null $image_filename Filename of the uploaded image (nullable).
     * @param int $sort_order Sort order.
     * @param string|null $social_twitter Twitter/X profile URL.
     * @param string|null $social_facebook Facebook profile URL.
     * @param string|null $social_linkedin LinkedIn profile URL.
     * @param int $is_board_member 1 if board member, 0 otherwise.
     * @param int $is_active 1 if active, 0 if inactive.
     * @return int|false New team member ID on success, false on failure.
     */
    public function createTeamMember($name, $title, $bio, $image_filename = null, $sort_order = 0, $social_twitter = null, $social_facebook = null, $social_linkedin = null, $is_board_member = 0, $is_active = 1) {
        try {
            $this->db->query("INSERT INTO team_members (name, title, bio, image_filename, sort_order, social_twitter, social_facebook, social_linkedin, is_board_member, is_active) VALUES (:name, :title, :bio, :image_filename, :sort_order, :social_twitter, :social_facebook, :social_linkedin, :is_board_member, :is_active)");
            $this->db->bind(':name', $name);
            $this->db->bind(':title', $title);
            $this->db->bind(':bio', $bio);
            // Allow null for image_filename
            $this->db->bind(':image_filename', $image_filename);
            $this->db->bind(':sort_order', $sort_order);
            $this->db->bind(':social_twitter', $social_twitter);
            $this->db->bind(':social_facebook', $social_facebook);
            $this->db->bind(':social_linkedin', $social_linkedin);
            $this->db->bind(':is_board_member', $is_board_member);
            $this->db->bind(':is_active', $is_active);

            if ($this->db->execute()) {
                return $this->db->pdo->lastInsertId();
            }
        } catch (PDOException $e) {
            error_log("TeamMemberModel::createTeamMember - Database Error: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Update an existing team member.
     * @param int $id Team member ID.
     * @param string $name Team member name.
     * @param string $title Team member title.
     * @param string $bio Team member biography (HTML).
     * @param string|null $image_filename New image filename (null if not changing).
     * @param int $sort_order Sort order.
     * @param string|null $social_twitter Twitter/X profile URL.
     * @param string|null $social_facebook Facebook profile URL.
     * @param string|null $social_linkedin LinkedIn profile URL.
     * @param int $is_board_member 1 if board member, 0 otherwise.
     * @param int $is_active 1 if active, 0 if inactive.
     * @return bool True on success, false on failure.
     */
    public function updateTeamMember($id, $name, $title, $bio, $image_filename = null, $sort_order = 0, $social_twitter = null, $social_facebook = null, $social_linkedin = null, $is_board_member = 0, $is_active = 1) {
        try {
            // Build the query dynamically based on whether image_filename is provided
            $sql = "UPDATE team_members SET name = :name, title = :title, bio = :bio, sort_order = :sort_order, social_twitter = :social_twitter, social_facebook = :social_facebook, social_linkedin = :social_linkedin, is_board_member = :is_board_member, is_active = :is_active";
            if ($image_filename !== null) {
                $sql .= ", image_filename = :image_filename";
            }
            $sql .= " WHERE id = :id";

            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':name', $name);
            $this->db->bind(':title', $title);
            $this->db->bind(':bio', $bio);
            if ($image_filename !== null) {
                $this->db->bind(':image_filename', $image_filename);
            }
            $this->db->bind(':sort_order', $sort_order);
            $this->db->bind(':social_twitter', $social_twitter);
            $this->db->bind(':social_facebook', $social_facebook);
            $this->db->bind(':social_linkedin', $social_linkedin);
            $this->db->bind(':is_board_member', $is_board_member);
            $this->db->bind(':is_active', $is_active);

            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("TeamMemberModel::updateTeamMember - Database Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a team member by ID.
     * @param int $id Team member ID.
     * @return bool True on success, false on failure.
     */
    public function deleteTeamMember($id) {
        try {
            // Optional: Delete the physical image file from the server here
            // $member = $this->getTeamMemberById($id);
            // if ($member && !empty($member['image_filename'])) {
            //     $imagePath = BASE_PATH . '/public_html/images/team/' . $member['image_filename'];
            //     if (file_exists($imagePath)) {
            //         unlink($imagePath);
            //     }
            // }

            $this->db->query("DELETE FROM team_members WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("TeamMemberModel::deleteTeamMember - Database Error: " . $e->getMessage());
            return false;
        }
    }
}
?>