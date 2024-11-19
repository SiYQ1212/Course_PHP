<?php
session_start();
require_once 'dbConfig.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $input_username = $_POST['username'] ?? '';
    $input_password = $_POST['password'] ?? '';
    
    // 保存用户输入
    $_SESSION['login_username'] = $input_username;
    $_SESSION['login_password'] = $input_password;
    
    if (empty($input_username) || empty($input_password)) {
        throw new Exception("请填写用户名和密码");
    }
    
    // 检查是否为超级管理员
    if ($input_username === 'admin' && $input_password === 'admin123') {
        // 为超级管理员设置必要的会话变量
        $_SESSION['user_id'] = 0; // 给超级管理员一个特殊的ID
        $_SESSION['user_role'] = 'super_admin';
        $_SESSION['username'] = 'admin';
        $_SESSION['success'] = "超级管理员登录成功！";
        unset($_SESSION['login_username'], $_SESSION['login_password']);
        header("Location: welcome.php");
        exit();
    }
    
    $stmt = $conn->prepare("SELECT * FROM info WHERE username = ?");
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($input_password, $user['password'])) {
            // 普通用户登录成功
            unset($_SESSION['login_username'], $_SESSION['login_password']);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = 'user'; // 明确设置为普通用户
            $_SESSION['success'] = "登录成功！";
            header("Location: welcome.php");
            exit();
        }
        throw new Exception("密码错误！");
    }
    throw new Exception("用户不存在！");
    
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: login.php");
    exit();
}
?>