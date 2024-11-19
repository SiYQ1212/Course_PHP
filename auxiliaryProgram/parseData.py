# -*- coding: utf-8 -*-
#!/usr/bin/python3
import os
import fitz
import re
import sys
from courseEmailDB import CourseEmailDB
from functions import *

PAT = re.compile(r"\d+-\d+")
CtoE = {
    "星期一": "Monday",
    "星期二": "Tuesday",
    "星期三": "Wednesday",
    "星期四": "Thursday",
    "星期五": "Friday",
    "星期六": "Saturday",
    "星期日": "Sunday"
}


def parseData(email, noticeTime):
    pdf_path = os.path.join(Path, 'uploadFiles', email + '.pdf')
    pdf = fitz.open(pdf_path)
    contents = ""
    for page in pdf:
        contents += page.get_text()
    course_information = contents.split('\n')[3:-3]
    course_information = course_information
    timetable = {
        "Monday": [],
        "Tuesday": [],
        "Wednesday": [],
        "Thursday": [],
        "Friday": [],
        "Saturday": [],
        "Sunday": []
    }
    PAT = re.compile(r"\d+-\d+")
    WEEK = CtoE[course_information[0]]
    CLASSHOUR = ""
    while len(course_information) >= 3:
        week = course_information[0]
        # week ?=  "星期X"
        if CtoE.get(week) in timetable:
            WEEK = CtoE[course_information.pop(0)]
        class_hour = course_information[0]
        # class_hour ?= "X-X"
        if PAT.match(class_hour):
            CLASSHOUR = course_information.pop(0)
        courseName, detailedInformation, wasteInfomation = course_information.pop(0), course_information.pop(
            0), course_information.pop(0)
        if "学分" not in wasteInfomation:
            course_information.pop(0)
        newDetailedInformation = {}
        detailedInformation = detailedInformation.split()[:6]
        for key in range(0, len(detailedInformation), 2):
            newDetailedInformation[detailedInformation[key][:-1]] = detailedInformation[key + 1]
        timetable[WEEK].append([CLASSHOUR, courseName, newDetailedInformation])
    # 存入MongoDB
    db = CourseEmailDB()
    db.insertCourseEmail(email, noticeTime, timetable)
    return True

if __name__ == "__main__":
    if len(sys.argv) > 1:
        # python parseData.py email noticeTime
        parseData(sys.argv[1], sys.argv[2])

