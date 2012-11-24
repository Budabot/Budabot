Feature: Show AI armor tradeskill processes
	In order to spank alien butt!
	As an alium hunter
	I need to see a guide which descripes how to build AI armors
	
	Background:
		Given "ALIEN_MODULE" module is enabled

	Scenario: Help is shown to user
		When I give command "!aiarmor"
		Then the response should contain phrase "help (aiarmor)"

	Scenario: Show tradeskill process for QL300 armor
		When I give command "!aiarmor cc"
		Then the response should contain phrase "Building process for 300 Combined Commando's Jacket"

	Scenario: Show tradeskill process for custom QL armor
		When I give command "!aiarmor strong 150"
		Then the response should contain phrase "Building process for 150 Strong"
