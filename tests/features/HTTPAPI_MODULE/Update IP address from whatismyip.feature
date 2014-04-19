Feature: Update IP address of HTTP_SERVER_MODULE from whatismyip.com
	In order to configure HTTP_SERVER_MODULE
	As a bot admin
	I need to set my public IP address easily

	Scenario: Updating is succesful
		Given "HTTP_SERVER_MODULE" module is enabled
		And my public IP address is "11.22.33.44"
		When I give command "!httpserver updateipaddress"
		Then the response should contain phrase "Success, updated http_server_address setting to: '11.22.33.44'"
