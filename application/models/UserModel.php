<?php
// application/models/UserModel.php

class UserModel {
    private $db; // Instance of Database class

    public function __construct() {
        $this->db = new Database(); // Assuming Database class is correctly implemented
    }

    /**
     * Find a user by username or email.
     *
     * @param string $identifier The username or email to search for.
     * @return array|false The user data array or false if not found.
     */
    public function findByUsernameOrEmail($identifier) {
        $sql = "SELECT id, username, email, password FROM users WHERE username = :identifier OR email = :identifier";
        $this->db->query($sql);
        $this->db->bind(':identifier', $identifier);
        $user = $this->db->single();

        return $user ? $user : false;
    }

    /**
     * Create a new user (for initial setup or user management).
     * Remember to hash the password before passing it here.
     *
     * @param string $username
     * @param string $email
     * @param string $hashedPassword
     * @return bool True on success, false on failure.
     */
    // public function createUser($username, $email, $hashedPassword) {
    //     $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
    //     $this->db->query($sql);
    //     $this->db->bind(':username', $username);
    //     $this->db->bind(':email', $email);
    //     $this->db->bind(':password', $hashedPassword);
    //
    //     try {
    //         $this->db->execute();
    //         return true;
    //     } catch (PDOException $e) {
    //         error_log("User creation failed: " . $e->getMessage());
    //         return false;
    //     }
    // }

    // Add other user-related methods as needed (e.g., updateUser, deleteUser)
}
?>