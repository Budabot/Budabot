#! /usr/bin/env python
# -*- coding: utf-8 -*-

import gobject
import gtk
import subprocess
import platform
import threading
import Queue
import os
import signal

if platform.system() == 'Windows':
	import win32com.client
	import win32api

class Process(gobject.GObject):
	"""The Process class executes new Budabot processes."""
	
	# Define custom signals that this class can emit.
	__gsignals__ = {
		# emitted when the process has finished executing
		'stopped': (gobject.SIGNAL_RUN_LAST, gobject.TYPE_NONE, ()),
		# emitted when the process sends data to standard output
		'stdout_received': (gobject.SIGNAL_RUN_LAST, gobject.TYPE_NONE, (gobject.TYPE_STRING,)),
		# emitted when the process sends data to standard error
		'stderr_received': (gobject.SIGNAL_RUN_LAST, gobject.TYPE_NONE, (gobject.TYPE_STRING,)),
	}
	
	def __init__(self):
		"""Constructor method."""
		self.__gobject_init__()
		self.workingDirectoryPath = ''
		self.configFilePath = ''
		self.process = None
		self.started = False
		self.timerId = None

	def setWorkingDirectoryPath(self, path):
		"""Sets path to current working directory."""
		self.workingDirectoryPath = path

	def setConfigFilePath(self, path):
		"""Sets path to bot's config file"""
		self.configFilePath = path

	def start(self):
		"""Calling this method will start the bot as its own process.
		When the bot is running its stdout and stderr is emitted
		with 'stdout_received' and 'stderr_received' signals.
		
		Call stop() to terminate the process.
		"""
		# do nothing if bot is already running
		if self.started:
			return
		self.reset()

		# build process arguments
		arguments = []
		if platform.system() == 'Windows':
			arguments.append(self.workingDirectoryPath + '\\win32\\php.exe')
			arguments.append('-c')
			arguments.append('php-win.ini')
		elif platform.system() == 'Linux':
			arguments.append('php')
		arguments.append('-f')
		arguments.append('main.php')
		arguments.append('--')
		arguments.append(self.configFilePath)

		# start the process
		useShell = False
		if platform.system() == 'Windows':
			useShell = True # prevents command prompt from opening
		self.process = subprocess.Popen(args = arguments, stdout = subprocess.PIPE, stderr = subprocess.PIPE, cwd = self.workingDirectoryPath, shell = useShell)

		# start stdout and stderr polling threads
		self.outQueue  = Queue.Queue()
		self.outThread = threading.Thread(target = self.readStdout)
		self.outThread.daemon = True
		self.outThread.start()
		self.errorQueue  = Queue.Queue()
		self.errorThread = threading.Thread(target = self.readStderr)
		self.errorThread.daemon = True
		self.errorThread.start()
		# start status checker timer
		self.timerId = gtk.timeout_add(100, self.checkStatus)
		self.started = True

	def stop(self):
		"""Calling this method terminates the running bot process.
		Emits 'stopped' when finished.
		"""
		if self.started:
			self.reset()
			# notify listeners
			self.emit('stopped')

	def isRunning(self):
		"""Returns true if the process is running."""
		return self.started

	def readStdout(self):
		"""Reads data from process's STDOUT and stores readed lines to queue
		for later emitting from main thread.
		This method is called from another thread.
		"""
		for line in iter(self.process.stdout.readline, ''):
			self.outQueue.put(line)

	def readStderr(self):
		"""Reads data from process's STDERR and stores readed lines to queue
		for later emitting from main thread.
		This method is called from another thread.
		"""
		for line in iter(self.process.stderr.readline, ''):
			self.errorQueue.put(line)

	def checkStatus(self):
		"""Checks if the process is currently running or not, calls stop() if not.
		This method is called automatically by a timer when process is running.
		"""
		# emit stdout lines
		try:
			while True:
				self.emit('stdout_received', self.outQueue.get_nowait())
		except Queue.Empty:
			pass
		# emit stderr lines
		try:
			while True:
				self.emit('stderr_received', self.errorQueue.get_nowait())
		except Queue.Empty:
			pass
		# set internal state to stopped if process is not running anymore
		if self.isRunning() == False:
			self.stop()
		return True

	def isRunning(self):
		"""Checks if the actual process is still running."""
		if self.process:
			return self.process.poll() is None
		return False

	def reset(self):
		"""Stops any polling timers, closes handles and terminates the running
		process if any and resets values back to default.
		"""
		# stop timer
		if self.timerId != None:
			gtk.timeout_remove(self.timerId)
		# terminate the process if still running
		if self.isRunning():
			if platform.system() == 'Windows':
				# since the bot is running inside a shell we need to
				# search & terminate all self.process's child processes
				WMI = win32com.client.GetObject('winmgmts:')
				processes = WMI.InstancesOf('Win32_Process')
				for process in processes:
					parent = process.Properties_('ParentProcessId').Value
					if parent == self.process.pid:
						pid = process.Properties_('ProcessID').Value
						handle = win32api.OpenProcess(1, False, pid)
						win32api.TerminateProcess(handle, -1)
						win32api.CloseHandle(handle)
			self.process.terminate()
		# reset values
		self.timerId = None
		self.process = None
		self.started = False

# register class so that custom signals will work
gobject.type_register(Process)
