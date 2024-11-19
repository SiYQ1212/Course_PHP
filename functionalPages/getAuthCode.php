<?php
session_start();
// 检查用户是否已登录
if (!isset($_SESSION['user_role'])) {
    header("Location: ../login.php");
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/common1.css">
    <meta charset="utf-8">
    <style>

        .demo-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 400px;
        }

        .demo-container img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            object-fit: contain;
        }

        .demo-title {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 16px;
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

        .image-gallery {
            display: flex;
            flex-direction: column;
            gap: 20px;
            justify-content: center;
            align-items: center;
        }

        .image-box {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 900px;
            height: 250px;
        }

        .image-box img {
            width: auto;
            max-width: 90%;
            height: 100%;
            border-radius: 5px;
            object-fit: cover;
            margin-right: 20px;
        }

        .image-description-box {
            width: 35%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .image-description {
            font-size: 24px;
            color: #333;
        }
    </style>
</head>
<body>
    <button class="back-button" onclick="window.history.back();"">
        <i class="icon fa-solid fa-cat"></i>
        <span class="text">返回上页</span>
    </button>
    <div class="image-gallery">
        <div class="image-box">
            <img src="../images/step1.png" alt="Image 1">
            <div class="image-description-box">
                <div class="image-description">
                    电脑浏览器登录QQ邮箱,找到右上角的"账号与安全",点击进入
                </div>
            </div>
        </div>
        <div class="image-box">
            <img src="../images/step2.png" alt="Image 1">
            <div class="image-description-box">
                <div class="image-description">
                选中左侧"安全设置",然后选则"生成授权码",之后按照步骤得到授权码<br>
                授权码获取后，形如"vhpzxcqkdmhtdxxy",复制即可
                </div>
            </div>
        </div>
    </div>
</body>
</html>