#! /usr/bin/env python
# -*- coding: utf-8 -*-

import os
# this application isn't translated to other languages so lets not use local
# language anywhere in the UI (like in default buttons which already have
# localized versions).
os.environ['LANGUAGE'] = 'C'

# on Windows we need to use external library 'elib.intl' to force the
# enviroment variable for use everywhere
try:
	import elib.intl
	elib.intl._putenv('LANGUAGE', 'C')
except ImportError:
	pass

# install Twisted/GTK reactor
from twisted.internet import gtk2reactor
gtk2reactor.install()

import sys
import gtk
from twisted.internet import reactor
from settingmodel import SettingModel
from botmodel import BotModel
from systraycontroller import SystrayController
from botwindowcontroller import BotWindowController
from controlpanelcontroller import ControlPanelController
from bot import Bot
from botconfigfile import BotPhpConfigFile
from configwindow import ConfigWindowController
from utils import resourcePath

class Application:
	"""The main application class"""

	botWindowControllers = {}

	def execute(self):
		""""""

		# intialize thread support (hangs on winxp)
		#gtk.gdk.threads_init()

		# load Cillop-Midnite theme
		gtk.rc_add_default_file(resourcePath("themes/Cillop-Midnite/gtk-2.0/gtkrc"))
		settings = gtk.settings_get_default()
		settings.set_string_property("gtk-theme-name", "Cillop-Midnite", "")

		# load icon theme
		theme = gtk.icon_theme_get_default()
		theme.prepend_search_path(resourcePath('themes'))
		settings.set_string_property("gtk-icon-theme-name", "budabot-icon-theme", "")
		gtk.icon_size_register('status-icon-size', 24, 24)

		self.settingModel = SettingModel()
		self.botModel = BotModel(self.settingModel)
		self.botModel.connect('botRemoved', self.onBotRemoved)

		systrayController = SystrayController()

		controlPanelController = ControlPanelController(self.botModel, self.settingModel)
		controlPanelController.connect('action_triggered', self.onControlPanelAction)

		# open control panel when user select 'open' from systray's context menu
		systrayController.connect_object('open_requested', ControlPanelController.show, controlPanelController)
		# opens/closes control panel when user clicks systray icon
		systrayController.connect_object('toggle_requested', ControlPanelController.toggle, controlPanelController)

		# notify systray controller of control panel's visibility
		controlPanelController.connect('visibility_changed', systrayController.onControlPanelVisibilityChanged)

		# connect exit requests to quit()-method
		controlPanelController.connect_object('exit_requested', Application.quit, self)
		systrayController.connect_object('exit_requested', Application.quit, self)

		# show errors to user
		self.settingModel.connect('error', self.onError)

		self.settingModel.load()

		controlPanelController.show()
		# run Twisted + GTK event loop
		reactor.run()
		
		systrayController.hideIcon()

	def quit(self):
		"""Calling this method will stop the event loop and execution returns
		from execute().
		"""
		dialog = gtk.MessageDialog(None, gtk.DIALOG_MODAL, gtk.MESSAGE_QUESTION, gtk.BUTTONS_OK_CANCEL, 'Exiting')
		dialog.set_markup("Exiting from the Bot Manager will terminate any running bots, are you sure?")
		if dialog.run() == gtk.RESPONSE_OK:
			# terminate all running bots
			for bot in self.botModel.getAllBots():
				bot.terminate()
			# hop out of event loop
			reactor.stop()
		dialog.destroy()

	def onControlPanelAction(self, controller, action, botName):
		"""This signal handler is called when user activates some action
		in control panel.
		"""
		bot = self.botModel.getBotByName(botName)
		if action == 'open':
			botController = self.botWindowController(botName)
			botController.show()
		elif action == 'configure':
			# show configuration window to user
			configPath = self.settingModel.getValue(botName, 'configfile')
			configFile = BotPhpConfigFile(configPath)
			self.configWindowController = ConfigWindowController(bot, configFile, parent=controller.getView())
			self.configWindowController.show()

		elif action == 'start':
			bot.start()
		elif action == 'restart':
			bot.restart()
		elif action == 'shutdown':
			bot.shutdown()
		elif action == 'terminate':
			bot.terminate()
		else:
			self.showErrorMessage("This action is not implemented!")

	def onError(self, sender, message):
		"""This signal handler is called when an error occurs within the application"""
		self.showErrorMessage(message)

	def onBotRemoved(self, caller, botName):
		"""This signal handler is called when a bot is removed from BotModel.
		
		Closes and deletes bot's window.
		"""
		if botName in self.botWindowControllers:
			ctrl = self.botWindowControllers.pop(botName)
			ctrl.destroy()
			# only references remaining should be ctrl and getrefcount's parameter
			assert sys.getrefcount(ctrl) == 2
		
	def showErrorMessage(self, message):
		"""Shows error dialog to user."""
		dialog = gtk.MessageDialog(None, gtk.DIALOG_MODAL, gtk.MESSAGE_ERROR, gtk.BUTTONS_OK, 'Error')
		dialog.set_markup(message)
		dialog.run()
		dialog.destroy()

	def botWindowController(self, botName):
		"""Creates bot window controller, if it doesn't exist yet, of
		name botName and returns it.
		"""
		if botName not in self.botWindowControllers:
			bot = self.botModel.getBotByName(botName)
			self.botWindowControllers[botName] = BotWindowController(bot);
		return self.botWindowControllers[botName]
