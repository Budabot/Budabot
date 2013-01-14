@disabled
Feature: Login
	In order to access the bot from web browser
	As a user
	I need to be able to log in with my API credentials
	
	Background:
		Given I'm not logged in

	Scenario: Login succesfully
		When I enter my username
		And I enter my password
		And I submit the credentials
		Then I'm logged in succesfully

	Scenario: Invalid username
		When I enter invalid username
		And I enter my password
		And I submit the credentials
		Then logging in fails

	Scenario: Invalid password
		When I enter invalid password
		And I enter my username
		And I submit the credentials
		Then logging in fails
