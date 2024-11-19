import os
from apscheduler.schedulers.blocking import BlockingScheduler
from apscheduler.events import EVENT_JOB_ERROR
import logging
import logging.handlers
import signal
import sys

from sendEmail import emailSend
from handData import createHtml
from courseEmailDB import CourseEmailDB
from configuration import Path 

# 配置日志路径
LOG_FILE = os.path.join(Path, "logs", "task.log")
os.makedirs(os.path.join(Path, "logs"), exist_ok=True)

# 配置日志
logger = logging.getLogger(__name__)
logger.setLevel(logging.INFO)

# 创建 RotatingFileHandler，修改日志格式
file_handler = logging.handlers.RotatingFileHandler(
    LOG_FILE,
    maxBytes=1024*1024,
    backupCount=5,
    encoding='utf-8'
)
# 修改日志格式，添加更详细的时间格式
file_handler.setFormatter(
    logging.Formatter('[%(asctime)s] - %(levelname)s - %(message)s', 
                     datefmt='%Y-%m-%d %H:%M:%S')
)
logger.addHandler(file_handler)

def sendTask():
    try:
        # print("Hello")
        receivers = CourseEmailDB().getAllEmails()
        for receiver in receivers:
            message = createHtml(receiver)
            emailSend(message, receiver)
        logger.info("邮件发送任务执行成功")
    except Exception as e:
        logger.error(f"邮件发送任务执行失败: {str(e)}")

def job_listener(event):
    if event.exception:
        logger.error(f"任务执行出错: {event.exception}")

def shutdown_handler(signum, frame):
    logger.info("收到停止信号，正在关闭程序...")
    scheduler.shutdown()
    logger.info("程序已安全停止")
    sys.exit(0)

if __name__ == "__main__":
    # 注册信号处理器
    signal.signal(signal.SIGTERM, shutdown_handler)
    signal.signal(signal.SIGINT, shutdown_handler)
    
    scheduler = BlockingScheduler()
    scheduler.add_listener(job_listener, EVENT_JOB_ERROR)
    scheduler.add_job(sendTask, 'cron', hour=17, minute=30)
    
    try:
        logger.info("定时任务启动...")
        scheduler.start()
    except (KeyboardInterrupt, SystemExit):
        logger.info("定时任务已停止")
