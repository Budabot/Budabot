#! /usr/bin/env python
# -*- coding: utf-8 -*-

import gobject
import gtk
from utils import weakConnect, getBotUIStatus, resourcePath

class BotWindowController(gobject.GObject):
	""""""
	
	# Define custom signals that this class can emit.
	__gsignals__ = {
	}
	
	def __init__(self, bot):
		"""Constructor method."""
		self.__gobject_init__()
		self.bot = bot
		
		# load botwindow.glade file
		self.builder = gtk.Builder()
		self.builder.add_from_file(resourcePath('botwindow.glade'))

		# get some widgets and objects for easier access
		self.botWindow  = self.builder.get_object('botwindow')
		outputScrollArea = self.builder.get_object('outputScrollArea')
		self.outputView = self.builder.get_object('outputView')
		self.commandEntry = self.builder.get_object('commandInputEntry')
		self.destinationSelector = self.builder.get_object('destinationSelector')
		self.startButton = self.builder.get_object('startButton')
		self.restartButton = self.builder.get_object('restartButton')
		self.shutdownButton = self.builder.get_object('shutdownButton')
		self.terminateButton = self.builder.get_object('terminateButton')
		self.statusImage = self.builder.get_object('statusImage')
		self.statusLabel = self.builder.get_object('statusLabel')

		# call scrollViewToBottom() when scroll area's vertical scrollbar changes
		weakConnect(outputScrollArea.get_vadjustment(), 'changed', self.scrollViewToBottom)

		# call onCommandGiven() when user hits enter-key within the entry
		weakConnect(self.commandEntry, 'activate', self.onCommandGiven)
		
		# prevent deletion of the window on close
		weakConnect(self.botWindow, 'delete-event', self.onDeleteEvent)
		
		# be notified of bot's changes
		weakConnect(self.bot, 'notify::isRunning', self.onBotPropertyChanged)
		weakConnect(self.bot, 'notify::apiAccessible', self.onBotPropertyChanged)

		# handle button clicks
		weakConnect(self.startButton, 'clicked', self.onButtonClicked)
		weakConnect(self.restartButton, 'clicked', self.onButtonClicked)
		weakConnect(self.shutdownButton, 'clicked', self.onButtonClicked)
		weakConnect(self.terminateButton, 'clicked', self.onButtonClicked)

		self.outputView.set_buffer(self.bot.getConsoleModel())
		self.statusImage.set_property('icon-size', gtk.icon_size_from_name('status-icon-size'))
		self.updateControlStates()

	def destroy(self):
		""""""
		self.botWindow.destroy()
		self.bot = None

	def setConsoleModel(self, model):
		"""Sets console window's buffer to given model."""
		if model:
			self.outputView.set_buffer(model)

	def show(self):
		"""Shows the window to user."""
		self.botWindow.set_property('title', 'Budabot - %s' % self.bot.getName())
		self.botWindow.show_all()

	def onDeleteEvent(self, sender, event):
		"""This method catches delete event and instead of simply deleting the
		dialog, it is hidden instead. Doing this it is possible to re-show the
		dialog next time.
		"""
		self.botWindow.hide()
		return True

	def scrollViewToBottom(self, adjustment):
		"""This callback is called when output view's vertical
		scrollbar (adjustment) changes.
		Scrolls the scrollbar to bottom of the scrollable area.
		"""
		adjustment.set_value(adjustment.upper - adjustment.page_size)

	def onButtonClicked(self, button):
		"""This signal handler is called when user clicks one of the buttons."""
		if button == self.startButton:
			self.bot.start()
		elif button == self.restartButton:
			self.bot.restart()
		elif button == self.shutdownButton:
			self.bot.shutdown()
		elif button == self.terminateButton:
			self.bot.terminate()

	def onCommandGiven(self, sender):
		"""This callback function is called when user hits enter-key in the command
		input entry.
		Sends the entry's text as a command to the bot and clears the entry field.
		"""
		# get command and clear the input entry
		command = self.commandEntry.get_text()
		self.commandEntry.set_text('')
		# get output channel
		# TODO: we should use Bot.CHANNEL_XXX constants instead of blindly
		#       trusting that the values in selector's model are correct
		channel = self.destinationSelector.get_model().get_value(self.destinationSelector.get_active_iter(), 1)
		# send the command
		self.bot.sendCommand(channel, command)

	def onBotPropertyChanged(self, caller, property):
		"""This handler is called when bot's some property has changed."""
		self.updateControlStates()

	def updateControlStates(self):
		"""Updates states of buttons, command entry, destination selector, etc..."""
		apiAccessible = self.bot.get_property('apiAccessible')
		isRunning = self.bot.get_property('isRunning')
		self.commandEntry.set_sensitive(isRunning and apiAccessible)
		self.destinationSelector.set_sensitive(isRunning and apiAccessible)
		self.startButton.set_sensitive(not isRunning)
		self.restartButton.set_sensitive(isRunning and apiAccessible)
		self.shutdownButton.set_sensitive(isRunning and apiAccessible)
		self.terminateButton.set_sensitive(isRunning)

		# set bot status indicator
		status = getBotUIStatus(self.bot)
		self.statusLabel.set_label(status[0])
		self.statusImage.set_property('icon-name', status[1])

# register class so that custom signals will work
gobject.type_register(BotWindowController)
