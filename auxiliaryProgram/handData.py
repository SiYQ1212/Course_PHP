# -*- coding: utf-8 -*-
#!/usr/bin/python3
from courseEmailDB import CourseEmailDB
from configuration import *
from functions import funcError

@funcError
def checkCourse(courseInformation, tomorrwWeek):
    haveWeek = set()
    solveData = courseInformation.split(',')
    for week in solveData:
        if '-' in week:
            if '单' in week:
                s, e = map(int, week[:-4].split('-'))
                for i in range(s, e + 1, 2):
                    haveWeek.add(i)
            elif '双' in week:
                s, e = map(int, week[:-4].split('-'))
                for i in range(s, e + 1, 2):
                    haveWeek.add(i)
            else:
                s, e = map(int, week[:-1].split('-'))
                for i in range(s, e + 1):
                    haveWeek.add(i)
        else:
            haveWeek.add(int(week[:-1]))
    return tomorrwWeek in haveWeek

@funcError
def getTomorrowCourse(email):
    data = CourseEmailDB().getCourseEmailByEmail(email)["course_schedule"]
    today = (datetime.datetime.now() + datetime.timedelta(days=0)).strftime("%A")
    days = list(data.keys())
    tomorrow = days[(days.index(today) + 1) % len(days)]
    data = data[tomorrow]

    tomorrowWeek = (datetime.date.today() + datetime.timedelta(days=1) - StartDate).days // 7 + 1

    tomorrowCourse = []
    for couse in data:
        if checkCourse(couse[2]['周数'], tomorrowWeek):
            tomorrowCourse.append(couse)
    return tomorrowCourse

@funcError
def createHtml(email):
    TIME = {
        "1-2": "8:00-9:30",
        "3-4": "9:45-11:15",
        "3-5": "9:45-12:10",
        "6-7": "14:00-15:30",
        "8-9": "15:45-17:15",
        "8-10": "15:45-18:10",
        "11-12": "19:00-20:30",
        "11-13": "19:00-21:25"
    }
    content = ""
    courseInformation = getTomorrowCourse(email)
    for course in courseInformation:
        content += f"""
                <div class="course-box">
                    <div class="time">⏰ {TIME[course[0]]}</div>
                    <div class="course-content">
                        <div class="course-name">{course[1]}</div>
                        <div class="location">🏫 {course[2]['地点'].replace('，', ',')} - {course[2]['教师']}</div>
                    </div>
                </div>
        """
    return htmlFront + content + htmlTail



if __name__ == '__main__':
    ...