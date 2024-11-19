<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>注册</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/common2.css">
    <style>
    
        body {
            background-image: url('images/background.png');
        }

        /* 添加表单验证提示样式 */
        .form-group .hint {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }

        .form-group {
            position: relative;
        }

        .form-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        /* 密码强度指示器 */
        .password-strength {
            height: 5px;
            margin-top: 8px;
            border-radius: 3px;
            background: #eee;
            overflow: hidden;
        }

        .password-strength span {
            display: block;
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }

        .message {
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease-out;
            text-align: center;
        }

        .error-text {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            display: none;
        }

        .success-message {
            background: white;
            padding: 20px 40px;
            border-radius: 10px;
            font-size: 18px;
            color: #155724;
        }
    </style>
</head>
<body>
    <!-- 飞行物容器 -->
    <div class="flying-elements" id="flyingElements"></div>

    <!-- 添加成功提示遮罩层 -->
    <div class="success-overlay">
        <div class="success-message">注册成功！</div>
    </div>

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
        <h2>创建账号</h2>
        <form action="do_register.php" method="post" id="registerForm">
            <div class="form-group">
                <label>用户名</label>
                <input type="text" name="username" required placeholder="请输入用户名(长度8-15个字符)">
                <div class="error-text" id="username-error">该用户名已存在</div>
            </div>
            <div class="form-group">
                <label>真实姓名</label>
                <input type="text" name="realname" required placeholder="请输入姓名">
            </div>
            <div class="form-group">
                <label>邮箱地址</label>
                <input type="email" name="email" required placeholder="请输入有效的邮箱地址">
            </div>
            <div class="form-group">
                <label>设置密码</label>
                <input type="password" name="password" required placeholder="请输入密码(建议使用字母、数字和符号的组合)">
                <span class="password-toggle" onclick="togglePassword(this)">👁️</span>
                <div class="password-strength">
                    <span></span>
                </div>
            </div>
            <div class="form-group">
                <label>确认密码</label>
                <input type="password" name="confirm_password" required placeholder="请再次输入密码">
                <span class="password-toggle" onclick="togglePassword(this)">👁️</span>
                <div class="error-text" id="password-error">两次输入的密码不一致</div>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn_action">注 册</button>
                <a href="login.php" class="action_link">已有账号？立即登录</a>
            </div>
        </form>
    </div>

    <script>
        // 切换密码可见性
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
        // 密码强度检测
        document.querySelector('input[name="password"]').addEventListener('input', function(e) {
            let strength = 0;
            const password = e.target.value;
            const strengthBar = document.querySelector('.password-strength span');
            
            if(password.length >= 8) strength += 25;
            if(password.match(/[a-z]/)) strength += 25;
            if(password.match(/[A-Z]/)) strength += 25;
            if(password.match(/[0-9]/)) strength += 25;
            
            strengthBar.style.width = strength + '%';
            
            if(strength <= 25) {
                strengthBar.style.backgroundColor = '#ff4444';
            } else if(strength <= 50) {
                strengthBar.style.backgroundColor = '#ffbb33';
            } else if(strength <= 75) {
                strengthBar.style.backgroundColor = '#00C851';
            } else {
                strengthBar.style.backgroundColor = '#007E33';
            }
        });

        // 表单验证和提交
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            
            // 重置错误提示
            document.querySelectorAll('.error-text').forEach(el => el.style.display = 'none');
            
            // 检查密码是否一致
            if(password !== confirmPassword) {
                document.getElementById('password-error').style.display = 'block';
                return;
            }

            // 发送表单数据
            const formData = new FormData(this);
            fetch('doRegister.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    // 显示成功提示
                    document.querySelector('.success-overlay').style.display = 'flex';
                    // 1秒后跳转
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1000);
                } else if(data.message === 'username_exists') {
                    document.getElementById('username-error').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        // 飞行物生成脚本
        function createFlyingElements() {
            const container = document.getElementById('flyingElements');
            const colors = ['#4a90e2', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6'];
            
            function createElement() {
                const element = document.createElement('div');
                element.className = 'flying-element';
                
                const size = Math.random() * 30 + 10;
                const color = colors[Math.floor(Math.random() * colors.length)];
                const startX = Math.random() * window.innerWidth;
                const startY = Math.random() * window.innerHeight;
                
                element.style.width = `${size}px`;
                element.style.height = `${size}px`;
                element.style.left = `${startX}px`;
                element.style.top = `${startY}px`;
                element.style.background = color;
                element.style.boxShadow = `0 0 ${size/2}px ${color}`;
                element.style.animationDelay = `${Math.random() * -20}s`;
                
                container.appendChild(element);
            }

            // 初始创建15个元素
            for(let i = 0; i < 45; i++) {
                createElement();
            }

            // 定期检查并补充元素
            setInterval(() => {
                const currentElements = container.getElementsByClassName('flying-element');
                if (currentElements.length < 15) {
                    createElement();
                }
            }, 3000);
        }

        // 页面加载完成后启动动画
        window.addEventListener('load', createFlyingElements);
    </script>
</body>
</html>