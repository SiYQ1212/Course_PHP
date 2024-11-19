<?php
    session_start(); // 开始会话
    $_SESSION = array(); // 清空会话变量
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy(); // 销毁会话
    header('Location: login.php'); // 重定向到登录页面
    exit;
?>