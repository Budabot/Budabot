Feature: Update IP address of HTTAPI from whatismyip.com
	In order to configure HTTPAPI module
	As a bot admin
	I need to set my public IP address easily

	Scenario: Updating is succesful
		Given "HTTPAPI_MODULE" module is enabled
		And my public IP address is "11.22.33.44"
		When I give command "!httpapi updateipaddress"
		Then the response should contain phrase "Success, updated httpapi_address setting to: '11.22.33.44'"
