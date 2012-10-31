#! /usr/bin/env python
# -*- coding: utf-8 -*-

import gobject
import gtk
from utils import setItemAsBold

# check which systray functionality we should use
haveAppIndicator = True
try:
	import appindicator
except:
	haveAppIndicator = False

class SystrayController(gobject.GObject):
	""""""

	# Define custom signals that this class can emit.
	__gsignals__ = {
		# this signal is emitted when user attempts to open the control panel
		'open_requested': (gobject.SIGNAL_RUN_LAST, gobject.TYPE_NONE, ()),
		# this signal is emitted when user attempts to change control panel's visibility
		'toggle_requested': (gobject.SIGNAL_RUN_LAST, gobject.TYPE_NONE, ()),
		# this signal is emitted when user attempts to exit the application
		'exit_requested': (gobject.SIGNAL_RUN_LAST, gobject.TYPE_NONE, ()),
	}

	def __init__(self):
		"""Constructor method."""
		self.__gobject_init__()

		self.contextMenu = gtk.Menu()

		if haveAppIndicator:
			iconPath = gtk.icon_theme_get_default().lookup_icon('program-icon', 16, 0).get_filename()
			self.icon = appindicator.Indicator('botmanager-indicator', iconPath, appindicator.CATEGORY_APPLICATION_STATUS)
			self.icon.set_status(appindicator.STATUS_ACTIVE)
			self.icon.set_menu(self.contextMenu)
		else:
			self.icon = gtk.StatusIcon()
			self.icon.set_from_icon_name('program-icon')
			self.icon.connect('activate', self.onSystrayClicked)
			self.icon.connect('popup-menu', self.onMenu)
			self.icon.set_visible(True)
			self.icon.set_blinking(False)
			self.icon.set_tooltip_text('Budabot Bot Manager')
			self.contextMenu.connect('enter-notify-event', self.onMouseEnterContextMenu)
			self.contextMenu.connect('leave-notify-event', self.onMouseLeaveContextMenu)
		
		# build context menu
		self.itemOpen = gtk.MenuItem('Open')
		self.itemOpen.set_visible(True)
		self.itemOpen.connect('activate', self.onOpenClicked)
		self.contextMenu.append(self.itemOpen)
		itemExit = gtk.MenuItem('Exit')
		itemExit.set_visible(True)
		itemExit.connect('activate', self.onExitClicked)
		self.contextMenu.append(itemExit)
		
		# set default action as bold
		setItemAsBold(self.itemOpen)
		
		self.closeTimerId = None

	def hideIcon(self):
		"""Hides the systray icon.
		Required for win32 as the icon doesn't disappear automatically on program exit.
		"""
		if not haveAppIndicator:
			self.icon.set_visible(False)

	def onSystrayClicked(self, sender):
		"""This callback handler is called when user clicks the systray icon."""
		self.emit('toggle_requested')

	def onOpenClicked(self, sender):
		"""This callback handler is called when user attempts to open the control panel."""
		self.emit('open_requested')

	def onExitClicked(self, sender):
		"""This callback handler is called when user attempts to exit the application."""
		self.emit('exit_requested')

	def onMouseLeaveContextMenu(self, sender, event):
		"""This callback handler is called when mouse cursor leaves
		right-click-context-menu's area.
		Starts the timeout which closes the context menu automatically.
		"""
		if self.closeTimerId == None:
			self.closeTimerId = gtk.timeout_add(1000, self.closeContextMenu)

	def onMouseEnterContextMenu(self, sender, event):
		"""This callback handler is called when mouse cursor enters
		right-click-context-menu's area.
		Stops the timeout which closes the context menu automatically.
		"""
		if self.closeTimerId != None:
			gtk.timeout_remove(self.closeTimerId)
			self.closeTimerId = None

	def closeContextMenu(self):
		"""This method closes the context menu."""
		self.contextMenu.popdown()
		return False

	def onMenu(self, sender, button, activateTime):
		"""This callback handler is called when popup menu should be shown."""
		#gtk.StatusIcon.position_menu(self.contextMenu, self.icon)
		self.contextMenu.popup(None, None, None, button, activateTime)
		#self.startCloseTimout()

	def onControlPanelVisibilityChanged(self, sender, visibility):
		"""This callback handler is called control panel is either shown or hidden."""
		# disable/enable context menu's open item
		self.itemOpen.set_sensitive(visibility == False)

# register class so that custom signals will work
gobject.type_register(SystrayController)
