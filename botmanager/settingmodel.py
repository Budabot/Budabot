#! /usr/bin/env python
# -*- coding: utf-8 -*-

import gtk
import gobject
import appdirs
from configobj import ConfigObj, ConfigObjError
import configobj
from validate import Validator
import os
from utils import resourcePath

class SettingModel(gtk.TreeStore):
	""""""

	COLUMN_NAME = 0
	COLUMN_VALUE = 1

	ROW_COMMON = 'common'
	ROW_BOTS   = 'bots'
	
	VALUE_SECTION = 'SECTION' 

	# Define custom signals that this class can emit.
	__gsignals__ = {
		# this signal is emitted when an error occurs
		'error': (gobject.SIGNAL_RUN_LAST, gobject.TYPE_NONE, (gobject.TYPE_STRING,)),
	}

	def __init__(self):
		"""Constructor method."""
		super(SettingModel, self).__init__(gobject.TYPE_STRING, gobject.TYPE_PYOBJECT)

	def load(self):
		"""This method loads the settings from a file."""
		config = self.createConfigObj()
		if config != None and self.validate(config):
			# populate the model
			self.clear()
			self.populate(config, None)

	def populate(self, section, parentIter):
		for key, value in section.items():
			if isinstance(value, dict):
				# change row type if necessary
				rowIter = self.append(parentIter, (key, self.VALUE_SECTION))
				self.populate(value, rowIter)
			else:
				self.append(parentIter, (key, value))

	def save(self):
		"""This method saves the settings to a file."""
		
		def addValues(section, rowIterator):
			for row in rowIterator:
				name  = row[self.COLUMN_NAME]
				value = row[self.COLUMN_VALUE]
				if value == self.VALUE_SECTION:
					section[name] = {}
					addValues(section[name], row.iterchildren())
				else:
					section[name] = value
		
		config = self.createConfigObj()
		if config != None:
			addValues(config, self)
			if self.validate(config):
				config.write()

	def getRowWithNamePath(self, namePath):
		def findRow(self, rowIterator, namePathList):
			name = namePathList.pop(0)
			for row in rowIterator:
				if row[self.COLUMN_NAME] == name:
					if len(namePathList) > 0:
						return findRow(self, row.iterchildren(), namePathList)
					else:
						return row
			return None
		return findRow(self, self, list(namePath))

	def getApiPortRangeLow(self):
		""""""
		row = self.getRowWithNamePath((self.ROW_COMMON, 'apiportrangelow'))
		return row[self.COLUMN_VALUE]

	def getApiPortRangeHigh(self):
		""""""
		row = self.getRowWithNamePath((self.ROW_COMMON, 'apiportrangehigh'))
		return row[self.COLUMN_VALUE]

	def getDefaultBotRootPath(self):
		""""""
		row = self.getRowWithNamePath((self.ROW_COMMON, 'defaultbotrootpath'))
		return row[self.COLUMN_VALUE]

	def setDefaultBotRootPath(self, path):
		""""""
		row = self.getRowWithNamePath((self.ROW_COMMON, 'defaultbotrootpath'))
		row[self.COLUMN_VALUE] = path

	def getBotNames(self):
		"""Returns a list of bot names."""
		names = []
		for row in self.getRowWithNamePath((self.ROW_BOTS,)).iterchildren():
			names.append(row[self.COLUMN_NAME])
		return names

	def addBot(self, botName, configFile, installDir):
		existingNames = self.getBotNames()
		# search unique bot name
		botName2 = botName
		counter = 2
		while botName2 in existingNames:
			botName2 = botName + ' ' + str(counter)
			counter += 1
		# set data
		botsRow = self.getRowWithNamePath((self.ROW_BOTS,))
		botIter = self.append(botsRow.iter, (botName2, self.VALUE_SECTION))
		self.append(botIter, ('configfile', configFile))
		self.append(botIter, ('installdir', installDir))

	def removeBot(self, botName):
		"""This method removes botName's settings."""
		botRow = self.getRowWithNamePath((self.ROW_BOTS, botName))
		if botRow != None:
			self.remove(botRow.iter)

	def getValue(self, botName, name):
		""""""
		row = self.getRowWithNamePath((self.ROW_BOTS, botName, name))
		return row[self.COLUMN_VALUE]

	def createConfigObj(self):
		"""This method loads the settings from a file."""
		config = None
		# get a path to the ini-file + create directory for it
		configDir = appdirs.user_data_dir('Budabot Bot Manager', 'budabot.com', '1.0')
		if not os.path.exists(configDir):
			os.makedirs(configDir)
		configPath = os.path.join(configDir, 'settings.ini')
		# load the ini-file
		try:
			config = ConfigObj(infile = configPath, create_empty = True, encoding = 'UTF8', configspec = resourcePath('settingsspec.ini'))
		except(ConfigObjError, IOError), e:
			self.emit('error', 'Failed to read settings from "%s": %s' % (configPath, e))
			return None
		return config

	def validate(self, config):
		"""This method validates given ConfigObj."""
		# validate the ini-file
		validator = Validator()
		results = config.validate(validator)
		if results != True:
			# report error
			message = 'Failed to read settings\n'
			for (sectionList, key, _) in configobj.flatten_errors(config, results):
				if key is not None:
					message += 'The "%s" key in the section "%s" failed validation\n' % (key, ', '.join(sectionList))
				else:
					message += 'The following section was missing: %s\n' % ', '.join(sectionList)
			self.emit('error', message)
			return False
		return True

# register class so that custom signals will work
gobject.type_register(SettingModel)

