#! /usr/bin/env python
# -*- coding: utf-8 -*-

import gobject
import gtk
from bot import Bot
from settingmodel import SettingModel

class BotModel(gtk.ListStore):
	""""""

	COLUMN_BOTOBJECT = 0
	COLUMN_SOURCEROWREFERENCE = 1

	# define custom signals that this class can emit
	__gsignals__ = {
		# this signal is emitted when a bot has been removed from the model,
		# the parameter contains bot's name
		'botRemoved': (gobject.SIGNAL_RUN_LAST, gobject.TYPE_NONE, (gobject.TYPE_STRING,)),
	}

	def __init__(self, sourceModel):
		super(BotModel, self).__init__(gobject.TYPE_PYOBJECT, gobject.TYPE_PYOBJECT)
		self.botRowReferenceMap = {}
		if len(sourceModel) != 0:
			raise NotImplementedError('Reading initial content of source model is not implemented')
		self.sourceModel = sourceModel
		self.botsRowReference = None
		sourceModel.connect('row-deleted', self.onSourceRowDeleted)
		sourceModel.connect('row-changed', self.onSourceRowChanged)
		sourceModel.connect('rows-reordered', self.onSourceRowsReordered)

	def getBotByName(self, name):
		""""""
		# loop through rows and return a bot with given name if found
		for row in self:
			bot = row[self.COLUMN_BOTOBJECT]
			if bot.getName() == name:
				return bot
		return None

	def getAllBots(self):
		"""Returns list of all bots in the model."""
		bots = []
		# collect and return all bots
		for row in self:
			bots.append(row[self.COLUMN_BOTOBJECT])
		return bots

	def onSourceRowDeleted(self, sourceModel, sourcePath):
		"""This signal handler is called when a row is removed from source model."""
		for row in self:
			if row[self.COLUMN_SOURCEROWREFERENCE].valid() == False:
				bot = row[self.COLUMN_BOTOBJECT]
				self.remove(row.iter)
				self.emit('botRemoved', bot.getName())

	def onSourceRowsReordered(self, sourceModel, sourcePath, sourceIter, new_order):
		raise NotImplementedError('Reordering rows of bot model is not implemented')

	def onSourceRowChanged(self, sourceModel, sourcePath, sourceIter):
		# get needed values from source model
		try:
			sourceRow = sourceModel[sourceIter]
			sourceRowName = sourceRow[SettingModel.COLUMN_NAME]
		except ValueError:
			return
		# update reference to bots-row
		if sourceRow.parent == None and sourceRowName == SettingModel.ROW_BOTS:
			self.botsRowReference = gtk.TreeRowReference(sourceModel, sourceRow.path)
		# check that the changed row is a bot row, ignore others
		if sourceRow.parent != None and self.botsRowReference != None and sourceRow.parent.path == self.botsRowReference.get_path():
			bot = None
			# try to find an existing bot
			for row in self:
				if sourceRow.path == row[self.COLUMN_SOURCEROWREFERENCE].get_path():
					bot = row[self.COLUMN_BOTOBJECT]
					break
			# if bot was found, update it
			if bot:
				if bot.getName() != sourceRowName:
					raise NotImplementedError('Changing bot name is not implemented')
			# if bot was not found, create it
			else:
				bot = Bot(sourceRowName, self.sourceModel)
				bot.connect('notify::apiAccessible', self.onBotPropertyChanged)
				bot.connect('notify::isRunning', self.onBotPropertyChanged)
				sourceRef = gtk.TreeRowReference(sourceModel, sourceRow.path)
				self.append((bot, sourceRef))
	
	def onBotPropertyChanged(self, bot, property):
		"""This signal handler is called when one of bot's
		properties change.
		
		Emits row-changed signal to notify listeners that
		bot's state has changed.
		"""
		for row in self:
			if row[self.COLUMN_BOTOBJECT] is bot:
				self.row_changed(row.path, row.iter)

# register class so that custom signals will work
gobject.type_register(BotModel)
