
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'admin');
define('DB_PASS', 'admin1234');
define('DB_NAME', 'cafeteria');
class DatabaseConfig {
    private $host;
    private $user;
    private $pass;
    private $dbname;

    public function __construct() {
        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
        $this->dbname = DB_NAME;
    }

    public function getHost() {
        return $this->host;
    }

    public function getUser() {
        return $this->user;
    }

    public function getPass() {
        return $this->pass;
    }

    public function getDbName() {
        return $this->dbname;
    }
}

?>
