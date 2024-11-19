# -*- coding: utf-8 -*-
#!/usr/bin/python3
import inspect
from configuration import *

def logdbError(string):
    filePath = os.path.join(Path, "logs", "dbError.log")
    with open(filePath, "a+", encoding="utf-8") as logFile:
        logFile.write(f"{datetime.datetime.now()}: {string}\n")

def logFunctionError(string):
    filePath = os.path.join(Path, "logs", "functionError.log")
    with open(filePath, "a+", encoding="utf-8") as logFile:
        logFile.write(f"{datetime.datetime.now()}: {string}\n")

def logSendEmailError(string):
    filePath = os.path.join(Path, "logs", "sendEmailError.log")
    with open(filePath, "a+", encoding="utf-8") as logFile:
        logFile.write(f"{datetime.datetime.now()}: {string}\n")

def funcError(function):
    def wrapper(*args, **kwargs):
        try:
            return function(*args, **kwargs)
        except Exception as e:
            logFunctionError(f"<{inspect.getsourcefile(function)}> [{function.__name__}] : {str(e)}")
            return False
    return wrapper


if __name__ == "__main__":
    ...