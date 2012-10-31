#! /usr/bin/env python
# -*- coding: utf-8 -*-

import re
import os
import shutil
import collections

class BotPhpConfigFile(object):
	""""""

	def __init__(self, filePath):
		"""Constructor method."""
		super(BotPhpConfigFile, self).__init__()
		self.filePath = filePath
		self.vars = {}
		self.contents = ''

	def load(self):
		"""This method loads the settings from a file."""
		try:
			# new in Python 2.7
			self.vars = collections.OrderedDict()
		except AttributeError:
			self.vars = {}
		sourceFilePath = self.filePath
		# if target file doesn't exist yet, use the template file instead
		if os.path.exists(sourceFilePath) == False:
			folderPath = os.path.dirname(sourceFilePath)
			sourceFilePath = os.path.join(folderPath, 'config.template.php')
			if os.path.exists(sourceFilePath) == False:
				raise ValueError("Failed to find 'config.template.php' from configuration folder '%s'!" % folderPath)

		with open(sourceFilePath, 'r') as file:
			self.contents = ''
			prefix = r'^\s*\$vars\[[\'"](.+)[\'"]\]\s*=\s*'
			postfix = r'\s*;'
			for line in file:
				if line.strip() != '?>':  # ignore possibly offending php end-tag
					self.contents += line
				# search for var with a string value:
				match = re.search(prefix + r'[\'"](.*)[\'"]' + postfix, line)
				if match:
					self.vars[match.group(1)] = match.group(2)
					continue
				# search for var with a non-string value:
				match = re.search(prefix + '(.*)' + postfix, line)
				if match:
					self.vars[match.group(1)] = int(match.group(2))

	def save(self):
		"""This method saves the settings to the target file."""
		with open(self.filePath, 'w+b') as file:
			for name, value in self.vars.items():
				matcher = re.compile(r'\$vars\[[\'"]{0}[\'"]\]\s*=.*;'.format(name), re.MULTILINE)
				# wrap string value to quotes
				if isinstance(value, str):
					value = '"{0}"'.format(value)
				varString = '$vars[\'{0}\'] = {1};'.format(name, value)
				if matcher.search(self.contents):
					# replace existing variable
					self.contents = matcher.sub(varString, self.contents)
				else:
					# add new variable to file's end
					self.contents += '\n{0}\n'.format(varString)
			# write contents back to the file
			file.write(self.contents)

	def getVar(self, name):
		"""Return variable's value of given name."""
		return self.vars[name]

	def setVar(self, name, value):
		"""Sets variable's value of given name."""
		self.vars[name] = value

	def getFilePath(self):
		"""Returns path to the configuration file."""
		return self.filePath

	def __iter__(self):
		"""Enables ability to iterate through the file's variables
		with 'for...in'.
		"""
		for key, value in self.vars.iteritems():
			yield key, value
