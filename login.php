<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>登录</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/common2.css">
    <style>

        body {
            background-image: url('images/background.png');
        }
    </style>
</head>
<body>
    <!-- 添加飞行物容器 -->
    <div class="flying-elements" id="flyingElements"></div>

    <div class="container">
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="message error">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="message success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        ?>
        <h2>欢迎登录</h2>
        <form action="checkLogin.php" method="post">
            <div class="form-group">
                <label>学号：</label>
                <input type="text" name="username" required 
                    value="<?php echo isset($_SESSION['login_username']) ? htmlspecialchars($_SESSION['login_username']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>密码：</label>
                <input type="password" name="password" required
                    value="<?php echo isset($_SESSION['login_password']) ? htmlspecialchars($_SESSION['login_password']) : ''; ?>">
                <span class="password-toggle" onclick="togglePassword(this)">👁️</span>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn_action">登 录</button>
                <a href="register.php" class="action_link">还没有账号？立即注册</a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(element) {
            const input = element.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                element.textContent = '🔒';
            } else {
                input.type = 'password';
                element.textContent = '👁️';
            }
        }

        /* 添加飞行物生成脚本 */
        function createFlyingElements() {
            const container = document.getElementById('flyingElements');
            const colors = ['#4a90e2', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6'];
            
            function createElement() {
                const element = document.createElement('div');
                element.className = 'flying-element';
                
                // 随机大小和颜色
                const size = Math.random() * 30 + 10;
                const color = colors[Math.floor(Math.random() * colors.length)];
                
                // 随机起始位置
                const startX = Math.random() * window.innerWidth;
                const startY = Math.random() * window.innerHeight;
                
                element.style.width = `${size}px`;
                element.style.height = `${size}px`;
                element.style.left = `${startX}px`;
                element.style.top = `${startY}px`;
                element.style.background = color;
                element.style.boxShadow = `0 0 ${size/2}px ${color}`;
                
                // 机动画延迟，使元素不同步移动
                element.style.animationDelay = `${Math.random() * -20}s`;
                
                container.appendChild(element);
            }

            // 初始创建多个元素
            for(let i = 0; i < 45; i++) {
                createElement();
            }

            // 每隔一段时间检查并补充元素
            setInterval(() => {
                const currentElements = container.getElementsByClassName('flying-element');
                if (currentElements.length < 15) {
                    createElement();
                }
            }, 3000);
        }

        /* 页面加载完成后启动动画 */
        window.addEventListener('load', createFlyingElements);
    </script>
</body>
</html> 