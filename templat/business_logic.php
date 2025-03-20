<?php
class Product {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function insert($name, $price, $category_id, $image_path) {
        $available = 'available';
        return $this->db->insert("Products", 
            ["name", "price", "c_id", "image_path", "available"], 
            [$name, $price, $category_id, $image_path, $available]);
    }

    public function getAllCategories() {
        return $this->db->selectAll("Category");
    }

    public function getAll() {
        return $this->db->select(
            "Products",
            ["P_id", "name", "price", "image_path"] 
        );
    }
    

    public function getById($id) {
        $sql = "SELECT name, price, image_path FROM Products WHERE P_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $name, $price, $image_path) {
        $sql = "UPDATE Products SET name = ?, price = ?, image_path = ? WHERE P_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$name, $price, $image_path, $id]);
    }
    

    public function delete($productId) {
        try {
            // تأكد من حذف الطلبات المرتبطة بالمنتج أولًا
            $this->db->delete('order_contents', 'P_id = ?', [$productId]);
    
            // الآن احذف المنتج من جدول products
            return $this->db->delete('products', 'P_id = ?', [$productId]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
}
?>
