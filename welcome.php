<?php
session_start();
if (!isset($_SESSION['user_role'])) { // 检查用户是否已登录
    header('Location: login.php'); // 重定向到登录页面
    exit; // 停止进一步执行
}
?><!DOCTYPE html>
<html>
<head>
    <title>欢迎</title>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/common1.css">
    <style>
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 20px;
            padding: 20px;
            max-width: 900px;
            width: 90%;
        }


        .grid-item {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            aspect-ratio: 1;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 40px;
            color: white;
            text-decoration: none;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: 0.3s;
        }
        
        .grid-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.5);
        }

        .admin-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 5px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            text-decoration: none;
        }

        .admin-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .admin-button i {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <?php if ($_SESSION['user_role'] === 'super_admin'): ?>
        <a href="functionalPages/admin/adminPage.php" class="admin-button">
            <i class="fas fa-user-shield"></i>
            <span>超级管理员面板</span>
        </a>
    <?php endif; ?>
    <button class="back-button" onclick="logout();">
        <i class="icon fa-solid fa-cat"></i>
        <span class="text">退出登录</span>
    </button>
    <script>
    function logout() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "logout.php", true);
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                window.location.href = '../login.php';
            }
        };
        xhr.send();
    }
    </script>
    <div class="grid-container">
        <a href="functionalPages/uploadFile.php" class="grid-item">课表上传<h5>💻</h5></a>
        <a href="functionalPages/testSend.php" class="grid-item">测试发送<h5>💌</h5></a>
        <a href="functionalPages/account.php" class="grid-item">账号管理<h5>✉️</h5></a>
        <div class="grid-item">功能暂未开放</div>
        <a href="functionalPages/joinUs.php" class="grid-item">作为贡献者<h5>👨‍💻</h5></a>
        <div class="grid-item">功能暂未开放</div>
    </div>
</body>
</html>