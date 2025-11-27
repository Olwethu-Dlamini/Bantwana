<?php
// Ensure Database class is available (loaded via config/autoload)
class HomeModel {
    private $db; // Instance of Database class

    public function __construct() {
        $this->db = new Database(); // Assuming Database class is correctly implemented
    }

    // Example method to get data
    public function getWelcomeMessage() {
         // Example: Fetch from database or return static data for now
         // $this->db->query("SELECT message FROM site_info WHERE id = 1");
         // $row = $this->db->single();
         // return $row ? $row['message'] : 'Default Message';
         return "Welcome! This message came from the HomeModel.";
    }

    // Add more methods as needed for data interaction
}
?>