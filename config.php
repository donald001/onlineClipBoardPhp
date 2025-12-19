<?php
// config.php - 数据库配置
class Database {
    private $host = '';
    private $db_name = '';
    private $username = '';
    private $password = '';
    private $conn;
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "连接失败: " . $e->getMessage();
        }
        return $this->conn;
    }
}
?>