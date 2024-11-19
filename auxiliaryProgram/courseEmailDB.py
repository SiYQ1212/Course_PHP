# -*- coding: utf-8 -*-
#!/usr/bin/python3
import sys

from pymongo import MongoClient
from functions import logdbError

class CourseEmailDB:
    def __init__(self):
        self.client = MongoClient('mongodb://localhost:27017/')
        self.db = self.client['emailDB']
        self.proxy_collection = self.db['course_emails']

        self.proxy_collection.create_index([('email', 1)], unique=True)

    def insertCourseEmail(self, email, notice_time, course_schedule):
        try:
            self.proxy_collection.update_one(
                {'email': email},
                {
                    '$set': {
                        'email': email,
                        'notice_time': notice_time,
                        'course_schedule': course_schedule,
                        'is_active': True
                    }
                },
                upsert=True
            )
            return True
        except Exception as e:
            logdbError(f"Insert course data error: {str(e)}")
            return False

    def getAllCourseEmails(self):
        try:
            return list(self.proxy_collection.find({}, {'_id': 0}))
        except Exception as e:
            logdbError(f"Get course data error: {str(e)}")
            return []
        
    def getCourseEmailByEmail(self, email):
        try:
            result = self.proxy_collection.find_one({'email': email}, {'_id': 0})
            return result if result else None
        except Exception as e:
            logdbError(f"Get course data by email error: {str(e)}")
            return None

    def deleteCourseEmail(self, email):
        try:
            result = self.proxy_collection.delete_one({'email': email})
            if result.deleted_count > 0:
                return True
            else:
                logdbError(f"No course record found for email: {email}")
                return False
        except Exception as e:
            logdbError(f"Delete course data error: {str(e)}")
            return False

    def getAllEmails(self):
        try:
            cursor = self.proxy_collection.find({}, {'_id': 0, 'email': 1})
            return [doc['email'] for doc in cursor]
        except Exception as e:
            logdbError(f"Get all emails error: {str(e)}")
            return []
        
    def updateCourseEmail(self, old_email, new_email):
        try:
            # 先获取旧邮箱的数据
            old_data = self.proxy_collection.find_one({'email': old_email})
            if not old_data:
                return False
                
            # 保存旧数据
            course_schedule = old_data.get('course_schedule')
            notice_time = old_data.get('notice_time')
            
            # 删除旧记录
            self.proxy_collection.delete_one({'email': old_email})
            
            # 插入新记录
            self.proxy_collection.update_one(
                {'email': new_email},
                {
                    '$set': {
                        'email': new_email,
                        'notice_time': notice_time,
                        'course_schedule': course_schedule,
                        'is_active': True
                    }
                },
                upsert=True
            )
            return True
        except Exception as e:
            logdbError(f"Update proxy email error: {str(e)}")
            return False

if __name__ == "__main__":
    if len(sys.argv) > 1:
        if sys.argv[1] == 'delete':
            CourseEmailDB().deleteCourseEmail(sys.argv[2])
        elif sys.argv[1] == 'update':
            CourseEmailDB().updateCourseEmail(sys.argv[2], sys.argv[3])


    