<?php
session_start();
// 检查用户是否已登录
if (!isset($_SESSION['user_role'])) {
    header("Location: ../login.php");
    exit();
}

// 获取用户邮箱
require_once '../dbConfig.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT email FROM info WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$userEmail = '';
if ($row = $result->fetch_assoc()) {
    $userEmail = $row['email'];
}

// 添加处理表单提交的代码
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    // 调用Python脚本
    // 注意python还是python3
    $command = "python ../auxiliaryProgram/sendEmail.py " . escapeshellarg($email);
    $output = [];
    $returnCode = -1;
    exec($command, $output, $returnCode);
    
    // 可以添加处理返回结果的代码
    if ($returnCode === 0) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var modal = document.getElementById('success-modal');
                    var modalContent = document.getElementById('success-modal-content').querySelector('p');
                    modalContent.textContent = '测试邮件发送成功！';
                    modal.style.display = 'block';
                    setTimeout(function() {
                        modal.style.display = 'none';
                        window.location.href = '../welcome.php';
                    }, 1000);
                });
            </script>";
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var modal = document.getElementById('success-modal');
                    var modalContent = document.getElementById('success-modal-content').querySelector('p');
                    modalContent.textContent = '发送出错，等待维护';
                    modal.style.display = 'block';
                    setTimeout(function() {
                        modal.style.display = 'none';
                    }, 1000);
                });
            </script>";
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
            height: 150px;
        }

        .page-container {
            align-items: center;
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
    </style>
</head>
<body>
    <button class="back-button" onclick="location.href='../welcome.php'"">
        <i class="icon fa-solid fa-cat"></i>
        <span class="text">返回上页</span>
    </button>
    <div class="page-container">
        <div class="disclaimer-container">
            <center>    
                <h1>测试说明</h1>
            </center>
            <p style="padding-top: 6px;">
                1. 用户没有上传课表，邮件默认认为您没有接收权限，会发送一个无权限提示邮件。
                只能证明您的邮箱可以使用。<br>
            </p>
            <p style="padding-top: 6px;">
                2. 用户没有上传课表，同时邮箱不正确，则不会收到邮箱。<br>
            </p>
            <p style="padding-top: 6px;">
                3. 用户上传了课表，但是课表格式不正确导致PDF解析失败，则会发送无权限提示邮件，
                并且该邮箱会被删除，之后无法接收邮件。解决办法就是重新上传正确的课表。<br>
            </p>
            <p style="padding-top: 6px;">
                4. 用户上传了课表，且课表格式正确，但是明日无课，会发送一个无课测试邮件。<br>
            </p>
            <p style="padding-top: 6px;">
                5. 用户上传了课表，且课表格式正确，且明日有课，会发送一个明日课表测试邮件。<br>
            </p>
        </div>
        <div class="container">
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="email">接收邮箱：</label>
                    <input type="email" id="email" name="email" required 
                           readonly value="<?php echo htmlspecialchars($userEmail); ?>" 
                           style="opacity: 0.6;">
                </div>
                    <button type="submit" class="submit-btn">提交</button>
            </form>
        </div>

    </div>
    <!-- 模态框 -->
    <div id="success-modal" style="display:none;">
        <div id="success-modal-content">
            <p>提交成功！</p>
        </div>
    </div>
</body>
</html>