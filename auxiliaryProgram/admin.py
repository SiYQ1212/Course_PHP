from courseEmailDB import CourseEmailDB
from proxyEmailDB import ProxyEmailDB
import sys
import json

if __name__ == "__main__":
    if len(sys.argv) > 1:
        if sys.argv[1] == "course":
            print(json.dumps(CourseEmailDB().getAllEmails()))
        elif sys.argv[1] == "proxy":
            print(json.dumps(ProxyEmailDB().getAllEmails()))


