Feature: ALIEN_MODULE module
	In order to spank alien butt!
	As a alium hunter
	I need to be able to have helpful commands for alien content

	Background:
		Given the bot is running
		And I am logged in

	Scenario: Command leprocs lists all professions
		When I give command "!leprocs"
		Then the response should contain words:
			| profession |
			| adv        |
			| agent      |
			| crat       |
			| doc        |
			| enf        |
			| eng        |
			| fix        |
			| keep       |
			| ma         |
			| mp         |
			| nt         |
			| sol        |
			| shade      |
			| trader     |

	Scenario Outline: Command leprocs lists procs for a profession
		When I give command "!leprocs <profession>"
		Then the response should contain word "<proc>"

		Examples:
			| profession | proc | 
			| adv        | N/A  |
			| agent      | N/A  |
			| crat       | N/A  |
			| doc        | N/A  |
			| enf        | N/A  |
			| eng        | N/A  |
			| fix        | N/A  |
			| keep       | N/A  |
			| ma         | N/A  |
			| mp         | N/A  |
			| nt         | N/A  |
			| sol        | N/A  |
			| shade      | N/A  |
			| trader     | N/A  |
