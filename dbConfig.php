<?php
class Database {
    private static $instance = null;
    private $conn;
    
    private $servername = "localhost";
    private $username = "root";
    private $password = "root";
    private $dbname = "xicoinfo";
    
    private function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password);
        
        if ($this->conn->connect_error) {
            throw new Exception("连接失败: " . $this->conn->connect_error);
        }
        
        // 创建数据库和表
        $this->initDatabase();
    }
    
    private function initDatabase() {
        // 创建数据库
        $this->conn->query("CREATE DATABASE IF NOT EXISTS {$this->dbname}");
        $this->conn->select_db($this->dbname);
        
        // 创建用户表
        $sql = "CREATE TABLE IF NOT EXISTS info (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(30) NOT NULL UNIQUE,
            realname VARCHAR(30) NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->conn->query($sql);
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
}
?> 