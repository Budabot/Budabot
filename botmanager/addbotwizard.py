#! /usr/bin/env python
# -*- coding: utf-8 -*-

import os
import webbrowser
import gtk
import sys
from botconfigfile import BotPhpConfigFile
from addbotwizardpages import SelectActionPage, SelectImportPage, NameBotPage
from addbotwizardpages import FinishPage, SelectBotInstallDirectoryPage, EnterAccountInfoPage
from addbotwizardpages import EnterCharacterInfoPage, SelectBotTypePage, EnterSuperAdminPage
from addbotwizardpages import SelectDatabaseSettingsPage, SelectDefaultModuleStatusPage
from addbotwizardpages import SelectDatabaseTypePage, EnterSqliteSettingsPage, EnterMysqlSettingsPage
import addbotwizardpages
from utils import resourcePath

class AddBotWizardController:
	""""""
	
	def __init__(self, parentWindow, botModel, settingModel):
		"""Constructor method."""
		self.settingModel = settingModel
		self.handler = None

		# load addbotwizard.glade file
		self.builder = gtk.Builder()
		self.builder.add_from_file(resourcePath('addbotwizard.glade'))

		self.assistant = Assistant()
		self.assistant.set_property('title', 'Budabot - Add Bot Wizard')

		# position the wizard on top of control panel's window
		self.assistant.set_transient_for(parentWindow)
		self.assistant.set_property('window-position', gtk.WIN_POS_CENTER_ON_PARENT)

		self.assistant.connect('apply', self.onApplyClicked)
		self.assistant.connect('cancel', self.onCancelClicked)
		self.assistant.connect('close', self.onCloseClicked)
		# create pages
		self.selectActionPage        = SelectActionPage(self)
		self.selectImportPage        = SelectImportPage(self)
		self.selectBotInstallDirPage = SelectBotInstallDirectoryPage(self)
		self.enterAccountInfoPage    = EnterAccountInfoPage(self)
		self.enterCharacterInfoPage  = EnterCharacterInfoPage(self)
		self.selectBotTypePage       = SelectBotTypePage(self)
		self.enterSuperAdminPage     = EnterSuperAdminPage(self)
		self.selectDBSettingsPage    = SelectDatabaseSettingsPage(self)
		self.selectDBTypePage        = SelectDatabaseTypePage(self)
		self.enterSqliteSettingsPage = EnterSqliteSettingsPage(self)
		self.enterMysqlSettingsPage  = EnterMysqlSettingsPage(self)
		self.selectModuleStatusPage  = SelectDefaultModuleStatusPage(self)
		self.botNamePage             = NameBotPage(self)
		self.finishPage              = FinishPage(self)
		# Add pages to the wizard
		self.assistant.appendPage(self.selectActionPage) # first appended page is the starting page
		self.assistant.appendPage(self.selectImportPage)
		self.assistant.appendPage(self.selectBotInstallDirPage)
		self.assistant.appendPage(self.enterAccountInfoPage)
		self.assistant.appendPage(self.enterCharacterInfoPage)
		self.assistant.appendPage(self.selectBotTypePage)
		self.assistant.appendPage(self.enterSuperAdminPage)
		self.assistant.appendPage(self.selectDBSettingsPage)
		self.assistant.appendPage(self.selectDBTypePage)
		self.assistant.appendPage(self.enterSqliteSettingsPage)
		self.assistant.appendPage(self.enterMysqlSettingsPage)
		self.assistant.appendPage(self.selectModuleStatusPage)
		self.assistant.appendPage(self.botNamePage)
		self.assistant.appendPage(self.finishPage)

		self.selectActionPage.connect('left', self.onSelectActionPageLeft)

		# connect any 'activate-link' signals (if available) to onLink() handler
		for object in self.builder.get_objects():
			try:
				object.connect('activate-link', self.onLink)
			except TypeError:
				pass

	def getViewObject(self, name):
		"""Wrapper method for requesting objects from Gtk's Builder."""
		return self.builder.get_object(name)

	def getSettingModel(self):
		"""Returns the SettingModel object."""
		return self.settingModel

	def getAssistant(self):
		"""Returns assistant's object."""
		return self.assistant

	def show(self):
		"""This method shows the wizard to user."""
		self.assistant.show_all()

	def hide(self):
		"""This method hides the wizard from user."""
		self.assistant.hide()

	def onLink(self, caller, uri):
		"""Handles any clicked hyperlinks by opening them to default browser."""
		webbrowser.open(uri)
		return True

	def onApplyClicked(self, caller):
		""""""
		self.handler.apply()

	def onCancelClicked(self, caller):
		""""""
		self.hide()

	def onCloseClicked(self, caller):
		""""""
		self.hide()

	def onSelectActionPageLeft(self, page):
		"""This signal handler is called when wizard leaves select action page."""
		if page.getActionType() == SelectActionPage.TYPE_IMPORT:
			self.handler = ImportHandler(self)
		elif page.getActionType() == SelectActionPage.TYPE_ADDNEW:
			self.handler = AddNewHandler(self)

	def __getattr__(self, name):
		"""Called when given attribute name is not found.
		Delegates any attribute requests to current handler.
		"""
		return getattr(self.handler, name)

class Assistant(gtk.Assistant):
	def __init__(self):
		super(Assistant, self).__init__()
		self.pages = []
		self.currentPage = None
		self.set_forward_page_func(self.getNextPage)
		self.connect('prepare', self.onPreparePage)

	def appendPage(self, page):
		"""Helper method for adding pages to the wizard."""
		self.pages.append(page)
		page.index = self.append_page(page.getWidget())
		self.set_page_title(page.getWidget(), page.getTitle())
		self.set_page_type(page.getWidget(), page.getType())
		page.connect('notify::complete', self.onPageCompletenessChanged)

	def getNextPage(self, currentPageIndex):
		"""This method is called by GtkAssistant's implementation to determine
		to which page index the wizard should change when user clicks
		forward-button. -1 means error.
		"""
		for page in self.pages:
			if page.index == currentPageIndex:
				for nextPage in self.pages:
					if nextPage.id == page.getNextPageId():
						return nextPage.index
		return -1

	def onPreparePage(self, caller, pageWidget):
		if self.currentPage != None:
			self.currentPage.leave()
		for page in self.pages:
			if page.getWidget() == pageWidget:
				page.enter()
				self.set_page_complete(page.getWidget(), page.get_property('complete'))
				self.currentPage = page

	def onPageCompletenessChanged(self, page, property):
		self.set_page_complete(page.getWidget(), page.get_property('complete'))

class AddNewHandler(object):
	""""""
	def __init__(self, controller):
		"""Constructor method."""
		self.controller = controller
		controller.selectBotInstallDirPage.connect('left', self.onSelectBotInstallDirectoryPageLeft)
		self.botConfig = None

	def apply(self):
		self.botConfig.save()
		rootPath = self.getBotInstallPath()
		confPath = self.botConfig.getFilePath()
		name = self.controller.botNamePage.getBotName()
		self.controller.settingModel.addBot(name, confPath, rootPath)
		self.controller.settingModel.save()

	def getBotInstallPath(self):
		"""Returns currently selected bot install path."""
		return self.controller.selectBotInstallDirPage.getSelectedBotRootPath()

	def getCharacterName(self):
		return self.controller.enterCharacterInfoPage.getCharacterName()

	def getDimension(self):
		return self.controller.enterCharacterInfoPage.getDimension()

	def updateConfig(self):
		self.botConfig.setVar('login', self.controller.enterAccountInfoPage.getUsername())
		self.botConfig.setVar('password', self.controller.enterAccountInfoPage.getPassword())

		self.botConfig.setVar('name', self.controller.enterCharacterInfoPage.getCharacterName())
		self.botConfig.setVar('dimension', int(self.controller.enterCharacterInfoPage.getDimension()))

		if self.controller.selectBotTypePage.isOrganizationBot():
			self.botConfig.setVar('my_guild', self.controller.selectBotTypePage.getOrganizationName())

		self.botConfig.setVar('SuperAdmin', self.controller.enterSuperAdminPage.getSuperAdminName())

		if self.controller.selectDBSettingsPage.areManualSettingsUsed():
			if self.controller.selectDBTypePage.isSqliteSelected():
				self.botConfig.setVar('DB Type', 'sqlite')
				self.botConfig.setVar('DB Name', self.controller.enterSqliteSettingsPage.getDatabaseFilename())
				path = self.controller.enterSqliteSettingsPage.getDatabaseFolderPath()
				if sys.platform.startswith('win32'):
					path = path.replace('\\', '/')
				path = './' + path + '/'
				self.botConfig.setVar('DB Host', path)
			elif self.controller.selectDBTypePage.isMysqlSelected():
				self.botConfig.setVar('DB Type', 'mysql')
				self.botConfig.setVar('DB Name', self.controller.enterMysqlSettingsPage.getDatabaseName())
				self.botConfig.setVar('DB Host', self.controller.enterMysqlSettingsPage.getHost())
				self.botConfig.setVar('DB username', self.controller.enterMysqlSettingsPage.getUsername())
				self.botConfig.setVar('DB password', self.controller.enterMysqlSettingsPage.getPassword())

		self.botConfig.setVar('default_module_status', int(self.controller.selectModuleStatusPage.areModulesEnabledByDefault()))

	def getSummaryValues(self):
		values = {}
		values['name'] = self.controller.botNamePage.getBotName()
		values['root path'] = self.getBotInstallPath()
		values['conf path'] = self.botConfig.getFilePath()
		self.updateConfig()
		values['config'] = self.botConfig
		return values

	def onSelectBotInstallDirectoryPageLeft(self, page):
		"""This signal handler is called when wizard leaves page where user can
		give path to the bot's directory.
		"""
		if page.get_property('complete'):
			# build a path to the new configuration file
			dirPath = os.path.join(self.getBotInstallPath(), 'conf')
			fileName = 'config.php'
			counter = 0
			while os.path.exists(os.path.join(dirPath, fileName)):
				fileName = 'config%s.php' % (counter + 2)
				counter += 1
			path = os.path.join(dirPath, fileName)
			# initialize the file
			self.botConfig = BotPhpConfigFile(path)
			self.botConfig.load()

class ImportHandler(object):
	""""""
	def __init__(self, controller):
		"""Constructor method."""
		self.controller = controller
		controller.selectImportPage.connect('left', self.onSelectImportPageLeft)
		self.botConfig = None

	def apply(self):
		rootPath = self.controller.selectImportPage.getSelectedBotRootPath()
		confPath = self.controller.selectImportPage.getSelectedBotConfFilePath()
		name = self.controller.botNamePage.getBotName()
		self.controller.settingModel.addBot(name, confPath, rootPath)
		self.controller.settingModel.save()

	def getCharacterName(self):
		return self.botConfig.getVar('name')

	def getDimension(self):
		return self.botConfig.getVar('dimension')

	def getSummaryValues(self):
		values = {}
		values['name'] = self.controller.botNamePage.getBotName()
		values['root path'] = self.controller.selectImportPage.getSelectedBotRootPath()
		values['conf path'] = self.controller.selectImportPage.getSelectedBotConfFilePath()
		values['config'] = self.botConfig
		return values

	def onSelectImportPageLeft(self, page):
		if page.get_property('complete'):
			path = self.controller.selectImportPage.getSelectedBotConfFilePath()
			if path:
				config = BotPhpConfigFile(path)
				config.load()
				self.botConfig = config
