<?php
session_start();
if (!isset($_SESSION['user_role'])) {
    header("Location: ../login.php");
    exit();
}

// 获取用户邮箱
require_once '../dbConfig.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT username, realname, email FROM info WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$username = $row['username'];
$realname = $row['realname'];
$email = $row['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {  // 处理提交修改
        $newRealname = $_POST['realname'];
        $newEmail = $_POST['email'];
        $newPassword = $_POST['password'];
        
        // 获取旧邮箱
        $stmt = $conn->prepare("SELECT email FROM info WHERE username = ?");
        $stmt->bind_param("s", $_SESSION['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $oldEmail = $row['email'];
        
        // 如果邮箱发生变化
        if ($oldEmail !== $newEmail) {
        
            // 更新课表邮件数据库
            $courseCommand = escapeshellcmd("python ../auxiliaryProgram/courseEmailDB.py update " . 
                                    escapeshellarg($oldEmail) . " " . 
                                    escapeshellarg($newEmail));
            $courseOutput = [];
            $courseReturnCode = -1;
            exec($courseCommand, $courseOutput, $courseReturnCode);
            
            if ($courseReturnCode !== 0) {
                // 更新失败，显示错误信息
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var modal = document.getElementById('success-modal');
                        var modalContent = document.getElementById('success-modal-content').querySelector('p');
                        modalContent.textContent = '邮箱更新失败，请重试！';
                        modal.style.display = 'block';
                        setTimeout(function() {
                            modal.style.display = 'none';
                        }, 1000);
                    });
                </script>";
                return;
            }
        }
        
        // 继续原有的更新用户信息逻辑
        $sql = "UPDATE info SET realname = ?, email = ?";
        $params = array($newRealname, $newEmail);
        $types = "ss";
        
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql .= ", password = ?";
            $params[] = $hashedPassword;
            $types .= "s";
        }
        
        $sql .= " WHERE username = ?";
        $params[] = $_SESSION['username'];
        $types .= "s";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $success = $stmt->execute();
        
        if ($success) {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var modal = document.getElementById('success-modal');
                        var modalContent = document.getElementById('success-modal-content').querySelector('p');
                        modalContent.textContent = '信息更新成功！';
                        modal.style.display = 'block';
                        setTimeout(function() {
                            modal.style.display = 'none';
                            window.location.href = '../welcome.php';
                        }, 1000);
                    });
                </script>";
        }
    } elseif (isset($_POST['delete'])) {  // 处理注销用户
        // 先获取用户邮箱
        $stmt = $conn->prepare("SELECT email FROM info WHERE username = ?");
        $stmt->bind_param("s", $_SESSION['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $userEmail = $row['email'];

        // 执行Python脚本删除邮箱信息
        $command = escapeshellcmd("python ../auxiliaryProgram/proxyEmailDB.py delete " . escapeshellarg($userEmail));
        $output = [];
        $returnCode = -1;
        exec($command, $output, $returnCode);

        $command = escapeshellcmd("python ../auxiliaryProgram/courseEmailDB.py delete " . escapeshellarg($userEmail));
        $output = [];
        $returnCode = -1;
        exec($command, $output, $returnCode);

        // 无论Python脚本执行结果如何，继续删除用户信息
        $stmt = $conn->prepare("DELETE FROM info WHERE username = ?");
        $stmt->bind_param("s", $_SESSION['username']);
        $success = $stmt->execute();
        
        if ($success) {
            session_destroy();
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var modal = document.getElementById('success-modal');
                        var modalContent = document.getElementById('success-modal-content').querySelector('p');
                        modalContent.textContent = '账号已注销！';
                        modal.style.display = 'block';
                        setTimeout(function() {
                            modal.style.display = 'none';
                            window.location.href = '../login.php';
                        }, 1000);
                    });
                </script>";
        } else {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var modal = document.getElementById('success-modal');
                        var modalContent = document.getElementById('success-modal-content').querySelector('p');
                        modalContent.textContent = '账号注销失败，请重试！';
                        modal.style.display = 'block';
                        setTimeout(function() {
                            modal.style.display = 'none';
                        }, 1000);
                    });
                </script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/common1.css">
    <title>欢迎</title>
    <meta charset="utf-8">
    <style>
        .container {
            height: auto;
            min-height: 200px;
            padding: 20px;
            width: 100% !important;
            max-width: 500px !important;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .page-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px 40px;
            width: 100%;
            box-sizing: border-box;
        }

        /* 调整响应式布局断点和宽度 */
        @media (max-width: 900px) {
            .page-container {
                flex-direction: column;
                align-items: center;
                padding: 20px 20px;
            }

            .container {
                width: 100% !important;
                max-width: 600px !important;
            }
        }

        /* 调整表单元素的间距 */
        .form-group {
            width: 100%;
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            font-size: 16px;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            width: 100%;
        }

        .button-group button {
            flex: 1;
            margin: 0 10px;
        }

        .submit-btn {
            padding: 20px 20px;
            padding-left: 40px;
            text-align: center;
        }

        @media (max-width: 900px) {
            .page-container {
                flex-direction: column;
                align-items: center;
            }

            .demo-container {
                width: 100%;
                max-width: 400px;
            }
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .button-group button {
            flex: 1;
            margin: 0 10px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .submit-btn {
            background-color: #4CAF50;
            color: white;
        }
        
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
        
        .cancel-btn {
            background-color: #808080;
            color: white;
        }
    </style>
</head>
<body>
    <button class="back-button" onclick="location.href='../welcome.php'"">
        <i class="icon fa-solid fa-cat"></i>
        <span class="text">返回上页</span>
    </button>
    <div class="page-container">
        <div class="container">
            <form action="#" method="post">
                <div class="form-group">
                    <label for="username">用户名：</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($username); ?>" 
                           readonly style="opacity: 0.6;">
                </div>
                <div class="form-group">
                    <label for="realname">真实姓名：</label>
                    <input type="text" id="realname" name="realname" 
                           value="<?php echo htmlspecialchars($realname); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">邮箱地址：</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">新密码：</label>
                    <input type="password" id="password" name="password" 
                           placeholder="留空代表不修改密码">
                </div>
                <div class="button-group">
                    <button type="submit" name="submit" class="submit-btn">提交修改</button>
                    <button type="button" class="delete-btn" onclick="showConfirmModal()">注销用户</button>
                    <button type="button" class="cancel-btn" onclick="window.history.back();">取消</button>
                </div>
            </form>
        </div>

    </div>

    <!-- 模态框 -->
    <div id="success-modal" style="display:none;">
        <div id="success-modal-content">
            <p>提交成功！</p>
        </div>
    </div>

    <!-- 在body末尾添加确认删除的模态框 -->
    <div id="confirm-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <p>确定要注销账号吗？此操作不可恢复！</p>
            <p>账号删除后会有以下变化：</p>
            <p>1.取消订阅课表邮件。</p>
            <p>2.如果您为我们提供了SMTP授权码，我们将会为您删除授权码，并取消您的贡献者身份。</p>
            <div class="modal-buttons">
                <button onclick="confirmDelete()" class="confirm-btn">确认</button>
                <button onclick="closeConfirmModal()" class="cancel-btn">取消</button>
            </div>
        </div>
    </div>
</body>
</html>

<!-- 添加相关的JavaScript代码 -->
<script>
    function showConfirmModal() {
        document.getElementById('confirm-modal').style.display = 'block';
    }

    function closeConfirmModal() {
        document.getElementById('confirm-modal').style.display = 'none';
    }

    function confirmDelete() {
        // 创建一个表单并提交
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete';
        input.value = '1';
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
</script>

<!-- 添加模态框样式 -->
<style>
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1000;
    }

    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        min-width: 300px;
    }

    .modal-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 20px;
    }

    .modal-buttons button {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .confirm-btn {
        background-color: #f44336;
        color: white;
    }

    .modal-buttons .cancel-btn {
        background-color: #808080;
        color: white;
    }

    /* 确保模态框在其他元素之上 */
    #confirm-modal {
        z-index: 1001;
    }
    
    #success-modal {
        z-index: 1002;
    }
</style>