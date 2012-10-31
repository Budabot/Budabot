#! /usr/bin/env python
# -*- coding: utf-8 -*-

import json
import struct
from twisted.internet import defer
from twisted.internet.protocol import Protocol, ClientFactory

# api request version
API_VERSION = '1.2'

# request types
API_SIMPLE_MSG = 0
API_ADVANCED_MSG = 1

# response status codes
API_SUCCESS = 0
API_INVALID_VERSION = 1
API_UNSET_PASSWORD = 2
API_INVALID_PASSWORD = 3
API_INVALID_REQUEST_TYPE = 4
API_UNKNOWN_COMMAND = 5
API_ACCESS_DENIED = 6
API_SYNTAX_ERROR = 7
API_EXCEPTION = 8

class Budapi(object):
	""""""

	def __init__(self):
		"""Constructor method."""
		self.port = 5250
		self.host = '127.0.0.1'
		self.username = None
		self.password = None

	def getPort(self):
		"""This method returns bot's ip port."""
		return self.port

	def setPort(self, port):
		"""This method sets port of the bot where to connect."""
		self.port = port

	def getHost(self):
		"""This method returns bot's address."""
		return self.host

	def setHost(self, host):
		"""This method sets bot's host address."""
		self.host = host

	def getUsername(self):
		"""This method returns name of the user."""
		return self.username

	def setUsername(self, name):
		"""This method sets name of an user who will access the bot."""
		self.username = name

	def getPassword(self):
		"""This method returns password of the user."""
		return self.password

	def setPassword(self, password):
		"""This method sets password of an user who will access the bot."""
		self.password = password

	def sendCommand(self, command, payload = None):
		"""This method sends a command to Budabot bot through its API.
		Returns twisted deferred.
		"""
		factory = BudapiClientFactory(self.username, self.password, command)
		from twisted.internet import reactor
		reactor.connectTCP(self.host, self.port, factory)
		return factory.deferred

class BudapiServerException(Exception):
	"""Raised when the server returns some status which is not API_SUCCESS.
	Contains two arguments: status code and message.
	"""

class BudapiProtocol(Protocol):
	"""Implements the BudAPI protocol."""
	
	STATE_READ_LENGTH = 0
	STATE_READ_RESPONSE = 1
	
	def connectionMade(self):
		self.receivedData = ''
		self.state = self.STATE_READ_LENGTH
		self.response = None
		self.responselength = 0
		request = {
			'version': API_VERSION,
			'username': self.factory.username,
			'password': self.factory.password,
			'command': self.factory.command,
			'type': API_SIMPLE_MSG,
			'syncId': 0
		}
		requestJson = json.dumps(request)
		data = '%s%s' % (struct.pack('!H', len(requestJson)), requestJson)
		self.transport.write(data)

	def dataReceived(self, data):
		"""This callback is called automatically from Twisted when new has
		been received from server.
		"""
		self.receivedData += data
		# read length of the response
		if self.state == self.STATE_READ_LENGTH:
			if self.consumeLength():
				self.state = self.STATE_READ_RESPONSE
		# read response data
		if self.state == self.STATE_READ_RESPONSE:
			if self.consumeResponse():
				# report results to caller
				if self.response['status'] == API_SUCCESS:
					self.factory.deferred.callback(self.response['message'])
				else:
					self.factory.deferred.errback(BudapiServerException(self.response['message'], self.response['status']))
				# break connection
				self.transport.loseConnection()

	def consumeLength(self):
		"""Reads first two bytes from response which indicates length of the response."""
		if len(self.receivedData) >= 2:
			self.responselength = struct.unpack('!H', self.receivedData[0:2])[0]
			self.receivedData = self.receivedData[2:]
			return True
		return False

	def consumeResponse(self):
		"""Reads the response."""
		if len(self.receivedData) >= self.responselength:
			# decode status and message from data
			responseJson = self.receivedData[0:self.responselength]
			self.response = json.loads(responseJson)
			return True
		return False

class BudapiClientFactory(ClientFactory):
	"""A factory for BudapiProtocols."""
	protocol = BudapiProtocol

	def __init__(self, username, password, command):
		self.deferred = defer.Deferred()
		self.username = username
		self.password = password
		self.command  = command

	def clientConnectionFailed(self, connector, reason):
		"""This callback is called when connection attempt fails."""
		if self.deferred is not None:
			d, self.deferred = self.deferred, None
			d.errback(reason)
