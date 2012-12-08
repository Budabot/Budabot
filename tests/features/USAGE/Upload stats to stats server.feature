Feature: Upload stats to stats server
	In order to see how Budabot is being used
	As a l337 budabot dev
	Budabot must be able to send usage and stats information to a stats server

	Scenario: Upload is succesful
		Given "USAGE" module is enabled
		And stats service is online
		When "24hrs" event is sent to "USAGE" module
		Then the received stats post contains all necessary information

