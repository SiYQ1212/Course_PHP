<?php
session_start();
header('Content-Type: application/json');
$response = ['status' => '', 'message' => ''];

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "xicoinfo";

// 创建连接
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    $response['status'] = 'error';
    $response['message'] = '系统错误，请稍后再试';
    echo json_encode($response);
    exit();
}

// 创建数据库和表的代码保持不变...
$sql = "CREATE DATABASE IF NOT EXISTS xicoinfo";
if ($conn->query($sql) === TRUE) {
    $conn->select_db($dbname);
    
    $sql = "CREATE TABLE IF NOT EXISTS info (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL UNIQUE,
        realname VARCHAR(30) NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(50) NOT NULL
    )";
    
    if ($conn->query($sql) === TRUE) {
        $username = $_POST['username'];
        $realname = $_POST['realname'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = $_POST['email'];
        
        // 检查用户名是否已存在
        $check = "SELECT * FROM info WHERE username = ?";
        $stmt = $conn->prepare($check);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response['status'] = 'error';
            $response['message'] = 'username_exists';
        } else {
            $sql = "INSERT INTO info (username, realname, password, email) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $realname, $password, $email);
            
            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'register_success';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'register_failed';
            }
        }
    }
}

$conn->close();
echo json_encode($response);
?>