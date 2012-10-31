#! /usr/bin/env python
# -*- coding: utf-8 -*-

import gtk
from utils import weakConnect, resourcePath
import weakref

class ConfigWindowController(object):
	"""Controller class for config file editor window."""
	
	RESPONSE_CANCEL = 0
	RESPONSE_SAVE = 1
	
	VARIABLE_WIDGET_DICT = {
		'login'                : 'loginNameEntry',
		'password'             : 'loginPasswordEntry',
		'name'                 : 'botNameEntry',
		'dimension'            : 'botDimensionCombobox',
		'my_guild'             : 'botOrganizationEntry',
		'SuperAdmin'           : 'superAdminEntry',
		'DB Type'              : 'dbTypeCombobox',
		'DB Name'              : 'dbNameEntry',
		'DB Host'              : 'dbHostEntry',
		'DB username'          : 'dbUsernameEntry',
		'DB password'          : 'dbPasswordEntry',
		'use_proxy'            : 'useProxyCheckbox',
		'proxy_server'         : 'proxyServerEntry',
		'proxy_port'           : 'proxyPortSpin',
		'default_module_status': 'moduleStatusCheckbox',
		'show_aoml_markup'     : 'showAomlCheckbox',
		'cachefolder'          : 'cacheFolderEntry',
		'API Port'             : 'apiPortSpin'
	}
	
	TOOLTIPS = [
		(('loginNameLabel', 'loginNameEntry'),             "Game account's login name which contains the bot character (case-sensitive)"),
		(('loginPasswordLabel', 'loginPasswordEntry'),     "Game account's login password which contains the bot character (case-sensitive)"),
		(('botNameLabel', 'botNameEntry'),                 "Name of the bot character"),
		(('botDimensionLabel', 'botDimensionLabel'),       "Game server dimension which has the bot character"),
		(('botOrganizationLabel', 'botOrganizationEntry'), "Name of bot character's organization, or leave it as blank to run the bot as a raid bot. The name must match exactly including case and punctuation!"),
		(('superAdminLabel', 'superAdminEntry'),           "Name of the character who has access to all commands and settings on the bot"),
		(('dbTypeLabel', 'dbTypeCombobox'),                "Type of the database which the bot will use"),
		(('dbNameLabel', 'dbNameEntry'),                   "Sqlite: file name of the database,\nMySQL: name of the database"),
		(('dbHostLabel', 'dbHostEntry'),                   "Sqlite: folder path where the database file is stored,\nMySQL: hostname or IP-address of the database server"),
		(('dbUsernameLabel', 'dbUsernameEntry'),           "Username of the MySQL server"),
		(('dbPasswordLabel', 'dbPasswordEntry'),           "Password of the MySQL server"),
		(('useProxyCheckbox',),                            "Enable if you're going to AO Chat Proxy"),
		(('proxyServerLabel', 'proxyServerEntry'),         "Hostname or IP-address of the AO Chat Proxy"),
		(('proxyPortLabel', 'proxyPortSpin'),              "Port of the AO Chat Proxy"),
		(('moduleStatusCheckbox',),                        "Enable to have all modules enabled by default"),
		(('showAomlCheckbox',),                            "Enable for bot to print AOML to console and logs, instead of pretty printing human-friendly messages"),
		(('cacheFolderLabel', 'cacheFolderEntry'),         "Cache folder for storing organization XML files"),
		(('apiPortLabel', 'apiPortSpin'),                  "Port where the bot will listen for BudAPI requests, this value will change automatically if the port is in use")
	]
	
	def __init__(self, bot, configFile, parent):
		"""Constructor method.
		
		bot        - bot object which is being edited
		configFile - bot's config file which is being edited
		parent     - a top level window, the config window will positioned on top this
		"""
		self.configFile = configFile
		self.builder = gtk.Builder()
		self.builder.add_from_file(resourcePath('configwindow.glade'))
		self.dialog = self.builder.get_object('configDialog')
		self.botRef = weakref.ref(bot)
		self.parentRef = weakref.ref(parent)
		# append bot name to dialog's title
		self.dialog.set_title(self.dialog.get_title() % bot.getName())
		weakConnect(self.dialog, 'response', self.onConfigDialogResponse)
		weakConnect(self.dialog, 'response', self.onConfigDialogResponse)
		weakConnect(self.builder.get_object('dbTypeCombobox'), 'changed', self.onDbTypeChanged)
		weakConnect(self.builder.get_object('useProxyCheckbox'), 'toggled', self.onUseProxyToggled)
		# add buttons, must be added in Gnome's preferred order, see:
		# http://developer.gnome.org/hig-book/3.4/windows-alert.html.en#alert-button-order
		self.dialog.add_button(gtk.STOCK_CANCEL, self.RESPONSE_CANCEL)
		saveButton = self.dialog.add_button(gtk.STOCK_SAVE, self.RESPONSE_SAVE)
		saveButton.grab_default()
		# add alternative order for Windows, see:
		# http://msdn.microsoft.com/en-us/library/windows/desktop/aa511268.aspx#commitButtons
		self.dialog.set_alternative_button_order([self.RESPONSE_SAVE, self.RESPONSE_CANCEL])
		# set tooltip texts to widgets
		for tooltipData in self.TOOLTIPS:
			widgetNames = tooltipData[0]
			text = tooltipData[1]
			for widgetName in widgetNames:
				widget = self.builder.get_object(widgetName)
				widget.set_tooltip_text(text)

	def show(self):
		"""Shows the config window to user."""
		self.loadConfigFile()
		self.dialog.set_transient_for(self.parentRef())
		self.dialog.set_property('window-position', gtk.WIN_POS_CENTER_ON_PARENT)
		self.dialog.show_all()
	
	def loadConfigFile(self):
		"""Loads variables from the config file and populates the
		window's input widgets.
		"""
		self.configFile.load()
		for varName, widgetName in self.VARIABLE_WIDGET_DICT.items():
			def getValue(default):
				try:
					return self.configFile.getVar(varName)
				except KeyError:
					return default

			widget = self.builder.get_object(widgetName)
			# set check box's state (value should be either 0 or 1)
			if isinstance(widget, gtk.CheckButton):
				widget.set_active(bool(getValue(default=0)))
			# set spin button's contents from value
			elif isinstance(widget, gtk.SpinButton):
				widget.set_value(int(getValue(default=0)))
			# set text entry widget's contents from value
			elif isinstance(widget, gtk.Entry):
				widget.set_text(str(getValue(default='')))
			# find value from combo box model's first column and select
			# row which matches
			elif isinstance(widget, gtk.ComboBox):
				model = widget.get_model()
				value = getValue(default=None)
				widget.set_active(0)
				for index in range(0, len(model)):
					if model[index][0] == value:
						widget.set_active(index)
						break

	def saveConfigFile(self):
		"""Collects values from input widgets and saves them to
		the config file.
		"""
		for varName, widgetName in self.VARIABLE_WIDGET_DICT.items():
			widget = self.builder.get_object(widgetName)
			value = None
			# get integer value (1 or 0) depending if check box is toggled or not
			if isinstance(widget, gtk.CheckButton):
				value = int(widget.get_active())
			# get int value from the entry widget
			elif isinstance(widget, gtk.SpinButton):
				value = widget.get_value_as_int()
			# get text value from the entry widget
			elif isinstance(widget, gtk.Entry):
				value = widget.get_text()
			# get value from combo box's selected row's first column
			elif isinstance(widget, gtk.ComboBox):
				model = widget.get_model()
				value = model[widget.get_active()][0]
			self.configFile.setVar(varName, value)
		self.configFile.save()
	
	def onDbTypeChanged(self, comboBox):
		"""This signal handler is called when database type's value changes.
		
		Enables database username and password entries if the new type
		is 'mysql', and disables if 'sqlite'.
		"""
		index = comboBox.get_active()
		enableCredentials = comboBox.get_model()[index][0] == 'mysql'
		self.builder.get_object('dbUsernameEntry').set_sensitive(enableCredentials)
		self.builder.get_object('dbPasswordEntry').set_sensitive(enableCredentials)

	def onUseProxyToggled(self, checkbox):
		"""This signal handler is called when proxy checkbox's state changes.
		
		Enables proxy's server and port entries when the checkbox is ticked
		and disables when it is unticked.
		"""
		proxyEnabled = checkbox.get_active()
		self.builder.get_object('proxyServerEntry').set_sensitive(proxyEnabled)
		self.builder.get_object('proxyPortSpin').set_sensitive(proxyEnabled)
		
	def onConfigDialogResponse(self, caller, responseId):
		"""This signal handler is called when user clicks either
		Save or Cancel button.
		
		In both cases the window is closed, but if Save is clicked the
		config file is also saved.
		"""
		if responseId == self.RESPONSE_CANCEL:
			self.dialog.destroy()
		elif responseId == self.RESPONSE_SAVE:
			self.saveConfigFile()
			self.dialog.destroy()
			if self.botRef().get_property('isRunning'):
				self.restartDialog = gtk.MessageDialog(
					parent = self.parentRef(),
					flags = gtk.DIALOG_MODAL,
					type = gtk.MESSAGE_QUESTION,
					buttons = gtk.BUTTONS_YES_NO,
					message_format = "The bot is currently running. These changes will not affect it before it is restarted. Would you like to restart it now?"
				)
				weakConnect(self.restartDialog, 'response', self.onRestartDialogResponse)
				self.restartDialog.show_all()

	def onRestartDialogResponse(self, caller, responseId):
		"""This signal handler is called when user closes the restart question
		dialog.
		
		If user clicked Yes-button then the bot is restarted.
		"""
		if responseId == gtk.RESPONSE_YES:
			if self.botRef().get_property('apiAccessible'):
				self.botRef().restart()
			else:
				self.botRef().terminate()
				self.botRef().start()
		self.restartDialog.destroy()
