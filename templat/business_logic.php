
<?php
require_once 'database.php';

class Product {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function insert($name, $price, $category_id, $image_path) {
        return $this->db->insert("Products", 
            ["name", "price", "c_id", "image_path", "available"], 
            [$name, $price, $category_id, $image_path, 'available']);
    }

    public function getAllCategories() {
        return $this->db->selectAll("Category");
    }

    public function getAll() {
        return $this->db->select("Products", ["P_id", "name", "price", "image_path"]);
    }

    public function getById($id) {
        $result = $this->db->select("Products", ["name", "price", "image_path"], "P_id = ?", [$id]);
        return $result ? $result[0] : null;
    }
    
    public function update($id, $name, $price, $image_path) {
        return $this->db->update("Products", ["name", "price", "image_path"], [$name, $price, $image_path, $id], "P_id = ?");
    }
    
    public function delete($productId) {
        try {
            $this->db->delete("order_contents", "P_id = ?", [$productId]);
            return $this->db->delete("Products", "P_id = ?", [$productId]); 
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
