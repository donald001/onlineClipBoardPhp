<?php
class Clipboard {
    private $conn;
    private $table_name = "clipboard_items";
    public function __construct($db) {
        $this->conn = $db;
    }
    // 创建剪贴板项
    public function create($content, $title = '') {
        $query = "INSERT INTO " . $this->table_name . " 
                  (title, content, created_at, expires_at) 
                  VALUES (:title, :content, NOW(), DATE_ADD(NOW(), INTERVAL 5 HOUR))";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    // 获取所有未过期的剪贴板项
    public function getAll() {
        // 先删除过期项
        $this->deleteExpired();
        
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE expires_at > NOW() 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // 根据ID获取单个项
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = :id AND expires_at > NOW()";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 更新剪贴板项
    public function update($id, $content, $title = '') {
        $query = "UPDATE " . $this->table_name . " 
                  SET title = :title, content = :content 
                  WHERE id = :id AND expires_at > NOW()";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        
        return $stmt->execute();
    }
    // 删除剪贴板项
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    // 删除过期项
    public function deleteExpired() {
        $query = "DELETE FROM " . $this->table_name . " WHERE expires_at <= NOW()";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
    // 获取剩余时间（分钟）
    public function getTimeRemaining($expires_at) {
        $now = new DateTime();
        $expires = new DateTime($expires_at);
        $diff = $now->diff($expires);
        
        $hours = $diff->h + ($diff->days * 24);
        $minutes = $diff->i;
        
        return ['hours' => $hours, 'minutes' => $minutes];
    }
}
?>