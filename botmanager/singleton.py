#! /usr/bin/env python

# THIS FILE IS EXTRACTED FROM TENDO LIBRARY: http://pypi.python.org/pypi/tendo
# Should be removed when the singleton works with py2exe

import sys, os, errno, tempfile, logging
from multiprocessing import Process

class SingleInstance:
	"""
	If you want to prevent your script from running in parallel just instantiate SingleInstance() class. If is there another instance already running it will exist the application with the message "Another instance is already running, quitting.", returning -1 error code.

	>>> import tendo
	... me = SingleInstance()

	This option is very useful if you have scripts executed by crontab at small amounts of time.

	Remember that this works by creating a lock file with a filename based on the full path to the script file.
	"""
	def __init__(self, flavor_id=""):
		import sys
		self.lockfile = os.path.normpath(tempfile.gettempdir() + '/' +
		    os.path.splitext(os.path.abspath(sys.argv[0]))[0].replace("/","-").replace(":","").replace("\\","-")  + '-%s' % flavor_id +'.lock')
		if sys.platform == 'win32':
			try:
				# file already exists, we try to remove (in case previous execution was interrupted)
				if os.path.exists(self.lockfile):
					os.unlink(self.lockfile)
				self.fd =  os.open(self.lockfile, os.O_CREAT|os.O_EXCL|os.O_RDWR)
			except OSError:
				type, e, tb = sys.exc_info()
				if e.errno == 13:
					sys.exit(-1)
				print(e.errno)
				raise
		else: # non Windows
			import fcntl
			self.fp = open(self.lockfile, 'w')
			try:
				fcntl.lockf(self.fp, fcntl.LOCK_EX | fcntl.LOCK_NB)
			except IOError:
				logger.warning("Another instance is already running, quitting.")
				sys.exit(-1)

	def __del__(self):
		import sys
		try:
			if sys.platform == 'win32':
				if hasattr(self, 'fd'):
					os.close(self.fd)
					os.unlink(self.lockfile)
			else:
				import fcntl
				fcntl.lockf(self.fp, fcntl.LOCK_UN)
				#os.close(self.fp)
				if os.path.isfile(self.lockfile):
					os.unlink(self.lockfile)
		except Exception, e:
			logger.warning(e)
			sys.exit(-1)

logger = logging.getLogger("tendo.singleton")
logger.addHandler(logging.StreamHandler())
