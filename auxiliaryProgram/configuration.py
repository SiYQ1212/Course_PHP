# -*- coding: utf-8 -*-
#!/usr/bin/python3
import datetime
import os

# 开学日期 firstDay
StartDate = datetime.date(2024, 8, 26)

if os.name == "posix":
    Path = "/var/www/html/"
elif os.name == "nt":
    Path = "C:/Users/dpdgp/Desktop/WWW/xico"

# 发送时间
Send_Time = "17:30"

htmlFront = """
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <div class="container">
        <h1>📚 课程表</h1>
        <div class="schedule-box">
            <div class="course-list">
"""

CSS = """
<style>
    .course-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .course-box {
        background: white;
        border-radius: 8px;
        padding: 15px;
        display: flex;
        align-items: center;
        transition: transform 0.2s;
        border: 1px solid #e0e0e0;  /* 给每个课程盒子添加浅色边框 */
    }

    .container {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
        background-color: white;  /* 容器背景色 */
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    h1 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 30px;
    }

    .course-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .course-box {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 20px;
        display: flex;
        align-items: center;
        transition: transform 0.2s;
    }

    .time {
        background: #3498db;
        color: white;
        padding: 8px 15px;
        border-radius: 6px;
        font-weight: bold;
        min-width: 120px;
        text-align: center;
    }

    .course-content {
        margin-left: 20px;
        flex-grow: 1;
    }

    .course-name {
        font-size: 1.2em;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .location {
        color: #7f8c8d;
    }

    .teacher {
        color: #7f8c8d;
    }

    /* 为不同课程设置不同的颜色 */
    .course-box:nth-child(1) .time {
        background: #3498db;
    }

    .course-box:nth-child(2) .time {
        background: #1abc9c;
    }

    .course-box:nth-child(3) .time {
        background: #2ecc71;
    }

    .course-box:nth-child(4) .time {
        background: #e74c3c;
    }

    .course-box:nth-child(5) .time {
        background: #9b59b6;
    }

    /* 添加响应式布局 */
    @media (max-width: 600px) {
        .course-box {
            flex-direction: column;
            text-align: center;
        }

        .course-content {
            margin-left: 0;
            margin-top: 10px;
        }
    }
</style>
"""

htmlTail = """
            </div>
        </div>
    </div>
</body>
"""




