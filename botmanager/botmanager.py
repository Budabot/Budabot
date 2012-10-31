#! /usr/bin/env python
# -*- coding: utf-8 -*-

# make sure that there is only one instance of this process running
#from tendo import singleton
import singleton
singletonGuard = singleton.SingleInstance()

from application import Application

# start the application
application = Application()
application.execute()
