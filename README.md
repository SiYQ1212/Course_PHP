# 课程提醒系统

## *文件结构*

```
Program
├─ login.php                  # 登录界面
├─ logout.php                 # 退出逻辑
├─ checkLogin.php             # 登录逻辑
├─ dbConfig.php               # Mysql操作
├─ register.php               # 注册界面
├─ doRegister.php             # 注册逻辑
├─ welcome.php                # 功能界面
├─ uploadFiles                # PDF暂存目录
│  └─ .init
├─ testSendPage               # 测试网页
│  ├─ noAuthority.html
│  └─ noCourse.html
├─ logs                       # 日志文件
│  ├─ dbError.log             # MongoDB数据库错误日志
│  ├─ functionError.log       # 辅助功能函数错误日志
│  └─ sendEmailError.log      # 邮件发送日志
├─ images 
│  └─ *.png
├─ functionalPages            # 功能页面  
│  ├─ account.php             # 账号管理功能
│  ├─ getAuthCode.php         # 获取授权码教程
│  ├─ joinUs.php              # 上传授权码功能
│  ├─ testSend.php            # 测试发送功能
│  ├─ uploadFile.php          # 课表上传功能
│  └─ admin                   # 超级管理员页面
│     ├─ adminPage.php
│     ├─ deleteUser.php
│     └─ updateUser.php
├─ fonts                      # 字体文件
│  └─ ChillReunion_Sans.otf
├─ error
│  └─ *.html
├─ css
│  └─ *.css
└─ auxiliaryProgram           # 辅助函数
   ├─ configuration.py        # 配置
   ├─ courseEmailDB.py        # 课程数据的存取
   ├─ functions.py            # 公共方法
   ├─ handData.py             # 合成网页
   ├─ monitorSend.py          # 邮件发送主函数
   ├─ parseData.py            # PDF数据处理
   ├─ proxyEmailDB.py         # 发送邮箱代理池
   └─ sendEmail.py            # 发送函数
```