#! /usr/bin/env python
# -*- coding: utf-8 -*-

"""This module contains all page classes used in wizard for adding and
importing bots.

This module also provides following constants which are used to identify
each page:

  SELECT_ACTION_PAGE_ID         - This is the first page where user can select
                                  if he is going add a new bot or import and
                                  existing bot.
  SELECT_IMPORT_PAGE_ID         - This is first page in the import functionality
                                  where user can browse for location of the bot.
  SELECT_BOT_DIRECTORY_PAGE_ID  - With this page user can give path to bot's
                                  install directory.
  ENTER_ACCOUNT_INFO_PAGE_ID    - With this page user provides login information
                                  of the game account where the bot will be running.
  ENTER_CHARACTER_INFO_PAGE_ID  - With this page user provides name and dimension
                                  of the bot's character.
  SELECT_BOT_TYPE_PAGE_ID       - With this page user can select if the bot will
                                  act as org or raid bot.
  ENTER_SUPER_ADMIN_PAGE_ID     - With this page user can give name of the super
                                  admin who will have access to all commands of 
                                  the bot.
  SELECT_DB_SETTINGS_PAGE_ID    - With this page user can select between default
                                  and manual database settings.
  SELECT_DB_TYPE_PAGE_ID        - With this page user can select between Sqlite
                                  and MySQL.
  ENTER_SQLITE_SETTINGS_PAGE_ID - With this page user can enter Sqlite settings.
  ENTER_MYSQL_SETTINGS_PAGE_ID  - With this page user can enter MySQL settings.
  SELECT_MODULE_STATUS_PAGE_ID  - With this page user can select if all modules
                                  are enabled or disabled by default.
  NAME_BOT_PAGE_ID              - In this page user can give the bot a name.
  FINISH_PAGE_ID                - This is the final page which shows summary of
                                  the bot settings.
"""

import os
import re
import sys
import gtk
import gobject
from botconfigfile import BotPhpConfigFile

SELECT_ACTION_PAGE_ID         = 1
SELECT_IMPORT_PAGE_ID         = 2
SELECT_BOT_DIRECTORY_PAGE_ID  = 3
ENTER_ACCOUNT_INFO_PAGE_ID    = 4
ENTER_CHARACTER_INFO_PAGE_ID  = 5
SELECT_BOT_TYPE_PAGE_ID       = 6
ENTER_SUPER_ADMIN_PAGE_ID     = 7
SELECT_DB_SETTINGS_PAGE_ID    = 8
SELECT_DB_TYPE_PAGE_ID        = 9
ENTER_SQLITE_SETTINGS_PAGE_ID = 10
ENTER_MYSQL_SETTINGS_PAGE_ID  = 11
SELECT_MODULE_STATUS_PAGE_ID  = 12
NAME_BOT_PAGE_ID              = 13
FINISH_PAGE_ID                = 14

class Page(gobject.GObject):
	"""A common base class for each page class.
	
	In order to use this class you need to either create an object of this or
	one the derived classes. You need to provide ID, type, title string, and
	a widget which is shown inside the Assistant.
	
	In addition, the page object must be added to the Assistant with
	appendPage() before it can be used.
	"""

	# Define custom signals that this class can emit.
	__gsignals__ = {
		# this signal is emitted wizard entered this page
		'entered': (gobject.SIGNAL_RUN_LAST, gobject.TYPE_NONE, ()),
		# this signal is emitted wizard left this page
		'left': (gobject.SIGNAL_RUN_LAST, gobject.TYPE_NONE, ()),
	}

	# custom properties
	__gproperties__ = {
		'complete' : (gobject.TYPE_BOOLEAN, 'complete', 'is page complete', False, gobject.PARAM_READWRITE),
	}

	def __init__(self, controller, id):
		"""Constructor method.
		
		The given id value is used to identify the page object by the
		Assistant to which it is appended.
		"""
		self.__gobject_init__()
		self.id = id
		self.controller = controller
		self.index = -1
		self.widget = None
		self.type = gtk.ASSISTANT_PAGE_CONTENT
		self.title = ''
		self.completenessFunc = lambda: True
		self.completenessFuncArgs = []
		self.nextPageIdFunc = lambda: None
		self.nextPageIdFuncArgs = []

	def do_get_property(self, property):
		"""Returns value of given property.
		This is required to make GTK's properties to work.
		"""
		if property.name == 'complete':
			return self.completenessFunc(*self.completenessFuncArgs)

	def enter(self):
		"""This method is called when wizard enters this page. By default it
		emits 'entered' signal.
		"""
		self.emit('entered')

	def leave(self):
		"""This method is called when wizard leaves this page. By default it
		emits 'left' signal.
		"""
		self.emit('left')

	def getWidget(self):
		"""Returns widget object which represents the visible contents
		of the page.
		
		This method is used by the Assistant.
		"""
		return self.widget

	def getNextPageId(self):
		"""Returns ID value of the next page to where the wizard should change
		when user clicks 'Forward' button.
		
		This method is used by the Assistant.
		
		Internally this method calls function object set with
		setNextPageIdFunc() and returns what it returns.
		"""
		return self.nextPageIdFunc(*self.nextPageIdFuncArgs)

	def getTitle(self):
		"""Returns title string of this page which should be shown in the Assistant.
		
		This method is used by the Assistant when it needs to draw the
		title to screen.
		"""
		return self.title

	def getType(self):
		"""This method returns the GTK type of this page.
		
		This method is used by the Assistant.
		"""
		return self.type

	def setType(self, type):
		"""This method sets the GTK type of the page.
		
		By default the type is gtk.ASSISTANT_PAGE_CONTENT, if some other type
		is required then is method should be called to set it correctly.
		
		List of other possible constants can be found from here:
		  http://www.pygtk.org/docs/pygtk/gtk-constants.html#gtk-assistant-page-type-constants
		"""
		self.type = type

	def setTitle(self, title):
		"""This method sets given string as page's title.
		
		By default the title string is an empty string and any sub classes
		should set the title explcitly by calling this method.
		"""
		self.title = title

	def setNextPageIdFunc(self, function, *args):
		"""Sets function which should return id of the next page."""
		self.nextPageIdFunc = function
		self.nextPageIdFuncArgs = args

	def setCompletenessFunc(self, function, *args):
		"""Sets function which should return boolean value which indicates if
		the page is complete or not.
		"""
		self.completenessFunc = function
		self.completenessFuncArgs = args

	def updateCompleteness(self, *args):
		"""Notifies any connected listeners that the page complete-status might
		have changed and should be updated.
		"""
		self.notify('complete')

# register class so that custom signals will work
gobject.type_register(Page)

class SelectActionPage(Page):
	"""This page class lets user select should he add a new bot or import
	a existing bot.
	"""

	TYPE_ADDNEW = 1
	TYPE_IMPORT = 2

	def __init__(self, controller):
		"""Constructor method."""
		super(SelectActionPage, self).__init__(controller, SELECT_ACTION_PAGE_ID)
		self.setType(gtk.ASSISTANT_PAGE_INTRO)
		self.setTitle('Add or import bot')
		self.setNextPageIdFunc(self.nextPageId)
		self.widget = controller.getViewObject('selectActionPage')
		self.addBotRadioButton = controller.getViewObject('addBotRadioButton')
		self.importBotRadioButton = controller.getViewObject('importBotRadioButton')
		self.importBotRadioButton.set_group(self.addBotRadioButton)

	def getActionType(self):
		"""Returns type of action."""
		if self.addBotRadioButton.get_property('active'):
			return self.TYPE_ADDNEW
		elif self.importBotRadioButton.get_property('active'):
			return self.TYPE_IMPORT

	def nextPageId(self):
		"""Returns ID of the next page to where wizard should change."""
		if self.getActionType() == self.TYPE_ADDNEW:
			return SELECT_BOT_DIRECTORY_PAGE_ID
		elif self.getActionType() == self.TYPE_IMPORT:
			return SELECT_IMPORT_PAGE_ID

class SelectImportPage(Page):
	"""This page class lets users browse for location of the Budabot
	installation and config file which should be imported to Bot Manager.
	"""
	
	def __init__(self, controller):
		"""Constructor method."""
		super(SelectImportPage, self).__init__(controller, SELECT_IMPORT_PAGE_ID)
		self.setTitle('Import existing bot')
		self.setNextPageIdFunc(lambda: NAME_BOT_PAGE_ID)
		self.setCompletenessFunc(lambda self: self.getSelectedBotConfFilePath() != None, self)
		self.widget = controller.getViewObject('selectImportPage')
		self.settingModel = controller.getSettingModel()
		self.modelPath = ''
		self.dirChooser = controller.getViewObject('botImportDirChooser')
		self.dirChooser.connect('current-folder-changed', self.onBotImportDirChoosen)
		self.dirChooser.set_current_folder(self.settingModel.getDefaultBotRootPath())
		self.botImportModel = BotImportModel()
		self.botView = controller.getViewObject('importBotListView')
		self.botView.set_model(self.botImportModel)
		self.botView.get_selection().connect('changed', self.updateCompleteness)

	def getSelectedBotRootPath(self):
		"""Returns path to the bot software's root folder."""
		return self.dirChooser.get_filename()

	def getSelectedBotConfFilePath(self):
		"""Returns path to the currently selected configuration file."""
		selected = self.botView.get_selection().get_selected()
		if selected[0] != None and selected[1] != None:
			filename = selected[0].get(selected[1], 0)[0]
			return os.path.join(self.modelPath, filename)
		return None

	def onBotImportDirChoosen(self, caller):
		"""This signal handler is called when user chooses a directory in import wizard."""
		rootPath = self.dirChooser.get_filename()
		isRequiredVersion = False
		try:
			with open(os.path.join(rootPath, 'main.php'), 'r') as file:
				# attempt to parse something like this: '$version = "3.0_Alpha"';
				match = re.search(r'\$version\s*=\s*[\'"](.+)[\'"]', file.read())
				if match == None:
					raise WrongRootPathError()
				currentVersion = match.group(1)
				minimumVersion = '3.0' # require at least Budabot 3.0

				def tryint(s):
					"""Part of natural sort algorithm from:
					  http://nedbatchelder.com/blog/200712.html#e20071211T054956
					"""
					try:
						return int(s)
					except:
						return s
				def alphanum_key(s):
					"""Turn a string into a list of string and number chunks.
					"z23a" -> ["z", 23, "a"]
					Part of natural sort algorithm from:
					  http://nedbatchelder.com/blog/200712.html#e20071211T054956
					"""
					return [ tryint(c) for c in re.split('([0-9]+)', s) ]

				# compare current and minimum required version using natural sort
				isRequiredVersion = alphanum_key(currentVersion) >= alphanum_key(minimumVersion)

			if isRequiredVersion == False:
				raise WrongRootPathError()

			self.modelPath = os.path.join(rootPath, 'conf')
			if os.path.isdir(self.modelPath):
				self.botImportModel.load(self.modelPath)
				# save current path to settings for later use if bots were found
				if len(self.botImportModel) > 0:
					self.settingModel.setDefaultBotRootPath(rootPath)
					self.settingModel.save()
			else:
				raise WrongRootPathError()
			
		except WrongRootPathError:
			self.botImportModel.clear()

class WrongRootPathError(Exception):
	pass

class BotImportModel(gtk.ListStore):
	def __init__(self):
		"""Constructor method."""
		super(BotImportModel, self).__init__(gobject.TYPE_STRING, gobject.TYPE_STRING, gobject.TYPE_STRING)

	def load(self, path):
		"""Loads all config files from given path and adds them to the model."""
		self.clear()
		for fileName in os.listdir(path):
			# ignore the template file
			if fileName == 'config.template.php':
				continue
			try:
				# try to load the file as a config file
				filePath = os.path.join(path, fileName)
				configFile = BotPhpConfigFile(filePath)
				configFile.load()
				name = configFile.getVar('name')
				dimension = 'RK %s' % configFile.getVar('dimension')
			except (KeyError, IOError):
				# ignore files which are not valid config files
				continue
			# add to the config file to model
			self.append((fileName, name, dimension))

class SelectBotInstallDirectoryPage(Page):
	"""This page class lets users browse for location of the Budabot installation."""
	
	def __init__(self, controller):
		"""Constructor method."""
		super(SelectBotInstallDirectoryPage, self).__init__(controller, SELECT_BOT_DIRECTORY_PAGE_ID)
		self.pathIsValid = False
		self.setTitle('Select Budabot\'s Directory')
		self.setNextPageIdFunc(lambda: ENTER_ACCOUNT_INFO_PAGE_ID)
		self.setCompletenessFunc(lambda self: self.pathIsValid, self)
		self.widget = controller.getViewObject('selectBotInstallDirectoryPage')
		self.settingModel = controller.getSettingModel()
		self.botPath = ''
		self.dirChooser = controller.getViewObject('botRootDirChooser')
		self.dirChooser.connect('current-folder-changed', self.onDirChoosen)
		self.dirChooser.set_current_folder(self.settingModel.getDefaultBotRootPath())

	def getSelectedBotRootPath(self):
		"""Returns path to the bot software's root directory."""
		return self.botPath

	def onDirChoosen(self, caller):
		"""This signal handler is called when user chooses a directory where
		the bot software has been installed.
		"""
		self.botPath = self.dirChooser.get_filename()
		# check that main.php exists in the directory before accepting the path
		if os.path.exists(os.path.join(self.botPath, 'main.php')):
			self.pathIsValid = True
			self.settingModel.setDefaultBotRootPath(self.botPath)
			self.settingModel.save()
		else:
			self.pathIsValid = False
		self.updateCompleteness()

class EnterAccountInfoPage(Page):
	"""This page class lets users to give AO account's username and password
	which contains the character that will act as the bot.
	"""

	def getUsername(self):
		"""This method returns the username that user has inputted."""
		return self.usernameEntry.get_text()

	def getPassword(self):
		"""This method returns the password that user has inputted."""
		return self.passwordEntry.get_text()

	def __init__(self, controller):
		"""Constructor method."""
		super(EnterAccountInfoPage, self).__init__(controller, ENTER_ACCOUNT_INFO_PAGE_ID)
		self.pathIsValid = False
		self.setTitle('Enter Account Information')
		self.setNextPageIdFunc(lambda: ENTER_CHARACTER_INFO_PAGE_ID)
		self.setCompletenessFunc(lambda self: len(self.usernameEntry.get_text()) > 0 and len(self.passwordEntry.get_text()) > 0, self)
		self.widget = controller.getViewObject('enterAccountInfoPage')
		self.usernameEntry = controller.getViewObject('accountUsernameEntry')
		self.usernameEntry.connect('notify::text', self.updateCompleteness)
		self.passwordEntry = controller.getViewObject('accountPasswordEntry')
		self.passwordEntry.connect('notify::text', self.updateCompleteness)

class EnterCharacterInfoPage(Page):
	"""This page class lets users to give dimension and name of the character
	on which the bot will run on.
	"""

	def __init__(self, controller):
		"""Constructor method."""
		super(EnterCharacterInfoPage, self).__init__(controller, ENTER_CHARACTER_INFO_PAGE_ID)
		self.pathIsValid = False
		self.setTitle('Enter Character Information')
		self.setNextPageIdFunc(lambda: SELECT_BOT_TYPE_PAGE_ID)
		self.setCompletenessFunc(lambda self: len(self.characterNameEntry.get_text()) > 0, self)
		self.widget = controller.getViewObject('enterCharacterInfoPage')
		self.dimensionComboBox = controller.getViewObject('dimensionComboBox')
		self.characterNameEntry = controller.getViewObject('characterNameEntry')
		self.characterNameEntry.connect('notify::text', self.updateCompleteness)

	def getDimension(self):
		"""Returns number of the dimension server where the bot character is at."""
		model = self.dimensionComboBox.get_model()
		iter = self.dimensionComboBox.get_active_iter()
		if model == None or iter == None:
			return None
		return model[iter][0]

	def getCharacterName(self):
		"""Returns character's name as a string."""
		return self.characterNameEntry.get_text()

class SelectBotTypePage(Page):
	"""This page class lets user to select which type of bot he would like
	to create:
	  - organization bot or
	  - raid bot?
	
	In addition, if organization bot is chosen, user must also give name of
	the organization.
	"""

	def __init__(self, controller):
		"""Constructor method."""
		super(SelectBotTypePage, self).__init__(controller, SELECT_BOT_TYPE_PAGE_ID)
		self.isComplete = True
		self.setTitle('Select Bot Type')
		self.setNextPageIdFunc(lambda: ENTER_SUPER_ADMIN_PAGE_ID)
		self.setCompletenessFunc(lambda self: self.isComplete, self)
		# get widgets from builder
		self.widget = controller.getViewObject('selectBotTypePage')
		self.raidBotRadioButton         = controller.getViewObject('raidBotRadioButton')
		self.organizationBotRadioButton = controller.getViewObject('organizationBotRadioButton')
		self.organizationNameEntry      = controller.getViewObject('organizationNameEntry')
		# connect signals to update() method
		self.raidBotRadioButton.connect('toggled', self.update)
		self.organizationBotRadioButton.connect('toggled', self.update)
		self.organizationNameEntry.connect('notify::text', self.update)
		# group the radio buttons together
		self.organizationBotRadioButton.set_group(self.raidBotRadioButton)
		self.update()

	def isOrganizationBot(self):
		"""Returns True if user has selected to create a organization bot."""
		return self.organizationBotRadioButton.get_property('active')

	def getOrganizationName(self):
		"""Returns name of the organization of which the new bot will act in."""
		return self.organizationNameEntry.get_text()

	def update(self, *args):
		"""This method updates states of the UI elements on the page."""
		if self.raidBotRadioButton.get_property('active'):
			self.organizationNameEntry.set_sensitive(False)
			self.isComplete = True
		elif self.organizationBotRadioButton.get_property('active'):
			self.organizationNameEntry.set_sensitive(True)
			self.isComplete = len(self.organizationNameEntry.get_text()) > 0
		self.updateCompleteness()

class EnterSuperAdminPage(Page):
	"""This page class lets user to enter name of the character who will be
	super administrator of the bot.
	"""

	def __init__(self, controller):
		"""Constructor method."""
		super(EnterSuperAdminPage, self).__init__(controller, ENTER_SUPER_ADMIN_PAGE_ID)
		self.setTitle('Super Administrator\'s Name')
		self.setNextPageIdFunc(lambda: SELECT_DB_SETTINGS_PAGE_ID)
		# page is complete if given admin name is not empty
		self.setCompletenessFunc(lambda self: len(self.superAdminNameEntry.get_text()) > 0, self)
		self.widget = controller.getViewObject('enterSuperAdminPage')
		self.superAdminNameEntry = controller.getViewObject('superAdminNameEntry')
		self.superAdminNameEntry.connect('notify::text', self.updateCompleteness)

	def getSuperAdminName(self):
		"""Returns name of the character that will act as the new bot's super administrator."""
		return self.superAdminNameEntry.get_text()

class SelectDatabaseSettingsPage(Page):
	"""This page class lets user to select if he wants to use default database
	settings or set it up manually.
	"""

	def __init__(self, controller):
		"""Constructor method."""
		super(SelectDatabaseSettingsPage, self).__init__(controller, SELECT_DB_SETTINGS_PAGE_ID)
		self.setTitle('Database Setup')
		self.setNextPageIdFunc(self.nextPageId)
		self.setCompletenessFunc(lambda: True)
		# get widgets from builder
		self.widget = controller.getViewObject('selectDatabaseSettingsPage')
		self.defaultRadioButton = controller.getViewObject('defaultDBSettingsRadioButton')
		self.manualRadioButton  = controller.getViewObject('manualDBSettingsRadioButton')
		# group the radio buttons together
		self.manualRadioButton.set_group(self.defaultRadioButton)

	def areManualSettingsUsed(self):
		"""Returns True if manual database settings should be used."""
		return self.manualRadioButton.get_property('active')

	def nextPageId(self):
		"""Returns ID of the next page to where wizard should change."""
		if self.defaultRadioButton.get_property('active'):
			return SELECT_MODULE_STATUS_PAGE_ID
		elif self.manualRadioButton.get_property('active'):
			return SELECT_DB_TYPE_PAGE_ID

class SelectDatabaseTypePage(Page):
	"""This page class lets user to select which database system he wishes to
	use, Sqlite or MySQL.
	"""

	def __init__(self, controller):
		"""Constructor method."""
		super(SelectDatabaseTypePage, self).__init__(controller, SELECT_DB_TYPE_PAGE_ID)
		self.setTitle('Database Setup - Select Type')
		self.setNextPageIdFunc(self.nextPageId)
		self.setCompletenessFunc(lambda: True)
		# get widgets from builder
		self.widget = controller.getViewObject('selectDatabaseTypePage')
		self.sqliteRadioButton = controller.getViewObject('sqliteTypeRadioButton')
		self.mysqlRadioButton  = controller.getViewObject('mysqlTypeRadioButton')
		# group the radio buttons together and select default button
		self.sqliteRadioButton.set_group(self.mysqlRadioButton)
		self.sqliteRadioButton.set_active(True)

	def isSqliteSelected(self):
		"""Returns True if user had selected to use Sqlite database."""
		return self.sqliteRadioButton.get_property('active')

	def isMysqlSelected(self):
		"""Returns True if user had selected to use MySQL database."""
		return self.mysqlRadioButton.get_property('active')

	def nextPageId(self):
		"""Returns ID of the next page to where wizard should change."""
		if self.sqliteRadioButton.get_property('active'):
			return ENTER_SQLITE_SETTINGS_PAGE_ID
		elif self.mysqlRadioButton.get_property('active'):
			return ENTER_MYSQL_SETTINGS_PAGE_ID

class EnterSqliteSettingsPage(Page):
	"""This page class lets user to enter Sqlite settings."""

	def __init__(self, controller):
		"""Constructor method."""
		super(EnterSqliteSettingsPage, self).__init__(controller, ENTER_SQLITE_SETTINGS_PAGE_ID)
		self.setTitle('Database Setup - Sqlite Settings')
		self.setNextPageIdFunc(lambda: SELECT_MODULE_STATUS_PAGE_ID)
		self.setCompletenessFunc(lambda: True)
		# get widgets from builder
		self.widget = controller.getViewObject('enterSqliteSettingsPage')
		self.sqliteDBFilePathEntry = controller.getViewObject('sqliteDBFilePathEntry')
		controller.getViewObject('sqliteDBFileBrowseButton').connect('clicked', self.onBrowseClicked)
		self.sqliteDBFilePathEntry.set_text(os.path.normpath('data/budabot.db'))

	def getDatabaseFolderPath(self):
		"""Returns relative path to the folder where Sqlite database file is located at."""
		filePath = self.sqliteDBFilePathEntry.get_text()
		return os.path.dirname(filePath)

	def getDatabaseFilename(self):
		"""Returns filename of the Sqlite database file."""
		filePath = self.sqliteDBFilePathEntry.get_text()
		return os.path.basename(filePath)

	def onBrowseClicked(self, caller):
		"""This signal handler method is called when user clicks the
		browse button.
		"""
		# build the file chooser dialog
		title = 'Select Sqlite Database File'
		parent = self.controller.getAssistant()
		action = gtk.FILE_CHOOSER_ACTION_SAVE
		buttons = (gtk.STOCK_APPLY,  gtk.RESPONSE_ACCEPT,
		           gtk.STOCK_CANCEL, gtk.RESPONSE_REJECT)
		dialog = gtk.FileChooserDialog(title, parent, action, buttons)
		# get path where the bot is installed
		installPath = self.controller.getBotInstallPath()
		# set dialog's starting path
		dbPath = os.path.normpath(os.path.join(installPath, self.sqliteDBFilePathEntry.get_text()))
		dialog.select_filename(dbPath)
		# run the dialog and if user clicked 'apply' set the selected file path
		# to the entry as relative to bot's install directory
		if dialog.run() == gtk.RESPONSE_ACCEPT:
			dbPath = dialog.get_filename()
			relativeDBPath = os.path.relpath(dbPath, installPath)
			self.sqliteDBFilePathEntry.set_text(relativeDBPath)
		dialog.destroy()

class EnterMysqlSettingsPage(Page):
	"""This page class lets user to enter MySQL settings."""

	def __init__(self, controller):
		"""Constructor method."""
		super(EnterMysqlSettingsPage, self).__init__(controller, ENTER_MYSQL_SETTINGS_PAGE_ID)
		self.setTitle('Database Setup - MySQL Settings')
		self.setNextPageIdFunc(lambda: SELECT_MODULE_STATUS_PAGE_ID)
		self.setCompletenessFunc(lambda: len(self.dbNameEntry.get_text()) > 0 and
		                                 len(self.hostEntry.get_text()) > 0 and
		                                 len(self.usernameEntry.get_text()) > 0)
		# get widgets from builder
		self.widget = controller.getViewObject('enterMysqlSettingsPage')
		self.dbNameEntry   = controller.getViewObject('mysqlDbNameEntry')
		self.hostEntry     = controller.getViewObject('mysqlHostEntry')
		self.usernameEntry = controller.getViewObject('mysqlUsernameEntry')
		self.passwordEntry = controller.getViewObject('mysqlPasswordEntry')
		# set default values
		self.dbNameEntry.set_text('budabot')
		self.hostEntry.set_text('localhost')
		self.usernameEntry.set_text('root')
		self.passwordEntry.set_text('')
		# update completeness when values in the entries change
		self.dbNameEntry.connect('notify::text', self.updateCompleteness)
		self.hostEntry.connect('notify::text', self.updateCompleteness)
		self.usernameEntry.connect('notify::text', self.updateCompleteness)

	def getDatabaseName(self):
		"""Returns name of the MySQL database."""
		return self.dbNameEntry.get_text()

	def getHost(self):
		"""Returns hostname of ip-address of the MySQL server."""
		return self.hostEntry.get_text()

	def getUsername(self):
		"""Returns username of the MySQL database."""
		return self.usernameEntry.get_text()

	def getPassword(self):
		"""Returns password of the MySQL database."""
		return self.passwordEntry.get_text()


class SelectDefaultModuleStatusPage(Page):
	"""This page class lets user to select if all modules are on or off
	by default.
	"""

	def __init__(self, controller):
		"""Constructor method."""
		super(SelectDefaultModuleStatusPage, self).__init__(controller, SELECT_MODULE_STATUS_PAGE_ID)
		self.isComplete = True
		self.setTitle('Enable/Disable All Commands')
		self.setNextPageIdFunc(lambda: NAME_BOT_PAGE_ID)
		self.setCompletenessFunc(lambda: True)
		# get widgets from builder
		self.widget = controller.getViewObject('selectDefaultModuleStatusPage')
		self.yesRadioButton = controller.getViewObject('moduleStatusYesRadioButton')
		self.noRadioButton  = controller.getViewObject('moduleStatusNoRadioButton')
		# group the radio buttons together and select default button
		self.yesRadioButton.set_group(self.noRadioButton)
		self.yesRadioButton.set_active(True)

	def areModulesEnabledByDefault(self):
		"""Returns True if all modules will be enabled by default.
		On False modules are disabled.
		"""
		return self.yesRadioButton.get_property('active')

class NameBotPage(Page):
	"""This page class lets user to give a name for the bot."""

	def __init__(self, controller):
		"""Constructor method."""
		super(NameBotPage, self).__init__(controller, NAME_BOT_PAGE_ID)
		self.setTitle('Bot Name')
		self.setNextPageIdFunc(lambda: FINISH_PAGE_ID)
		# page is complete if given bot name is not empty
		self.setCompletenessFunc(lambda self: len(self.botNameEntry.get_text()) > 0, self)
		self.widget = controller.getViewObject('nameBotPage')
		self.botNameEntry = controller.getViewObject('botNameEntry')
		self.botNameEntry.connect('notify::text', self.updateCompleteness)
		self.name = ''

	def enter(self):
		# only modify the bot name if user hasn't changed it
		if self.name == self.botNameEntry.get_text():
			self.name = '%s @ RK%s' % (self.controller.getCharacterName(), self.controller.getDimension())
			self.botNameEntry.set_text(self.name)
		super(NameBotPage, self).enter()

	def getBotName(self):
		return self.botNameEntry.get_text()

	def setBotName(self, name):
		self.botNameEntry.set_text(name)

class FinishPage(Page):
	"""The final page in the wizard. Lists a summary of the bot settings
	before it is added to the Bot Manager.
	"""

	def __init__(self, controller):
		"""Constructor method."""
		super(FinishPage, self).__init__(controller, FINISH_PAGE_ID)
		self.setType(gtk.ASSISTANT_PAGE_CONFIRM)
		self.setTitle('Summary')
		self.widget = controller.getViewObject('finishPage')
		self.summaryLabel = controller.getViewObject('summaryLabel')

	def enter(self):
		values = self.controller.getSummaryValues()
		config = values['config']
		super(FinishPage, self).enter()

		def buildLine(name, value):
			return '<b>' + name + ':</b> ' + str(value) + '\n'

		contents = ''
		contents += '------ General Settings ------\n'
		contents += buildLine('Name', values['name'])
		contents += buildLine('Bot Software Path', values['root path'])
		contents += buildLine('Bot Config Path', values['conf path'])
		contents += '---- Game Account Settings ----\n'
		contents += buildLine('Game Account Username', len(config.getVar('login')) * '*')
		contents += buildLine('Game Account Password', len(config.getVar('password')) * '*')
		contents += buildLine('Game Character', config.getVar('name'))
		contents += buildLine('Game Dimension', config.getVar('dimension'))
		if len(config.getVar('my_guild')) > 0:
			contents += buildLine('Organization Name', config.getVar('my_guild'))
		contents += buildLine('Super Administrator', config.getVar('SuperAdmin'))
		contents += '------ Database Settings ------\n'
		if config.getVar('DB Type') == 'sqlite':
			path = os.path.join(values['root path'], config.getVar('DB Host'), config.getVar('DB Name'))
			path = os.path.normpath(path)
			contents += buildLine('Sqlite Database File Path', path)
		elif config.getVar('DB Type') == 'mysql':
			contents += buildLine('MySQL Database Name', config.getVar('DB Name'))
			contents += buildLine('MySQL Host Address', config.getVar('DB Host'))
			contents += buildLine('MySQL Username', len(config.getVar('DB username')) * '*')
			contents += buildLine('MySQL Password', len(config.getVar('DB password')) * '*')
		self.summaryLabel.set_markup(contents)
