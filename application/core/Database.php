<?php


require_once BASE_PATH . '/application/config/config.php'; // Ensure config is loaded

class Database {
    // --- Explicitly declare properties to avoid deprecation warnings in PHP 8.2+ ---
    private $host;
    private $user;
    private $pass;
    private $dbname;

    public $pdo;        // Public property for PDO connection
    public $stmt;       // <--- ADD THIS LINE: Declare the $stmt property
    private $error;     // Private property for error messages

    // --- End of property declarations ---

    public function __construct() {
        // Initialize properties from config (as you likely already do)
        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
        $this->dbname = DB_NAME;

        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Fetch associative arrays by default
        );
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            // Log error or handle gracefully
            error_log("Database Connection Failed: " . $this->error);
            die("Database Connection Failed. Please check the logs.");
        }
    }

    // Example method to prepare a statement (you can add more as needed)
    public function query($query) {
        // Assigning to $this->stmt is now okay because it's declared above
        $this->stmt = $this->pdo->prepare($query);
    }

    // Example method to bind values (you can add more as needed)
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        // Binding to $this->stmt is now okay
        $this->stmt->bindValue($param, $value, $type);
    }

    // Execute the prepared statement
    public function execute() {
        // Executing $this->stmt is now okay
        return $this->stmt->execute();
    }

    // Get result set as associative array
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    // Get single record as associative array
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }

    // Get row count (Note: Not always reliable with SELECT)
    public function rowCount() {
        return $this->stmt->rowCount();
    }
}
?>