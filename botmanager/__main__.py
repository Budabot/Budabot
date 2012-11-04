#! /usr/bin/env python
# -*- coding: utf-8 -*-

import os, sys

# make sure that the the application can be executed from anywhere
currentDir = os.path.dirname(os.path.abspath(__file__))
sys.path.append(currentDir)
os.chdir(currentDir)

# make sure that there is only one instance of this process running
#from tendo import singleton
import singleton
singletonGuard = singleton.SingleInstance()

from application import Application

# start the application
application = Application()
application.execute()
