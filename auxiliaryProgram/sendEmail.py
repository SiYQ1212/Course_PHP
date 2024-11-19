# -*- coding: utf-8 -*-
#!/usr/bin/python3
import smtplib
import sys
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from email.header import Header

from handData import createHtml
from handData import getTomorrowCourse
from proxyEmailDB import  ProxyEmailDB
from courseEmailDB import CourseEmailDB
from random import choice
from functions import *


def emailSend(message, receiver, css=CSS, subject="明日课程"):
    newMessage = MIMEMultipart()
    htmlPart = MIMEText(message, "html", "utf-8")
    newMessage.attach(htmlPart)

    cssPart = MIMEText(css, "html", "utf-8")
    newMessage.attach(cssPart)

    emailProxy = ProxyEmailDB().getAllProxyEmails()
    if not emailProxy:
        print("Email Proxy Pool Empty")
        return False

    sender = choice(emailProxy)
    newMessage["From"] = Header(f"Sender <{sender['email']}>")
    newMessage["To"] = Header(f"Receiver <{receiver}>")
    newMessage["Subject"] = Header(subject, "utf-8")
    server = smtplib.SMTP_SSL("smtp.qq.com", 465)

    try:
        server.login(sender['email'], sender['token'])
    except Exception as e:
        logSendEmailError(f"{sender['email']} don't match the token")
        ProxyEmailDB().deleteProxyEmail(sender['email'])
        return emailSend(message, receiver, css, subject)

    try:
        server.sendmail(sender['email'], receiver, newMessage.as_string())
    except Exception as e:
        logSendEmailError(f"{sender['email']} : could not send to {receiver}")
        server.close()
        return False

    server.close()
    logSendEmailError(f"Successful send to {receiver}")
    return True


if __name__ == '__main__':
    # python sendEmail.py email
    if len(sys.argv) > 1:
        receiver = sys.argv[1]
        receivers = set(CourseEmailDB().getAllEmails())
        if receiver in receivers:
            message = createHtml(receiver)
            if message:
                emailSend(message, receiver, css=CSS)
            else:  # tomorrow no course
                with open(os.path.join(Path, "testSendPage", "noCourse.html"), "r", encoding="utf-8") as f:
                    message = f.read()
                emailSend(message, receiver, subject="无课测试邮件")
        else:
            with open(os.path.join(Path, "testSendPage", "noAuthority.html"), "r", encoding="utf-8") as f:
                message = f.read()
            emailSend(message, receiver, subject="无权限测试邮件")
