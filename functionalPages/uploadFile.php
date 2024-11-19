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

// 处理文件上传
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $time = htmlspecialchars($_POST['time']);
    $uploadDir = "../uploadFiles/";
    
    // 确保上传目录存在并设置正确的权限
    if (!file_exists($uploadDir)) {
        error_log("尝试创建目录: " . $uploadDir);
        if (!mkdir($uploadDir, 0755, true)) {
            $error = "无法创建上传目录";
            error_log("创建目录失败: " . error_get_last()['message']);
        }
    }
    
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        // 使用邮箱作为文件名
        $fileName = $email . '.pdf';
        $targetPath = $uploadDir . $fileName;
        
        // 获取文件扩展名
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // 只允许PDF文件
        if ($fileExtension === 'pdf' && $file['type'] === 'application/pdf') {
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                chmod($targetPath, 0644);
                
                // 执行Python脚本解析数据
                $command = escapeshellcmd("python ../auxiliaryProgram/parseData.py " . 
                                        escapeshellarg($email) . " " . 
                                        escapeshellarg($time));
                $output = [];
                $returnCode = -1;
                exec($command, $output, $returnCode);
                
                // 无论脚本执行成功与否，都删除PDF文件
                if (file_exists($targetPath)) {
                    unlink($targetPath);
                }
                
                if ($returnCode === 0) {
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var modal = document.getElementById('success-modal');
                                var modalContent = document.getElementById('success-modal-content').querySelector('p');
                                modalContent.textContent = '上传成功！';
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
                                modalContent.textContent = '数据解析失败，请重试！';
                                modal.style.display = 'block';
                                setTimeout(function() {
                                    modal.style.display = 'none';
                                }, 1000);
                            });
                        </script>";
                }
            } else {
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var modal = document.getElementById('success-modal');
                            var modalContent = document.getElementById('success-modal-content').querySelector('p');
                            modalContent.textContent = '文件上传失败！';
                            modal.style.display = 'block';
                            setTimeout(function() {
                                modal.style.display = 'none';
                            }, 1000);
                        });
                    </script>";
            }
        } else {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var modal = document.getElementById('success-modal');
                        var modalContent = document.getElementById('success-modal-content').querySelector('p');
                        modalContent.textContent = '只允许上传PDF文件！';
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
    <title>欢迎</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/common1.css">
    <style>
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
        <div class="container">
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="demo-title">提交成功后请去测试发送;解析错误就是PDF格式错误</div>
                <div class="form-group">
                    <label for="email">接收邮箱：</label>
                    <input type="email" id="email" name="email" required 
                           readonly value="<?php echo htmlspecialchars($userEmail); ?>" 
                           style="opacity: 0.6;">
                </div>
                
                <div class="form-group">
                    <label for="time">预约时间：(暂时不支持自定义时间发送，目前统一)</label>
                    <input type="time" id="time" name="time" readonly value="<?php echo date('17:30'); ?>">
                </div>

                <div class="form-group">
                    <label for="file">选择文件(仅支持PDF格式)</label>
                    <input type="file" id="file" name="file" accept=".pdf" required>
                </div>
                
                <button type="submit" class="submit-btn">提交</button>
            </form>
        </div>

        <div class="demo-container">
            <div class="demo-title">上传示例课表：(列表式课表)</div>
            <img src="../images/demo.png" alt="课表示例">
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