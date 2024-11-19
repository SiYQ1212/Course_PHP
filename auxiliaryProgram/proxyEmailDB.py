# -*- coding: utf-8 -*-
#!/usr/bin/python3
import sys
from pymongo import MongoClient
from functions import *

class ProxyEmailDB:
    def __init__(self):
        self.client = MongoClient('mongodb://localhost:27017/')
        self.db = self.client['emailDB']
        self.proxy_collection = self.db['proxy_emails']

        self.proxy_collection.create_index([('email', 1)], unique=True)

    def insertProxyEmail(self, email, token):
        try:
            self.proxy_collection.update_one(
                {'email': email},
                {
                    '$set': {
                        'email': email,
                        'token': token,
                        'is_active': True
                    }
                },
                upsert=True
            )
            return True
        except Exception as e:
            logdbError(f"Insert data error: {str(e)}")
            return False

    def getAllProxyEmails(self):
        try:
            return list(self.proxy_collection.find({}, {'_id': 0}))
        except Exception as e:
            logdbError(f"Get data error: {str(e)}")
            return []

    def getAllEmails(self):
        try:
            cursor = self.proxy_collection.find({}, {'_id': 0, 'email': 1})
            return [doc['email'] for doc in cursor]
        except Exception as e:
            logdbError(f"Get all emails error: {str(e)}")
            return []

    def deleteProxyEmail(self, email):
        try:
            result = self.proxy_collection.delete_one({'email': email})
            if result.deleted_count > 0:
                return True
            else:
                logdbError(f"No record found for email: {email}")
                return False
        except Exception as e:
            logdbError(f"Delete data error: {str(e)}")
            return False

if __name__ == "__main__":
    db = ProxyEmailDB()
    if len(sys.argv) > 1:
        # python proxyEmailDB.py insert email token
        if sys.argv[1] == 'insert':
            db.insertProxyEmail(sys.argv[2], sys.argv[3])
        # python proxyEmailDB.py delete email
        elif sys.argv[1] == 'delete':
            db.deleteProxyEmail(sys.argv[2])



