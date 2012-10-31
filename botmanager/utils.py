#! /usr/bin/env python
# -*- coding: utf-8 -*-

import weakref
import gtk
import os
import sys

class WeakCallback(object):
	"""
	Wrapper for callbacks.
	The wrapper holds weak reference to the given callback.
	
	Modified from Liori's example at
	  http://stackoverflow.com/questions/1364923/
	"""

	def __init__(self, callback):
		self.weak_obj = weakref.ref(callback.im_self)
		self.weak_fun = weakref.ref(callback.im_func)

	def __call__(self, *things):
		obj = self.weak_obj()
		fun = self.weak_fun()
		if obj is not None and fun is not None:
			return fun(obj, *things)

def weakConnect(sender, signal, callback):
	"""Connects given callback weakly to given signal so that the callback
	will not increase reference count.
	
	This will help with memory handling as the signal-connects will not prevent
	garbage collector from removing unneeded objects.
	"""
	wrapper = WeakCallback(callback)
	return sender.connect(signal, wrapper)

def setItemAsBold(item):
	"""Sets item's text to bold, to indicate that the item is the default."""
	label = item.get_children()
	label = label[0]
	label.set_markup('<b>' + label.get_text() + '</b>')

def getBotUIStatus(bot):
	"""Returns bot's status as a text and pixbuf, ready to be usable on UI."""
	apiAccessible = bot.get_property('apiAccessible')
	isRunning = bot.get_property('isRunning')
	if isRunning and apiAccessible:
		return ('Running', 'status-running')
	elif isRunning:
		return ('Started', 'status-started')
	else:
		return ('Not running', 'status-not-running')

def resourcePath(relative):
	"""Returns path to where application resource are."""
	try:
		basePath = sys._MEIPASS
	except AttributeError:
		basePath = os.path.abspath(".")
	return os.path.join(basePath, relative)

