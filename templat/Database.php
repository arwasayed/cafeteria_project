<?php
require_once 'config.php';
class Databases {
    private $pdo;
    private $operation;
    private $params;
    public function __construct() {
        $dbConfig = new DatabaseConfig();
        $host = $dbConfig->getHost();
        $user = $dbConfig->getUser();
        $pass = $dbConfig->getPass();
        $dbname = $dbConfig->getDbName();

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    public function getConnection() {
        return $this->pdo;
    }
    public function getPdo() {
        return $this->pdo;
    }

    public function setOperation($operation, $params) {
        $this->operation = $operation;
        $this->params = $params;
    }

    public function getOperation() {
        return $this->operation;
    }

    public function executeQuery() {
        switch ($this->operation) {
            case 'myinsert':
                return $this->insert(
                    $this->params['tablename'],
                    $this->params['columns'],
                    $this->params['values']
                );
            case 'myupdate':
                return $this->update(
                    $this->params['tablename'],
                    $this->params['columns'],
                    $this->params['values'],
                    $this->params['condition']
                );
            case 'mydelete':
                return $this->delete(
                    $this->params['tablename'],
                    $this->params['condition'],
                    $this->params['params']
                );
            case 'select':
                return $this->select(
                    $this->params['tablename'],
                    $this->params['columns'],
                    $this->params['condition'] ?? "",
                    $this->params['params'] ?? []
                );
            default:
                throw new Exception("Invalid operation: " . $this->operation);
        }
    }
    
    private function insert($table, $columns, $values) {
        $colNames = implode(", ", $columns);
        $placeholders = implode(", ", array_fill(0, count($values), "?"));
        $sql = "INSERT INTO $table ($colNames) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }
    public function selectAll($table) {
        $stmt = $this->pdo->query("SELECT * FROM $table");
        return $stmt->fetchAll();
    }
    public function select($table, $columns, $condition = "", $params = []) {
        $colNames = implode(", ", $columns);
        $sql = "SELECT $colNames FROM $table";
        if (!empty($condition)) {
            $sql .= " WHERE $condition";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    private function update($table, $columns, $values, $condition) {
        $setClause = implode(" = ?, ", $columns) . " = ?";
        $sql = "UPDATE $table SET $setClause WHERE $condition";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }
    private function delete($table, $condition, $params = []) {
        $sql = "DELETE FROM $table WHERE $condition";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
?>